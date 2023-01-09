<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Price;


/**
 * Default implementation of catalog price HTML client
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Filter\Standard
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/price/name
	 * Class name of the used catalog price client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Price\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Price\Myprice
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/price/name = Myprice
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyPrice"!
	 *
	 * @param string Last part of the class name
	 * @since 2018.04
	 */


	/**
	 * Returns the names of the subpart clients
	 *
	 * @return array List of client names
	 */
	protected function getSubClientNames() : array
	{
		return ['price'];
	}
}
