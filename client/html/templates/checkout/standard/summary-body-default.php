<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$errors = $this->get( 'summaryErrorCodes', [] );

$addresses = $this->standardBasket->getAddresses();
$services = $this->standardBasket->getServices();


$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketCntl = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );

$coTarget = $this->config( 'client/html/checkout/standard/url/target' );
$coCntl = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$coAction = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$coConfig = $this->config( 'client/html/checkout/standard/url/config', [] );

$checkoutAddressUrl = $this->url( $coTarget, $coCntl, $coAction, array( 'c_step' => 'address' ), [], $coConfig );
$checkoutDeliveryUrl = $this->url( $coTarget, $coCntl, $coAction, array( 'c_step' => 'delivery' ), [], $coConfig );
$checkoutPaymentUrl = $this->url( $coTarget, $coCntl, $coAction, array( 'c_step' => 'payment' ), [], $coConfig );
$basketUrl = $this->url( $basketTarget, $basketCntl, $basketAction, [], [], $basketConfig );


?>
<?php $this->block()->start( 'checkout/standard/summary' ); ?>
<section class="checkout-standard-summary common-summary">
	<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'cs_order' ) ) ); ?>" value="1" />

	<h1><?php echo $enc->html( $this->translate( 'client', 'summary' ), $enc::TRUST ); ?></h1>
	<p class="note"><?php echo $enc->html( $this->translate( 'client', 'Please check your order' ), $enc::TRUST ); ?></p>


	<div class="common-summary-address container">
		<div class="item payment <?php echo ( isset( $errors['address']['payment'] ) ? 'error' : '' ); ?>">
			<div class="header">
				<a class="modify" href="<?php echo $enc->attr( $checkoutAddressUrl ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
				<h3><?php echo $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['payment'] ) ) : ?>
					<?php echo $this->partial(
						/** client/html/checkout/standard/summary/address
						 * Location of the address partial template for the checkout summary
						 *
						 * To configure an alternative template for the address partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery address block on the summary page during the
						 * checkout process.
						 *
						 * @param string Relative path to the address partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/checkout/standard/summary/detail
						 * @see client/html/checkout/standard/summary/options
						 * @see client/html/checkout/standard/summary/service
						 */
						$this->config( 'client/html/checkout/standard/summary/address', 'common/summary/address-default.php' ),
						array( 'address' => $addresses['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item delivery <?php echo ( isset( $errors['address']['delivery'] ) ? 'error' : '' ); ?>">
			<div class="header">
				<a class="modify" href="<?php echo $enc->attr( $checkoutAddressUrl ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
				<h3><?php echo $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['delivery'] ) ) : ?>
					<?php echo $this->partial(
						$this->config( 'client/html/checkout/standard/summary/address', 'common/summary/address-default.php' ),
						array( 'address' => $addresses['delivery'], 'type' => 'delivery' )
					); ?>
				<?php else : ?>
					<?php echo $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>


	<div class="common-summary-service container">
		<div class="item delivery <?php echo ( isset( $errors['service']['delivery'] ) ? 'error' : '' ); ?>">
			<div class="header">
				<a class="modify" href="<?php echo $enc->attr( $checkoutDeliveryUrl ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
				<h3><?php echo $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['delivery'] ) ) : ?>
					<?php echo $this->partial(
						/** client/html/checkout/standard/summary/service
						 * Location of the service partial template for the checkout summary
						 *
						 * To configure an alternative template for the service partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery service block on the summary page during the
						 * checkout process.
						 *
						 * @param string Relative path to the service partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/checkout/standard/summary/address
						 * @see client/html/checkout/standard/summary/detail
						 * @see client/html/checkout/standard/summary/options
						 */
						$this->config( 'client/html/checkout/standard/summary/service', 'common/summary/service-default.php' ),
						array( 'service' => $services['delivery'], 'type' => 'delivery' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item payment <?php echo ( isset( $errors['service']['payment'] ) ? 'error' : '' ); ?>">
			<div class="header">
				<a class="modify" href="<?php echo $enc->attr( $checkoutPaymentUrl ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
				<h3><?php echo $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['payment'] ) ) : ?>
					<?php echo $this->partial(
						$this->config( 'client/html/checkout/standard/summary/service', 'common/summary/service-default.php' ),
						array( 'service' => $services['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div>

	</div>


	<div class="common-summary-additional container">
		<div class="item coupon <?php echo ( isset( $errors['coupon'] ) ? 'error' : '' ); ?>">
			<div class="header">
				<a class="modify" href="<?php echo $enc->attr( $basketUrl ); ?>">
					<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
				</a>
				<h3><?php echo $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( ( $coupons = $this->standardBasket->getCoupons() ) !== [] ) : ?>
					<ul class="attr-list">
						<?php foreach( $coupons as $code => $products ) : ?>
							<li class="attr-item"><?php echo $enc->html( $code ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item comment">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<textarea class="comment-value" name="<?php echo $this->formparam( array( 'cs_comment' ) ); ?>"><?php echo $enc->html( $this->standardBasket->getComment() ); ?></textarea>
			</div>
		</div>
	</div>


	<div class="common-summary-detail container">
		<div class="header">
			<a class="modify" href="<?php echo $enc->attr( $basketUrl ); ?>">
				<?php echo $enc->html( $this->translate( 'client', 'Change' ), $enc::TRUST ); ?>
			</a>
			<h2><?php echo $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
		</div>

		<div class="basket">
			<?php echo $this->partial(
				/** client/html/checkout/standard/summary/detail
				 * Location of the detail partial template for the checkout summary
				 *
				 * To configure an alternative template for the detail partial, you
				 * have to configure its path relative to the template directory
				 * (usually client/html/templates/). It's then used to display the
				 * product detail block on the summary page during the checkout process.
				 *
				 * @param string Relative path to the detail partial
				 * @since 2017.01
				 * @category Developer
				 * @see client/html/checkout/standard/summary/address
				 * @see client/html/checkout/standard/summary/options
				 * @see client/html/checkout/standard/summary/service
				 */
				$this->config( 'client/html/checkout/standard/summary/detail', 'common/summary/detail-default.php' ),
				array(
					'summaryBasket' => $this->standardBasket,
					'summaryTaxRates' => $this->get( 'summaryTaxRates' ),
					'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes' ),
				)
			); ?>
		</div>
	</div>


	<div class="checkout-standard-summary-option container">
		<?php echo $this->partial(
			/** client/html/checkout/standard/summary/options
			 * Location of the options partial template for the checkout summary
			 *
			 * To configure an alternative template for the options partial, you
			 * have to configure its path relative to the template directory
			 * (usually client/html/templates/). It's then used to display the
			 * options block on the summary page during the checkout process.
			 *
			 * @param string Relative path to the options partial
			 * @since 2017.01
			 * @category Developer
			 * @see client/html/checkout/standard/summary/address
			 * @see client/html/checkout/standard/summary/detail
			 * @see client/html/checkout/standard/summary/service
			 */
			$this->config( 'client/html/checkout/standard/summary/options', 'checkout/standard/option-partial-default.php' ),
			array( 'errors' => $this->get( 'summaryErrorCodes', [] ), 'customerId' => $this->get( 'summaryCustomerId' ) )
		); ?>
	</div>


	<div class="button-group">
		<a class="standardbutton btn-back" href="<?php echo $enc->attr( $this->get( 'standardUrlBack' ) ); ?>">
			<?php echo $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ); ?>
		</a>
		<button class="standardbutton btn-action">
			<?php echo $enc->html( $this->translate( 'client', 'Buy now' ), $enc::TRUST ); ?>
		</button>
	</div>

</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/summary' ); ?>
