<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );

$checkoutTarget = $this->config( 'client/html/checkout/standard/url/target' );
$checkoutController = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$checkoutAction = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$checkoutConfig = $this->config( 'client/html/checkout/standard/url/config', [] );

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );


?>
<section class="aimeos basket-standard" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ); ?>">

	<?php if( isset( $this->standardErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->standardErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>



	<?php if( isset( $this->standardBasket ) ) : ?>

		<h1><?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ); ?></h1>

		<form method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, [], [], $basketConfig ) ); ?>">
			<?= $this->csrf()->formfield(); ?>


			<div class="common-summary-detail">
				<div class="header">
					<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
				</div>

				<div class="basket">
					<?= $this->partial(
						/** client/html/basket/standard/summary/detail
						 * Location of the detail partial template for the basket standard component
						 *
						 * To configure an alternative template for the detail partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * product detail block in the basket standard component.
						 *
						 * @param string Relative path to the detail partial
						 * @since 2017.01
						 * @category Developer
						 */
						$this->config( 'client/html/basket/standard/summary/detail', 'common/summary/detail-standard.php' ),
						array(
							'summaryEnableModify' => true,
							'summaryBasket' => $this->standardBasket,
							'summaryTaxRates' => $this->get( 'standardTaxRates', [] ),
							'summaryErrorCodes' => $this->get( 'standardErrorCodes', [] ),
						)
					); ?>
				</div>
			</div>


			<div class="basket-standard-coupon">
				<div class="header">
					<h2><?= $enc->html( $this->translate( 'client', 'Coupon codes' ) ); ?></h2>
				</div>

				<div class="content">
					<?php $coupons = $this->standardBasket->getCoupons(); ?>

					<div class="input-group coupon-new">
						<input class="form-control coupon-code" name="<?= $enc->attr( $this->formparam( 'b_coupon' ) ); ?>" type="text" maxlength="255" /><!--
						--><button class="btn btn-primary" type="submit"><?= $enc->html( $this->translate( 'client', '+' ) ); ?></button>
					</div>

					<?php if( !empty( $coupons ) ) : ?>
						<ul class="attr-list">
							<?php foreach( $coupons as $code => $products ) : $params = array( 'b_action' => 'coupon-delete', 'b_coupon' => $code ); ?>
							<li class="attr-item">
								<span class="coupon-code"><?= $enc->html( $code ); ?></span>
								<a class="minibutton delete" href="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, $params, [], $basketConfig ) ); ?>"></a>
							</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>


			<div class="button-group">

				<?php if( isset( $this->standardBackUrl ) ) : ?>
					<a class="btn btn-default btn-lg btn-back" href="<?= $enc->attr( $this->standardBackUrl ); ?>">
						<?= $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ); ?>
					</a>
				<?php endif; ?>

				<button class="btn btn-default btn-lg btn-update" type="submit">
					<?= $enc->html( $this->translate( 'client', 'Update' ), $enc::TRUST ); ?>
				</button>

				<?php if( $this->get( 'standardCheckout', false ) === true ) : ?>
					<a class="btn btn-primary btn-lg btn-action"
						href="<?= $enc->attr( $this->url( $checkoutTarget, $checkoutController, $checkoutAction, [], [], $checkoutConfig ) ); ?>">
						<?= $enc->html( $this->translate( 'client', 'Checkout' ), $enc::TRUST ); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-primary btn-lg btn-action"
						href="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array( 'b_check' => 1 ), [], $basketConfig ) ); ?>">
						<?= $enc->html( $this->translate( 'client', 'Check' ), $enc::TRUST ); ?>
					</a>
				<?php endif; ?>

			</div>
		</form>

	<?php endif; ?>

</section>
