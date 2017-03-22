<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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

$regex = $this->config( 'client/html/checkout/standard/address/validate', array() );

$addr = $this->get( 'address', array() );
$salutations = $this->get( 'salutations', array() );
$languages = $this->get( 'languages', array() );
$countries = $this->get( 'countries', array() );
$states = $this->get( 'states', array() );
$type = $this->get( 'type', 'billing' );
$css = $this->get( 'css', array() );
$id = $this->get( 'id' );

$idstr = ( $id != null ? '-' . $id : '' );
$fname = ( $id != null ? 'ca_' . $type . '_' . $id : 'ca_' . $type );


?>
<li class="form-item salutation <?php echo ( isset( $css['order.base.address.salutation'] ) ? join( ' ', $css['order.base.address.salutation'] ) : '' ); ?>">

	<label for="address-<?php echo $type ?>-salutation<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ); ?>
	</label><!--

	--><select id="address-<?php echo $type ?>-salutation<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.salutation' ) ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.salutation' ); ?> >

		<?php if( count( $salutations ) > 1 ) : ?>
			<option value=""><?php echo $enc->html( $this->translate( 'client', 'Select salutation' ), $enc::TRUST ); ?></option>
		<?php endif; ?>

		<?php foreach( $salutations as $salutation ) : ?>
			<option value="<?php echo $enc->attr( $salutation ); ?>" <?php echo $selectfcn( $addr, 'order.base.address.salutation', $salutation ); ?> >
				<?php echo $enc->html( $this->translate( 'client/code', $salutation ) ); ?>
			</option>
		<?php endforeach; ?>

	</select>
</li>


<li class="form-item firstname <?php echo ( isset( $css['order.base.address.firstname'] ) ? join( ' ', $css['order.base.address.firstname'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.firstname' ); ?>" >

	<label for="address-<?php echo $type ?>-firstname<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-firstname<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.firstname' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.firstname' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'First name' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.firstname' ); ?>
	/>

</li>


<li class="form-item lastname <?php echo ( isset( $css['order.base.address.lastname'] ) ? join( ' ', $css['order.base.address.lastname'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.lastname' ); ?>">

	<label for="address-<?php echo $type ?>-lastname<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-lastname<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.lastname' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.lastname' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Last name' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.lastname' ); ?>
	/>

</li>


<li class="form-item company <?php echo ( isset( $css['order.base.address.company'] ) ? join( ' ', $css['order.base.address.company'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.company' ); ?>">

	<label for="address-<?php echo $type ?>-company<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-company<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.company' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.company' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Company' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.company' ); ?>
	/>

</li>


<li class="form-item address1 <?php echo ( isset( $css['order.base.address.address1'] ) ? join( ' ', $css['order.base.address.address1'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.address1' ); ?>">

	<label for="address-<?php echo $type ?>-address1<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-address1<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.address1' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.address1' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Street' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.address1' ); ?>
	/>

</li>


<li class="form-item address2 <?php echo ( isset( $css['order.base.address.address2'] ) ? join( ' ', $css['order.base.address.address2'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.address2' ); ?>">

	<label for="address-<?php echo $type ?>-address2<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-address2<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.address2' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.address2' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Additional' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.address2' ); ?>
	/>

</li>


<li class="form-item address3 <?php echo ( isset( $css['order.base.address.address3'] ) ? join( ' ', $css['order.base.address.address3'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.address3' ); ?>">

	<label for="address-<?php echo $type ?>-address3<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-address3<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.address3' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.address3' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Additional 2' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.address3' ); ?>
	/>

</li>


<li class="form-item city <?php echo ( isset( $css['order.base.address.city'] ) ? join( ' ', $css['order.base.address.city'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.city' ); ?>">

	<label for="address-<?php echo $type ?>-city<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-city<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.city' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.city' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'City' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.city' ); ?>
	/>

</li>


<?php if( count( $states ) > 0 ) : ?>
	<li class="form-item state <?php echo ( isset( $css['order.base.address.state'] ) ? join( ' ', $css['order.base.address.state'] ) : '' ); ?>">

		<label for="address-<?php echo $type ?>-state<?php echo $idstr ?>">
			<?php echo $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ); ?>
		</label><!--

		--><select id="address-<?php echo $type ?>-state<?php echo $idstr ?>"
			name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.state' ) ) ); ?>"
			<?php echo $disablefcn( $css, 'order.base.address.state' ); ?> >

			<option value=""><?php echo $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ); ?></option>
			<?php foreach( $states as $regioncode => $stateList ) : ?>
				<optgroup class="<?php echo $regioncode; ?>" label="<?php echo $enc->attr( $this->translate( 'client/country', $regioncode ) ); ?>">
					<?php foreach( $stateList as $stateCode => $stateName ) : ?>
						<option value="<?php echo $enc->attr( $stateCode ); ?>" <?php echo $selectfcn( $addr, 'order.base.address.state', $stateCode ); ?> >
							<?php echo $enc->html( $stateName ); ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>

		</select>

	</li>
