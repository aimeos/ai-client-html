<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();


?>
<div class="section aimeos checkout-confirm" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
	<div class="container-xxl">

		<h1><?= $enc->html( $this->translate( 'client', 'Confirmation' ), $enc::TRUST ) ?></h1>

		<div class="checkout-confirm-intro">
			<?php switch( $this->confirmOrderItem->getStatusPayment() ) : case \Aimeos\MShop\Order\Item\Base::PAY_CANCELED: ?>
				<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', "The order was canceled.
Do you wish to retry your order?" ), $enc::TRUST ) ); break ?></p>
			<?php case \Aimeos\MShop\Order\Item\Base::PAY_REFUSED: ?>
				<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', "Unfortunately, the payment for your order was refused.
Do you wish to retry?" ), $enc::TRUST ) ); break ?></p>
			<?php case \Aimeos\MShop\Order\Item\Base::PAY_PENDING: ?>
				<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', "The payment confirmation for your order is still pending.
You will get an e-mail as soon as the payment is authorized." ), $enc::TRUST ) ); break ?></p>
			<?php case \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED: ?>
				<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', "Thank you for your order and authorizing the payment.
An e-mail with the order details will be sent to you within the next few minutes." ), $enc::TRUST ) ); break ?></p>
			<?php case \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED: ?>
				<p class="note"><?= nl2br( $enc->html( $this->translate( 'client', "Thank you for your order.
We received your payment and an e-mail with the order details will be sent to you within the next few minutes." ), $enc::TRUST ) ); break ?></p>
			<?php endswitch ?>
		</div>

		<div class="checkout-confirm-basic">
			<h2><?= $enc->html( $this->translate( 'client', 'Order status' ), $enc::TRUST ) ?></h2>

			<?php if( isset( $this->confirmOrderItem ) ) : ?>
				<ul class="attr-list">
					<li class="form-item">
						<span class="name">
							<?= $enc->html( $this->translate( 'client', 'Order ID' ), $enc::TRUST ) ?>
						</span>
						<span class="value">
							<?= $enc->html( $this->confirmOrderItem->getInvoiceNumber() ) ?>
						</span>
					</li>
					<li class="form-item">
						<span class="name">
							<?= $enc->html( $this->translate( 'client', 'Payment status' ), $enc::TRUST ) ?>
						</span>
						<span class="value">
							<?php $code = 'pay:' . $this->confirmOrderItem->getStatusPayment() ?>
							<?= $enc->html( $this->translate( 'mshop/code', $code ) ) ?>
						</span>
					</li>
				</ul>
			<?php endif ?>

		</div>

		<div class="checkout-confirm-retry">

			<?php if( isset( $this->confirmOrderItem ) && $this->confirmOrderItem->getStatusPayment() < \Aimeos\MShop\Order\Item\Base::PAY_REFUND ) : ?>
				<div class="button-group">
					<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url', ['c_step' => 'payment'] ) ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ) ?>
					</a>
					<a class="btn btn-primary btn-lg" href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url', ['c_step' => 'process', 'cs_option_terms' => 1, 'cs_option_terms_value' => 1, 'cs_order' => 1] ) ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Try again' ), $enc::TRUST ) ?>
					</a>
				</div>
			<?php endif ?>

		</div>


		<div class="checkout-confirm-detail common-summary">
			<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ) ?></h2>

			<div class="common-summary-address row">
				<div class="item payment col-sm-6">
					<div class="header">
						<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h3>
					</div>

					<div class="content">
						<?php if( ( $addresses = $this->summaryBasket->getAddress( 'payment' ) ) !== [] ) : ?>
							<?= $this->partial(
								/** client/html/checkout/confirm/summary/address
								 * Location of the address partial template for the confirmation component
								 *
								 * To configure an alternative template for the address partial, you
								 * have to configure its path relative to the template directory
								 * (usually templates/client/html/). It's then used to display the
								 * payment or delivery address block on the confirm page during the
								 * checkout process.
								 *
								 * @param string Relative path to the address partial
								 * @since 2017.01
								 * @see client/html/checkout/confirm/summary/detail
								 * @see client/html/checkout/confirm/summary/service
								 */
								$this->config( 'client/html/checkout/confirm/summary/address', 'common/summary/address' ),
								['addresses' => $addresses]
							) ?>
						<?php endif ?>
					</div>
				</div><!--

				--><div class="item delivery col-sm-6">
					<div class="header">
						<h3><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h3>
					</div>

					<div class="content">
						<?php if( ( $addresses = $this->summaryBasket->getAddress( 'delivery' ) ) !== [] ) : ?>
							<?= $this->partial(
								$this->config( 'client/html/checkout/confirm/summary/address', 'common/summary/address' ),
								['addresses' => $addresses]
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
						<?php if( ( $services = $this->summaryBasket->getService( 'delivery' ) ) !== [] ) : ?>
							<?= $this->partial(
								/** client/html/checkout/confirm/summary/service
								 * Location of the service partial template for the confirmation component
								 *
								 * To configure an alternative template for the service partial, you
								 * have to configure its path relative to the template directory
								 * (usually templates/client/html/). It's then used to display the
								 * payment or delivery service block on the confirm page during the
								 * checkout process.
								 *
								 * @param string Relative path to the service partial
								 * @since 2017.01
								 * @see client/html/checkout/confirm/summary/address
								 * @see client/html/checkout/confirm/summary/detail
								 */
								$this->config( 'client/html/checkout/confirm/summary/service', 'common/summary/service' ),
								['service' => $services, 'type' => 'delivery']
							) ?>
						<?php endif ?>
					</div>
				</div><!--

				--><div class="item payment col-sm-6">
					<div class="header">
						<h3><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ) ?></h3>
					</div>

					<div class="content">
						<?php if( ( $services = $this->summaryBasket->getService( 'payment' ) ) !== [] ) : ?>
							<?= $this->partial(
								$this->config( 'client/html/checkout/confirm/summary/service', 'common/summary/service' ),
								['service' => $services, 'type' => 'payment']
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
						<?php if( !( $coupons = $this->summaryBasket->getCoupons() )->isEmpty() ) : ?>
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
						<?= $enc->html( $this->summaryBasket->getCustomerReference() ) ?>
					</div>
				</div><!--

				--><div class="item comment col-sm-4">
					<div class="header">
						<h3><?= $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ) ?></h3>
					</div>

					<div class="content">
						<?= $enc->html( $this->summaryBasket->getComment() ) ?>
					</div>
				</div>
			</div>


			<div class="common-summary-detail row">
				<div class="header col-sm-12">
					<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
				</div>

				<div class="basket col-sm-12">
					<?= $this->partial(
						/** client/html/checkout/confirm/summary/detail
						 * Location of the detail partial template for the confirmation component
						 *
						 * To configure an alternative template for the detail partial, you
						 * have to configure its path relative to the template directory
						 * (usually templates/client/html/). It's then used to display the
						 * product detail block on the confirm page during the checkout process.
						 *
						 * @param string Relative path to the detail partial
						 * @since 2017.01
						 * @see client/html/checkout/confirm/summary/address
						 * @see client/html/checkout/confirm/summary/service
						 */
						$this->config( 'client/html/checkout/confirm/summary/detail', 'common/summary/detail' ),
						['orderItem' => $this->confirmOrderItem, 'summaryBasket' => $this->summaryBasket]
					) ?>
				</div>
			</div>

		</div>
	</div>
</div>
