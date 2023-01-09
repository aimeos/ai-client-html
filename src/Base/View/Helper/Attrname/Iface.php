<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 * @package MW
 * @subpackage View
 */


namespace Aimeos\Base\View\Helper\Attrname;


/**
 * View helper class for creating an attribute names with prices
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\Base\View\Helper\Iface
{
	/**
	 * Returns the attribute name with price if available
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item
	 * @return string Attribute name with price (optional)
	 */
	public function transform( \Aimeos\MShop\Attribute\Item\Iface $item ) : string;
}
