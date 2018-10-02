<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

if( !isset( $this->address ) ) {
	throw new \Aimeos\Client\Html\Exception( 'No "address" item to address partial given' );
}

$testfcn = function( $list, $key, $default = '' ) {
	return ( isset( $list[$key] ) ? $list[$key] : $default );
};

$selectfcn = function( $list, $key, $value ) {
	return ( isset( $list[$key] ) && $list[$key] == $value ? 'selected="selected"' : '' );
};

$disablefcn = function( $list, $key ) {
	return ( !isset( $list[$key] ) ? 'disabled="disabled"' : '' );
};

$regex = $this->config( 'client/html/checkout/standard/address/validate', [] );

$addr = $this->get( 'address', [] );
$salutations = $this->get( 'salutations', [] );
$languages = $this->get( 'languages', [] );
$countries = $this->get( 'countries', [] );
$states = $this->get( 'states', [] );
$type = $this->get( 'type', 'billing' );
$css = $this->get( 'css', [] );
$id = $this->get( 'id' );

$idstr = ( $id != null ? '-' . $id : '' );
$fname = ( $id != null ? 'ca_' . $type . '_' . $id : 'ca_' . $type );


?>
<li class="form-item form-group salutation <?= ( isset( $css['order.base.address.salutation'] ) ? join( ' ', $css['order.base.address.salutation'] ) : '' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-salutation<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<select class="form-control" id="address-<?= $type ?>-salutation<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.salutation' ) ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.salutation' ); ?> >

			<?php if( count( $salutations ) > 1 ) : ?>
				<option value=""><?= $enc->html( $this->translate( 'client', 'Select salutation' ), $enc::TRUST ); ?></option>
			<?php endif; ?>

			<?php foreach( $salutations as $salutation ) : ?>
				<option value="<?= $enc->attr( $salutation ); ?>" <?= $selectfcn( $addr, 'order.base.address.salutation', $salutation ); ?> >
					<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ); ?>
				</option>
			<?php endforeach; ?>

		</select>
	</div>
</li>


<li class="form-item form-group firstname <?= ( isset( $css['order.base.address.firstname'] ) ? join( ' ', $css['order.base.address.firstname'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.firstname' ); ?>" >

	<label class="col-md-5" for="address-<?= $type ?>-firstname<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-firstname<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.firstname' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.firstname' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.firstname' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group lastname <?= ( isset( $css['order.base.address.lastname'] ) ? join( ' ', $css['order.base.address.lastname'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.lastname' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-lastname<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-lastname<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.lastname' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.lastname' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.lastname' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group company <?= ( isset( $css['order.base.address.company'] ) ? join( ' ', $css['order.base.address.company'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.company' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-company<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-company<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.company' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.company' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.company' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group address1 <?= ( isset( $css['order.base.address.address1'] ) ? join( ' ', $css['order.base.address.address1'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.address1' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-address1<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-address1<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.address1' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.address1' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.address1' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group address2 <?= ( isset( $css['order.base.address.address2'] ) ? join( ' ', $css['order.base.address.address2'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.address2' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-address2<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-address2<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.address2' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.address2' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.address2' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group address3 <?= ( isset( $css['order.base.address.address3'] ) ? join( ' ', $css['order.base.address.address3'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.address3' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-address3<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-address3<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.address3' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.address3' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.address3' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group city <?= ( isset( $css['order.base.address.city'] ) ? join( ' ', $css['order.base.address.city'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.city' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-city<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-city<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.city' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.city' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.city' ); ?>
		/>
	</div>

</li>


