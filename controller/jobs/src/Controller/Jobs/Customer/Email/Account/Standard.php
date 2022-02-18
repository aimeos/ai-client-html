<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
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
	use \Aimeos\Controller\Jobs\Mail;


	private $client;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Customer account e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Sends e-mails for new customer accounts' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->context();
		$queue = $context->queue( 'mq-email', 'customer/email/account' );
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

				$password = $list['customer.password'] ?? null;
				$item = $custManager->create()->fromArray( $list, true );

				$this->send( $item, $password );

				$str = sprintf( 'Sent customer account e-mail to "%1$s"', $item->getPaymentAddress()->getEmail() );
				$context->logger()->debug( $str, 'email/customer/account' );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send customer account e-mail: ' . $e->getMessage();
				$context->logger()->error( $str . PHP_EOL . $e->getTraceAsString(), 'email/customer/account' );
			}

			$queue->del( $msg );
		}
	}


	/**
	 * Sends the account creation e-mail to the e-mail address of the customer
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param string|null $password Customer clear text password
	 */
	protected function send( \Aimeos\MShop\Customer\Item\Iface $item, string $password = null )
	{
		$context = $this->context();
		$config = $context->config();
		$address = $item->getPaymentAddress();

		$view = $this->call( 'mailView', $address->getLanguageId() );
		$view->intro = $this->call( 'mailIntro', $address );
		$view->account = $item->getCode();
		$view->password = $password;
		$view->addressItem = $address;

		return $this->call( 'mailTo', $address )
			->subject( $context->translate( 'client', 'Your new account' ) )
			->html( $view->render( $config->get( 'controller/jobs/customer/email/account/template-html', 'customer/email/account/html' ) ) )
			->text( $view->render( $config->get( 'controller/jobs/customer/email/account/template-text', 'customer/email/account/text' ) ) )
			->send();
	}
}
