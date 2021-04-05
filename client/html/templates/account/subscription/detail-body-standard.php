<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

$enc = $this->encoder();

$accountTarget = $this->config( 'client/html/account/subscription/url/target' );
$accountController = $this->config( 'client/html/account/subscription/url/controller', 'account' );
$accountAction = $this->config( 'client/html/account/subscription/url/action', 'subscription' );
$accountConfig = $this->config( 'client/html/account/subscription/url/config', [] );


?>
<?php $this->block()->start( 'account/subscription/detail' ) ?>
<div class="account-subscription-detail common-summary col-sm-12">

	<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Subscription details' ), $enc::TRUST ) ?></h2>

	<div class="common-summary-address row">
		<div class="item payment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !empty( $this->summaryBasket->getAddress( 'payment' ) ) ) : ?>
					<?= $this->partial(
						/** client/html/account/subscription/summary/address
						 * Location of the address partial template for the account subscription component
						 *
						 * To configure an alternative template for the address partial, you
						 * have to configure its path relative to the template directory
						 * (usually client/html/templates/). It's then used to display the
						 * payment or delivery address block in the account subscription component.
						 *
						 * @param string Relative path to the address partial
						 * @since 2018.04
						 * @category Developer
						 * @see client/html/account/subscription/summary/detail
						 * @see client/html/account/subscription/summary/service
						 */
						$this->config( 'client/html/account/subscription/summary/address', 'common/summary/address-standard' ),
						array( 'addresses' => $this->summaryBasket->getAddress( 'payment' ), 'type' => 'payment' )
					) ?>
				<?php endif ?>
			</div>
		</div><!--

		--><div class="item delivery col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !empty( $this->summaryBasket->getAddress( 'delivery' ) ) ) : ?>
					<?= $this->partial(
						$this->config( 'client/html/account/subscription/summary/address', 'common/summary/address-standard' ),
						array( 'addresses' => $this->summaryBasket->getAddress( 'delivery' ), 'type' => 'delivery' )
					) ?>
				<?php else : ?>
					<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
				<?php endif ?>
			</div>
		</div>
	</div>


	<div class="common-summary-detail row">
		<div class="header">
			<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
		</div>

		<div class="basket">
			<?= $this->partial(
				/** client/html/account/subscription/summary/detail
				 * Location of the detail partial template for the account subscription component
				 *
				 * To configure an alternative template for the detail partial, you
				 * have to configure its path relative to the template directory
				 * (usually client/html/templates/). It's then used to display the
				 * product detail block in the account subscription component.
				 *
				 * @param string Relative path to the detail partial
				 * @since 2018.04
				 * @category Developer
				 * @see client/html/account/subscription/summary/address
				 * @see client/html/account/subscription/summary/service
				 */
				$this->config( 'client/html/account/subscription/summary/detail', 'common/summary/detail-standard' ),
				array(
					'summaryBasket' => $this->summaryBasket,
					'summaryTaxRates' => $this->get( 'summaryTaxRates', [] ),
					'summaryNamedTaxes' => $this->get( 'summaryNamedTaxes', [] ),
					'summaryShowDownloadAttributes' => false,
				)
			) ?>
		</div>
	</div>


	<div class="button-group">
		<a class="btn btn-close" href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, [], [], $accountConfig ) ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ) ?>
		</a>
		<?php if( $this->detailItem->getDateEnd() == null ) : ?>
			<?php $params = array( 'sub_action' => 'cancel', 'sub_id' => $this->detailItem->getId() ) ?>
			<a class="btn btn-primary" href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, $params, [], $accountConfig ) ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Cancel' ), $enc::TRUST ) ?>
			</a>
		<?php endif ?>
	</div>
</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'account/subscription/detail' ) ?>
