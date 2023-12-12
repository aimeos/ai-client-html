<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


/** client/html/account/basket/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2022.10
 * @see client/html/account/basket/url/controller
 * @see client/html/account/basket/url/action
 * @see client/html/account/basket/url/config
 * @see client/html/account/basket/url/filter
 */

/** client/html/account/basket/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2022.10
 * @see client/html/account/basket/url/target
 * @see client/html/account/basket/url/action
 * @see client/html/account/basket/url/config
 * @see client/html/account/basket/url/filter
 */

/** client/html/account/basket/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2022.10
 * @see client/html/account/basket/url/target
 * @see client/html/account/basket/url/controller
 * @see client/html/account/basket/url/config
 * @see client/html/account/basket/url/filter
 */

/** client/html/account/basket/url/config
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
 * @since 2022.10
 * @see client/html/account/basket/url/target
 * @see client/html/account/basket/url/controller
 * @see client/html/account/basket/url/action
 * @see client/html/account/basket/url/filter
 */

/** client/html/account/basket/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/account/basket/url/target
 * @see client/html/account/basket/url/controller
 * @see client/html/account/basket/url/action
 * @see client/html/account/basket/url/config
 */


/** client/html/account/basket/summary/address
 * Relative path to the HTML template of the account basket address partial.
 *
 * The template file contains the HTML code and processing instructions
 * to generate the result shown in the body of the frontend. The
 * configuration string is the path to the template file relative
 * to the templates directory (usually in templates/client/html).
 *
 * You can overwrite the template file configuration in extensions and
 * provide alternative templates. These alternative templates should be
 * named like the default one but suffixed by
 * an unique name. You may use the name of your project for this. If
 * you've implemented an alternative client class as well, it
 * should be suffixed by the name of the new class.
 *
 * @param string Relative path to the template creating the HTML fragment
 * @since 2022.10
 */

/** client/html/account/basket/summary/service
 * Relative path to the HTML template of the account basket service partial.
 *
 * The template file contains the HTML code and processing instructions
 * to generate the result shown in the body of the frontend. The
 * configuration string is the path to the template file relative
 * to the templates directory (usually in templates/client/html).
 *
 * You can overwrite the template file configuration in extensions and
 * provide alternative templates. These alternative templates should be
 * named like the default one but suffixed by
 * an unique name. You may use the name of your project for this. If
 * you've implemented an alternative client class as well, it
 * should be suffixed by the name of the new class.
 *
 * @param string Relative path to the template creating the HTML fragment
 * @since 2022.10
 */

/** client/html/account/basket/summary/detail
 * Relative path to the HTML template of the account basket details partial.
 *
 * The template file contains the HTML code and processing instructions
 * to generate the result shown in the body of the frontend. The
 * configuration string is the path to the template file relative
 * to the templates directory (usually in templates/client/html).
 *
 * You can overwrite the template file configuration in extensions and
 * provide alternative templates. These alternative templates should be
 * named like the default one but suffixed by
 * an unique name. You may use the name of your project for this. If
 * you've implemented an alternative client class as well, it
 * should be suffixed by the name of the new class.
 *
 * @param string Relative path to the template creating the HTML fragment
 * @since 2022.10
 */

$enc = $this->encoder();


