<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


?>
<?php $this->block()->start( 'checkout/standard/address/billing' ) ?>
<div class="checkout-standard-address-billing col-xs-12 col-xl">
	<h2><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h2>


	<?php if( isset( $this->addressPaymentItem ) && $this->addressPaymentItem->getAddressId() ) : ?>
		<div class="item-address">
			<div class="header">
				<input id="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>" type="radio"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ) ?>"
					value="<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>"
					<?= $this->get( 'addressBillingOption' ) == $this->addressPaymentItem->getAddressId() ? 'checked="checked"' : '' ?>
				>
				<label for="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>" class="values">
					<?= nl2br( $enc->html( $this->get( 'addressBillingString', '' ) ) ) ?>
				</label>
			</div>
			<div class="form-list">
				<?= $this->partial(
					/** client/html/checkout/standard/partials/address
					 * Relative path to the address partial template file
					 *
					 * Partials are templates which are reused in other templates and generate
					 * reoccuring blocks filled with data from the assigned values. The address
					 * partial creates an HTML block with input fields for address forms.
					 *
					 * @param string Relative path to the template file
					 * @since 2017.01
					 * @category Developer
					 * @category User
					 */
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'id' => $this->addressPaymentItem->getAddressId(),
						'address' => $this->addressPaymentItem->toArray(),
						'error' => $this->get( 'addressBillingOption' ) == $this->addressPaymentItem->getAddressId() ? $this->get( 'addressBillingError', [] ) : [],
						'salutations' => $this->get( 'addressBillingSalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressBillingCss', [] ),
						'type' => 'billing',
					)
				) ?>
			</div>

		</div>
	<?php endif ?>


	<?php if( !$this->config( 'client/html/checkout/standard/address/billing/disable-new', false ) ) : ?>
		<div class="item-address item-new" data-option="<?= $enc->attr( $this->get( 'addressBillingOption' ) ) ?>">
			<div class="header">
				<input id="ca_billingoption-new" type="radio" value="null"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ) ?>"
					<?= $this->get( 'addressBillingOption' ) == 'null' ? 'checked="checked"' : '' ?>
				>
				<label for="ca_billingoption-new" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ) ?>
				</label>
			</div>
			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $this->get( 'addressBillingValuesNew', [] ),
						'error' => $this->get( 'addressBillingOption' ) == 'null' ? $this->get( 'addressBillingError', [] ) : [],
						'salutations' => $this->get( 'addressBillingSalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressBillingCss', [] ),
						'type' => 'billing',
					)
				) ?>
			</div>

		</div>
	<?php endif ?>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/address/billing' ) ?>
