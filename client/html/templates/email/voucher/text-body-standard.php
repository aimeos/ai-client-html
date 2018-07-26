<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();
$voucher = $this->extVoucherCode;
$product = $this->extOrderProductItem;

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'email/voucher/text' ); ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'Your voucher: ' ) . $voucher ) ); ?>

<?php $price = $product->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ); ?>
<?php $value = sprintf( $priceFormat, $this->number( $price->getValue() + $price->getRebate() ), $priceCurrency ); ?>
<?= wordwrap( strip_tags( sprintf( $this->translate( 'client', 'The value of your voucher is %1$s' ), $value ) ) ); ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'You can use your voucher any time in our online shop' ) ) ); ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'email/voucher/text' ); ?>