<?php endif; ?>


<li class="form-item postal <?php echo ( isset( $css['order.base.address.postal'] ) ? join( ' ', $css['order.base.address.postal'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.postal' ); ?>">

	<label for="address-<?php echo $type ?>-postal<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-postal<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.postal' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.postal' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'Postal code' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.postal' ); ?>
	/>

</li>


<?php if( count( $countries ) > 0 ) : ?>
	<li class="form-item countryid <?php echo ( isset( $css['order.base.address.countryid'] ) ? join( ' ', $css['order.base.address.countryid'] ) : '' ); ?>">

		<label for="address-<?php echo $type ?>-countryid<?php echo $idstr ?>">
			<?php echo $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ); ?>
		</label><!--

		--><select id="address-<?php echo $type ?>-countryid<?php echo $idstr ?>"
			name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.countryid' ) ) ); ?>"
			<?php echo $disablefcn( $css, 'order.base.address.countryid' ); ?> >

			<?php if( count( $countries ) > 1 ) : ?>
        			<option value=""><?php echo $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ); ?></option>
      			<?php endif; ?>
			<?php foreach( $countries as $countryId ) : ?>
				<option value="<?php echo $enc->attr( $countryId ); ?>" <?php echo $selectfcn( $addr, 'order.base.address.countryid', $countryId ); ?> >
					<?php echo $enc->html( $this->translate( 'client/country', $countryId ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>

	</li>
<?php endif; ?>


<li class="form-item languageid <?php echo ( isset( $css['order.base.address.languageid'] ) ? join( ' ', $css['order.base.address.languageid'] ) : '' ); ?>"
	<?php echo $disablefcn( $css, 'order.base.address.languageid' ); ?> >

	<label for="address-<?php echo $type ?>-languageid<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ); ?>
	</label><!--

	--><select id="address-<?php echo $type ?>-languageid<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.languageid' ) ) ); ?>">

		<?php foreach( $languages as $languageId ) : ?>
			<option value="<?php echo $enc->attr( $languageId ); ?>" <?php echo $selectfcn( $addr, 'order.base.address.languageid', $languageId ); ?> >
				<?php echo $enc->html( $this->translate( 'client/language', $languageId ) ); ?>
			</option>
		<?php endforeach; ?>

	</select>

</li>


<li class="form-item vatid <?php echo ( isset( $css['order.base.address.vatid'] ) ? join( ' ', $css['order.base.address.vatid'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.vatid' ); ?>">

	<label for="address-<?php echo $type ?>-vatid<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="text"
		id="address-<?php echo $type ?>-vatid<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.vatid' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.vatid' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', 'GB999999973' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.vatid' ); ?>
	/>

</li>


<li class="form-item email <?php echo ( isset( $css['order.base.address.email'] ) ? join( ' ', $css['order.base.address.email'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.email', '^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$' ); ?>">

	<label for="address-<?php echo $type ?>-email<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="email"
		id="address-<?php echo $type ?>-email<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.email' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.email' ) ); ?>"
		placeholder="name@example.com" <?php echo $disablefcn( $css, 'order.base.address.email' ); ?>
	/>

</li>


<li class="form-item telephone <?php echo ( isset( $css['order.base.address.telephone'] ) ? join( ' ', $css['order.base.address.telephone'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.telephone' ); ?>">

	<label for="address-<?php echo $type ?>-telephone<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="tel"
		id="address-<?php echo $type ?>-telephone<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.telephone' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.telephone' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.telephone' ); ?>
	/>
</li>


<li class="form-item telefax <?php echo ( isset( $css['order.base.address.telefax'] ) ? join( ' ', $css['order.base.address.telefax'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.telefax' ); ?>">

	<label for="address-<?php echo $type ?>-telefax<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="tel"
		id="address-<?php echo $type ?>-telefax<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.telefax' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.telefax' ) ); ?>"
		placeholder="<?php echo $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
		<?php echo $disablefcn( $css, 'order.base.address.telefax' ); ?>
	/>

</li>


<li class="form-item website <?php echo ( isset( $css['order.base.address.website'] ) ? join( ' ', $css['order.base.address.website'] ) : '' ); ?>"
	data-regex="<?php echo $testfcn( $regex, 'order.base.address.website', '^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$' ); ?>">

	<label for="address-<?php echo $type ?>-website<?php echo $idstr ?>">
		<?php echo $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ); ?>
	</label><!--

	--><input type="url"
		id="address-<?php echo $type ?>-website<?php echo $idstr ?>"
		name="<?php echo $enc->attr( $this->formparam( array( $fname, 'order.base.address.website' ) ) ); ?>"
		value="<?php echo $enc->attr( $testfcn( $addr, 'order.base.address.website' ) ); ?>"
		placeholder="http://example.com"
		<?php echo $disablefcn( $css, 'order.base.address.website' ); ?>
	/>
</li>
