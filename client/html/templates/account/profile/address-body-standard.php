<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

$enc = $this->encoder();

$selectfcn = function( $list, $key, $value ) {
	return ( isset( $list[$key] ) && $list[$key] == $value ? 'selected="selected"' : '' );
};

/** client/html/account/profile/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/config
 */

/** client/html/account/profile/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2019.10
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/url/config
 */

/** client/html/account/profile/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * For SEO, it's nice to have URLs which contains only required parameters.
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2019.10
 * @category User
 * @category Developer
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

$addr = $this->get( 'addressBilling', [] );
$pos = 0;


?>
<?php $this->block()->start( 'account/profile/address' ) ?>
<?php if( isset( $this->profileCustomerItem ) ) : ?>

<div class="account-profile-address">
	<h1 class="header"><?= $enc->html( $this->translate( 'client', 'address' ) ) ?></h1>

	<form class="container-fluid" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/profile/url' ) ) ?>">
		<?= $this->csrf()->formfield() ?>

		<div class="row">
			<div class="billing col-lg-6">
				<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Billing address' ) ) ?></h2>

				<div class="panel panel-default address-billing">
					<div class="panel-heading" role="button" data-toggle="collapse" href="#address-payment" aria-expanded="false" aria-controls="address-payment">
						<?= nl2br( $enc->html( $addr['string'] ?: $this->translate( 'client', 'Add billing address' ) ) ) ?>
						<span class="act-show"></span>
					</div>
					<div class="panel-body collapse" id="address-payment">

						<div class="form-list">

							<div class="form-item form-group row salutation">

								<label class="col-md-4" for="address-payment-salutation">
									<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<select class="form-control" id="address-payment-salutation"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.salutation' ) ) ) ?>"
									>

										<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
											<option value="<?= $enc->attr( $salutation ) ?>" <?= $selectfcn( $addr, 'customer.salutation', $salutation ) ?>>
												<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ) ?>
											</option>
										<?php endforeach ?>

									</select>
								</div>
							</div>


							<div class="form-item form-group row firstname">

								<label class="col-md-4" for="address-payment-firstname">
									<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-firstname"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.firstname' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.firstname' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row lastname">

								<label class="col-md-4" for="address-payment-lastname">
									<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-lastname"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.lastname' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.lastname' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row company">

								<label class="col-md-4" for="address-payment-company">
									<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-company"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.company' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.company' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address1">

								<label class="col-md-4" for="address-payment-address1">
									<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-address1"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address1' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.address1' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address2">

								<label class="col-md-4" for="address-payment-address2">
									<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-address2"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address2' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.address2' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address3">

								<label class="col-md-4" for="address-payment-address3">
									<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-address3"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.address3' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.address3' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row city">

								<label class="col-md-4" for="address-payment-city">
									<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-city"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.city' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.city' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ) ?>"
									>
								</div>

							</div>


							<?php if( !empty( $this->get( 'addressStates', [] ) ) ) : ?>
								<div class="form-item form-group row state">

									<label class="col-md-4" for="address-payment-state">
										<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-payment-state"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.state' ) ) ) ?>">

											<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ) ?></option>
											<?php foreach( $this->get( 'addressStates', [] ) as $regioncode => $stateList ) : ?>
												<optgroup class="<?= $regioncode ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ) ?>">
													<?php foreach( $stateList as $stateCode => $stateName ) : ?>
														<option value="<?= $enc->attr( $stateCode ) ?>" <?= $selectfcn( $addr, 'customer.state', $stateCode ) ?>>
															<?= $enc->html( $stateName ) ?>
														</option>
													<?php endforeach ?>
												</optgroup>
											<?php endforeach ?>

										</select>
									</div>

								</div>
							<?php endif ?>


							<div class="form-item form-group row postal">

								<label class="col-md-4" for="address-payment-postal">
									<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-postal"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.postal' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.postal' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ) ?>"
									>
								</div>

							</div>


							<?php if( !empty( $this->get( 'addressCountries', [] ) ) ) : ?>
								<div class="form-item form-group row countryid">

									<label class="col-md-4" for="address-payment-countryid">
										<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-payment-countryid"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.countryid' ) ) ) ?>">

											<?php if( count( $this->get( 'addressCountries', [] ) ) > 1 ) : ?>
												<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ) ?></option>
											<?php endif ?>
											<?php foreach( $this->get( 'addressCountries', [] ) as $countryId ) : ?>
												<option value="<?= $enc->attr( $countryId ) ?>" <?= $selectfcn( $addr, 'customer.countryid', $countryId ) ?>>
													<?= $enc->html( $this->translate( 'country', $countryId ) ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>

								</div>
							<?php endif ?>


							<div class="form-item form-group row languageid">

								<label class="col-md-4" for="address-payment-languageid">
									<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<select class="form-control" id="address-payment-languageid"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.languageid' ) ) ) ?>">

										<?php if( count( $this->get( 'addressLanguages', [] ) ) > 1 ) : ?>
											<option value=""><?= $enc->html( $this->translate( 'client', 'Select language' ), $enc::TRUST ) ?></option>
										<?php endif ?>

										<?php foreach( $this->get( 'addressLanguages', [] ) as $languageId ) : ?>
											<option value="<?= $enc->attr( $languageId ) ?>" <?= $selectfcn( $addr, 'customer.languageid', $languageId ) ?>>
												<?= $enc->html( $this->translate( 'language', $languageId ) ) ?>
											</option>
										<?php endforeach ?>

									</select>
								</div>

							</div>


							<div class="form-item form-group row vatid">

								<label class="col-md-4" for="address-payment-vatid">
									<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text"
										id="address-payment-vatid"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.vatid' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.vatid' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row email">

								<label class="col-md-4" for="address-payment-email">
									<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="email"
										id="address-payment-email"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.email' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.email' ) ) ?>"
										placeholder="name@example.com"
									>
								</div>

							</div>


							<div class="form-item form-group row telephone">

								<label class="col-md-4" for="address-payment-telephone">
									<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="tel"
										id="address-payment-telephone"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.telephone' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.telephone' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row telefax">

								<label class="col-md-4" for="address-payment-telefax">
									<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="tel"
										id="address-payment-telefax"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.telefax' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.telefax' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row website"
								data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

								<label class="col-md-4" for="address-payment-website">
									<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="url"
										id="address-payment-website"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.website' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.website' ) ) ?>"
										placeholder="https://example.com"
									>
								</div>
							</div>

							<div class="form-item form-group row birthday">
								<label class="col-md-4" for="address-payment-birthday">
									<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control birthday" type="date"
										id="address-payment-birthday"
										name="<?= $enc->attr( $this->formparam( array( 'address', 'payment', 'customer.birthday' ) ) ) ?>"
										value="<?= $enc->attr( $this->value( $addr, 'customer.birthday' ) ) ?>"
									>
								</div>
							</div>

						</div>

						<div class="button-group">
							<button class="btn btn-primary btn-save" value="1" name="<?= $enc->attr( $this->formparam( array( 'address', 'save' ) ) ) ?>">
								<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
							</button>
						</div>

					</div>
				</div>
			</div>


			<div class="delivery col-lg-6">
				<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Delivery address' ) ) ?></h2>

				<?php foreach( $this->addressDelivery as $pos => $addr ) : ?>
					<div class="panel panel-default address-delivery">
						<div class="panel-heading" data-toggle="collapse" href="#address-delivery-<?= $enc->attr( $pos ) ?>" aria-expanded="false" aria-controls="address-delivery-<?= $enc->attr( $pos ) ?>">
							<?= nl2br( $enc->html( $addr['string'] ?? '' ) ) ?><span class="act-show"></span>
						</div>
						<div class="panel-body collapse" id="address-delivery-<?= $enc->attr( $pos ) ?>">

							<input type="hidden"
								name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.id', $pos ) ) ) ?>"
								value="<?= $enc->attr( $this->value( $addr, 'customer.address.id' ) ) ?>"
							>

							<div class="form-list">

								<div class="form-item form-group row salutation">

									<label class="col-md-4" for="address-delivery-salutation-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-delivery-salutation-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.salutation', $pos ) ) ) ?>"
										>

											<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
												<option value="<?= $enc->attr( $salutation ) ?>" <?= $selectfcn( $addr, 'customer.address.salutation', $salutation ) ?>>
													<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ) ?>
												</option>
											<?php endforeach ?>

										</select>
									</div>
								</div>


								<div class="form-item form-group row firstname">

									<label class="col-md-4" for="address-delivery-firstname-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-firstname-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.firstname', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.firstname' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row lastname">

									<label class="col-md-4" for="address-delivery-lastname-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-lastname-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.lastname', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.lastname' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row company">

									<label class="col-md-4" for="address-delivery-company-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-company-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.company', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.company' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row address1">

									<label class="col-md-4" for="address-delivery-address1-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-address1-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address1', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.address1' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row address2">

									<label class="col-md-4" for="address-delivery-address2-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-address2-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address2', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.address2' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row address3">

									<label class="col-md-4" for="address-delivery-address3-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-address3-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address3', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.address3' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row city">

									<label class="col-md-4" for="address-delivery-city-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-city-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.city', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.city' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ) ?>"
										>
									</div>

								</div>


								<?php if( !empty( $this->get( 'addressStates', [] ) ) ) : ?>
									<div class="form-item form-group row state">

										<label class="col-md-4" for="address-delivery-state-<?= $pos ?>">
											<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ) ?>
										</label>
										<div class="col-md-8">
											<select class="form-control" id="address-delivery-state-<?= $pos ?>"
												name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.state', $pos ) ) ) ?>">

												<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ) ?></option>
												<?php foreach( $this->get( 'addressStates', [] ) as $regioncode => $stateList ) : ?>
													<optgroup class="<?= $regioncode ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ) ?>">
														<?php foreach( $stateList as $stateCode => $stateName ) : ?>
															<option value="<?= $enc->attr( $stateCode ) ?>" <?= $selectfcn( $addr, 'customer.address.state', $stateCode ) ?>>
																<?= $enc->html( $stateName ) ?>
															</option>
														<?php endforeach ?>
													</optgroup>
												<?php endforeach ?>

											</select>
										</div>

									</div>
								<?php endif ?>


								<div class="form-item form-group row postal">

									<label class="col-md-4" for="address-delivery-postal-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-postal-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.postal', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.postal' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ) ?>"
										>
									</div>

								</div>


								<?php if( !empty( $this->get( 'addressCountries', [] ) ) ) : ?>
									<div class="form-item form-group row countryid">

										<label class="col-md-4" for="address-delivery-countryid-<?= $pos ?>">
											<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ) ?>
										</label>
										<div class="col-md-8">
											<select class="form-control" id="address-delivery-countryid-<?= $pos ?>"
												name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.countryid', $pos ) ) ) ?>">

												<?php if( count( $this->get( 'addressCountries', [] ) ) > 1 ) : ?>
													<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ) ?></option>
												<?php endif ?>
												<?php foreach( $this->get( 'addressCountries', [] ) as $countryId ) : ?>
													<option value="<?= $enc->attr( $countryId ) ?>" <?= $selectfcn( $addr, 'customer.address.countryid', $countryId ) ?>>
														<?= $enc->html( $this->translate( 'country', $countryId ) ) ?>
													</option>
												<?php endforeach ?>
											</select>
										</div>

									</div>
								<?php endif ?>


								<div class="form-item form-group row languageid">

									<label class="col-md-4" for="address-delivery-languageid-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-delivery-languageid-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.languageid', $pos ) ) ) ?>">

											<?php if( count( $this->get( 'addressLanguages', [] ) ) > 1 ) : ?>
												<option value=""><?= $enc->html( $this->translate( 'client', 'Select language' ), $enc::TRUST ) ?></option>
											<?php endif ?>

											<?php foreach( $this->get( 'addressLanguages', [] ) as $languageId ) : ?>
												<option value="<?= $enc->attr( $languageId ) ?>" <?= $selectfcn( $addr, 'customer.address.languageid', $languageId ) ?>>
													<?= $enc->html( $this->translate( 'language', $languageId ) ) ?>
												</option>
											<?php endforeach ?>

										</select>
									</div>

								</div>


								<div class="form-item form-group row vatid">

									<label class="col-md-4" for="address-delivery-vatid-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="text"
											id="address-delivery-vatid-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.vatid', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.vatid' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row email">

									<label class="col-md-4" for="address-delivery-email-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="email"
											id="address-delivery-email-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.email', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.email' ) ) ?>"
											placeholder="name@example.com"
										>
									</div>

								</div>


								<div class="form-item form-group row telephone">

									<label class="col-md-4" for="address-delivery-telephone-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="tel"
											id="address-delivery-telephone-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telephone', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.telephone' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row telefax">

									<label class="col-md-4" for="address-delivery-telefax-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="tel"
											id="address-delivery-telefax-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telefax', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.telefax' ) ) ?>"
											placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
										>
									</div>

								</div>


								<div class="form-item form-group row website"
									data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

									<label class="col-md-4" for="address-delivery-website-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control" type="url"
											id="address-delivery-website-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.website', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.website' ) ) ?>"
											placeholder="https://example.com"
										>
									</div>
								</div>

								<div class="form-item form-group row birthday">
									<label class="col-md-4" for="address-delivery-birthday-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<input class="form-control birthday" type="date"
											id="address-delivery-birthday-<?= $pos ?>"
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.birthday', $pos ) ) ) ?>"
											value="<?= $enc->attr( $this->value( $addr, 'customer.address.birthday' ) ) ?>"
										>
									</div>
								</div>

							</div>

							<div class="button-group">
								<button class="btn btn-delete" value="<?= $pos ?>" name="<?= $enc->attr( $this->formparam( array( 'address', 'delete' ) ) ) ?>">
									<?= $enc->html( $this->translate( 'client', 'Delete' ), $enc::TRUST ) ?>
								</button>
								<button class="btn btn-primary btn-save" value="1" name="<?= $enc->attr( $this->formparam( array( 'address', 'save' ) ) ) ?>">
									<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
								</button>
							</div>

						</div>
					</div>
				<?php endforeach ?>


				<?php $pos++ ?>
				<div class="panel panel-default address-delivery-new">
					<div class="panel-heading" data-toggle="collapse" href="#address-delivery-<?= $enc->attr( $pos ) ?>" aria-expanded="false" aria-controls="address-delivery-<?= $enc->attr( $pos ) ?>">
						<?= $enc->html( $this->translate( 'client', 'New delivery address' ) ) ?><span class="act-show"></span>
					</div>
					<div class="panel-body collapse" id="address-delivery-<?= $enc->attr( $pos ) ?>">

						<input type="hidden" value="" disabled
							name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.id', $pos ) ) ) ?>"
						>

						<div class="form-list">

							<div class="form-item form-group row salutation">

								<label class="col-md-4" for="address-delivery-salutation-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Salutation' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<select class="form-control" id="address-delivery-salutation-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.salutation', $pos ) ) ) ?>"
									>

										<?php foreach( $this->get( 'addressSalutations', [] ) as $salutation ) : ?>
											<option value="<?= $enc->attr( $salutation ) ?>"
												<?= $this->param( 'address/delivery/customer.address.salutation/' . $pos ) === $salutation ? 'selected="selected"' : '' ?>>
												<?= $enc->html( $this->translate( 'mshop/code', $salutation ) ) ?>
											</option>
										<?php endforeach ?>

									</select>
								</div>
							</div>


							<div class="form-item form-group row firstname">

								<label class="col-md-4" for="address-delivery-firstname-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'First name' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-firstname-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.firstname', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.firstname/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'First name' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row lastname">

								<label class="col-md-4" for="address-delivery-lastname-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Last name' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-lastname-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.lastname', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.lastname/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Last name' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row company">

								<label class="col-md-4" for="address-delivery-company-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Company' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-company-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.company', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.company/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Company' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address1">

								<label class="col-md-4" for="address-delivery-address1-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Street' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-address1-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address1', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.address1/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Street' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address2">

								<label class="col-md-4" for="address-delivery-address2-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Additional' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-address2-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address2', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.address2/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row address3">

								<label class="col-md-4" for="address-delivery-address3-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Additional 2' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-address3-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.address3', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.address3/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Additional 2' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row city">

								<label class="col-md-4" for="address-delivery-city-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'City' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-city-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.city', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.city/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'City' ) ) ?>"
									>
								</div>

							</div>


							<?php if( !empty( $this->get( 'addressStates', [] ) ) ) : ?>
								<div class="form-item form-group row state">

									<label class="col-md-4" for="address-delivery-state-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'State' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-delivery-state-<?= $pos ?>" disabled
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.state', $pos ) ) ) ?>">

											<option value=""><?= $enc->html( $this->translate( 'client', 'Select state' ), $enc::TRUST ) ?></option>
											<?php foreach( $this->get( 'addressStates', [] ) as $regioncode => $stateList ) : ?>
												<optgroup class="<?= $regioncode ?>" label="<?= $enc->attr( $this->translate( 'country', $regioncode ) ) ?>">
													<?php foreach( $stateList as $stateCode => $stateName ) : ?>
														<option value="<?= $enc->attr( $stateCode ) ?>"
															<?= $this->param( 'address/delivery/customer.address.state/' . $pos ) === $stateCode ? 'selected="selected"' : '' ?>>
															<?= $enc->html( $stateName ) ?>
														</option>
													<?php endforeach ?>
												</optgroup>
											<?php endforeach ?>

										</select>
									</div>

								</div>
							<?php endif ?>


							<div class="form-item form-group row postal">

								<label class="col-md-4" for="address-delivery-postal-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Postal code' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-postal-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.postal', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.postal/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'Postal code' ) ) ?>"
									>
								</div>

							</div>


							<?php if( !empty( $this->get( 'addressCountries', [] ) ) ) : ?>
								<div class="form-item form-group row countryid">

									<label class="col-md-4" for="address-delivery-countryid-<?= $pos ?>">
										<?= $enc->html( $this->translate( 'client', 'Country' ), $enc::TRUST ) ?>
									</label>
									<div class="col-md-8">
										<select class="form-control" id="address-delivery-countryid-<?= $pos ?>" disabled
											name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.countryid', $pos ) ) ) ?>">

											<?php if( count( $this->get( 'addressCountries', [] ) ) > 1 ) : ?>
												<option value=""><?= $enc->html( $this->translate( 'client', 'Select country' ), $enc::TRUST ) ?></option>
											<?php endif ?>
											<?php foreach( $this->get( 'addressCountries', [] ) as $countryId ) : ?>
												<option value="<?= $enc->attr( $countryId ) ?>"
													<?= $this->param( 'address/delivery/customer.address.countryid/' . $pos ) === $countryId ? 'selected="selected"' : '' ?>>
													<?= $enc->html( $this->translate( 'country', $countryId ) ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>

								</div>
							<?php endif ?>


							<div class="form-item form-group row languageid">

								<label class="col-md-4" for="address-delivery-languageid-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Language' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<select class="form-control" id="address-delivery-languageid-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.languageid', $pos ) ) ) ?>">

										<?php if( count( $this->get( 'addressLanguages', [] ) ) > 1 ) : ?>
											<option value=""><?= $enc->html( $this->translate( 'client', 'Select language' ), $enc::TRUST ) ?></option>
										<?php endif ?>

										<?php foreach( $this->get( 'addressLanguages', [] ) as $languageId ) : ?>
											<option value="<?= $enc->attr( $languageId ) ?>"
												<?= $this->param( 'address/delivery/customer.address.languageid/' . $pos, $this->get( 'contextLanguage' ) ) === $languageId ? 'selected="selected"' : '' ?>>
												<?= $enc->html( $this->translate( 'language', $languageId ) ) ?>
											</option>
										<?php endforeach ?>

									</select>
								</div>

							</div>


							<div class="form-item form-group row vatid">

								<label class="col-md-4" for="address-delivery-vatid-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Vat ID' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="text" id="address-delivery-vatid-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.vatid', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.vatid/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', 'GB999999973' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row email"
								data-regex="^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$">

								<label class="col-md-4" for="address-delivery-email-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'E-Mail' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="email" id="address-delivery-email-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.email', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.email/' . $pos ) ) ?>"
										placeholder="name@example.com"
									>
								</div>

							</div>


							<div class="form-item form-group row telephone">

								<label class="col-md-4" for="address-delivery-telephone-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Telephone' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="tel" id="address-delivery-telephone-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telephone', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.telephone/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row telefax">

								<label class="col-md-4" for="address-delivery-telefax-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Fax' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="tel" id="address-delivery-telefax-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.telefax', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.telefax/' . $pos ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'client', '+1 123 456 7890' ) ) ?>"
									>
								</div>

							</div>


							<div class="form-item form-group row website"
								data-regex="^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$">

								<label class="col-md-4" for="address-delivery-website-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Web site' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="url" id="address-delivery-website-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.website', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.website/' . $pos ) ) ?>"
										placeholder="https://example.com"
									>
								</div>
							</div>

							<div class="form-item form-group row birthday">
								<label class="col-md-4" for="address-delivery-birthday-<?= $pos ?>">
									<?= $enc->html( $this->translate( 'client', 'Birthday' ), $enc::TRUST ) ?>
								</label>
								<div class="col-md-8">
									<input class="form-control" type="date" id="address-delivery-birthday-<?= $pos ?>" disabled
										name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', 'customer.address.birthday', $pos ) ) ) ?>"
										value="<?= $enc->attr( $this->param( 'address/delivery/customer.address.birthday/' . $pos ) ) ?>"
									>
								</div>
							</div>

						</div>

						<div class="button-group">
							<button class="btn btn-cancel" value="1" type="reset">
								<?= $enc->html( $this->translate( 'client', 'Cancel' ), $enc::TRUST ) ?>
							</button>
							<button class="btn btn-primary btn-save" value="1" name="<?= $enc->attr( $this->formparam( array( 'address', 'save' ) ) ) ?>">
								<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
							</button>
						</div>

					</div>
				</div>

			</div>
		</div>
	</form>
</div>

<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'account/profile/address' ) ?>
