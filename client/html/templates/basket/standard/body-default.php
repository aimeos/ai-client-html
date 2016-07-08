<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );

$checkoutTarget = $this->config( 'client/html/checkout/standard/url/target' );
$checkoutController = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$checkoutAction = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$checkoutConfig = $this->config( 'client/html/checkout/standard/url/config', array() );


/** client/html/basket/standard/check
 * Alters the behavior of the product checks before continuing with the checkout
 *
 * By default, the product related checks are performed every time the basket
 * is shown. They test if there are any products in the basket and execute all
 * basket plugins that have been registered for the "check.before" and "check.after"
 * events.
 *
 * Using this configuration setting, you can either disable all checks completely
 * (0) or display a "Check" button instead of the "Checkout" button (2). In the
 * later case, customers have to click on the "Check" button first to perform
 * the checks and if everything is OK, the "Checkout" button will be displayed
 * that allows the customers to continue the checkout process. If one of the
 * checks fails, the customers have to fix the related basket item and must click
 * on the "Check" button again before they can continue.
 *
 * Available values are:
 *  0 = no product related checks
 *  1 = checks are performed every time when the basket is displayed
 *  2 = checks are performed only when clicking on the "check" button
 *
 * @param integer One of the allowed values (0, 1 or 2)
 * @since 2016.08
 * @category Developer
 * @category User
 */
$check = $this->config( 'client/html/basket/standard/check', 1 );

try {
	switch( $check )
	{
		case 2: if( $this->param( 'b_check', 0 ) == 0 ) { break; }
		case 1: $this->standardBasket->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
		default: $checkout = true;
	}
} catch( Exception $e ) {
	$checkout = false;
}

$enc = $this->encoder();

?>
<?php $this->block()->start( 'basket/stardard' ); ?>
<section class="aimeos basket-standard">
<?php if( isset( $this->standardErrorList ) ) : ?>
	<ul class="error-list">
<?php foreach( (array) $this->standardErrorList as $errmsg ) : ?>
		<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
	<h1><?php echo $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ); ?></h1>
	<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>
<?php echo $this->get( 'standardBody' ); ?>
		<div class="button-group">
<?php if( isset( $this->standardBackUrl ) ) : ?>
			<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->standardBackUrl ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ); ?></a>
<?php endif; ?>
			<button class="standardbutton btn-update" type="submit"><?php echo $enc->html( $this->translate( 'client', 'Update' ), $enc::TRUST ); ?></button>
<?php if( $checkout === true ) : ?>
			<a class="standardbutton btn-action" href="<?php echo $enc->attr( $this->url( $checkoutTarget, $checkoutController, $checkoutAction, array(), array(), $checkoutConfig ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Checkout' ), $enc::TRUST ); ?></a>
<?php else : ?>
			<a class="standardbutton btn-action" href="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array( 'b_check' => 1 ), array(), $basketConfig ) ); ?>"><?php echo $enc->html( $this->translate( 'client', 'Check' ), $enc::TRUST ); ?></a>
<?php endif; ?>
		</div>
	</form>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'basket/stardard' ); ?>
