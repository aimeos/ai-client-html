<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
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
$disablenew = (bool) $this->config( 'client/html/common/address/billing/disable-new', false );


try {
	$addrArray = $this->standardBasket->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT )->toArray();
} catch( Exception $e ) {
	$addrArray = [];
}


if( !isset( $addrArray['order.base.address.addressid'] ) || $addrArray['order.base.address.addressid'] == '' ) {
	$billingDefault = ( isset( $this->addressCustomerItem ) ? $this->addressCustomerItem->getId() : 'null' );
} else {
	$billingDefault = $addrArray['order.base.address.addressid'];
}

$billingOption = $this->param( 'ca_billingoption', $billingDefault );
$billingSalutations = $this->get( 'billingSalutations', [] );
$billingCountries = $this->get( 'addressCountries', [] );
$billingStates = $this->get( 'addressStates', [] );
$billingLanguages = $this->get( 'addressLanguages', [] );


$paymentCssAll = [];

foreach( $this->get( 'billingMandatory', [] ) as $name ) {
	$paymentCssAll[$name][] = 'mandatory';
}

foreach( $this->get( 'billingOptional', [] ) as $name ) {
	$paymentCssAll[$name][] = 'optional';
}

foreach( $this->get( 'billingHidden', [] ) as $name ) {
	$paymentCssAll[$name][] = 'hidden';
}


?>
<?php $this->block()->start( 'checkout/standard/address/billing' ); ?>
<div class="checkout-standard-address-billing col-sm-6">
	<h2><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h2>


	<?php if( isset( $this->addressPaymentItem )  ) : ?>
		<div class="item-address">
			<div class="header">

				<input id="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ); ?>" type="radio"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ); ?>"
					value="<?= $enc->attr( $this->addressPaymentItem->getAddressId() ); ?>"
					<?= ( $billingOption == $this->addressPaymentItem->getAddressId() ? 'checked="checked"' : '' ); ?>
				/>
				<label for="ca_billingoption-<?= $enc->attr( $this->addressPaymentItem->getAddressId() ); ?>" class="values">
<?php
	$addr = $this->addressPaymentItem;

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
	$paymentCss = $paymentCssAll;
	if( $billingOption == $addr->getAddressId() )
	{
		foreach( $this->get( 'billingError', [] ) as $name => $msg ) {
			$paymentCss[$name][] = 'error';
		}
	}

	$addrValues = array_merge( $addr->toArray(), $this->param( 'ca_billing_' . $this->addressPaymentItem->getAddressId(), [] ) );

	if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
		$addrValues['order.base.address.languageid'] = $this->get( 'billingLanguage', 'en' );
	}
?>
			<ul class="form-list">
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
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard.php' ),
					array(
						'address' => $addrValues,
						'salutations' => $billingSalutations,
						'languages' => $billingLanguages,
						'countries' => $billingCountries,
						'states' => $billingStates,
						'type' => 'billing',
						'css' => $paymentCss,
						'id' => $addr->getAddressId(),
					)
				); ?>
			</ul>

		</div>
	<?php endif; ?>


	<?php if( $disablenew === false ) : ?>

		<div class="item-address item-new" data-option="<?= $enc->attr( $billingOption ); ?>">
			<div class="header">
				<input id="ca_billingoption-new" type="radio"
					name="<?= $enc->attr( $this->formparam( array( 'ca_billingoption' ) ) ); ?>"
					value="null"
					<?= ( $billingOption == 'null' ? 'checked="checked"' : '' ); ?>
				/>
				<label for="ca_billingoption-new" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ); ?>
				</label>
			</div>
<?php
	$paymentCss = $paymentCssAll;
	if( $billingOption == 'null' )
	{
		foreach( $this->get( 'billingError', [] ) as $name => $msg ) {
			$paymentCss[$name][] = 'error';
		}
	}

	$addrValues = array_merge( $addrArray, $this->param( 'ca_billing', [] ) );

	if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
		$addrValues['order.base.address.languageid'] = $this->get( 'billingLanguage', 'en' );
	}

	$values = array(
		'address' => $addrValues,
		'salutations' => $billingSalutations,
		'languages' => $billingLanguages,
		'countries' => $billingCountries,
		'states' => $billingStates,
		'type' => 'billing',
		'css' => $paymentCss,
	);
?>
			<ul class="form-list">

				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard.php' ),
					$values
				); ?>

				<li class="form-item birthday">
					<label class="col-md-5" for="customer-birthday">
						<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control birthday" type="date"
						       id="customer-birthday"
							name="<?= $enc->attr( $this->formparam( array( 'ca_extra', 'customer.birthday' ) ) ); ?>"
							value="<?= $enc->attr( $this->get( 'addressExtra/customer.birthday' ) ); ?>"
						/>
					</div>
				</li>
			</ul>

		</div>
	<?php endif; ?>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/address/billing' ); ?>
