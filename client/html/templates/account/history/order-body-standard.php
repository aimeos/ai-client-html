<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();

$accountTarget = $this->config( 'client/html/account/history/url/target' );
$accountController = $this->config( 'client/html/account/history/url/controller', 'account' );
$accountAction = $this->config( 'client/html/account/history/url/action', 'history' );
$accountConfig = $this->config( 'client/html/account/history/url/config', [] );

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'standard' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );
$basketSite = $this->config( 'client/html/basket/standard/url/site' );


?>
<?php $this->block()->start( 'account/history/order' ) ?>
<div class="account-history-order common-summary container-fluid">

	<h2 class="header"><?= $enc->html( $this->translate( 'client', 'Order details' ), $enc::TRUST ) ?></h2>

	<div class="common-summary-address row">
		<div class="item payment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Billing address' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !empty( $this->summaryBasket->getAddress( 'payment' ) ) ) : ?>
					<?= $this->partial(
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
						$this->config( 'client/html/account/history/summary/address', 'common/summary/address-standard' ),
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
						$this->config( 'client/html/account/history/summary/address', 'common/summary/address-standard' ),
						array( 'addresses' => $this->summaryBasket->getAddress( 'delivery' ), 'type' => 'delivery' )
					) ?>
				<?php else : ?>
					<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
				<?php endif ?>
			</div>
		</div>
	</div>


	<div class="common-summary-service row">
		<div class="item delivery col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !empty( $this->summaryBasket->getService( 'delivery' ) ) ) : ?>
					<?= $this->partial(
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
						$this->config( 'client/html/account/history/summary/service', 'common/summary/service-standard' ),
						array( 'service' => $this->summaryBasket->getService( 'delivery' ), 'type' => 'delivery' )
					) ?>
				<?php endif ?>
			</div>
		</div><!--

		--><div class="item payment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !empty( $this->summaryBasket->getService( 'payment' ) ) ) : ?>
					<?= $this->partial(
						$this->config( 'client/html/account/history/summary/service', 'common/summary/service-standard' ),
						array( 'service' => $this->summaryBasket->getService( 'payment' ), 'type' => 'payment' )
					) ?>
				<?php endif ?>
			</div>
		</div>

	</div>


	<div class="common-summary-additional row">
		<div class="item coupon col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Coupon codes' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?php if( !( $coupons = $this->summaryBasket->getCoupons() )->isEmpty() ) : ?>
					<ul class="attr-list">
						<?php foreach( $coupons as $code => $products ) : ?>
							<li class="attr-item"><?= $enc->html( $code ) ?></li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</div>
		</div><!--

		--><div class="item comment col-sm-6">
			<div class="header">
				<h3><?= $enc->html( $this->translate( 'client', 'Your comment' ), $enc::TRUST ) ?></h3>
			</div>

			<div class="content">
				<?= $enc->html( $this->summaryBasket->getComment() ) ?>
			</div>
		</div>
	</div>


	<div class="common-summary-detail row">
		<div class="header col-sm-12">
			<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
		</div>

		<div class="basket col-sm-12">
			<?= $this->partial(
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
				$this->config( 'client/html/account/history/summary/detail', 'common/summary/detail-standard' ),
				array(
					'summaryBasket' => $this->summaryBasket,
					'summaryTaxRates' => $this->get( 'summaryTaxRates', [] ),
					'summaryNamedTaxes' => $this->get( 'summaryNamedTaxes', [] ),
					'summaryShowDownloadAttributes' => $this->get( 'summaryShowDownloadAttributes' ),
				)
			) ?>
		</div>
	</div>


	<form method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, ( $basketSite ? ['site' => $basketSite] : [] ), [], $basketConfig ) ) ?>">
		<?= $this->csrf()->formfield() ?>

		<?php if( $basketSite ) : ?>
			<input type="hidden" name="<?= $this->formparam( 'site' ) ?>" value="<?= $enc->attr( $basketSite ) ?>">
		<?php endif ?>

		<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">

		<?php foreach( $this->summaryBasket->getProducts() as $pos => $orderProduct ) : ?>
			<input type="hidden" name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'prodid'] ) ) ?>" value="<?= $enc->attr( $orderProduct->getParentProductId() ?: $orderProduct->getProductId() ) ?>">
			<input type="hidden" name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'quantity'] ) ) ?>" value="<?= $enc->attr( $orderProduct->getQuantity() ) ?>">

			<?php foreach( $orderProduct->getAttributeItems( 'variant' ) as $attrItem ) : ?>
				<input type="hidden" value="<?= $enc->attr( $attrItem->getAttributeId() ) ?>"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrvarid', $attrItem->getCode()] ) ) ?>"
				>
			<?php endforeach ?>

			<?php foreach( $orderProduct->getAttributeItems( 'custom' ) as $attrItem ) : ?>
				<input type="hidden" value="<?= $enc->attr( $attrItem->getValue() ) ?>"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrcustid', $attrItem->getAttributeId()] ) ) ?>"
				>
			<?php endforeach ?>

			<?php foreach( $orderProduct->getAttributeItems( 'config' ) as $attrItem ) : ?>
				<input type="hidden" value="<?= $enc->attr( $attrItem->getAttributeId() ) ?>"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrconfid', 'id', ''] ) ) ?>"
				>
				<input type="hidden" value="<?= $enc->attr( $attrItem->getQuantity() ) ?>"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $pos, 'attrconfid', 'qty', ''] ) ) ?>"
				>
			<?php endforeach ?>
		<?php endforeach ?>

		<div class="button-group">
			<a class="btn btn-default btn-close"
				href="<?= $enc->attr( $this->url( $accountTarget, $accountController, $accountAction, [], [], $accountConfig ) ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Close' ), $enc::TRUST ) ?>
			</a>
			<button class="btn btn-primary btn-action">
				<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
			</button>
		</div>
	</form>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'account/history/order' ) ?>
