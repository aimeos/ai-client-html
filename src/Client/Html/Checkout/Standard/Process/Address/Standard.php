<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Address;


/**
 * Default implementation of checkout address process HTML client.
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
	 * Processes the input, e.g. provides the address form.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$context = $this->context();

		try
		{
			if( $context->user() !== null )
			{
				$basket = \Aimeos\Controller\Frontend::create( $context, 'basket' )->get();
				$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
				$item = $cntl->uses( ['customer/address'] )->get();

				foreach( $basket->getAddress( 'delivery' ) as $address )
				{
					if( $address->getAddressId() == '' && $address->get( 'nostore', false ) == false )
					{
						$addrItem = $cntl->createAddressItem()->copyFrom( $address );
						$cntl->addAddressItem( $addrItem )->store();
						$address->setAddressId( (string) $addrItem->getId() );
					}
				}
			}
		}
		catch( \Exception $e )
		{
			$msg = sprintf( 'Unable to save address for customer "%1$s": %2$s', $context->user(), $e->getMessage() );
			$context->logger()->info( $msg, 'client/html' );
		}

		parent::init();
	}
}
