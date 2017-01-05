<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2016
 */

$enc = $this->encoder();

$addresses = $this->summaryBasket->getAddresses();
$services = $this->summaryBasket->getServices();


?>
<?php $this->block()->start( 'email/common/html/summary' ); ?>
<div class="common-summary content-block">

	<div class="common-summary-address container">
		<div class="item payment">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['payment'] ) ) : ?>
					<?php echo $this->partial(
						/** client/html/email/common/summary/address/html
						 * Location of the address partial template for the HTML e-mails
						 *
						 * To configure an alternative template for the address partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery address block in the HTML e-mails.
						 *
						 * @param string Relative path to the address partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/email/common/summary/detail/html
						 * @see client/html/email/common/summary/service/html
						 */
						$this->config( 'client/html/email/common/summary/address/html', 'common/summary/address-default.php' ),
						array( 'address' => $addresses['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item delivery">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['delivery'] ) ) : ?>
					<?php echo $this->partial(
						$this->config( 'client/html/email/common/summary/address/html', 'common/summary/address-default.php' ),
						array( 'address' => $addresses['delivery'], 'type' => 'delivery' )
					); ?>
				<?php else : ?>
					<?php echo $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>


	<div class="common-summary-service container">
		<div class="item delivery">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['delivery'] ) ) : ?>
					<?php echo $this->partial(
						/** client/html/email/common/summary/service/html
						 * Location of the service partial template for the HTML e-mails
						 *
						 * To configure an alternative template for the service partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery service block in the HTML e-mails.
						 *
						 * @param string Relative path to the service partial
						 * @since 2017.01
						 * @category Developer
						 * @see client/html/email/common/summary/address/html
						 * @see client/html/email/common/summary/detail/html
						 */
						$this->config( 'client/html/email/common/summary/service/html', 'common/summary/service-default.php' ),
						array( 'service' => $services['delivery'], 'type' => 'delivery' )
					); ?>
				<?php endif; ?>
			</div>
		</div><!--

		--><div class="item payment">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $services['payment'] ) ) : ?>
					<?php echo $this->partial(
						$this->config( 'client/html/email/common/summary/service/html', 'common/summary/service-default.php' ),
						array( 'service' => $services['payment'], 'type' => 'payment' )
					); ?>
				<?php endif; ?>
			</div>
		</div>

	</div>


	<div class="common-summary-additional container">
		<div class="item coupon">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( ( $coupons = $this->summaryBasket->getCoupons() ) !== array() ) : ?>
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
				<?php echo $enc->html( $this->summaryBasket->getComment() ); ?>
			</div>
		</div>
	</div>


	<div class="common-summary-detail container">
		<div class="header">
			<h2><?php echo $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ); ?></h2>
		</div>

		<div class="basket">
			<?php echo $this->partial(
				/** client/html/email/common/summary/detail/html
				 * Location of the detail partial template for the HTML e-mails
				 *
				 * To configure an alternative template for the detail partial, you
				 * have to configure its path relative to the template directory
				 * (usually client/html/templates/). It's then used to display the
				 * product detail block in the HTML e-mails.
				 *
				 * @param string Relative path to the detail partial
				 * @since 2017.01
				 * @category Developer
				 * @see client/html/email/common/summary/address/html
				 * @see client/html/email/commonsummary/service/html
				 */
				$this->config( 'client/html/email/common/summary/detail/html', 'common/summary/detail-default.php' ),
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
<?php echo $this->block()->get( 'email/common/html/summary' ); ?>