?>
<?php if( !$this->get( 'basketItems', map() )->isEmpty() ) : ?>

	<div class="section aimeos account-basket" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Saved baskets' ), $enc::TRUST ) ?></h2>

			<div class="basket-list">

				<?php foreach( $this->get( 'basketItems', [] ) as $basketItem ) : ?>
					<?php if( $basket = $basketItem->getItem() ) : ?>

						<div class="basket-item row">

							<div class="col-12 col-md-5">
								<h2 class="basket-basic">
									<span class="name">
										<?= $enc->html( $this->translate( 'client', 'Name' ), $enc::TRUST ) ?>
									</span>
									<span class="value">
										<?= $enc->html( $basketItem->getName() ) ?>
									</span>
								</h2>
							</div>

							<div class="col-6 col-md-5">
								<a class="btn btn-primary delete" href="<?= $enc->attr( $this->link( 'client/html/account/basket/url', ['bas_action' => 'delete', 'bas_id' => $basketItem->getId()] ) ) ?>">
									<?= $enc->html( $this->translate( 'client', 'Delete' ) ) ?>
								</a>
							</div>

							<div class="action col-6 col-md-2">
								<a class="btn btn-secondary show" href="#"><?= $enc->html( $this->translate( 'client', 'Show' ) ) ?></a>
								<a class="btn btn-secondary close hidden" href="#"><?= $enc->html( $this->translate( 'client', 'Close' ) ) ?></a>
							</div>

							<div class="account-basket-detail common-summary col-12" style="display: none">

								<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Basket details' ), $enc::TRUST ) ?></h2>

								<div class="common-summary-address row">
									<div class="item payment col-sm-6">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?php if( !empty( $basket->getAddress( 'payment' ) ) ) : ?>
												<?= $this->partial(
													$this->config( 'client/html/account/basket/summary/address', 'common/summary/address' ),
													['addresses' => $basket->getAddress( 'payment' )]
												) ?>
											<?php endif ?>
										</div>
									</div><!--

									--><div class="item delivery col-sm-6">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?php if( !empty( $basket->getAddress( 'delivery' ) ) ) : ?>
												<?= $this->partial(
													$this->config( 'client/html/account/basket/summary/address', 'common/summary/address' ),
													['addresses' => $basket->getAddress( 'delivery' )]
												) ?>
											<?php else : ?>
												<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
											<?php endif ?>
										</div>
									</div>
								</div>


								<div class="common-summary-service row">
									<div class="item delivery col-sm-6">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?php if( !empty( $basket->getService( 'delivery' ) ) ) : ?>
												<?= $this->partial(
													$this->config( 'client/html/account/basket/summary/service', 'common/summary/service' ),
													array( 'service' => $basket->getService( 'delivery' ), 'type' => 'delivery' )
												) ?>
											<?php endif ?>
										</div>
									</div><!--

									--><div class="item payment col-sm-6">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?php if( !empty( $basket->getService( 'payment' ) ) ) : ?>
												<?= $this->partial(
													$this->config( 'client/html/account/basket/summary/service', 'common/summary/service' ),
													array( 'service' => $basket->getService( 'payment' ), 'type' => 'payment' )
												) ?>
											<?php endif ?>
										</div>
									</div>

								</div>


								<div class="common-summary-additional row">
									<div class="item coupon col-sm-4">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?php if( !( $coupons = $basket->getCoupons() )->isEmpty() ) : ?>
												<ul class="attr-list">
													<?php foreach( $coupons as $code => $products ) : ?>
														<li class="attr-item"><?= $enc->html( $code ) ?></li>
													<?php endforeach ?>
												</ul>
											<?php endif ?>
										</div>
									</div><!--

									--><div class="item customerref col-sm-4">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'Your reference number' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?= $enc->html( $basket->getCustomerReference() ) ?>
										</div>
									</div><!--

									--><div class="item comment col-sm-4">
										<div class="header">
											<h3><?= $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ) ?></h3>
										</div>

										<div class="content">
											<?= $enc->html( $basket->getComment() ) ?>
										</div>
									</div>
								</div>


								<div class="common-summary-detail row">
									<div class="header col-sm-12">
										<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
									</div>

									<div class="basket col-sm-12">
										<?= $this->partial(
											$this->config( 'client/html/account/basket/summary/detail', 'common/summary/detail' ),
											['summaryBasket' => $basket]
										) ?>
									</div>
								</div>


								<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
									<?= $this->csrf()->formfield() ?>

									<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">

									<?php foreach( $basket->getProducts() as $pos => $orderProduct ) : ?>
										<input type="hidden" name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'prodid'] ) ) ?>" value="<?= $enc->attr( $orderProduct->getParentProductId() ?: $orderProduct->getProductId() ) ?>">
										<input type="hidden" name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'quantity'] ) ) ?>" value="<?= $enc->attr( $orderProduct->getQuantity() ) ?>">

										<?php foreach( $orderProduct->getAttributeItems( 'variant' ) as $attrItem ) : ?>
											<input type="hidden" value="<?= $enc->attr( $attrItem->getAttributeId() ) ?>"
												name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrvarid', $attrItem->getCode()] ) ) ?>"
											>
										<?php endforeach ?>

										<?php foreach( $orderProduct->getAttributeItems( 'custom' ) as $attrItem ) : ?>
											<input type="hidden" value="<?= $enc->attr( $attrItem->getValue() ) ?>"
												name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrcustid', $attrItem->getAttributeId()] ) ) ?>"
											>
										<?php endforeach ?>

										<?php foreach( $orderProduct->getAttributeItems( 'config' ) as $attrItem ) : ?>
											<input type="hidden" value="<?= $enc->attr( $attrItem->getAttributeId() ) ?>"
												name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrconfid', 'id', ''] ) ) ?>"
											>
											<input type="hidden" value="<?= $enc->attr( $attrItem->getQuantity() ) ?>"
												name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrconfid', 'qty', ''] ) ) ?>"
											>
										<?php endforeach ?>
									<?php endforeach ?>

									<div class="button-group">
										<a class="btn btn-secondary close" href="<?= $enc->attr( $this->link( 'client/html/account/basket/url', [], ['account-basket'] ) ) ?>">
											<?= $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ) ?>
										</a>
										<button class="btn btn-primary btn-action">
											<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
										</button>
									</div>
								</form>

							</div>
						</div>

					<?php endif ?>
				<?php endforeach ?>

			</div>
		</div>
	</div>

<?php endif ?>
