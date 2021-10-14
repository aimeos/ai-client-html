<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 * - summaryTaxRates : List of tax values grouped by tax rates
 * - summaryNamedTaxes : Calculated taxes grouped by the tax names
 * - summaryShowDownloadAttributes : True if product download links should be shown, false if not
 * - summaryCostsDelivery : Sum of all shipping costs
 * - summaryCostsPayment : Sum of all payment costs
 * - priceFormat : Format of the shown prices
 */


$dlTarget = $this->config( 'client/html/account/download/url/target' );
$dlController = $this->config( 'client/html/account/download/url/controller', 'account' );
$dlAction = $this->config( 'client/html/account/download/url/action', 'download' );
$dlConfig = $this->config( 'client/html/account/download/url/config', ['absoluteUri' => 1] );


?>
<?= strip_tags( $this->translate( 'client', 'Billing address' ) ) ?>:

<?= $this->partial(
		/** client/html/email/common/summary/address/text
		 * Location of the address partial template for the text e-mails
		 *
		 * To configure an alternative template for the address partial, you
		 * have to configure its path relative to the template directory
		 * (usually client/html/templates/). It's then used to display the
		 * payment or delivery address block in the text e-mails.
		 *
		 * @param string Relative path to the address partial
		 * @since 2017.01
		 * @category Developer
		 * @see client/html/email/common/summary/detail/text
		 * @see client/html/email/common/summary/service/text
		 */
		$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address-standard' ),
		['addresses' => $this->summaryBasket->getAddress( 'payment' ), 'separator' => "\n"]
	)
?>



<?= strip_tags( $this->translate( 'client', 'Delivery address' ) ) ?>:

<?php if( ( $addrItems = $this->summaryBasket->getAddress( 'delivery' ) ) !== [] ) : ?>
<?=		$this->partial(
			$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address-standard' ),
			array( 'addresses' => $addrItems, 'separator' => "\n" )
		)
?>
<?php else : ?>
<?=		$this->translate( 'client', 'like billing address' ) ?>
<?php endif ?>



<?php if( ( $services = $this->summaryBasket->getService( 'delivery' ) ) !== [] ) : ?>
<?=		strip_tags( $this->translate( 'client', 'delivery' ) ) ?>:
<?php	foreach( $services as $service ) : ?>

<?=			strip_tags( $service->getName() ) ?>

<?php		foreach( $service->getAttributeItems() as $attribute )
			{
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}

				echo '- ' . strip_tags( $name ) . ': ' . strip_tags( $value ) . "\n";
			}
?>
<?php	endforeach ?>
<?php endif ?>


<?php if( ( $services = $this->summaryBasket->getService( 'payment' ) ) !== [] ) : ?>
<?=		strip_tags( $this->translate( 'client', 'payment' ) ) ?>:
<?php	foreach( $services as $service ) : ?>

<?=			strip_tags( $service->getName() ) ?>

<?php		foreach( $service->getAttributeItems() as $attribute )
			{
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}

				echo '- ' . strip_tags( $name ) . ': ' . strip_tags( $value ) . "\n";
			}
?>
<?php	endforeach ?>
<?php endif ?>


<?php if( !( $coupons = $this->summaryBasket->getCoupons() )->isEmpty() ) : ?>
<?= 	strip_tags( $this->translate( 'client', 'Coupons' ) ) ?>:
<?php	foreach( $coupons as $code => $products ) : ?>
<?= 		'- ' . $code . "\n" ?>
<?php	endforeach ?>

<?php endif ?>
<?php if( $this->summaryBasket->getCustomerReference() != '' ) : ?>
<?= 	strip_tags( $this->translate( 'client', 'Your reference number' ) ) ?>:
<?= 	strip_tags( $this->summaryBasket->getCustomerReference() ) . "\n" ?>

<?php endif ?>
<?php if( $this->summaryBasket->getComment() != '' ) : ?>
<?= 	strip_tags( $this->translate( 'client', 'Your comment' ) ) ?>:
<?= 	strip_tags( $this->summaryBasket->getComment() ) . "\n" ?>

<?php endif ?>