<?php if( count( $states ) > 0 ) : ?>
	<li class="form-item form-group state <?= ( isset( $css['order.base.address.state'] ) ? join( ' ', $css['order.base.address.state'] ) : '' ); ?>">

		<label class="col-md-5" for="address-<?= $type ?>-state<?= $idstr ?>">
			<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ); ?>
		</label>
		<div class="col-md-7">
			<select class="form-control" id="address-<?= $type ?>-state<?= $idstr ?>"
				name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.state' ) ) ); ?>"
				<?= $disablefcn( $css, 'order.base.address.state' ); ?> >

				<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ); ?></option>
				<?php foreach( $states as $regioncode => $stateList ) : ?>
					<optgroup class="<?= $regioncode; ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ); ?>">
						<?php foreach( $stateList as $stateCode => $stateName ) : ?>
							<option value="<?= $enc->attr( $stateCode ); ?>" <?= $selectfcn( $addr, 'order.base.address.state', $stateCode ); ?> >
								<?= $enc->html( $stateName ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>

			</select>
		</div>

	</li>
<?php endif; ?>


<li class="form-item form-group postal <?= ( isset( $css['order.base.address.postal'] ) ? join( ' ', $css['order.base.address.postal'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.postal' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-postal<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-postal<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.postal' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.postal' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.postal' ); ?>
		/>
	</div>

</li>


<?php if( count( $countries ) > 0 ) : ?>
	<li class="form-item form-group countryid <?= ( isset( $css['order.base.address.countryid'] ) ? join( ' ', $css['order.base.address.countryid'] ) : '' ); ?>">

		<label class="col-md-5" for="address-<?= $type ?>-countryid<?= $idstr ?>">
			<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ); ?>
		</label>
		<div class="col-md-7">
			<select class="form-control" id="address-<?= $type ?>-countryid<?= $idstr ?>"
				name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.countryid' ) ) ); ?>"
				<?= $disablefcn( $css, 'order.base.address.countryid' ); ?> >

				<?php if( count( $countries ) > 1 ) : ?>
					<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ); ?></option>
				<?php endif; ?>
				<?php foreach( $countries as $countryId ) : ?>
					<option value="<?= $enc->attr( $countryId ); ?>" <?= $selectfcn( $addr, 'order.base.address.countryid', $countryId ); ?> >
						<?= $enc->html( $this->translate( 'country', $countryId ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

	</li>
<?php endif; ?>


<li class="form-item form-group languageid <?= ( isset( $css['order.base.address.languageid'] ) ? join( ' ', $css['order.base.address.languageid'] ) : '' ); ?>"
	<?= $disablefcn( $css, 'order.base.address.languageid' ); ?> >

	<label class="col-md-5" for="address-<?= $type ?>-languageid<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<select class="form-control" id="address-<?= $type ?>-languageid<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.languageid' ) ) ); ?>">

			<?php foreach( $languages as $languageId ) : ?>
				<option value="<?= $enc->attr( $languageId ); ?>" <?= $selectfcn( $addr, 'order.base.address.languageid', $languageId ); ?> >
					<?= $enc->html( $this->translate( 'language', $languageId ) ); ?>
				</option>
			<?php endforeach; ?>

		</select>
	</div>

</li>


<li class="form-item form-group vatid <?= ( isset( $css['order.base.address.vatid'] ) ? join( ' ', $css['order.base.address.vatid'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.vatid' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-vatid<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="text"
			id="address-<?= $type ?>-vatid<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.vatid' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.vatid' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.vatid' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group email <?= ( isset( $css['order.base.address.email'] ) ? join( ' ', $css['order.base.address.email'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.email', '^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-email<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="email"
			id="address-<?= $type ?>-email<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.email' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.email' ) ); ?>"
			placeholder="name@example.com" <?= $disablefcn( $css, 'order.base.address.email' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group telephone <?= ( isset( $css['order.base.address.telephone'] ) ? join( ' ', $css['order.base.address.telephone'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.telephone' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-telephone<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="tel"
			id="address-<?= $type ?>-telephone<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.telephone' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.telephone' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.telephone' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group telefax <?= ( isset( $css['order.base.address.telefax'] ) ? join( ' ', $css['order.base.address.telefax'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.telefax' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-telefax<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="tel"
			id="address-<?= $type ?>-telefax<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.telefax' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.telefax' ) ); ?>"
			placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
			<?= $disablefcn( $css, 'order.base.address.telefax' ); ?>
		/>
	</div>

</li>


<li class="form-item form-group website <?= ( isset( $css['order.base.address.website'] ) ? join( ' ', $css['order.base.address.website'] ) : '' ); ?>"
	data-regex="<?= $testfcn( $regex, 'order.base.address.website', '^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$' ); ?>">

	<label class="col-md-5" for="address-<?= $type ?>-website<?= $idstr ?>">
		<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ); ?>
	</label>
	<div class="col-md-7">
		<input class="form-control" type="url"
			id="address-<?= $type ?>-website<?= $idstr ?>"
			name="<?= $enc->attr( $this->formparam( array( $fname, 'order.base.address.website' ) ) ); ?>"
			value="<?= $enc->attr( $testfcn( $addr, 'order.base.address.website' ) ); ?>"
			placeholder="http://example.com"
			<?= $disablefcn( $css, 'order.base.address.website' ); ?>
		/>
	</div>
</li>
