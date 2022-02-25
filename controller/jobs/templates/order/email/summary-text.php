<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

/** Available data
 * - summaryBasket : Order base item (basket) with addresses, services, products, etc.
 */

$pricefmt = $this->translate( 'client/code', 'price:default' );
/// Price format with price value (%1$s) and currency (%2$s)
$pricefmt = ( $pricefmt === 'price:default' ? $this->translate( 'client', '%1$s %2$s' ) : $pricefmt );


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
		 * @see client/html/email/common/summary/detail/text
		 * @see client/html/email/common/summary/service/text
		 */
		$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address' ),
		['addresses' => $this->summaryBasket->getAddress( 'payment' ), 'separator' => "\n"]
	)
?>



<?= strip_tags( $this->translate( 'client', 'Delivery address' ) ) ?>:

<?php if( ( $addrItems = $this->summaryBasket->getAddress( 'delivery' ) ) !== [] ) : ?>
<?=		$this->partial(
			$this->config( 'client/html/email/common/summary/address/text', 'common/summary/address' ),
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
<?php		if( $this->orderItem->getStatusPayment() >= $this->config( 'client/html/common/summary/detail/download/payment-status', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED ) && $attribute->getCode() === 'download' ) : ?>
- <?=			strip_tags( $attribute->getName() ) ?>: <?= $this->link( 'client/html/account/download/url', ['dl_id' => $attribute->getId()], ['absoluteUri' => true] ) ?>

<?php		endif ?>
<?php	endforeach ?>
<?=		strip_tags( $this->translate( 'client', 'Quantity' ) ) ?>: <?= $product->getQuantity() ?>

<?=		strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $pricefmt, $this->number( $priceItem->getValue() * $product->getQuantity(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	if( ( $status = $product->getStatusDelivery() ) >= 0 ) : $key = 'stat:' . $status ?>
<?=			strip_tags( $this->translate( 'client', 'Status' ) ) ?>: <?= strip_tags( $this->translate( 'mshop/code', $key ) ) ?>
<?php	endif ?>
<?php endforeach ?>

<?php foreach( $this->summaryBasket->getService( 'delivery' ) as $service ) : ?>
<?php	if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
<?=			strip_tags( $service->getName() ) ?>

<?=			strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $pricefmt, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	endif ?>
<?php endforeach ?>
<?php foreach( $this->summaryBasket->getService( 'payment' ) as $service ) : ?>
<?php	if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
<?=			strip_tags( $service->getName() ) ?>

<?=			strip_tags( $this->translate( 'client', 'Price' ) ) ?>: <?php printf( $pricefmt, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php	endif ?>
<?php endforeach ?>

<?= strip_tags( $this->translate( 'client', 'Sub-total' ) ) ?>: <?php printf( $pricefmt, $this->number( $this->summaryBasket->getPrice()->getValue(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php if( ( $costs = $this->summaryBasket->getCosts() ) > 0 ) : ?>
<?= strip_tags( $this->translate( 'client', '+ Shipping' ) ) ?>: <?php printf( $pricefmt, $this->number( $costs, $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( ( $costs = $this->summaryBasket->getCosts( 'payment' ) ) > 0 ) : ?>
<?php	echo strip_tags( $this->translate( 'client', '+ Payment costs' ) ) ?>: <?php printf( $pricefmt, $this->number( $costs, $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( $this->summaryBasket->getPrice()->getTaxFlag() === true ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ) ?>: <?php printf( $pricefmt, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php foreach( $this->summaryBasket->getTaxes() as $taxName => $map ) : ?>
<?php 	foreach( $map as $taxRate => $priceItem ) : ?>
<?php		if( ( $taxValue = $priceItem->getTaxValue() ) > 0 ) : ?>
<?php			$taxFormat = ( $priceItem->getTaxFlag() ? $this->translate( 'client', 'Incl. %1$s%% %2$s' ) : $this->translate( 'client', '+ %1$s%% %2$s' ) ) ?>
<?php			echo strip_tags( sprintf( $taxFormat, $this->number( $taxRate ), $this->translate( 'client/code', $taxName ) ) ) ?>: <?php printf( $pricefmt, $this->number( $taxValue, $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ?>

<?php		endif ?>
<?php	endforeach ?>
<?php endforeach ?>
<?php if( $this->summaryBasket->getPrice()->getTaxFlag() === false ) : ?>
<?php	echo strip_tags( $this->translate( 'client', 'Total' ) ) ?>: <?php printf( $pricefmt, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts() + $this->summaryBasket->getPrice()->getTaxValue(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
<?php if( $this->summaryBasket->getPrice()->getRebate() > 0 ) : ?>
<?= strip_tags( $this->translate( 'client', 'Included rebates' ) ) ?>: <?php printf( $pricefmt, $this->number( $this->summaryBasket->getPrice()->getRebate(), $this->summaryBasket->getPrice()->getPrecision() ), $this->summaryBasket->getPrice()->getCurrencyId() ) ?>

<?php endif ?>
