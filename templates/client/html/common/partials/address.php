<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */

/* Available data:
 * - countries: List of two letter ISO country codes
 * - css: Associative list of item keys as keys and list of CSS classes as values, e.g. "salutation" => ["mandatory"]
 * - disabled: If form fields should be initially disabled
 * - error: Associative list of item keys as keys and error message as values , e.g. "salutation" => "Invalid salutation"
 * - formnames: List of names to build the name of the form fields, e.g. ['address', 'payment']
 * - id: ID of the address item or unset if not available
 * - languages: List of 2-5 letter ISO language codes
 * - languageid: Current 2-5 letter ISO language code
 * - prefix : Prefix of the item keys including the dots, e.g. "order.address."
 * - salutations: List of salutation codes
 * - states: Associative list of two letter ISO country code as keys and list of custom state codes as values
 * - type: Address type, i.e "payment", "delivery"
 */

$enc = $this->encoder();
$addr = $this->get( 'address', [] );
$prefix = $this->get( 'prefix', '' );
$disabled = $this->get( 'disabled', true );
$fnames = $this->get( 'formnames', [] );
$error = $this->get( 'error', [] );
$css = $this->get( 'css', [] );
$id = $this->get( 'id' );


?>
<div class="row form-item form-group salutation <?= $enc->attr( ( $this->value( $error, 'salutation' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'salutation', [] ) ) ) ?>">
	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-salutation-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<select class="form-control" autocomplete="honorific-prefix"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-salutation-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'salutation'] ) ) ) ?>"
			<?= $this->value( $css, 'salutation' ) ? '' : 'disabled' ?> >

			<?php if( count( $this->get( 'salutations', [] ) ) > 1 ) : ?>
				<option value=""><?= $enc->html( $this->translate( 'client', 'Select salutation' ), $enc::TRUST ) ?></option>
			<?php endif ?>

			<?php foreach( $this->get( 'salutations', [] ) as $salutation ) : ?>
				<option value="<?= $enc->attr( $salutation ) ?>" <?= $this->value( $addr, $prefix . 'salutation' ) == $salutation ? 'selected' : '' ?>>
					<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ) ?>
				</option>
			<?php endforeach ?>

		</select>
	</div>
</div>


