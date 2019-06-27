<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */

$enc = $this->encoder();

$selectfcn = function( $list, $key, $value ) {
	return ( isset( $list[$key] ) && $list[$key] == $value ? 'selected="selected"' : '' );
};

$languages = $this->get( 'languages', [] );
$countries = $this->get( 'countries', [] );
$states = $this->get( 'states', [] );


?>
<?php if( isset( $this->addressCustomer ) ) : $addr = $this->addressCustomer->toArray() ?>

<div class="account-profile-address">
	<div class="panel panel-default">
		<div class="panel-heading"><?= $enc->html( $this->translate( 'client', 'Billing address' ) ) ?></div>
		<div class="panel-body">

			<ul class="form-list">

				<li class="form-item form-group salutation">

					<label class="col-md-5" for="address-payment-salutation">
						<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<select class="form-control" id="address-payment-salutation"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.salutation' ) ) ); ?>" >

							<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
								<option value="<?= $enc->attr( $salutation ); ?>" <?= $selectfcn( $addr, 'customer.salutation', $salutation ); ?> >
									<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ); ?>
								</option>
							<?php endforeach; ?>

						</select>
					</div>
				</li>


				<li class="form-item form-group firstname">

					<label class="col-md-5" for="address-payment-firstname">
						<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-firstname"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.firstname' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.firstname' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group lastname">

					<label class="col-md-5" for="address-payment-lastname">
						<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-lastname"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.lastname' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.lastname' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group company">

					<label class="col-md-5" for="address-payment-company">
						<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-company"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.company' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.company' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address1">

					<label class="col-md-5" for="address-payment-address1">
						<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-address1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address1' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address1' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address2">

					<label class="col-md-5" for="address-payment-address2">
						<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-address2"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address2' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address2' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address3">

					<label class="col-md-5" for="address-payment-address3">
						<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-address3"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address3' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address3' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group city">

					<label class="col-md-5" for="address-payment-city">
						<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-city"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.city' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.city' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ); ?>"
						/>
					</div>

				</li>


				<?php if( count( $states ) > 0 ) : ?>
					<li class="form-item form-group state>

						<label class="col-md-5" for="address-payment-state">
							<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-payment-state"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.state' ) ) ); ?>">

								<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ); ?></option>
								<?php foreach( $states as $regioncode => $stateList ) : ?>
									<optgroup class="<?= $regioncode; ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ); ?>">
										<?php foreach( $stateList as $stateCode => $stateName ) : ?>
											<option value="<?= $enc->attr( $stateCode ); ?>" <?= $selectfcn( $addr, 'customer.state', $stateCode ); ?> >
												<?= $enc->html( $stateName ); ?>
											</option>
										<?php endforeach; ?>
									</optgroup>
								<?php endforeach; ?>

							</select>
						</div>

					</li>
				<?php endif; ?>


				<li class="form-item form-group postal">

					<label class="col-md-5" for="address-payment-postal">
						<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-postal"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.postal' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.postal' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ); ?>"
						/>
					</div>

				</li>


				<?php if( count( $countries ) > 0 ) : ?>
					<li class="form-item form-group countryid">

						<label class="col-md-5" for="address-payment-countryid">
							<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-payment-countryid"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.countryid' ) ) ); ?>">

								<?php if( count( $countries ) > 1 ) : ?>
									<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ); ?></option>
								<?php endif; ?>
								<?php foreach( $countries as $countryId ) : ?>
									<option value="<?= $enc->attr( $countryId ); ?>" <?= $selectfcn( $addr, 'customer.countryid', $countryId ); ?> >
										<?= $enc->html( $this->translate( 'country', $countryId ) ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

					</li>
				<?php endif; ?>


				<li class="form-item form-group languageid">

					<label class="col-md-5" for="address-payment-languageid">
						<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<select class="form-control" id="address-payment-languageid"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.languageid' ) ) ); ?>">

							<?php foreach( $languages as $languageId ) : ?>
								<option value="<?= $enc->attr( $languageId ); ?>" <?= $selectfcn( $addr, 'customer.languageid', $languageId ); ?> >
									<?= $enc->html( $this->translate( 'language', $languageId ) ); ?>
								</option>
							<?php endforeach; ?>

						</select>
					</div>

				</li>


				<li class="form-item form-group vatid">

					<label class="col-md-5" for="address-payment-vatid">
						<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-payment-vatid"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.vatid' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.vatid' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group email"
					data-regex="^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$">

					<label class="col-md-5" for="address-payment-email">
						<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="email"
							id="address-payment-email"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.email' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.email' ) ); ?>"
							placeholder="name@example.com"
						/>
					</div>

				</li>


				<li class="form-item form-group telephone">

					<label class="col-md-5" for="address-payment-telephone">
						<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="tel"
							id="address-payment-telephone"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.telephone' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.telephone' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group telefax">

					<label class="col-md-5" for="address-payment-telefax">
						<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="tel"
							id="address-payment-telefax"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.telefax' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.telefax' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group website"
					data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

					<label class="col-md-5" for="address-payment-website">
						<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="url"
							id="address-payment-website"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.website' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.website' ) ); ?>"
							placeholder="https://example.com"
						/>
					</div>
				</li>

				<li class="form-item form-group birthday">
					<label class="col-md-5" for="customer-payment-birthday">
						<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control birthday" type="date"
							id="customer-payment-birthday"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.birthday' ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.birthday' ) ); ?>"
						/>
					</div>
				</li>

			</ul>
		</div>
	</div>



	<?php foreach( $this->addressCustomer->getAddressItems() as $pos => $addrItem ) : $addr = $addrItem->toArray( true ); ?>
		<div class="panel panel-default">
			<div class="panel-heading"><?= $enc->html( ( $addrItem->getCompany() ? $addrItem->getCompany() . ' - ' : '' ) . $addrItem->getFirstName() . ' ' . $addrItem->getLastName() . ', ' . $addrItem->getAddress1() . ', ' . $addrItem->getCity() ) ?></div>
			<div class="panel-body">

				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.id', $pos ) ) ); ?>"
					value="<?= $enc->attr( $addrItem->getId() ); ?>" />

				<ul class="form-list">

					<li class="form-item form-group salutation">

						<label class="col-md-5" for="address-delivery-salutation-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-delivery-salutation-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.salutation', $pos ) ) ); ?>" >

								<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
									<option value="<?= $enc->attr( $salutation ); ?>" <?= $selectfcn( $addr, 'customer.address.salutation', $salutation ); ?> >
										<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ); ?>
									</option>
								<?php endforeach; ?>

							</select>
						</div>
					</li>


					<li class="form-item form-group firstname">

						<label class="col-md-5" for="address-delivery-firstname-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-firstname-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.firstname', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.firstname' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group lastname">

						<label class="col-md-5" for="address-delivery-lastname-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-lastname-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.lastname', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.lastname' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group company">

						<label class="col-md-5" for="address-delivery-company-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-company-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.company', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.company' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group address1">

						<label class="col-md-5" for="address-delivery-address1-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-address1-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address1', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.address1' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group address2">

						<label class="col-md-5" for="address-delivery-address2-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-address2-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address2', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.address2' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group address3">

						<label class="col-md-5" for="address-delivery-address3-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-address3-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address3', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.address3' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group city">

						<label class="col-md-5" for="address-delivery-city-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-city-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.city', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.city' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ); ?>"
							/>
						</div>

					</li>


					<?php if( count( $states ) > 0 ) : ?>
						<li class="form-item form-group state>

							<label class="col-md-5" for="address-delivery-state-<?= $pos ?>">
								<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ); ?>
							</label>
							<div class="col-md-7">
								<select class="form-control" id="address-delivery-state-<?= $pos ?>"
									name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.state', $pos ) ) ); ?>">

									<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ); ?></option>
									<?php foreach( $states as $regioncode => $stateList ) : ?>
										<optgroup class="<?= $regioncode; ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ); ?>">
											<?php foreach( $stateList as $stateCode => $stateName ) : ?>
												<option value="<?= $enc->attr( $stateCode ); ?>" <?= $selectfcn( $addr, 'customer.address.state', $stateCode ); ?> >
													<?= $enc->html( $stateName ); ?>
												</option>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>

								</select>
							</div>

						</li>
					<?php endif; ?>


					<li class="form-item form-group postal">

						<label class="col-md-5" for="address-delivery-postal-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-postal-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.postal', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.postal' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ); ?>"
							/>
						</div>

					</li>


					<?php if( count( $countries ) > 0 ) : ?>
						<li class="form-item form-group countryid">

							<label class="col-md-5" for="address-delivery-countryid-<?= $pos ?>">
								<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ); ?>
							</label>
							<div class="col-md-7">
								<select class="form-control" id="address-delivery-countryid-<?= $pos ?>"
									name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.countryid', $pos ) ) ); ?>">

									<?php if( count( $countries ) > 1 ) : ?>
										<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ); ?></option>
									<?php endif; ?>
									<?php foreach( $countries as $countryId ) : ?>
										<option value="<?= $enc->attr( $countryId ); ?>" <?= $selectfcn( $addr, 'customer.address.countryid', $countryId ); ?> >
											<?= $enc->html( $this->translate( 'country', $countryId ) ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

						</li>
					<?php endif; ?>


					<li class="form-item form-group languageid">

						<label class="col-md-5" for="address-delivery-languageid-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-delivery-languageid-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.languageid', $pos ) ) ); ?>">

								<?php foreach( $languages as $languageId ) : ?>
									<option value="<?= $enc->attr( $languageId ); ?>" <?= $selectfcn( $addr, 'customer.address.languageid', $languageId ); ?> >
										<?= $enc->html( $this->translate( 'language', $languageId ) ); ?>
									</option>
								<?php endforeach; ?>

							</select>
						</div>

					</li>


					<li class="form-item form-group vatid">

						<label class="col-md-5" for="address-delivery-vatid-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="text"
								id="address-delivery-vatid-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.vatid', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.vatid' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group email"
						data-regex="^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$">

						<label class="col-md-5" for="address-delivery-email-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="email"
								id="address-delivery-email-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.email', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.email' ) ); ?>"
								placeholder="name@example.com"
							/>
						</div>

					</li>


					<li class="form-item form-group telephone">

						<label class="col-md-5" for="address-delivery-telephone-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="tel"
								id="address-delivery-telephone-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telephone', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.telephone' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group telefax">

						<label class="col-md-5" for="address-delivery-telefax-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="tel"
								id="address-delivery-telefax-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telefax', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.telefax' ) ); ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
							/>
						</div>

					</li>


					<li class="form-item form-group website"
						data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

						<label class="col-md-5" for="address-delivery-website-<?= $pos ?>">
							<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<input class="form-control" type="url"
								id="address-delivery-website-<?= $pos ?>"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.website', $pos ) ) ); ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.website' ) ); ?>"
								placeholder="https://example.com"
							/>
						</div>
					</li>

				</ul>
		</div>
	</div>
	<?php endforeach ?>



	<div class="panel panel-default">
		<div class="panel-heading"><?= $enc->html( $this->translate( 'client', 'New delivery address' ) ) ?></div>
		<div class="panel-body">

			<input type="hidden" value=""
				name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.id', -1 ) ) ); ?>" />

			<ul class="form-list">

				<li class="form-item form-group salutation">

					<label class="col-md-5" for="address-delivery-salutation--1">
						<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<select class="form-control" id="address-delivery-salutation--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.salutation', $pos ) ) ); ?>" >

							<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
								<option value="<?= $enc->attr( $salutation ); ?>" <?= $selectfcn( $addr, 'customer.address.salutation', $salutation ); ?> >
									<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ); ?>
								</option>
							<?php endforeach; ?>

						</select>
					</div>
				</li>


				<li class="form-item form-group firstname">

					<label class="col-md-5" for="address-delivery-firstname--1">
						<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-firstname--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.firstname', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.firstname' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group lastname">

					<label class="col-md-5" for="address-delivery-lastname--1">
						<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-lastname--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.lastname', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.lastname' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group company">

					<label class="col-md-5" for="address-delivery-company--1">
						<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-company--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.company', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.company' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address1">

					<label class="col-md-5" for="address-delivery-address1--1">
						<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-address1--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address1', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.address1' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address2">

					<label class="col-md-5" for="address-delivery-address2--1">
						<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-address2--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address2', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.address2' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group address3">

					<label class="col-md-5" for="address-delivery-address3--1">
						<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-address3--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address3', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.address3' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group city">

					<label class="col-md-5" for="address-delivery-city--1">
						<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-city--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.city', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.city' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ); ?>"
						/>
					</div>

				</li>


				<?php if( count( $states ) > 0 ) : ?>
					<li class="form-item form-group state>

						<label class="col-md-5" for="address-delivery-state--1">
							<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-delivery-state--1"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.state', $pos ) ) ); ?>">

								<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ); ?></option>
								<?php foreach( $states as $regioncode => $stateList ) : ?>
									<optgroup class="<?= $regioncode; ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ); ?>">
										<?php foreach( $stateList as $stateCode => $stateName ) : ?>
											<option value="<?= $enc->attr( $stateCode ); ?>" <?= $selectfcn( $addr, 'customer.address.state', $stateCode ); ?> >
												<?= $enc->html( $stateName ); ?>
											</option>
										<?php endforeach; ?>
									</optgroup>
								<?php endforeach; ?>

							</select>
						</div>

					</li>
				<?php endif; ?>


				<li class="form-item form-group postal">

					<label class="col-md-5" for="address-delivery-postal--1">
						<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-postal--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.postal', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.postal' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ); ?>"
						/>
					</div>

				</li>


				<?php if( count( $countries ) > 0 ) : ?>
					<li class="form-item form-group countryid">

						<label class="col-md-5" for="address-delivery-countryid--1">
							<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ); ?>
						</label>
						<div class="col-md-7">
							<select class="form-control" id="address-delivery-countryid--1"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.countryid', $pos ) ) ); ?>">

								<?php if( count( $countries ) > 1 ) : ?>
									<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ); ?></option>
								<?php endif; ?>
								<?php foreach( $countries as $countryId ) : ?>
									<option value="<?= $enc->attr( $countryId ); ?>" <?= $selectfcn( $addr, 'customer.address.countryid', $countryId ); ?> >
										<?= $enc->html( $this->translate( 'country', $countryId ) ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

					</li>
				<?php endif; ?>


				<li class="form-item form-group languageid">

					<label class="col-md-5" for="address-delivery-languageid--1">
						<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<select class="form-control" id="address-delivery-languageid--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.languageid', $pos ) ) ); ?>">

							<?php foreach( $languages as $languageId ) : ?>
								<option value="<?= $enc->attr( $languageId ); ?>" <?= $selectfcn( $addr, 'customer.address.languageid', $languageId ); ?> >
									<?= $enc->html( $this->translate( 'language', $languageId ) ); ?>
								</option>
							<?php endforeach; ?>

						</select>
					</div>

				</li>


				<li class="form-item form-group vatid">

					<label class="col-md-5" for="address-delivery-vatid--1">
						<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="text"
							id="address-delivery-vatid--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.vatid', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.vatid' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group email"
					data-regex="^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$">

					<label class="col-md-5" for="address-delivery-email--1">
						<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="email"
							id="address-delivery-email--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.email', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.email' ) ); ?>"
							placeholder="name@example.com"
						/>
					</div>

				</li>


				<li class="form-item form-group telephone">

					<label class="col-md-5" for="address-delivery-telephone--1">
						<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="tel"
							id="address-delivery-telephone--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telephone', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.telephone' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group telefax">

					<label class="col-md-5" for="address-delivery-telefax--1">
						<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="tel"
							id="address-delivery-telefax--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telefax', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.telefax' ) ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ); ?>"
						/>
					</div>

				</li>


				<li class="form-item form-group website"
					data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

					<label class="col-md-5" for="address-delivery-website--1">
						<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ); ?>
					</label>
					<div class="col-md-7">
						<input class="form-control" type="url"
							id="address-delivery-website--1"
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.website', $pos ) ) ); ?>"
							value="<?= $enc->attr( $this->value( $addr, 'customer.address.website' ) ); ?>"
							placeholder="https://example.com"
						/>
					</div>
				</li>

			</ul>
		</div>
	</div>
</div>

<?php endif ?>
