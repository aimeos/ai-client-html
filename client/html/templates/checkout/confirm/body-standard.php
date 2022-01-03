<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<section class="aimeos checkout-confirm" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( isset( $this->confirmErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->confirmErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>


	<h1><?= $enc->html( $this->translate( 'client', 'Confirmation' ), $enc::TRUST ) ?></h1>


	<?= $this->block()->get( 'checkout/confirm/intro' ) ?>


	<div class="checkout-confirm-basic">
		<h2><?= $enc->html( $this->translate( 'client', 'Order status' ), $enc::TRUST ) ?></h2>
		<?php if( isset( $this->confirmOrderItem ) ) : ?>
			<ul class="attr-list">
				<li class="form-item">
					<span class="name">
						<?= $enc->html( $this->translate( 'client', 'Order ID' ), $enc::TRUST ) ?>
					</span>
					<span class="value">
						<?= $enc->html( $this->confirmOrderItem->getOrderNumber() ) ?>
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


	<?= $this->block()->get( 'checkout/confirm/order' ) ?>

</section>
