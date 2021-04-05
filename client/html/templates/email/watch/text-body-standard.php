<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array( 'absoluteUri' => 1 ) );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );


$pricefmt = $this->translate( 'client/code', 'price:default' );
/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $pricefmt !== 'price:default' ? $pricefmt : $this->translate( 'client', '%1$s %2$s' );

/// Price quantity format with quantity (%1$s)
$quantityFormat = $this->translate( 'client', 'from %1$s' );

/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
$costFormat = $this->translate( 'client', '+ %1$s %2$s/item' );

/// Rebate format with rebate value (%1$s) and currency (%2$s)
$rebateFormat = $this->translate( 'client', '%1$s %2$s off' );

/// Rebate percent format with rebate percent value (%1$s)
$rebatePercentFormat = '(' . $this->translate( 'client', '-%1$s%%' ) . ')';

/// Tax rate format with tax rate in percent (%1$s)
$vatFormat = $this->translate( 'client', 'Incl. %1$s%% VAT' );


?>
<?php $this->block()->start( 'email/watch/text' ) ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'One or more products you are watching have been updated.' ) ) ) ?>



<?= strip_tags( $this->translate( 'client', 'Watched products' ) ) ?>:
<?php foreach( $this->extProducts as $entry ) : $product = $entry['item'] ?>

<?= strip_tags( $product->getName() ) ?>


<?php $price = $entry['price']; $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ) ?>
<?php printf( $priceFormat, $this->number( $price->getValue(), $price->getPrecision() ), $priceCurrency ) ?> <?php ( $price->getRebate() > '0.00' ? printf( $rebatePercentFormat, $this->number( round( $price->getRebate() * 100 / ( $price->getValue() + $price->getRebate() ) ), 0 ) ) : '' ) ?>
<?php if( $price->getCosts() > 0 ) { echo ' ' . strip_tags( sprintf( $costFormat, $this->number( $price->getCosts(), $price->getPrecision() ), $priceCurrency ) ); } ?>
<?php if( $price->getTaxrate() > 0 ) { echo ', ' . strip_tags( sprintf( $vatFormat, $this->number( $price->getTaxrate() ) ) ); } ?>

<?php $params = array_diff_key( array_merge( $this->param(), ['currency' => $entry['currency'], 'd_name' => $product->getName( 'url' ), 'd_prodid' => $product->getId(), 'd_pos' => ''] ), $detailFilter ) ?>
<?= $this->url( ( $product->getTarget() ?: $detailTarget ), $detailController, $detailAction, $params, [], $detailConfig ) ?>

<?php endforeach ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/watch/text' ) ?>
