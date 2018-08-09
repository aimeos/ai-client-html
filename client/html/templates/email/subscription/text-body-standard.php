<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();


$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array( 'absoluteUri' => 1 ) );

$product = $this->extOrderProductItem;


/// Price quantity format with quantity (%1$s)
$quantityFormat = $this->translate( 'client', 'from %1$s' );

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );

/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
$costFormat = $this->translate( 'client', '+ %1$s %2$s/item' );

/// Rebate format with rebate value (%1$s) and currency (%2$s)
$rebateFormat = $this->translate( 'client', '%1$s %2$s off' );

/// Rebate percent format with rebate percent value (%1$s)
$rebatePercentFormat = '(' . $this->translate( 'client', '-%1$s%%' ) . ')';

/// Tax rate format with tax rate in percent (%1$s)
$vatFormat = $this->translate( 'client', 'Incl. %1$s%% VAT' );


?>
<?php $this->block()->start( 'email/subscription/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'The subscription for the product has ended' ) ) ); ?>:

<?php switch( $this->extSubscriptionItem->getReason() ) : case -1: ?>
	<?= wordwrap( strip_tags( $this->translate( 'client', 'The payment couldn\'t be renewed' ) ) ); ?>
<?php break; case 1: ?>
	<?= wordwrap( strip_tags( $this->translate( 'client', 'You\'ve cancelled the subscription' ) ) ); ?>
<?php endswitch; ?>



<?= strip_tags( $this->translate( 'client', 'Subscription product' ) ); ?>:
<?= strip_tags( $product->getName() ); ?>


<?php $price = $product->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ); ?>
<?php printf( $priceFormat, $this->number( $price->getValue() ), $priceCurrency ); ?> <?php ( $price->getRebate() > '0.00' ? printf( $rebatePercentFormat, $this->number( round( $price->getRebate() * 100 / ( $price->getValue() + $price->getRebate() ) ), 0 ) ) : '' ); ?>
<?php if( $price->getCosts() > 0 ) { echo ' ' . strip_tags( sprintf( $costFormat, $this->number( $price->getCosts() ), $priceCurrency ) ); } ?>
<?php if( $price->getTaxrate() > 0 ) { echo ', ' . strip_tags( sprintf( $vatFormat, $this->number( $price->getTaxrate() ) ) ); } ?>

<?php $params = array_merge( $this->param(), ['currency' => $product->getPrice()->getCurrencyId(), 'd_prodid' => $product->getProductId(), 'd_name' => $product->getName( 'url' )] ); ?>
<?= $this->url( ( $product->getTarget() ?: $detailTarget ), $detailController, $detailAction, $params, [], $detailConfig ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'If you have any questions, please reply to this e-mail' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/subscription/text' ); ?>
