<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Account;


/**
 * Customer account e-mail job controller
 *
 * @package Controller
 * @subpackage Jobs
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	private $client;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Customer account e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Sends e-mails for new customer accounts' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$queue = $context->getMessageQueue( 'mq-email', 'customer/email/account' );
		$custManager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );

		while( ( $msg = $queue->get() ) !== null )
		{
			try
			{
				if( ( $list = json_decode( $msg->getBody(), true ) ) === null )
				{
					$str = sprintf( 'Invalid JSON encode message: %1$s', $msg->getBody() );
					throw new \Aimeos\Controller\Jobs\Exception( $str );
				}

				$password = ( isset( $list['customer.password'] ) ? $list['customer.password'] : '' );
				$item = $custManager->createItem();
				$item->fromArray( $list );

				$this->sendEmail( $context, $item, $password );

				$str = sprintf( 'Sent customer account e-mail to "%1$s"', $item->getPaymentAddress()->getEmail() );
				$context->getLogger()->log( $str, \Aimeos\MW\Logger\Base::DEBUG );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send customer account e-mail: ' . $e->getMessage();
				$context->getLogger()->log( $str );
			}

			$queue->del( $msg );
		}
	}


	/**
	 * Returns the product notification e-mail client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return \Aimeos\Client\Html\Iface Product notification e-mail client
	 */
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( !isset( $this->client ) )
		{
			$templatePaths = $this->getAimeos()->getCustomPaths( 'client/html/templates' );
			$this->client = \Aimeos\Client\Html\Email\Account\Factory::createClient( $context, $templatePaths );
		}

		return $this->client;
	}


	/**
	 * Sends the account creation e-mail to the e-mail address of the customer
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param string $password Customer clear text password
	 */
	protected function sendEmail( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Customer\Item\Iface $item, $password )
	{
		$address = $item->getPaymentAddress();

		$view = $context->getView();
		$view->extAddressItem = $address;
		$view->extAccountCode = $item->getCode();
		$view->extAccountPassword = $password;

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $address->getLanguageId() ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->getClient( $context );
		$client->setView( $view );
		$client->getHeader();
		$client->getBody();

		$mailer->send( $message );
	}
}
