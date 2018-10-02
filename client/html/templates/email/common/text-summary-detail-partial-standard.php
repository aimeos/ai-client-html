<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 * - summaryTaxRates : List of tax values grouped by tax rates
 * - summaryShowDownloadAttributes : True if product download links should be shown, false if not
 */


$dlTarget = $this->config( 'client/html/account/download/url/target' );
$dlController = $this->config( 'client/html/account/download/url/controller', 'account' );
$dlAction = $this->config( 'client/html/account/download/url/action', 'download' );
$dlConfig = $this->config( 'client/html/account/download/url/config', array( 'absoluteUri' => 1 ) );

try {
	$products = $this->summaryBasket->getProducts();
} catch( Exception $e ) {
	$products = [];
}

$priceTaxvalue = '0.00';

try
{
	$price = $this->summaryBasket->getPrice();
	$priceValue = $price->getValue();
	$priceService = $price->getCosts();
	$priceRebate = $price->getRebate();
	$priceTaxflag = $price->getTaxFlag();
	$priceCurrency = $this->translate( 'currency', $price->getCurrencyId() );
}
catch( Exception $e )
{
	$priceValue = '0.00';
	$priceRebate = '0.00';
	$priceService = '0.00';
	$priceTaxflag = true;
	$priceCurrency = '';
}


$deliveryPriceValue = '0.00';
$deliveryPriceService = '0.00';

foreach( $this->summaryBasket->getService( 'delivery' ) as $service )
{
	$deliveryPriceItem = $service->getPrice();
	$deliveryPriceService += $deliveryPriceItem->getCosts();
	$deliveryPriceValue += $deliveryPriceItem->getValue();
}

$paymentPriceValue = '0.00';
$paymentPriceService = '0.00';

foreach( $this->summaryBasket->getService( 'payment' ) as $service )
{
	$paymentPriceItem = $service->getPrice();
	$paymentPriceService += $paymentPriceItem->getCosts();
	$paymentPriceValue += $paymentPriceItem->getValue();
}


/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );
$unhide = $this->get( 'summaryShowDownloadAttributes', false );


?>
<?= strip_tags( $this->translate( 'client', 'Order details' ) ); ?>:
<?php foreach( $products as $product ) : ?>
<?php	$price = $product->getPrice(); ?>

<?= strip_tags( $product->getName() ); ?> (<?= $product->getProductCode(); ?>)
<?php	foreach( array_merge( $product->getAttributes( 'config' ), $product->getAttributes( 'custom' ) ) as $attribute ) : ?>
- <?php 	echo strip_tags( $this->translate( 'client/code', $attribute->getCode() ) ); ?>: <?= $attribute->getQuantity() > 1 ? $attribute->getQuantity() . '× ' : '' ?><?= strip_tags( ( $attribute->getName() != '' ? $attribute->getName() : $attribute->getValue() ) ); ?>

<?php	endforeach; ?>
<?php	foreach( $product->getAttributes( 'hidden' ) as $attribute ) : ?>
<?php		if( $unhide && $attribute->getCode() === 'download' ) : ?>
- <?php 		echo strip_tags( $attribute->getName()); ?>: <?= $this->url( $dlTarget, $dlController, $dlAction, array( 'dl_id' => $attribute->getId() ), [], $dlConfig ); ?>

<?php		endif; ?>
<?php	endforeach; ?>
<?= strip_tags( $this->translate( 'client', 'Quantity' ) ); ?>: <?= $product->getQuantity(); ?>

<?= strip_tags( $this->translate( 'client', 'Price' ) ); ?>: <?php printf( $priceFormat, $this->number( $price->getValue() ), $priceCurrency ); ?>

<?= strip_tags( $this->translate( 'client', 'Sum' ) ); ?>: <?php printf( $priceFormat, $this->number( $price->getValue() * $product->getQuantity() ), $priceCurrency ); ?>

<?php endforeach; ?>

<?php if( ( $serviceValue = $deliveryPriceValue + $paymentPriceValue ) > 0 ) : ?>

<?php	echo strip_tags( $this->translate( 'client', 'Service fees' ) ); ?>: <?php printf( $priceFormat, $this->number( $serviceValue ), $priceCurrency ); ?>


<?php endif; ?>
<?= strip_tags( $this->translate( 'client', 'Sub-total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue ), $priceCurrency ); ?>

<?php if( $priceService - $paymentPriceService > 0 ) : ?>
<?= strip_tags( $this->translate( 'client', '+ Shipping' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceService - $paymentPriceService ), $priceCurrency ); ?>

<?php endif; ?>
<?php if( $paymentPriceService > 0 ) : ?>
<?php	echo strip_tags( $this->translate( 'client', '+ Payment costs' ) ); ?>: <?php printf( $priceFormat, $this->number( $paymentPriceService ), $priceCurrency ); ?>

<?php endif; ?>
<?php if( $priceTaxflag === true ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue + $priceService ), $priceCurrency ); ?>

<?php endif; ?>
<?php foreach( $this->get( 'summaryTaxRates', [] ) as $taxRate => $priceItem ) : $taxValue = $priceItem->getTaxValue(); ?>
<?php	if( $taxRate > '0.00' && $taxValue > '0.00' ) : $priceTaxvalue += $taxValue; ?>
<?php		$taxFormat = ( $priceItem->getTaxFlag() ? $this->translate( 'client', 'Incl. %1$s%% VAT' ) : $this->translate( 'client', '+ %1$s%% VAT' ) ); ?>
<?php		echo strip_tags( sprintf( $taxFormat, $this->number( $taxRate ) ) ); ?>: <?php printf( $priceFormat, $this->number( $taxValue ), $priceCurrency ); ?>

<?php	endif; ?>
<?php endforeach; ?>
<?php if( $priceTaxflag === false ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceValue + $priceService + $priceTaxvalue ), $priceCurrency ); ?>

<?php endif; ?>
<?php if( $priceRebate > '0.00' ) : ?>
<?= strip_tags( $this->translate( 'client', 'Included rebates' ) ); ?>: <?php printf( $priceFormat, $this->number( $priceRebate ), $priceCurrency ); ?>

<?php endif; ?>
