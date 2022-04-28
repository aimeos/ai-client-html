<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


/** client/html/account/history/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */

/** client/html/account/history/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/action
 * @see client/html/account/history/url/config
 */

/** client/html/account/history/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/config
 */

/** client/html/account/history/url/config
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
 * @since 2014.03
 * @see client/html/account/history/url/target
 * @see client/html/account/history/url/controller
 * @see client/html/account/history/url/action
 * @see client/html/url/config
 */

/// Date format with year (Y), month (m) and day (d). See http://php.net/manual/en/function.date.php
$dateformat = $this->translate( 'client', 'Y-m-d' );
/// Order status (%1$s) and date (%2$s), e.g. "received at 2000-01-01"
$attrformat = $this->translate( 'client', '%1$s at %2$s' );

$enc = $this->encoder();


?>
<?php if( !$this->get( 'historyItems', map() )->isEmpty() ) : ?>

	<section class="aimeos account-history" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<h1 class="header"><?= $enc->html( $this->translate( 'client', 'Order history' ), $enc::TRUST ) ?></h1>

			<div class="history-list">

				<?php foreach( $this->get( 'historyItems', [] ) as $id => $orderItem ) : ?>

					<div class="history-item row">

						<div class="col-12">
							<h2 class="order-basic">
								<span class="name">
									<?= $enc->html( $this->translate( 'client', 'Order ID' ), $enc::TRUST ) ?>
								</span>
								<span class="value">
									<?= $enc->html( $id ) ?>
								</span>
							</h2>
						</div>

						<div class="col-12 col-md-10">
							<div class="row">
								<div class="col-md-6">
									<div class="order-created row">
										<span class="name col-5">
											<?= $enc->html( $this->translate( 'client', 'Created' ), $enc::TRUST ) ?>
										</span>
										<span class="value col-7">
											<?= $enc->html( date_create( $orderItem->getTimeCreated() )->format( $dateformat ) ) ?>
										</span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="order-channel row">
										<span class="name col-5">
											<?= $enc->html( $this->translate( 'client', 'Channel' ), $enc::TRUST ) ?>
										</span>
										<span class="value col-7">
											<?php $code = 'order:' . $orderItem->getChannel() ?>
											<?= $enc->html( $this->translate( 'mshop/code', $code ), $enc::TRUST ) ?>
										</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="order-payment row">
										<span class="name col-5">
											<?= $enc->html( $this->translate( 'client', 'Payment' ), $enc::TRUST ) ?>
										</span>
										<span class="value col-7">
											<?php if( ( $date = $orderItem->getDatePayment() ) !== null ) : ?>
												<?php $code = 'pay:' . $orderItem->getStatusPayment(); $paystatus = $this->translate( 'mshop/code', $code ) ?>
												<?= $enc->html( sprintf( $attrformat, $paystatus, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) ?>
											<?php endif ?>
										</span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="order-delivery row">
										<span class="name col-5">
											<?= $enc->html( $this->translate( 'client', 'Delivery' ), $enc::TRUST ) ?>
										</span>
										<span class="value col-7">
											<?php if( ( $date = $orderItem->getDateDelivery() ) !== null ) : ?>
												<?php $code = 'stat:' . $orderItem->getStatusDelivery(); $status = $this->translate( 'mshop/code', $code ) ?>
												<?= $enc->html( sprintf( $attrformat, $status, date_create( $date )->format( $dateformat ) ), $enc::TRUST ) ?>
											<?php endif ?>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="action col-12 col-md-2">
							<a class="btn btn-secondary show" href="#"><?= $enc->html( $this->translate( 'client', 'Show' ) ) ?></a>
							<a class="btn btn-secondary close hidden" href="#"><?= $enc->html( $this->translate( 'client', 'Close' ) ) ?></a>
						</div>

						<div class="account-history-detail common-summary col-12" style="display: none">

							<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ) ?></h2>

							<div class="common-summary-address row">
								<div class="item payment col-sm-6">
									<div class="header">
										<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h3>
									</div>

									<div class="content">
										<?php if( !empty( $orderItem->getBaseItem()->getAddress( 'payment' ) ) ) : ?>
											<?= $this->partial(
												/** client/html/account/history/summary/address
												 * Location of the address partial template for the account history component
												 *
												 * To configure an alternative template for the address partial, you
												 * have to configure its path relative to the template directory
												 * (usually client/html/templates/). It's then used to display the
												 * payment or delivery address block in the account history component.
												 *
												 * @param string Relative path to the address partial
												 * @since 2017.01
												 * @see client/html/account/history/summary/detail
												 * @see client/html/account/history/summary/service
												 */
												$this->config( 'client/html/account/history/summary/address', 'common/summary/address' ),
												['addresses' => $orderItem->getBaseItem()->getAddress( 'payment' )]
											) ?>
										<?php endif ?>
									</div>
								</div><!--

								--><div class="item delivery col-sm-6">
									<div class="header">
										<h3><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h3>
									</div>

									<div class="content">
										<?php if( !empty( $orderItem->getBaseItem()->getAddress( 'delivery' ) ) ) : ?>
											<?= $this->partial(
												$this->config( 'client/html/account/history/summary/address', 'common/summary/address' ),
												['addresses' => $orderItem->getBaseItem()->getAddress( 'delivery' )]
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
										<?php if( !empty( $orderItem->getBaseItem()->getService( 'delivery' ) ) ) : ?>
											<?= $this->partial(
												/** client/html/account/history/summary/service
												 * Location of the service partial template for the account history component
												 *
												 * To configure an alternative template for the service partial, you
												 * have to configure its path relative to the template directory
												 * (usually client/html/templates/). It's then used to display the
												 * payment or delivery service block in the account history component
												 *
												 * @param string Relative path to the service partial
												 * @since 2017.01
												 * @see client/html/account/history/summary/address
												 * @see client/html/account/history/summary/detail
												 */
												$this->config( 'client/html/account/history/summary/service', 'common/summary/service' ),
												array( 'service' => $orderItem->getBaseItem()->getService( 'delivery' ), 'type' => 'delivery' )
											) ?>
										<?php endif ?>
									</div>
								</div><!--

								--><div class="item payment col-sm-6">
									<div class="header">
										<h3><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ) ?></h3>
									</div>

									<div class="content">
										<?php if( !empty( $orderItem->getBaseItem()->getService( 'payment' ) ) ) : ?>
											<?= $this->partial(
												$this->config( 'client/html/account/history/summary/service', 'common/summary/service' ),
												array( 'service' => $orderItem->getBaseItem()->getService( 'payment' ), 'type' => 'payment' )
											) ?>
										<?php endif ?>
									</div>
								</div>

							</div>


							<div class="common-summary-additional row">
								<div class="item coupon col-sm-6">
									<div class="header">
										<h3><?= $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ) ?></h3>
									</div>

									<div class="content">
										<?php if( !( $coupons = $orderItem->getBaseItem()->getCoupons() )->isEmpty() ) : ?>
											<ul class="attr-list">
												<?php foreach( $coupons as $code => $products ) : ?>
													<li class="attr-item"><?= $enc->html( $code ) ?></li>
												<?php endforeach ?>
											</ul>
										<?php endif ?>
									</div>
								</div><!--

								--><div class="item comment col-sm-6">
									<div class="header">
										<h3><?= $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ) ?></h3>
									</div>

									<div class="content">
										<?= $enc->html( $orderItem->getBaseItem()->getComment() ) ?>
									</div>
								</div>
							</div>


							<div class="common-summary-detail row">
								<div class="header col-sm-12">
									<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
								</div>

								<div class="basket col-sm-12">
									<?= $this->partial(
										/** client/html/account/history/summary/detail
										 * Location of the detail partial template for the account history component
										 *
										 * To configure an alternative template for the detail partial, you
										 * have to configure its path relative to the template directory
										 * (usually client/html/templates/). It's then used to display the
										 * product detail block in the account history component.
										 *
										 * @param string Relative path to the detail partial
										 * @since 2017.01
										 * @see client/html/account/history/summary/address
										 * @see client/html/account/history/summary/service
										 */
										$this->config( 'client/html/account/history/summary/detail', 'common/summary/detail' ),
										['orderItem' => $orderItem, 'summaryBasket' => $orderItem->getBaseItem()]
									) ?>
								</div>
							</div>


							<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
								<?= $this->csrf()->formfield() ?>

								<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">

								<?php foreach( $orderItem->getBaseItem()->getProducts() as $pos => $orderProduct ) : ?>
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
									<a class="btn btn-secondary close" href="<?= $enc->attr( $this->link( 'client/html/account/history/url', [], ['account-history'] ) ) ?>">
										<?= $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ) ?>
									</a>
									<button class="btn btn-primary btn-action">
										<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
									</button>
								</div>
							</form>

						</div>
					</div>

				<?php endforeach ?>

			</div>
		</div>
	</section>

<?php endif ?>
