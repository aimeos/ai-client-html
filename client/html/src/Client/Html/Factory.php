<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html;


/**
 * Common factory for HTML clients.
 *
 * @package Client
 * @subpackage Html
 * @deprecated Use Html class instead
 */
class Factory extends \Aimeos\Client\Html
{
	/**
	 * Creates a new client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string $type Type of the client, e.g 'account/favorite' for \Aimeos\Client\Html\Account\Favorite\Standard
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\Html\Iface HTML client implementing \Aimeos\Client\Html\Iface
	 * @throws \Aimeos\Client\Html\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, $type, $name = null )
	{
		return parent::create( $context, $type, $name );
	}
}
