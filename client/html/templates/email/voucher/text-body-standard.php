<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2020
 */

$enc = $this->encoder();


$pricefmt = $this->translate( 'client/code', 'price:default' );
/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $pricefmt !== 'price:default' ? $pricefmt : $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'email/voucher/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'Your voucher: ' ) . $this->extVoucherCode ) ); ?>

<?php $price = $this->extOrderProductItem->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ); ?>
<?php $value = sprintf( $priceFormat, $this->number( $price->getValue() + $price->getRebate(), $price->getPrecision() ), $priceCurrency ); ?>
<?= wordwrap( strip_tags( sprintf( $this->translate( 'client', 'The value of your voucher is %1$s' ), $value ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'You can use your voucher any time in our online shop' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/voucher/text' );
