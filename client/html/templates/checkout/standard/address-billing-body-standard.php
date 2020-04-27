<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

$enc = $this->encoder();

/** client/html/common/address/billing/disable-new
 * Disables the billing address form for a new address
 *
 * Normally, customers are allowed to enter a different billing address in the
 * checkout process that is only stored along with the current order. Registered
 * customers also have the possibility to change their current billing address
 * but this updates the existing one in their profile.
 *
 * You can disable the address form for the new billing address by this setting
 * if it shouldn't be allowed to enter a different billing address.
 *
 * @param boolean True to disable the "new billing address" form, false to allow a new address
 * @since 2014.03
 * @category Developer
 * @category User
 * @see client/html/common/address/delivery/disable-new
 */


?>
<?php $this->block()->start( 'checkout/standard/address/billing' ); ?>
<div class="checkout-standard-address-billing col-xs-12 col-xl">
	<h2><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h2>


	<?php if( isset( $this->addressPaymentItem ) && $this->addressPaymentItem->getAddressId() ) : ?>
		<div class="item-address">
			<div class="header">
				<input id="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>" type="radio"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ) ?>"
					value="<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>"
					<?= $this->get( 'addressBillingOption' ) == $this->addressPaymentItem->getAddressId() ? 'checked="checked"' : '' ?>
				/>
				<label for="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ) ?>" class="values">
					<?= nl2br( $this->get( 'addressBillingString', '' ) ) ?>
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
						'languages' => $this->get( 'addressLanguages', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressBillingCss', [] ),
						'type' => 'billing',
					)
				); ?>
			</div>

		</div>
	<?php endif; ?>


	<?php if( !$this->config( 'client/html/common/address/billing/disable-new', false ) ) : ?>
		<div class="item-address item-new" data-option="<?= $enc->attr( $this->get( 'addressBillingOption' ) ); ?>">
			<div class="header">
				<input id="ca_billingoption-new" type="radio" value="null"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ); ?>"
					<?= $this->get( 'addressBillingOption' ) == 'null' ? 'checked="checked"' : '' ?>
				/>
				<label for="ca_billingoption-new" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ); ?>
				</label>
			</div>
			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $this->get( 'addressBillingValuesNew', [] ),
						'error' => $this->get( 'addressBillingOption' ) == 'null' ? $this->get( 'addressBillingError', [] ) : [],
						'salutations' => $this->get( 'addressBillingSalutations', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressBillingCss', [] ),
						'type' => 'billing',
					)
				); ?>
			</div>

		</div>
	<?php endif; ?>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/address/billing' ); ?>
