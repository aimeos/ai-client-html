<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', [] );


?>
<?php $this->block()->start( 'checkout/standard/address/delivery' ) ?>
<div class="checkout-standard-address-delivery col-xs-12 col-xl">

	<h2><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h2>

	<div class="item-address item-like">
		<div class="header">
			<input id="ca_deliveryoption-like" type="radio" value="like"
				name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
				<?= $this->get( 'addressDeliveryOption', 'like' ) == 'like' ? 'checked="checked"' : '' ?>>
			<label for="ca_deliveryoption-like" class="values value-like">
				<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
			</label>
		</div>
	</div>


	<?php foreach( $this->get( 'addressDeliveryValues', [] ) as $id => $addr ) : ?>

		<div class="item-address">
			<div class="header">
				<a class="modify minibutton delete"
					href="<?= $enc->attr( $this->url( $target, $controller, $action, ['step' => 'address', 'ca_delivery_delete' => $id], [], $config ) ) ?>">
				</a>
				<input id="ca_deliveryoption-<?= $id ?>" type="radio" value="<?= $enc->attr( $id ) ?>"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
					<?= $this->get( 'addressDeliveryOption' ) == $id ? 'checked="checked"' : '' ?>>
				<label for="ca_deliveryoption-<?= $id ?>" class="values">
					<?= nl2br( $enc->html( $this->value( 'addressDeliveryStrings', $id, '' ) ) ) ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $addr,
						'error' => $this->get( 'addressDeliveryOption' ) == $id ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery',
						'id' => $id,
					)
				) ?>
			</div>
		</div>

	<?php endforeach ?>


	<?php if( !$this->config( 'client/html/checkout/standard/address/delivery/disable-new', false ) ) : ?>

		<div class="item-address item-new" data-option="<?= $enc->attr( $this->get( 'addressDeliveryOption' ) ) ?>">
			<div class="header">
				<input id="ca_deliveryoption-null" type="radio" value="null"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
					<?= $this->get( 'addressDeliveryOption' ) == 'null' ? 'checked="checked"' : '' ?>>
				<label for="ca_deliveryoption-null" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ) ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $this->get( 'addressDeliveryValuesNew', [] ),
						'error' => $this->get( 'addressDeliveryOption' ) == 'null' ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery'
					)
				) ?>

				<div class="row form-item form-group store <?= join( ' ', $this->value( 'addressDeliveryCss', 'nostore', [] ) ) ?>">
					<label class="col-md-5" for="address-delivery-store">
						<?= $enc->html( $this->translate( 'client', 'Don\'t store address' ), $enc::TRUST ) ?>
					</label>
					<div class="col-md-7">
						<input class="custom-control custom-checkbox" type="checkbox" value="1" name="<?= $enc->attr( $this->formparam( ['ca_delivery', 'nostore'] ) ) ?>">
					</div>
				</div>
			</div>
		</div>

	<?php endif ?>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/address/delivery' ) ?>
