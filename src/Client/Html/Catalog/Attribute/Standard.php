<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Attribute;


/**
 * Default implementation of catalog attribute HTML client
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Filter\Standard
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/attribute/name
	 * Class name of the used catalog attribute client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Attribute\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Attribute\Myattribute
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/attribute/name = Myattribute
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyAttribute"!
	 *
	 * @param string Last part of the class name
	 * @since 2018.04
	 */


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		return null;
	}


	/**
	 * Returns the names of the subpart clients
	 *
	 * @return array List of client names
	 */
	protected function getSubClientNames() : array
	{
		return ['attribute'];
	}


	/** client/html/catalog/attribute/decorators/excludes
	 * Excludes decorators added by the "common" option from the catalog attribute html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "client/html/common/decorators/default" before they are wrapped
	 * around the html client.
	 *
	 *  client/html/catalog/attribute/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/attribute/decorators/global
	 * @see client/html/catalog/attribute/decorators/local
	 */

	/** client/html/catalog/attribute/decorators/global
	 * Adds a list of globally available decorators only to the catalog attribute html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/attribute/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/attribute/decorators/excludes
	 * @see client/html/catalog/attribute/decorators/local
	 */

	/** client/html/catalog/attribute/decorators/local
	 * Adds a list of local decorators only to the catalog attribute html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/attribute/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/attribute/decorators/excludes
	 * @see client/html/catalog/attribute/decorators/global
	 */
}
