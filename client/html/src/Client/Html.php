<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client;


/**
 * Common factory for HTML clients
 *
 * @package Client
 * @subpackage Html
 */
class Html
{
	/**
	 * Creates a new client object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string $path Type of the client, e.g 'account/favorite' for \Aimeos\Client\Html\Account\Favorite\Standard
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\Html\Iface HTML client implementing \Aimeos\Client\Html\Iface
	 * @throws \Aimeos\Client\Html\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $path, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		if( empty( $path ) ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Client path is empty' ) );
		}

		$parts = explode( '/', $path );

		foreach( $parts as $key => $part )
		{
			if( ctype_alnum( $part ) === false )
			{
				$msg = sprintf( 'Invalid characters in client name "%1$s"', $path );
				throw new \Aimeos\Client\Html\Exception( $msg, 400 );
			}

			$parts[$key] = ucfirst( $part );
		}

		$factory = '\\Aimeos\\Client\\Html\\' . join( '\\', $parts ) . '\\Factory';

		if( class_exists( $factory ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
		}

		if( ( $client = @call_user_func_array( [$factory, 'create'], [$context, $name] ) ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
		}

		return $client;
	}
}
