<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$target = $this->config( 'client/html/catalog/detail/url/target' );
$cntl = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$config = $this->config( 'client/html/catalog/detail/url/config', [] );

$items = [];
$enc = $this->encoder();

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


foreach( $this->get( 'suggestItems', [] ) as $id => $productItem )
{
	$media = $price = '';
	$name = $productItem->getName();
	$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );
	$priceItems = $productItem->getRefItems( 'price', 'default', 'default' );

	if( ( $mediaItem = reset( $mediaItems ) ) !== false ) {
		$media = $this->content( $mediaItem->getPreview() );
	}

	if( ( $priceItem = reset( $priceItems ) ) !== false ) {
		$price = sprintf( $priceFormat, $this->number( $priceItem->getValue() ), $this->translate( 'currency', $priceItem->getCurrencyId() ) );
	}

	$items[] = array(
		'label' => $name,
		'html' => '
			<li class="aimeos catalog-suggest">
				<a class="suggest-item" href="' . $enc->attr( $this->url( $target, $cntl, $action, ['d_prodid' => $id, 'd_name' => $name], [], $config ) ).'">
					<div class="item-name">' . $enc->html( $name ) . '</div>
					<div class="item-price">' . $enc->html( $price ) . '</div>
					<div class="item-image" style="background-image: url(' . $enc->attr( $media ) . ')"></div>
				</a>
			</li>
		'
	);
}

echo json_encode( $items );

?>
