<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Decorator interface for html clients.
 *
 * @package Client
 * @subpackage Html
 */
interface Iface
	extends \Aimeos\Client\Html\Iface
{
	/**
	 * Initializes a new client decorator object.
	 *
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 */
	public function __construct( \Aimeos\Client\Html\Iface $client, \Aimeos\MShop\ContextIface $context );
}
