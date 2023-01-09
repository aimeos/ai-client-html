<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 * @package MW
 * @subpackage View
 */


namespace Aimeos\Base\View\Helper\Image;


/**
 * View helper class for creating an HTML image tag
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\Base\View\Helper\Iface
{
	/**
	 * Returns the HTML image tag for the given media item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $media Media item
	 * @param string $sizes Preferred image srcset sizes
	 * @param bool $main TRUE for main image, FALSE for secondary images
	 * @return string HTML image tag
	 */
	public function transform( \Aimeos\MShop\Media\Item\Iface $media, string $sizes = '', $main = true ) : string;
}
