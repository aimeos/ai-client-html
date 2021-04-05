<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

$enc = $this->encoder();


$pricefmt = $this->translate( 'client/code', 'price:default' );
/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $pricefmt !== 'price:default' ? $pricefmt : $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'email/voucher/text' ) ?>
<?= wordwrap( strip_tags( $this->get( 'emailIntro' ) ) ) ?>


<?php if( is_array( $this->extVoucherCode ) ) : ?>
<?= wordwrap( strip_tags( $this->translate( 'client', 'Your vouchers: ' ) ) ) ?>
<?php foreach( $this->extVoucherCode as $code ) : ?>
- <?= $code ?>
<?php endforeach ?>
<?php else : ?>
<?= wordwrap( strip_tags( $this->translate( 'client', 'Your voucher: ' ) . $this->extVoucherCode ) ) ?>
<?php endif ?>

<?php $price = $this->extOrderProductItem->getPrice(); $priceCurrency = $this->translate( 'currency', $price->getCurrencyId() ) ?>
<?php $value = sprintf( $priceFormat, $this->number( $price->getValue() + $price->getRebate(), $price->getPrecision() ), $priceCurrency ) ?>
<?= wordwrap( strip_tags( sprintf( $this->translate( 'client', 'The value of your voucher is %1$s', 'The value of your vouchers are %1$s', count( (array) $this->extVoucherCode ) ), $value ) ) ) ?>


<?= wordwrap( strip_tags( $this->translate( 'client', 'You can use your vouchers at any time in our online shop' ) ) ) ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'email/voucher/text' );
