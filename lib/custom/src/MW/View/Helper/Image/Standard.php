<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Image;


/**
 * View helper class for creating an HTML image tag
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Image\Iface
{
	/**
	 * Returns the HTML image tag for the given media item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $media Media item
	 * @return string HTML image tag
	 */
	public function transform( \Aimeos\MShop\Media\Item\Iface $media ) : string
	{
		$view = $this->getView();
		$enc = $view->encoder();

		$sources = [];
		foreach( $media->getPreviews() as $type => $path ) {
			$sources[$type] = $view->content( $path );
		}

		$variant = '';
		foreach( $media->getRefItems( 'attribute', null, 'variant' ) as $id => $item ) {
			$variant .= ' data-variant-' . $item->getType() . '="' . $enc->attr( $id ) . '"';
		}

		if( !strncmp( $media->getMimetype(), 'video/', 6 ) )
		{
			return '<video autoplay muted class="item" id="image-' . $media->getId() . '"
				itemscope itemtype="http://schema.org/VideoObject"
				poster="' . $enc->attr( $view->content( $media->getPreview( 600 ) ) ) . '"
				src="' . $enc->attr( $view->content( $media->getUrl() ) ) . '"
				alt="' . $enc->attr( $media->getProperties( 'title' )->first( $media->getName() ) ) . '"
				' . $variant . '></video>';
		}

		return '<img class="item" id="image-' . $media->getId() . '"
			itemscope itemprop="image" itemtype="http://schema.org/ImageObject"
			src="' . $enc->attr( $view->content( $media->getPreview() ) ) . '"
			srcset="' . $enc->attr( $view->imageset( $media->getPreviews() ) ) . '"
			data-image="' . $enc->attr( $view->content( $media->getPreview() ) ) . '"
			data-sources="' . $enc->attr( json_encode( $sources, JSON_FORCE_OBJECT ) ) . '"
			alt="' . $enc->attr( $media->getProperties( 'title' )->first( $media->getName() ) ) . '"
			' . $variant . ' />';
	}
}
