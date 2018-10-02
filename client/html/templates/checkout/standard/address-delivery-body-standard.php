<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

/** client/html/common/address/delivery/disable-new
 * Disables the billing address form for a new address
 *
 * Normally, customers are allowed to enter new delivery addresses in the
 * checkout process which are stored in the current order. For registered
 * customers they are also added to the list of delivery addresses in their
 * profile.
 *
 * You can disable the address form for the new delivery address by this setting
 * if it shouldn't be allowed to add another delivery address.
 *
 * @param boolean True to disable the "new delivery address" form, false to allow a new address
 * @since 2014.03
 * @category Developer
 * @category User
 * @see client/html/common/address/billing/disable-new
 */
$disablenew = (bool) $this->config( 'client/html/common/address/delivery/disable-new', false );


$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', [] );

try {
	$addrArray = $this->standardBasket->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY )->toArray();
} catch( Exception $e ) {
	$addrArray = [];
}


$deliveryDefault = ( $addrArray === [] ? -1 : 'null' );
$deliveryOption = $this->param( 'ca_deliveryoption', ( isset( $addrArray['order.base.address.addressid'] ) && $addrArray['order.base.address.addressid'] != '' ? $addrArray['order.base.address.addressid'] : $deliveryDefault ) );

$deliverySalutations = $this->get( 'deliverySalutations', [] );
$deliveryCountries = $this->get( 'addressCountries', [] );
$deliveryStates = $this->get( 'addressStates', [] );
$deliveryLanguages = $this->get( 'addressLanguages', [] );


$deliveryCssAll = [];

foreach( $this->get( 'deliveryMandatory', [] ) as $name ) {
	$deliveryCssAll[$name][] = 'mandatory';
}

foreach( $this->get( 'deliveryOptional', [] ) as $name ) {
	$deliveryCssAll[$name][] = 'optional';
}

foreach( $this->get( 'deliveryHidden', [] ) as $name ) {
	$deliveryCssAll[$name][] = 'hidden';
}


?>
<?php $this->block()->start( 'checkout/standard/address/delivery' ); ?>
<div class="checkout-standard-address-delivery col-sm-6">

	<h2><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ); ?></h2>

	<div class="item-address item-like">
		<div class="header">
			<input id="ca_deliveryoption-like" type="radio" name="<?= $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="-1" <?= ( $deliveryOption == -1 ? 'checked="checked"' : '' ); ?> />
			<label for="ca_deliveryoption-like" class="values value-like"><?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ); ?></label>
		</div>
	</div>


	<?php foreach( $this->get( 'addressDeliveryItems', [] ) as $id => $addr ) : ?>

		<div class="item-address">

			<div class="header">
				<a class="modify minibutton" href="<?= $enc->attr( $this->url( $target, $controller, $action, array( 'step' => 'address', 'ca_delivery_delete' => $id ), [], $config ) ); ?>">X</a>
				<input id="ca_deliveryoption-<?= $id; ?>" type="radio" name="<?= $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="<?= $enc->attr( $addr->getAddressId() ); ?>" <?= ( $deliveryOption == $id ? 'checked="checked"' : '' ); ?> />
				<label for="ca_deliveryoption-<?= $id; ?>" class="values">
<?php
	echo preg_replace( "/\n+/m", "<br/>", trim( $enc->html( sprintf(
		/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
		/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
		/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
		/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
		$this->translate( 'client', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'
		),
		$addr->getCompany(),
		( !in_array( $addr->getSalutation(), array( 'company' ) ) ? $this->translate( 'mshop/code', $addr->getSalutation() ) : '' ),
		$addr->getTitle(),
		$addr->getFirstName(),
		$addr->getLastName(),
		$addr->getAddress1(),
		$addr->getAddress2(),
		$addr->getAddress3(),
		$addr->getPostal(),
		$addr->getCity(),
		$addr->getState(),
		$this->translate( 'country', $addr->getCountryId() ),
		$this->translate( 'language', $addr->getLanguageId() ),
		$addr->getEmail(),
		$addr->getTelephone(),
		$addr->getTelefax(),
		$addr->getWebsite(),
		$addr->getVatID()
	) ) ) );
?>
				</label>
			</div>

<?php
	$deliveryCss = $deliveryCssAll;
	if( $deliveryOption == $id )
	{
		foreach( $this->get( 'deliveryError', [] ) as $name => $msg ) {
			$deliveryCss[$name][] = 'error';
		}
	}

	$addrValues = $addr->toArray();

	if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
		$addrValues['order.base.address.languageid'] = $this->get( 'deliveryLanguage', 'en' );
	}
?>
			<ul class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard.php' ),
					array(
						'address' => $addrValues,
						'salutations' => $deliverySalutations,
						'languages' => $deliveryLanguages,
						'countries' => $deliveryCountries,
						'states' => $deliveryStates,
						'type' => 'delivery',
						'css' => $deliveryCss,
						'id' => $id,
					)
				); ?>
			</ul>

		</div>
	<?php endforeach; ?>


	<?php if( $disablenew === false ) : ?>

		<div class="item-address item-new" data-option="<?= $enc->attr( $deliveryOption ); ?>">

			<div class="header">
				<input id="ca_deliveryoption-null" type="radio" name="<?= $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="null" <?= ( $deliveryOption == 'null' ? 'checked="checked"' : '' ); ?> />
				<label for="ca_deliveryoption-null" class="values value-new"><?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ); ?></label>
			</div>

<?php
	$deliveryCss = $deliveryCssAll;
	if( $deliveryOption == 'null' )
	{
		foreach( $this->get( 'deliveryError', [] ) as $name => $msg ) {
			$deliveryCss[$name][] = 'error';
		}
	}

	$addrValues = array_merge( $addrArray, $this->param( 'ca_delivery', [] ) );

	if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
		$addrValues['order.base.address.languageid'] = $this->get( 'deliveryLanguage', 'en' );
	}
?>
			<ul class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard.php' ),
					array(
						'address' => $addrValues,
						'salutations' => $deliverySalutations,
						'languages' => $deliveryLanguages,
						'countries' => $deliveryCountries,
						'states' => $deliveryStates,
						'type' => 'delivery',
						'css' => $deliveryCss,
					)
				); ?>
			</ul>

		</div>

	<?php endif; ?>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/address/delivery' ); ?>
