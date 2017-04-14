<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$accountTarget = $this->config( 'client/html/account/history/url/target' );
$accountController = $this->config( 'client/html/account/history/url/controller', 'account' );
$accountAction = $this->config( 'client/html/account/history/url/action', 'history' );
$accountConfig = $this->config( 'client/html/account/history/url/config', [] );

$addresses = $this->summaryBasket->getAddresses();
$services = $this->summaryBasket->getServices();


?>
<?php $this->block()->start( 'account/history/order' ); ?>
<div class="account-history-order common-summary">

	<a class="modify minibutton btn-close"
		href="<?php echo $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, [], [], $accountConfig ) ); ?>">
		<?php echo $enc->html( $this->translate( 'client', 'X' ), $enc::TRUST ); ?>
	</a>

	<h2 class="header"><?php echo $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ); ?></h2>


	<div class="common-summary-address container">
		<div class="item payment">
			<div class="header">
				<h3><?php echo $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ); ?></h3>
			</div>

			<div class="content">
				<?php if( isset( $addresses['payment'] ) ) : ?>
					<?php echo $this->partial(
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
						 * @category Developer
						 * @see client/html/account/history/summary/detail
						 * @see client/html/account/history/summary/service
						 */
						$this->config( 'client/html/account/history/summary/address', 'common/summary/address-default.php' ),
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
						$this->config( 'client/html/account/history/summary/address', 'common/summary/address-default.php' ),
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
						 * @category Developer
						 * @see client/html/account/history/summary/address
						 * @see client/html/account/history/summary/detail
						 */
						$this->config( 'client/html/account/history/summary/service', 'common/summary/service-default.php' ),
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
						$this->config( 'client/html/account/history/summary/service', 'common/summary/service-default.php' ),
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
				<?php if( ( $coupons = $this->summaryBasket->getCoupons() ) !== [] ) : ?>
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
				 * @category Developer
				 * @see client/html/account/history/summary/address
				 * @see client/html/account/history/summary/service
				 */
				$this->config( 'client/html/account/history/summary/detail', 'common/summary/detail-default.php' ),
				array(
					'summaryBasket' => $this->summaryBasket,
					'summaryTaxRates' => $this->get( 'summaryTaxRates' ),
					'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes' ),
				)
			); ?>
		</div>
	</div>


	<div class="button-group">
		<a class="standardbutton btn-close"
			href="<?php echo $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, [], [], $accountConfig ) ); ?>">
			<?php echo $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ); ?>
		</a>
	</div>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'account/history/order' ); ?>
