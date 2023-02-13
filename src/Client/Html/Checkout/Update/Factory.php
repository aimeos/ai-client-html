<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Update;


/**
 * Factory for update checkout implementation for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Factory
	extends \Aimeos\Client\Html\Common\Factory\Base
	implements \Aimeos\Client\Html\Common\Factory\Iface
{
	/**
	 * Creates a update checkout client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Shop context instance with necessary objects
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\Html\Iface Update part implementing \Aimeos\Client\Html\Iface
	 * @throws \LogicException If class can't be instantiated
	 */
	public static function create( \Aimeos\MShop\ContextIface $context, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/checkout/update/name
		 * Class name of the used checkout update client implementation
		 *
		 * Each default HTML client can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Client\Html\Checkout\Update\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Client\Html\Checkout\Update\Myupdate
		 *
		 * then you have to set the this configuration option:
		 *
		 *  client/html/checkout/update/name = Myupdate
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyUpdate"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 */
		if( $name === null ) {
			$name = $context->config()->get( 'client/html/checkout/update/name', 'Standard' );
		}

		$iface = '\\Aimeos\\Client\\Html\\Iface';
		$classname = '\\Aimeos\\Client\\Html\\Checkout\\Update\\' . $name;

		if( ctype_alnum( $name ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in class name "%1$s"', $name ), 400 );
		}

		$client = self::createClient( $context, $classname, $iface );
		$client = self::addClientDecorators( $context, $client, 'checkout/update' );

		return $client->setObject( $client );
	}
}
