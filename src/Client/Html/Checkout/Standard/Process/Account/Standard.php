<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Account;


/**
 * Default implementation of checkout process account HTML client
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return '';
	}


	/**
	 * Processes the input, e.g. provides the account form.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$context = $this->context();

		try
		{
			$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT;
			$addresses = \Aimeos\Controller\Frontend::create( $context, 'basket' )->get()->getAddress( $type );

			if( $context->user() === null && ( $address = current( $addresses ) ) !== false )
			{
				$create = (bool) $this->view()->param( 'cs_option_account' );
				$userId = $this->getCustomerId( $address, $create );
				$context->setUserId( $userId );
			}
		}
		catch( \Exception $e )
		{
			$msg = sprintf( 'Unable to create an account: %1$s', $e->getMessage() );
			$context->logger()->notice( $msg, 'client/html' );
		}

		parent::init();
	}


	/**
	 * Creates a new account (if necessary) and returns its customer ID
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address object from order
	 * @param bool $new True to create the customer if it doesn't exist, false if not
	 * @return string|null Unique customer ID or null if no customer is available
	 */
	protected function getCustomerId( \Aimeos\MShop\Common\Item\Address\Iface $addr, bool $new ) : ?string
	{
		$context = $this->context();
		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );

		try {
			$id = $cntl->find( $addr->getEmail() )->getId();
		} catch( \Exception $e ) {
			$id = $new ? $cntl->add( $addr->toArray() )->store()->get()->getId() : null;
		}

		return $id;
	}
}
