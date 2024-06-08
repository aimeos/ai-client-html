<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */

$enc = $this->encoder();


?>
<?php $this->block()->start( 'checkout/standard/address/delivery' ) ?>
<div class="checkout-standard-address-delivery col-xs-12 col-xl">

	<fieldset class="address">
		<legend><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></legend>

		<div id="address-delivery-list" class="accordion">

			<div class="accordion-item address-delivery item-address item-like">
				<div class="header" role="button"
					data-bs-toggle="collapse" data-bs-target="#address-delivery-like"
					aria-controls="address-delivery-like" aria-expanded="false">

					<input id="ca_deliveryoption-like" type="radio" value="like"
						name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
						<?= $this->get( 'addressDeliveryOption', 'like' ) == 'like' ? 'checked="checked"' : '' ?> >
					<label for="ca_deliveryoption-like" class="values value-like">
						<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
					</label>
				</div>
				<div class="accordion-collapse collapse" id="address-delivery-like" data-bs-parent="#address-delivery-list"></div>
			</div>

			<?php foreach( $this->get( 'addressDeliveryValues', [] ) as $id => $addr ) : ?>

				<div class="accordion-item address-delivery item-address item-default">
					<div class="header" role="button"
						data-bs-toggle="collapse" data-bs-target="#address-delivery-default"
						aria-controls="address-delivery-default" aria-expanded="false">

						<input id="ca_deliveryoption-<?= $id ?>" type="radio" value="<?= $enc->attr( $id ) ?>"
							name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
							<?= $this->get( 'addressDeliveryOption' ) == $id ? 'checked="checked"' : '' ?>>
						<label for="ca_deliveryoption-<?= $id ?>" class="values">
							<?= nl2br( $enc->html( $this->value( 'addressDeliveryStrings', $id, '' ) ) ) ?>
						</label>
					</div>
					<div class="address accordion-collapse collapse"
						id="address-delivery-default" data-bs-parent="#address-delivery-list">

						<div class="form-list">

							<?= $this->partial(
								$this->config( 'client/html/checkout/standard/partials/address', 'common/partials/address' ),
								[
									'id' => $id,
									'address' => $addr,
									'countries' => $this->get( 'addressCountries', [] ),
									'css' => $this->get( 'addressDeliveryCss', [] ),
									'error' => $this->get( 'addressDeliveryOption' ) == $id ? $this->get( 'addressDeliveryError', [] ) : [],
									'formnames' => ['ca_delivery_' . $id],
									'languages' => $this->get( 'addressLanguages', [] ),
									'languageid' => $this->get( 'contextLanguage' ),
									'salutations' => $this->get( 'addressSalutations', [] ),
									'states' => $this->get( 'addressStates', [] ),
									'prefix' => 'order.address.',
									'type' => 'delivery',
								]
							) ?>

							<div class="button-group">
								<a class="btn btn-delete" title="<?= $enc->attr( $this->translate( 'client', 'Delete' ), $enc::TRUST ) ?>"
									href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url', ['step' => 'address', 'ca_delivery_delete' => $id] ) ) ?>">
									<?= $enc->html( $this->translate( 'client', 'Delete' ), $enc::TRUST ) ?>
								</a>
							</div>

						</div>
					</div>
				</div>

			<?php endforeach ?>

			<?php if( !$this->config( 'client/html/checkout/standard/address/delivery/disable-new', false ) ) : ?>

				<div class="accordion-item address-delivery item-address item-new">
					<div class="header" role="button"
						data-bs-toggle="collapse" data-bs-target="#address-delivery-new"
						aria-controls="address-delivery-new" aria-expanded="false">

						<input id="ca_deliveryoption-new" type="radio" value="null"
							name="<?= $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ) ?>"
							<?= $this->get( 'addressDeliveryOption' ) == 'null' ? 'checked="checked"' : '' ?>
						>
						<label for="ca_deliveryoption-new" class="values value-new">
							<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ) ?>
						</label>
					</div>
					<div class="address accordion-collapse collapse"
						id="address-delivery-new" data-bs-parent="#address-delivery-list">

						<div class="form-list">

							<?= $this->partial(
								$this->config( 'client/html/checkout/standard/partials/address', 'common/partials/address' ),
								[
									'id' => null,
									'address' => $this->get( 'addressDeliveryValuesNew', [] ),
									'countries' => $this->get( 'addressCountries', [] ),
									'css' => $this->get( 'addressDeliveryCss', [] ),
									'error' => $this->get( 'addressDeliveryOption' ) == 'null' ? $this->get( 'addressPaymentError', [] ) : [],
									'formnames' => ['ca_delivery'],
									'languages' => $this->get( 'addressLanguages', [] ),
									'languageid' => $this->get( 'contextLanguage' ),
									'salutations' => $this->get( 'addressSalutations', [] ),
									'states' => $this->get( 'addressStates', [] ),
									'prefix' => 'order.address.',
									'type' => 'delivery',
								]
							) ?>

							<div class="row form-item form-group store <?= join( ' ', $this->value( 'addressDeliveryCss', 'nostore', [] ) ) ?>">
								<label class="col-md-5" for="address-delivery-store">
									<?= $enc->html( $this->translate( 'client', 'Don\'t store address' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-7">
									<input id="address-delivery-store" class="custom-control custom-checkbox" type="checkbox" value="1" name="<?= $enc->attr( $this->formparam( ['ca_delivery', 'nostore'] ) ) ?>">
								</div>
							</div>

						</div>
					</div>
				</div>

			<?php endif ?>

		</div>
	</fieldset>
</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/address/delivery' ) ?>