<?= strip_tags( $this->translate( 'client', 'Order details' ) ) ?>:
<?php foreach( $this->summaryBasket->getProducts() as $product ) : $priceItem = $product->getPrice() ?>

<?=		strip_tags( $product->getName() ) ?> (<?= $product->getProductCode() ?>)
<?php	foreach( $this->config( 'client/html/common/summary/detail/product/attribute/types', ['variant', 'config', 'custom'] ) as $attrType ) : ?>
<?php		foreach( $product->getAttributeItems( $attrType ) as $attribute ) : ?>
- <?php 		echo strip_tags( $this->translate( 'client/code', $attribute->getCode() ) ) ?>: <?= $attribute->getQuantity() > 1 ? $attribute->getQuantity() . '× ' : '' ?><?= strip_tags( ( $attribute->getName() != '' ? $attribute->getName() : $attribute->getValue() ) ) ?>

<?php		endforeach ?>
<?php	endforeach ?>
<?php	foreach( $product->getAttributeItems( 'hidden' ) as $attribute ) : ?>
<?php		if( $this->get( 'summaryShowDownloadAttributes', false ) && $attribute->getCode() === 'download' ) : ?>
- <?=			strip_tags( $attribute->getName() ) ?>: <?= $this->url( $dlTarget, $dlController, $dlAction, array( 'dl_id' => $attribute->getId() ), [], $dlConfig ) ?>

<?php		endif ?>
<?php	endforeach ?>
<?=		strip_tags( $this->translate( 'client', 'Quantity' ) ) ?>: <?= $product->getQuantity() ?>

<?=		strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $priceItem->getValue() * $product->getQuantity(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	if( ( $status = $product->getStatus() ) >= 0 ) : $key = 'stat:' . $status ?>
<?=			strip_tags( $this->translate( 'client', 'Status' ) ) ?>: <?= strip_tags( $this->translate( 'mshop/code', $key ) ) ?>
<?php	endif ?>
<?php endforeach ?>

<?php foreach( $this->summaryBasket->getService( 'delivery' ) as $service ) : ?>
<?php	if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
<?=			strip_tags( $service->getName() ) ?>

<?=			strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	endif ?>
<?php endforeach ?>
<?php foreach( $this->summaryBasket->getService( 'payment' ) as $service ) : ?>
<?php	if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
<?=			strip_tags( $service->getName() ) ?>

<?=			strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	endif ?>
<?php endforeach ?>

<?= strip_tags( $this->translate( 'client', 'Sub-total' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $this->summaryBasket->getPrice()->getValue(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php if( ( $costs = $this->get( 'summaryCostsDelivery', 0 ) ) > 0 ) : ?>
<?= strip_tags( $this->translate( 'client', '+ Shipping' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $costs, $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( ( $costs = $this->get( 'summaryCostsPayment', 0 ) ) > 0 ) : ?>
<?php	echo strip_tags( $this->translate( 'client', '+ Payment costs' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $costs, $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( $this->summaryBasket->getPrice()->getTaxFlag() === true ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php foreach( $this->get( 'summaryNamedTaxes', [] ) as $taxName => $map ) : ?>
<?php 	foreach( $map as $taxRate => $priceItem ) : ?>
<?php		if( ( $taxValue = $priceItem->getTaxValue() ) > 0 ) : ?>
<?php			$taxFormat = ( $priceItem->getTaxFlag() ? $this->translate( 'client', 'Incl. %1$s%% %2$s' ) : $this->translate( 'client', '+ %1$s%% %2$s' ) ) ?>
<?php			echo strip_tags( sprintf( $taxFormat, $this->number( $taxRate ), $this->translate( 'client/code', 'tax' . $taxName ) ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $taxValue, $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php		endif ?>
<?php	endforeach ?>
<?php endforeach ?>
<?php if( $this->summaryBasket->getPrice()->getTaxFlag() === false ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts() + $this->summaryBasket->getPrice()->getTaxValue(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( $this->summaryBasket->getPrice()->getRebate() > 0 ) : ?>
<?= strip_tags( $this->translate( 'client', 'Included rebates' ) ) ?>: <?php printf( $this->get( 'priceFormat' ), $this->number( $this->summaryBasket->getPrice()->getRebate(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
