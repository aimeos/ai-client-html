<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();
$fname = 'ca_' . $this->get( 'type', 'billing' ) . ( $this->get( 'id' ) ? '_' : '' ) . $this->get( 'id' );


?>
<div class="row form-item form-group salutation <?= $enc->attr( ( $this->value( 'error', 'order.address.salutation' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.salutation', [] ) ) ) ?>">
	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-salutation-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<select class="form-control" id="address-<?= $this->get( 'type', 'billing' ) ?>-salutation-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.salutation' ) ) ) ?>"
			<?= $this->value( 'css', 'order.address.salutation' ) ? '' : 'disabled' ?>>

			<?php if( count( $this->get( 'salutations', [] ) ) > 1 ) : ?>
				<option value=""><?= $enc->html( $this->translate( 'client', 'Select salutation' ), $enc::TRUST ) ?></option>
			<?php endif ?>

			<?php foreach( $this->get( 'salutations', [] ) as $salutation ) : ?>
				<option value="<?= $enc->attr( $salutation ) ?>" <?= $this->value( 'address', 'order.address.salutation' ) == $salutation ? 'selected' : '' ?>>
					<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ) ?>
				</option>
			<?php endforeach ?>

		</select>
	</div>
</div>


<div class="row form-item form-group firstname <?= $enc->attr( ( $this->value( 'error', 'order.address.firstname' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.firstname', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/firstname' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-firstname-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-firstname-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.firstname' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.firstname' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ) ?>"
			<?= $this->value( 'css', 'order.address.firstname' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group lastname <?= $enc->attr( ( $this->value( 'error', 'order.address.lastname' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.lastname', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/lastname' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-lastname-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-lastname-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.lastname' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.lastname' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ) ?>"
			<?= $this->value( 'css', 'order.address.lastname' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group company <?= $enc->attr( ( $this->value( 'error', 'order.address.company' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.company', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/company' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-company-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-company-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.company' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.company' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ) ?>"
			<?= $this->value( 'css', 'order.address.company' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address1 <?= $enc->attr( ( $this->value( 'error', 'order.address.address1' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.address1', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/address1' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-address1-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-address1-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.address1' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.address1' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ) ?>"
			<?= $this->value( 'css', 'order.address.address1' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address2 <?= $enc->attr( ( $this->value( 'error', 'order.address.address2' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.address2', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/address2' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-address2-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-address2-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.address2' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.address2' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ) ?>"
			<?= $this->value( 'css', 'order.address.address2' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group address3 <?= $enc->attr( ( $this->value( 'error', 'order.address.address3' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.address3', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/address3' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-address3-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-address3-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.address3' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.address3' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ) ?>"
			<?= $this->value( 'css', 'order.address.address3' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group city <?= $enc->attr( ( $this->value( 'error', 'order.address.city' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.city', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/city' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-city-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-city-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.city' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.city' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ) ?>"
			<?= $this->value( 'css', 'order.address.city' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group postal <?= $enc->attr( ( $this->value( 'error', 'order.address.postal' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.postal', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/postal' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-postal-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-postal-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.postal' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.postal' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ) ?>"
			<?= $this->value( 'css', 'order.address.postal' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<?php if( count( $this->get( 'states', [] ) ) > 0 ) : ?>
	<div class="row form-item form-group state <?= $enc->attr( ( $this->value( 'error', 'order.address.state' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.state', [] ) ) ) ?>">

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'billing' ) ?>-state-<?= $this->get( 'id' ) ?>">
				<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" id="address-<?= $this->get( 'type', 'billing' ) ?>-state-<?= $this->get( 'id' ) ?>"
				name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.state' ) ) ) ?>"
				<?= $this->value( 'css', 'order.address.state' ) ? '' : 'disabled' ?>>

				<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ) ?></option>

				<?php foreach( $this->get( 'states', [] ) as $regioncode => $stateList ) : ?>

					<optgroup class="<?= $regioncode ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ) ?>">

						<?php foreach( $stateList as $stateCode => $stateName ) : ?>
							<option value="<?= $enc->attr( $stateCode ) ?>" <?= $this->value( 'address', 'order.address.state' ) == $stateCode ? 'selected' : '' ?>>
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
	<div class="row form-item form-group countryid <?= $enc->attr( ( $this->value( 'error', 'order.address.countryid' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.countryid', [] ) ) ) ?>">

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'billing' ) ?>-countryid-<?= $this->get( 'id' ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" id="address-<?= $this->get( 'type', 'billing' ) ?>-countryid-<?= $this->get( 'id' ) ?>"
				name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.countryid' ) ) ) ?>"
				<?= $this->value( 'css', 'order.address.countryid' ) ? '' : 'disabled' ?>>

				<?php if( count( $this->get( 'countries', [] ) ) > 1 ) : ?>
					<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ) ?></option>
				<?php endif ?>

				<?php foreach( $this->get( 'countries', [] ) as $countryId => $name ) : ?>
					<option value="<?= $enc->attr( $countryId ) ?>" <?= $this->value( 'address', 'order.address.countryid' ) == $countryId ? 'selected' : '' ?>>
						<?= $enc->html( $name ) ?>
					</option>
				<?php endforeach ?>

			</select>
		</div>
	</div>
<?php endif ?>


<?php if( count( $this->get( 'languages', [] ) ) > 0 ) : ?>
	<div class="row form-item form-group languageid <?= $enc->attr( ( $this->value( 'error', 'order.address.languageid' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.languageid', [] ) ) ) ?>"
		<?= $this->value( 'css', 'order.address.languageid' ) ? '' : 'disabled' ?>>

		<div class="col-md-5">
			<label for="address-<?= $this->get( 'type', 'billing' ) ?>-languageid-<?= $this->get( 'id' ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ) ?>
			</label>
		</div>
		<div class="col-md-7">
			<select class="form-control" id="address-<?= $this->get( 'type', 'billing' ) ?>-languageid-<?= $this->get( 'id' ) ?>"
				name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.languageid' ) ) ) ?>">

				<?php if( count( $this->get( 'languages', [] ) ) > 1 ) : ?>
					<option value=""><?= $enc->html( $this->translate( 'client', 'Select language' ), $enc::TRUST ) ?></option>
				<?php endif ?>

				<?php foreach( $this->get( 'languages', [] ) as $languageId ) : ?>
					<option value="<?= $enc->attr( $languageId ) ?>" <?= $this->value( 'address', 'order.address.languageid', $this->get( 'languageid' ) ) == $languageId ? 'selected' : '' ?>>
						<?= $enc->html( $this->translate( 'language', $languageId ) ) ?>
					</option>
				<?php endforeach ?>

			</select>
		</div>
	</div>
<?php endif ?>


<div class="row form-item form-group vatid <?= $enc->attr( ( $this->value( 'error', 'order.address.vatid' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.vatid', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/vatid' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-vatid-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-vatid-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.vatid' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.vatid' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ) ?>"
			<?= $this->value( 'css', 'order.address.vatid' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group email <?= $enc->attr( ( $this->value( 'error', 'order.address.email' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.email', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/email', '^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-email-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="email"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-email-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.email' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.email' ) ) ?>"
			placeholder="name@example.com"
			<?= $this->value( 'css', 'order.address.email' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group telephone <?= $enc->attr( ( $this->value( 'error', 'order.address.telephone' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.telephone', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/telephone' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-telephone-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="tel"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-telephone-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.telephone' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.telephone' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
			<?= $this->value( 'css', 'order.address.telephone' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group telefax <?= $enc->attr( ( $this->value( 'error', 'order.address.telefax' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.telefax', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/telefax' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-telefax-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="tel"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-telefax-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.telefax' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.telefax' ) ) ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
			<?= $this->value( 'css', 'order.address.telefax' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group website <?= $enc->attr( ( $this->value( 'error', 'order.address.website' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.website', [] ) ) ) ?>"
	data-regex="<?= $enc->attr( $this->config( 'client/html/checkout/standard/address/validate/website', '^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$' ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-website-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="url"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-website-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.website' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.website' ) ) ?>"
			placeholder="https://example.com"
			<?= $this->value( 'css', 'order.address.website' ) ? '' : 'disabled' ?>
		>
	</div>
</div>


<div class="row form-item form-group birthday <?= $enc->attr( ( $this->value( 'error', 'order.address.birthday' ) ? 'error ' : '' ) . join( ' ', $this->value( 'css', 'order.address.birthday', [] ) ) ) ?>">

	<div class="col-md-5">
		<label for="address-<?= $this->get( 'type', 'billing' ) ?>-birthday-<?= $this->get( 'id' ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ) ?>
		</label>
	</div>
	<div class="col-md-7">
		<input class="form-control" type="date"
			id="address-<?= $this->get( 'type', 'billing' ) ?>-birthday-<?= $this->get( 'id' ) ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.address.birthday' ) ) ) ?>"
			value="<?= $enc->attr( $this->value( 'address', 'order.address.birthday' ) ) ?>"
		>
	</div>
</div>
