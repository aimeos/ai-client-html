<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage View
 * @todo 2022.01 Rename namespace to Aimeos\MW\View\Helper\Media
 */


namespace Aimeos\MW\View\Helper\Image;


/**
 * View helper class for creating an HTML image tag
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the HTML image tag for the given media item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $media Media item
	 * @return string HTML image tag
	 */
	public function transform( \Aimeos\MShop\Media\Item\Iface $media ) : string;
}
