<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );

$checkoutTarget = $this->config( 'client/html/checkout/standard/url/target' );
$checkoutController = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$checkoutAction = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$checkoutConfig = $this->config( 'client/html/checkout/standard/url/config', array() );


?>
<section class="aimeos basket-standard">

	<?php if( isset( $this->standardErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->standardErrorList as $errmsg ) : ?>
				<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>



	<?php if( isset( $this->standardBasket ) ) : ?>

		<h1><?php echo $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ); ?></h1>

		<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
			<?php echo $this->csrf()->formfield(); ?>


			<div class="common-summary-detail container">
				<div class="header">
					<h2><?php echo $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
				</div>

				<div class="basket">
					<?php echo $this->partial(
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
						$this->config( 'client/html/basket/standard/summary/detail', 'common/summary/detail-default.php' ),
						array(
							'summaryEnableModify' => true,
							'summaryBasket' => $this->standardBasket,
							'summaryTaxRates' => $this->get( 'standardTaxRates', array() ),
							'summaryErrorCodes' => $this->get( 'standardErrorCodes', array() ),
						)
					); ?>
				</div>
			</div>


			<div class="basket-standard-coupon container">
				<div class="header">
					<h2><?php echo $enc->html( $this->translate( 'client', 'Coupon codes' ) ); ?></h2>
				</div>

				<div class="content">
					<?php $coupons = $this->standardBasket->getCoupons(); ?>

					<?php if( count( $coupons ) < $this->config( 'client/html/basket/standard/coupon/allowed', 1 ) ) : ?>
						<div class="coupon-new">
							<input class="coupon-code" name="<?php echo $enc->attr( $this->formparam( 'b_coupon' ) ); ?>" type="text" maxlength="255" />
							<button class="standardbutton" type="submit"><?php echo $enc->html( $this->translate( 'client', '+' ) ); ?></button>
						</div>
					<?php endif; ?>

					<?php if( !empty( $coupons ) ) : ?>
						<ul class="attr-list">
							<?php foreach( $coupons as $code => $products ) : $params = array( 'b_action' => 'coupon-delete', 'b_coupon' => $code ); ?>
							<li class="attr-item">
								<span class="coupon-code"><?php echo $enc->html( $code ); ?></span>
								<a class="minibutton change"
									href="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, $params, array(), $basketConfig ) ); ?>">
									<?php echo $enc->html( $this->translate( 'client', 'X' ) ); ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>


			<div class="button-group">

				<?php if( isset( $this->standardBackUrl ) ) : ?>
					<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->standardBackUrl ); ?>">
						<?php echo $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ); ?>
					</a>
				<?php endif; ?>

				<button class="standardbutton btn-update" type="submit">
					<?php echo $enc->html( $this->translate( 'client', 'Update' ), $enc::TRUST ); ?>
				</button>

				<?php if( $this->get( 'standardCheckout', false ) === true ) : ?>
					<a class="standardbutton btn-action"
						href="<?php echo $enc->attr( $this->url( $checkoutTarget, $checkoutController, $checkoutAction, array(), array(), $checkoutConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'client', 'Checkout' ), $enc::TRUST ); ?>
					</a>
				<?php else : ?>
					<a class="standardbutton btn-action"
						href="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array( 'b_check' => 1 ), array(), $basketConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'client', 'Check' ), $enc::TRUST ); ?>
					</a>
				<?php endif; ?>

			</div>
		</form>

	<?php endif; ?>

</section>
