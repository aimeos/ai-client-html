<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Factory;


/**
 * Common methods for all client factories.
 *
 * @package Client
 * @subpackage Html
 */
class Base
{
	private static $objects = [];


	/**
	 * Injects a client object.
	 * The object is returned via create() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Client\Html\Iface|null $client ExtJS client object
	 */
	public static function injectClient( $classname, \Aimeos\Client\Html\Iface $client = null )
	{
		self::$objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Client\Html\Catalog\Decorator\"
	 * @return \Aimeos\Client\Html\Iface Client object
	 */
	protected static function addDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Client\Html\Iface $client, array $decorators, $classprefix )
	{
		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client = new $classname( $client, $context );

			\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $client );
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param string $path Path of the client in lower case, e.g. "catalog/detail"
	 * @return \Aimeos\Client\Html\Iface Client object
	 */
	protected static function addClientDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Client\Html\Iface $client, $path )
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );
		$config = $context->getConfig();

		/** client/html/common/decorators/default
		 * Configures the list of decorators applied to all html clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  client/html/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" and
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'client/html/common/decorators/default', [] );
		$excludes = $config->get( 'client/html/' . $path . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Client\\Html\\Common\\Decorator\\';
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Client\\Html\\Common\\Decorator\\';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/global', [] );
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Client\\Html\\' . $localClass . '\\Decorator\\';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/local', [] );
		$client = self::addDecorators( $context, $client, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Creates a client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @return \Aimeos\Client\Html\\Iface Client object
	 * @throws \Aimeos\Client\Html\Exception If client couldn't be found or doesn't implement the interface
	 */
	protected static function createClient( \Aimeos\MShop\Context\Item\Iface $context, $classname, $interface )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$client = new $classname( $context );

		\Aimeos\MW\Common\Base::checkClass( $interface, $client );

		return $client;
	}
}
