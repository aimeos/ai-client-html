<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Email\Account;


/**
 * Factory for account creation email HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Factory
	extends \Aimeos\Client\Html\Common\Factory\Base
	implements \Aimeos\Client\Html\Common\Factory\Iface
{
	/**
	 * Creates an account creation email client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\Html\Iface Filter part implementing \Aimeos\Client\Html\Iface
	 * @throws \Aimeos\Client\Html\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** client/html/email/account/name
		 * Class name of the used email account client implementation
		 *
		 * Each default HTML client can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Client\Html\Email\Account\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Client\Html\Email\Account\Myaccount
		 *
		 * then you have to set the this configuration option:
		 *
		 *  client/html/email/account/name = Myaccount
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAccount"!
		 *
		 * @param string Last part of the class name
		 * @since 2015.09
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'client/html/email/account/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\Client\\Html\\Email\\Account\\' . $name : '<not a string>';
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\Client\\Html\\Iface';
		$classname = '\\Aimeos\\Client\\Html\\Email\\Account\\' . $name;

		$client = self::createClient( $context, $classname, $iface );
		$client = self::addClientDecorators( $context, $client, 'email/account' );

		return $client->setObject( $client );
	}
}
