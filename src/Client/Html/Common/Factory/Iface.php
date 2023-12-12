<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Factory;


/**
 * Generic interface for all HTML client factories.
 *
 * @package Client
 * @subpackage Html
 * @deprecated 2023.01
 */
interface Iface
{
	/**
	 *	Creates a client object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param string $name Client name (from configuration or "Standard" if null)
	 * @return \Aimeos\Client\Html\Iface New client object
	 */
	public static function create( \Aimeos\MShop\ContextIface $context, string $name = null ) : \Aimeos\Client\Html\Iface;
}
