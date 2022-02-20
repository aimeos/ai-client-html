<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */

$enc = $this->encoder();


$target = $this->config( 'client/html/catalog/detail/url/target' );
$cntl = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$config = $this->config( 'client/html/catalog/detail/url/config', ['absoluteUri' => 1] );
$filter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );


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
<?= wordwrap( strip_tags( $this->get( 'intro', '' ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'The subscription for the product has ended' ) ) ) ?>:

<?php switch( $this->subscriptionItem->getReason() ) : case -1: ?>
	<?= wordwrap( strip_tags( $this->translate( 'client', 'The payment couldn\'t be renewed' ) ) ) ?>
<?php break; case 1: ?>
	<?= wordwrap( strip_tags( $this->translate( 'client', 'You\'ve cancelled the subscription' ) ) ) ?>
<?php endswitch ?>



<?= strip_tags( $this->translate( 'client', 'Subscription product' ) ) ?>:
<?= strip_tags( $this->orderProductItem->getName() ) ?>


<?php $price = $this->orderProductItem->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ) ?>
<?php printf( $priceFormat, $this->number( $price->getValue(), $price->getPrecision() ), $priceCurrency ) ?> <?php ( $price->getRebate() > '0.00' ? printf( $rebatePercentFormat, $this->number( round( $price->getRebate() * 100 / ( $price->getValue() + $price->getRebate() ) ), 0 ) ) : '' ) ?>
<?php if( $price->getCosts() > 0 ) { echo ' ' . strip_tags( sprintf( $costFormat, $this->number( $price->getCosts(), $price->getPrecision() ), $priceCurrency ) ); } ?>
<?php if( $price->getTaxrate() > 0 ) { echo ', ' . strip_tags( sprintf( $vatFormat, $this->number( $price->getTaxrate() ) ) ); } ?>

<?php $params = array_diff_key( array_merge( $this->get( 'urlparams' ), ['currency' => $this->orderProductItem->getPrice()->getCurrencyId(), 'd_name' => $this->orderProductItem->getName( 'url' ), 'd_prodid' => $this->orderProductItem->getParentProductId() ?: $this->orderProductItem->getProductId(), 'd_pos' => ''] ), $filter ) ?>
<?= $this->url( ( $this->orderProductItem->getTarget() ?: $target ), $cntl, $action, $params, [], $config ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ) ?>
