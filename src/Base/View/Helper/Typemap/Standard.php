<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2022
 * @package MW
 * @subpackage View
 */


namespace Aimeos\Base\View\Helper\Typemap;


/**
 * View helper class for mapping items by type
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\Base\View\Helper\Base
	implements \Aimeos\Base\View\Helper\Typemap\Iface
{
	/**
	 * Returns a map with type/ID/item structure
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Type\Iface
	 * @return \Aimeos\Map Map with type/ID/item structure
	 */
	public function transform( \Aimeos\Map $items ) : \Aimeos\Map
	{
		$map = [];

		foreach( $items as $item ) {
			$map[$item->getType()][$item->getId()] = $item;
		}

		return map( $map );
	}
}
