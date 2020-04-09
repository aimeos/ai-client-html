<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
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


$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', [] );


?>
<?php $this->block()->start( 'checkout/standard/address/delivery' ); ?>
<div class="checkout-standard-address-delivery col-xs-12 col-xl">

	<h2><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ); ?></h2>

	<div class="item-address item-like">
		<div class="header">
			<input id="ca_deliveryoption-like" type="radio" value="like"
				name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
				<?= $this->get( 'addressDeliveryOption', 'like' ) == 'like' ? 'checked="checked"' : '' ?> />
			<label for="ca_deliveryoption-like" class="values value-like">
				<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ); ?>
			</label>
		</div>
	</div>


	<?php foreach( $this->get( 'addressDeliveryValues', [] ) as $id => $addr ) : ?>

		<div class="item-address">
			<div class="header">
				<a class="modify minibutton delete"
					href="<?= $enc->attr( $this->url( $target, $controller, $action, ['step' => 'address', 'ca_delivery_delete' => $id], [], $config ) ) ?>">
				</a>
				<input id="ca_deliveryoption-<?= $id; ?>" type="radio" value="<?= $enc->attr( $id ) ?>"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
					<?= $this->get( 'addressDeliveryOption' ) == $id ? 'checked="checked"' : '' ?> />
				<label for="ca_deliveryoption-<?= $id; ?>" class="values">
					<?= nl2br( $this->value( 'addressDeliveryStrings', $id, '' ) ) ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $addr,
						'error' => $this->get( 'addressDeliveryOption' ) == $id ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery',
						'id' => $id,
					)
				); ?>
			</div>
		</div>

	<?php endforeach; ?>


	<?php if( !$this->config( 'client/html/common/address/delivery/disable-new', false ) ) : ?>

		<div class="item-address item-new" data-option="<?= $enc->attr( $this->get( 'addressDeliveryOption' ) ); ?>">
			<div class="header">
				<input id="ca_deliveryoption-null" type="radio" value="null"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ); ?>"
					<?= $this->get( 'addressDeliveryOption' ) == 'null' ? 'checked="checked"' : '' ?> />
				<label for="ca_deliveryoption-null" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ); ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $this->get( 'addressDeliveryValuesNew', [] ),
						'error' => $this->get( 'addressDeliveryOption' ) == 'null' ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery'
					)
				); ?>
			</div>
		</div>

	<?php endif; ?>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/address/delivery' ); ?>
