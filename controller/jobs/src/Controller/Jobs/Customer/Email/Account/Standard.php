<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Account;

use \Aimeos\MW\Logger\Base as Log;


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
	public function getName() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Customer account e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Sends e-mails for new customer accounts' );
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
		$custManager = \Aimeos\MShop::create( $context, 'customer' );

		while( ( $msg = $queue->get() ) !== null )
		{
			try
			{
				if( ( $list = json_decode( $msg->getBody(), true ) ) === null )
				{
					$str = sprintf( 'Invalid JSON encode message: %1$s', $msg->getBody() );
					throw new \Aimeos\Controller\Jobs\Exception( $str );
				}

				$password = ( isset( $list['customer.password'] ) ? $list['customer.password'] : null );
				$item = $custManager->create()->fromArray( $list, true );

				$this->sendEmail( $context, $item, $password );

				$str = sprintf( 'Sent customer account e-mail to "%1$s"', $item->getPaymentAddress()->getEmail() );
				$context->getLogger()->log( $str, Log::DEBUG, 'email/customer/account' );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send customer account e-mail: ' . $e->getMessage();
				$context->getLogger()->log( $str . PHP_EOL . $e->getTraceAsString(), Log::ERR, 'email/customer/account' );
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
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context ) : \Aimeos\Client\Html\Iface
	{
		if( !isset( $this->client ) ) {
			$this->client = \Aimeos\Client\Html\Email\Account\Factory::create( $context );
		}

		return $this->client;
	}


	/**
	 * Sends the account creation e-mail to the e-mail address of the customer
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param string|null $password Customer clear text password
	 */
	protected function sendEmail( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Customer\Item\Iface $item, string $password = null )
	{
		$address = $item->getPaymentAddress();

		$view = $context->view();
		$view->extAddressItem = $address;
		$view->extAccountCode = $item->getCode();
		$view->extAccountPassword = $password;

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $address->getLanguageId() ?: 'en' ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->getClient( $context );
		$client->setView( $view );
		$client->header();
		$client->body();

		$mailer->send( $message );
	}
}
