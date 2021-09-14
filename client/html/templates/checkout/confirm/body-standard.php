<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', [] );

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );


?>
<section class="aimeos checkout-confirm" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ) ?>">

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
				<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->url( $target, $controller, $action, ['c_step' => 'payment'], [], $config ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ) ?>
				</a>
				<a class="btn btn-primary btn-lg" href="<?= $enc->attr( $this->url( $target, $controller, $action, ['c_step' => 'process', 'cs_option_terms' => 1, 'cs_option_terms_value' => 1, 'cs_order' => 1], [], $config ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Try again' ), $enc::TRUST ) ?>
				</a>
			</div>
		<?php endif ?>
	</div>


	<?= $this->block()->get( 'checkout/confirm/order' ) ?>

</section>
