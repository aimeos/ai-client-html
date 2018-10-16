<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.php
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

$addresses = $this->summaryBasket->getAddresses();
$services = $this->summaryBasket->getServices();


?>
<?php $this->block()->start( 'checkout/confirm/order' ); ?>
<div class="checkout-confirm-detail common-summary">
	<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ); ?></h2>

	<div class="common-summary-address row">
		<div class="item payment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['payment'] ) ) : ?>
					<?= $this->partial(
						/** client/html/checkout/confirm/summary/address
						 * Location of the address partial template for the confirmation component
						 *
						 * To configure an alternative template for the address partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery address block on the confirm page during the
						 * checkout process.
						 *
						 * @param string Relative path to the address partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/checkout/confirm/summary/detail
						 * @see client/html/checkout/confirm/summary/service
						 */
						$this->config( 'client/html/checkout/confirm/summary/address', 'common/summary/address-standard.php' ),
						array( 'address' => $addresses['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item delivery col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['delivery'] ) ) : ?>
					<?= $this->partial(
						$this->config( 'client/html/checkout/confirm/summary/address', 'common/summary/address-standard.php' ),
						array( 'address' => $addresses['delivery'], 'type' => 'delivery' )
					); ?>
				<?php else : ?>
					<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>


	<div class="common-summary-service row">
		<div class="item delivery col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['delivery'] ) ) : ?>
					<?= $this->partial(
						/** client/html/checkout/confirm/summary/service
						 * Location of the service partial template for the confirmation component
						 *
						 * To configure an alternative template for the service partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery service block on the confirm page during the
						 * checkout process.
						 *
						 * @param string Relative path to the service partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/checkout/confirm/summary/address
						 * @see client/html/checkout/confirm/summary/detail
						 */
						$this->config( 'client/html/checkout/confirm/summary/service', 'common/summary/service-standard.php' ),
						array( 'service' => $services['delivery'], 'type' => 'delivery' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item payment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['payment'] ) ) : ?>
					<?= $this->partial(
						$this->config( 'client/html/checkout/confirm/summary/service', 'common/summary/service-standard.php' ),
						array( 'service' => $services['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div>

	</div>


	<div class="common-summary-additional row">
		<div class="item coupon col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( ( $coupons = $this->summaryBasket->getCoupons() ) !== [] ) : ?>
					<ul class="attr-list">
						<?php foreach( $coupons as $code => $products ) : ?>
							<li class="attr-item"><?= $enc->html( $code ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item comment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?= $enc->html( $this->summaryBasket->getComment() ); ?>
			</div>
		</div>
	</div>


	<div class="common-summary-detail row">
		<div class="header col-sm-12">
			<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
		</div>

		<div class="basket col-sm-12">
			<?= $this->partial(
				/** client/html/checkout/confirm/summary/detail
				 * Location of the detail partial template for the confirmation component
				 *
				 * To configure an alternative template for the detail partial, you
				 * have to configure its path relative to the template directory
				 * (usually client/html/templates/). It's then used to display the
				 * product detail block on the confirm page during the checkout process.
				 *
				 * @param string Relative path to the detail partial
				 * @since 2017.01
				 * @category Developer
				 * @see client/html/checkout/confirm/summary/address
				 * @see client/html/checkout/confirm/summary/service
				 */
				$this->config( 'client/html/checkout/confirm/summary/detail', 'common/summary/detail-standard.php' ),
				array(
					'summaryBasket' => $this->summaryBasket,
					'summaryTaxRates' => $this->get( 'summaryTaxRates' ),
					'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes' ),
				)
			); ?>
		</div>
	</div>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/confirm/order' ); ?>
