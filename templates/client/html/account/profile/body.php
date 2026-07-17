<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 */

$enc = $this->encoder();

/** client/html/account/profile/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2019.10
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
 * @see client/html/account/profile/url/target
 * @see client/html/account/profile/url/controller
 * @see client/html/account/profile/url/action
 * @see client/html/account/profile/url/config
 */

$addr = $this->get( 'addressPayment', [] );
$pos = 0;
$url = $this->link( 'client/html/account/profile/url' );


?>
<?php if( isset( $this->profileItem ) ) : ?>

<div class="section aimeos account-profile" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
	<div class="container-xxl">
		<div class="account-profile-address">

			<h2 class="header"><?= $enc->html( $this->translate( 'client', 'address' ) ) ?></h2>

			<div class="profile-address-forms">
				<div class="row">
					<div class="payment col-md-6">
						<h3 class="header"><?= $enc->html( $this->translate( 'client', 'Billing address' ) ) ?></h3>

						<div id="address-payment-list" class="accordion">

							<div class="accordion-item address-item address-payment">
								<div class="header" role="button"
									data-bs-toggle="collapse" data-bs-target="#address-payment"
									aria-controls="address-payment" aria-expanded="false">

									<?= nl2br( $enc->html( $addr['string'] ?: $this->translate( 'client', 'Add billing address' ) ) ) ?>
									<a class="act-show" href="#" title="<?= $enc->attr( $this->translate( 'client', 'Change billing address' ), $enc::TRUST ) ?>"></a>
								</div>
								<div class="address accordion-collapse collapse" id="address-payment" data-bs-parent="#address-payment-list">

									<form class="address-save address-payment-save" method="POST" action="<?= $enc->attr( $url ) ?>"
										toolname="update_billing_address"
										tooldescription="<?= $enc->attr( $this->translate( 'client', 'Updates the billing address for the current customer account.' ) ) ?>">
										<?= $this->csrf()->formfield() ?>
										<input type="hidden" name="<?= $enc->attr( $this->formparam( ['address', 'save'] ) ) ?>" value="1">

										<div class="form-list">

											<?= $this->partial(
												/** client/html/account/profile/address
												 * Relative path to the address partial template file
												 *
												 * Partials are templates which are reused in other templates and generate
												 * reoccuring blocks filled with data from the assigned values. The address
												 * partial creates an HTML block with input fields for address forms.
												 *
												 * @param string Relative path to the template file
												 * @since 2024.04
												 */
												$this->config( 'client/html/account/profile/address', 'common/partials/address' ),
												[
													'address' => $addr,
													'id' => $addr['customer.id'] ?? null,
													'countries' => $this->get( 'addressCountries', [] ),
													'css' => $this->get( 'addressPaymentCss', [] ),
													'disabled' => false,
													'error' => $this->get( 'addressPaymentError', [] ),
													'formnames' => ['address', 'payment'],
													'languages' => $this->get( 'addressLanguages', [] ),
													'languageid' => $this->get( 'contextLanguage' ),
													'salutations' => $this->get( 'addressSalutations', [] ),
													'states' => $this->get( 'addressStates', [] ),
													'prefix' => 'customer.',
													'type' => 'payment',
												]
											) ?>

											<div class="button-group">
												<button class="btn btn-primary btn-save" type="submit">
													<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
												</button>
											</div>

										</div>
									</form>
								</div>
							</div>
						</div>
					</div>


					<div class="delivery col-md-6">
						<h3 class="header"><?= $enc->html( $this->translate( 'client', 'Delivery address' ) ) ?></h3>

						<div id="address-delivery-list" class="accordion">

							<?php foreach( $this->addressDelivery as $pos => $addr ) : ?>
								<div class="accordion-item address-item address-delivery">
									<div class="header" role="button"
										data-bs-toggle="collapse" data-bs-target="#address-delivery-<?= $enc->attr( $pos ) ?>"
										aria-controls="address-delivery-<?= $enc->attr( $pos ) ?>" aria-expanded="false">

										<?= nl2br( $enc->html( $addr['string'] ?? '' ) ) ?>
										<a class="act-show" href="#" title="<?= $enc->attr( $this->translate( 'client', 'Change delivery address' ), $enc::TRUST ) ?>"></a>
									</div>
									<div class="address accordion-collapse collapse" id="address-delivery-<?= $enc->attr( $pos ) ?>" data-bs-parent="#address-delivery-list">

										<form id="profile-address-delivery-save-<?= $enc->attr( $pos ) ?>" class="address-save address-delivery-save" method="POST" action="<?= $enc->attr( $url ) ?>"
											toolname="update_delivery_address_<?= $enc->attr( $pos ) ?>"
											tooldescription="<?= $enc->attr( sprintf( $this->translate( 'client', 'Updates the delivery address "%1$s" for the current customer account.' ), $addr['string'] ?? '' ) ) ?>">
											<?= $this->csrf()->formfield() ?>
											<input type="hidden" name="<?= $enc->attr( $this->formparam( ['address', 'save'] ) ) ?>" value="1">

											<div class="form-list">
												<input type="hidden" disabled
													name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', $pos, 'customer.address.id' ) ) ) ?>"
													value="<?= $enc->attr( $this->value( $addr, 'customer.address.id' ) ) ?>"
												>

												<?= $this->partial(
													$this->config( 'client/html/account/profile/address', 'common/partials/address' ),
													[
														'address' => $addr,
														'id' => $addr['customer.address.id'] ?? null,
														'countries' => $this->get( 'addressCountries', [] ),
														'css' => $this->get( 'addressDeliveryCss', [] ),
														'disabled' => false,
														'error' => $this->get( 'addressDeliveryError', [] ),
														'formnames' => ['address', 'delivery', $pos],
														'languages' => $this->get( 'addressLanguages', [] ),
														'languageid' => $this->get( 'contextLanguage' ),
														'salutations' => $this->get( 'addressSalutations', [] ),
														'states' => $this->get( 'addressStates', [] ),
														'prefix' => 'customer.address.',
														'type' => 'delivery',
													]
												) ?>

											</div>
										</form>

										<form id="profile-address-delivery-delete-<?= $enc->attr( $pos ) ?>" class="address-delete address-delivery-delete" method="POST" action="<?= $enc->attr( $url ) ?>"
											toolname="delete_delivery_address_<?= $enc->attr( $pos ) ?>"
											tooldescription="<?= $enc->attr( sprintf( $this->translate( 'client', 'Deletes the delivery address "%1$s" from the current customer account.' ), $addr['string'] ?? '' ) ) ?>">
											<?= $this->csrf()->formfield() ?>
											<input type="hidden" name="<?= $enc->attr( $this->formparam( ['address', 'delete'] ) ) ?>" value="<?= $enc->attr( $pos ) ?>">
										</form>

										<div class="button-group">
											<button class="btn btn-delete" type="submit" form="profile-address-delivery-delete-<?= $enc->attr( $pos ) ?>">
												<?= $enc->html( $this->translate( 'client', 'Delete' ), $enc::TRUST ) ?>
											</button>
											<button class="btn btn-primary btn-save" type="submit" form="profile-address-delivery-save-<?= $enc->attr( $pos ) ?>">
												<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
											</button>
										</div>
									</div>
								</div>
							<?php endforeach ?>


							<?php $pos++ ?>
							<div class="accordion-item address-item address-delivery-new">
								<div class="header" role="button"
									data-bs-toggle="collapse" data-bs-target="#address-delivery-<?= $enc->attr( $pos ) ?>"
									aria-controls="address-delivery-<?= $enc->attr( $pos ) ?>" aria-expanded="false">

									<?= $enc->html( $this->translate( 'client', 'New delivery address' ) ) ?>
									<a class="act-show" href="#" title="<?= $enc->attr( $this->translate( 'client', 'Add delivery address' ), $enc::TRUST ) ?>"></a>
								</div>
								<div class="address accordion-collapse collapse" id="address-delivery-<?= $enc->attr( $pos ) ?>" data-bs-parent="#address-delivery-list">

									<form class="address-save address-delivery-save address-delivery-new-save" method="POST" action="<?= $enc->attr( $url ) ?>"
										toolname="add_delivery_address"
										tooldescription="<?= $enc->attr( $this->translate( 'client', 'Adds a new delivery address to the current customer account.' ) ) ?>">
										<?= $this->csrf()->formfield() ?>
										<input type="hidden" name="<?= $enc->attr( $this->formparam( ['address', 'save'] ) ) ?>" value="1">

										<div class="form-list">
											<input type="hidden" value="" disabled
												name="<?= $enc->attr( $this->formparam( array( 'address', 'delivery', $pos, 'customer.address.id' ) ) ) ?>"
											>

											<?= $this->partial(
												$this->config( 'client/html/account/profile/address', 'common/partials/address' ),
												[
													'id' => null,
													'address' => [],
													'countries' => $this->get( 'addressCountries', [] ),
													'css' => $this->get( 'addressDeliveryCss', [] ),
													'disabled' => false,
													'error' => $this->get( 'addressDeliveryError', [] ),
													'formnames' => ['address', 'delivery', $pos],
													'languages' => $this->get( 'addressLanguages', [] ),
													'languageid' => $this->get( 'contextLanguage' ),
													'salutations' => $this->get( 'addressSalutations', [] ),
													'states' => $this->get( 'addressStates', [] ),
													'prefix' => 'customer.address.',
													'type' => 'delivery',
												]
											) ?>

											<div class="button-group">
												<button class="btn btn-cancel" type="reset" data-bs-toggle="collapse" href="#address-delivery-<?= $enc->attr( $pos ) ?>">
													<?= $enc->html( $this->translate( 'client', 'Cancel' ), $enc::TRUST ) ?>
												</button>
												<button class="btn btn-primary btn-save" type="submit">
													<?= $enc->html( $this->translate( 'client', 'Save' ), $enc::TRUST ) ?>
												</button>
											</div>

										</div>
									</form>
								</div>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php endif ?>
