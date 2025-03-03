<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */

$enc = $this->encoder();

$items = [];

$pricetype = 'price:default';
$pricefmt = $this->translate( 'client/code', $pricetype );
/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $pricefmt !== 'price:default' ? $pricefmt : $this->translate( 'client', '%1$s %2$s' );


foreach( $this->get( 'suggestItems', [] ) as $id => $productItem )
{
	$price = '';
	$media = 'display: none';
	$name = strip_tags( $productItem->getName() );
	$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );
	$priceItems = $productItem->getRefItems( 'price', 'default', 'default' );

	if( ( $mediaItem = $mediaItems->first() ) !== null ) {
		$media = 'background-image: url(\'' . $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) . '\')';
	}

	if( ( $priceItem = $priceItems->first() ) !== null ) {
		$price = sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $this->translate( 'currency', $priceItem->getCurrencyId() ) );
	}

	$url = $productItem->getName( 'url' );
	$params = ['path' => $url, 'd_name' => $url, 'd_prodid' => $productItem->getId(), 'd_pos' => ''];
	$items[] = array(
		'label' => $name,
		'html' => '
			<div class="aimeos catalog-suggest">
				<a class="suggest-item" href="' . $enc->attr( $this->link( 'client/html/catalog/detail/url', $params ) ) . '">
					<div class="item-image" style="' . $media . '"></div>
					<div class="item-name">' . $enc->html( $name ) . '</div>
					<div class="item-price">' . $enc->html( $price ) . '</div>
				</a>
			</div>
		'
	);
}

echo json_encode( $items );