<div class="row form-item form-group firstname <?= $enc->attr( ( $this->value( $error, 'firstname' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'firstname', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/firstname' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-firstname-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="given-name"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-firstname-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'firstname'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'firstname' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ) ?>"
			<?= $this->value( $css, 'firstname' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group lastname <?= $enc->attr( ( $this->value( $error, 'lastname' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'lastname', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/lastname' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-lastname-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="family-name"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-lastname-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'lastname'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'lastname' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ) ?>"
			<?= $this->value( $css, 'lastname' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group title <?= $enc->attr( ( $this->value( $error, 'title' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'title', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/title' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-title-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Title' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="given-name"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-title-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'title'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'title' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Title' ) ) ?>"
			<?= $this->value( $css, 'title' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group company <?= $enc->attr( ( $this->value( $error, 'company' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'company', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/company' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-company-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="organization"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-company-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'company'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'company' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ) ?>"
			<?= $this->value( $css, 'company' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address1 <?= $enc->attr( ( $this->value( $error, 'address1' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'address1', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/address1' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-address1-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="address-line1"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-address1-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'address1'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'address1' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ) ?>"
			<?= $this->value( $css, 'address1' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address2 <?= $enc->attr( ( $this->value( $error, 'address2' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'address2', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/address2' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-address2-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="address-line2"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-address2-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'address2'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'address2' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ) ?>"
			<?= $this->value( $css, 'address2' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address3 <?= $enc->attr( ( $this->value( $error, 'address3' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'address3', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/address3' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-address3-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="address-line3"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-address3-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'address3'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'address3' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ) ?>"
			<?php echo $this->value( $css, 'address3' ) ? '' : 'disabled' ?>
		>
	</div>
</div>

<div class="row form-item form-group city <?= $enc->attr( ( $this->value( $error, 'city' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'city', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/city' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-city-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="address-level2"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-city-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'city'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'city' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ) ?>"
			<?= $this->value( $css, 'city' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group postal <?= $enc->attr( ( $this->value( $error, 'postal' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'postal', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/postal' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-postal-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text" autocomplete="postal-code"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-postal-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'postal'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'postal' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ) ?>"
			<?= $this->value( $css, 'postal' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<?php if( count( $this->get( 'states', [] ) ) > 0 ) : ?>
	<div class="row form-item form-group state <?= $enc->attr( ( $this->value( $error, 'state' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'state', [] ) ) ) ?>">

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'payment' ) ?>-state-<?= $id ?>">
				<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" autocomplete="address-level1"
				id="address-<?= $this->get( 'type', 'payment' ) ?>-state-<?= $id ?>"
				name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'state'] ) ) ) ?>"
				<?= $this->value( $css, 'state' ) ? '' : 'disabled' ?> >

				<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ) ?></option>

				<?php foreach( $this->get( 'states', [] ) as $regioncode => $stateList ) : ?>

					<optgroup class="<?= $regioncode ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ) ?>">

						<?php foreach( $stateList as $stateCode => $stateName ) : ?>
							<option value="<?= $enc->attr( $stateCode ) ?>" <?= $this->value( $addr, $prefix . 'state' ) == $stateCode ? 'selected' : '' ?>>
								<?= $enc->html( $stateName ) ?>
							</option>
						<?php endforeach ?>

					</optgroup>

				<?php endforeach ?>

			</select>
		</div>
	</div>
<?php endif ?>


<?php if( count( $this->get( 'countries', [] ) ) > 0 ) : ?>
	<div class="row form-item form-group countryid <?= $enc->attr( ( $this->value( $error, 'countryid' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'countryid', [] ) ) ) ?>">

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'payment' ) ?>-countryid-<?= $id ?>">
				<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" autocomplete="country"
				id="address-<?= $this->get( 'type', 'payment' ) ?>-countryid-<?= $id ?>"
				name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'countryid'] ) ) ) ?>"
				<?= $this->value( $css, 'countryid' ) ? '' : 'disabled' ?> >

				<?php if( count( $this->get( 'countries', [] ) ) > 1 ) : ?>
					<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ) ?></option>
				<?php endif ?>

				<?php foreach( $this->get( 'countries', [] ) as $countryId ) : ?>
					<option value="<?= $enc->attr( $countryId ) ?>" <?= $this->value( $addr, $prefix . 'countryid' ) == $countryId ? 'selected' : '' ?>>
						<?= $enc->html( $this->translate( 'country', $countryId ) ) ?>
					</option>
				<?php endforeach ?>

			</select>
		</div>
	</div>
<?php endif ?>


<?php if( count( $this->get( 'languages', [] ) ) > 0 ) : ?>
	<div class="row form-item form-group languageid <?= $enc->attr( ( $this->value( $error, 'languageid' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'languageid', [] ) ) ) ?>">

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'payment' ) ?>-languageid-<?= $id ?>">
				<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" autocomplete="language"
				id="address-<?= $this->get( 'type', 'payment' ) ?>-languageid-<?= $id ?>"
				name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'languageid'] ) ) ) ?>"
				<?= $this->value( $css, 'languageid' ) ? '' : 'disabled' ?> >

				<?php if( count( $this->get( 'languages', [] ) ) > 1 ) : ?>
					<option value=""><?= $enc->html( $this->translate( 'client', 'Select language' ), $enc::TRUST ) ?></option>
				<?php endif ?>

				<?php foreach( $this->get( 'languages', [] ) as $languageId ) : ?>
					<option value="<?= $enc->attr( $languageId ) ?>" <?= $this->value( $addr, $prefix . 'languageid', $this->get( 'languageid' ) ) == $languageId ? 'selected' : '' ?>>
						<?= $enc->html( $this->translate( 'language', $languageId ) ) ?>
					</option>
				<?php endforeach ?>

			</select>
		</div>
	</div>
<?php endif ?>


<div class="row form-item form-group vatid <?= $enc->attr( ( $this->value( $error, 'vatid' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'vatid', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/vatid' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-vatid-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-vatid-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'vatid'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'vatid' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ) ?>"
			<?= $this->value( $css, 'vatid' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group email <?= $enc->attr( ( $this->value( $error, 'email' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'email', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/email', '^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-email-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="email" autocomplete="email"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-email-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'email'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'email' ) ) ?>"
			placeholder="name@example.com"
			<?= $this->value( $css, 'email' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group telephone <?= $enc->attr( ( $this->value( $error, 'telephone' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'telephone', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/telephone' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-telephone-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="tel" autocomplete="tel"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-telephone-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'telephone'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'telephone' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
			<?= $this->value( $css, 'telephone' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group mobile <?= $enc->attr( ( $this->value( $error, 'mobile' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'mobile', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/mobile' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-mobile-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Mobile' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="tel" autocomplete="tel"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-mobile-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'mobile'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'mobile' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
			<?= $this->value( $css, 'mobile' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group telefax <?= $enc->attr( ( $this->value( $error, 'telefax' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'telefax', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/telefax' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-telefax-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="tel" autocomplete="tel"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-telefax-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'telefax'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'telefax' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
			<?= $this->value( $css, 'telefax' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group website <?= $enc->attr( ( $this->value( $error, 'website' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'website', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/common/address/validate/website', '^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-website-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="url" autocomplete="url"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-website-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'website'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'website' ) ) ?>"
			placeholder="https://example.com"
			<?= $this->value( $css, 'website' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group birthday <?= $enc->attr( ( $this->value( $error, 'birthday' ) ? 'error ' : '' ) . join( ' ', $this->value( $css, 'birthday', [] ) ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'payment' ) ?>-birthday-<?= $id ?>">
			<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="date" autocomplete="bday"
			id="address-<?= $this->get( 'type', 'payment' ) ?>-birthday-<?= $id ?>"
			name="<?= $enc->attr( $this->formparam( array_merge( $fnames, [$prefix . 'birthday'] ) ) ) ?>"
			value="<?= $enc->attr( $this->value( $addr, $prefix . 'birthday' ) ) ?>"
			<?= $this->value( $css, 'birthday' ) ? '' : 'disabled' ?>
		>
	</div>
</div>
