<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->standardBasket ) ) : ?>

	<div class="section aimeos basket-standard" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<div class="row header">
				<h2 class="col-12 col-md-6"><?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ) ?></h2>

				<div class="col-12 col-md-6">
					<form class="input-group basket-save" method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', ['b_action' => 'save'] ) ) ?>"
						toolname="save_basket"
						tooldescription="<?= $enc->attr( $this->translate( 'client', 'Saves the current basket under a name for later use.' ) ) ?>">
						<?= $this->csrf()->formfield() ?>
						<input class="form-control basket-name" type="text" maxlength="255"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Basket name' ) ) ?>"
							name="<?= $enc->attr( $this->formparam( 'b_name' ) ) ?>"
							toolparamdescription="<?= $enc->attr( $this->translate( 'client', 'Name for the saved basket.' ) ) ?>"
							required="required"
						>
						<button class="btn" type="submit">
							<?= $enc->attr( $this->translate( 'client', 'Save' ) ) ?>
						</button>
					</form>
				</div>
			</div>

			<form id="basket-standard-update" method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
				<?= $this->csrf()->formfield() ?>

				<div class="common-summary-detail">
					<div class="header">
						<h2><?= $enc->html( $this->translate( 'client', 'Details' ), $enc::TRUST ) ?></h2>
					</div>

					<div class="basket">
						<?= $this->partial(
							/** client/html/basket/standard/summary/detail
							 * Location of the detail partial template for the basket standard component
							 *
							 * To configure an alternative template for the detail partial, you
							 * have to configure its path relative to the template directory
							 * (usually templates/client/html/). It's then used to display the
							 * product detail block in the basket standard component.
							 *
							 * @param string Relative path to the detail partial
							 * @since 2017.01
							 */
							$this->config( 'client/html/basket/standard/summary/detail', 'common/summary/detail' ),
							[
								'summaryEnableModify' => true,
								'summaryBasket' => $this->standardBasket,
								'summaryErrorCodes' => $this->get( 'standardErrorCodes', [] )
							]
						) ?>
					</div>
				</div>
			</form>

			<div class="basket-standard-coupon row">
				<div class="col-12 col-md-6 header">
					<h2><?= $enc->html( $this->translate( 'client', 'Coupon codes' ) ) ?></h2>
				</div>

				<div class="col-12 col-md-6 content">

					<form class="input-group coupon-new" method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>"
						toolname="apply_coupon"
						tooldescription="<?= $enc->attr( $this->translate( 'client', 'Applies a coupon code to the current basket.' ) ) ?>">
						<?= $this->csrf()->formfield() ?>
						<input class="form-control coupon-code" type="text" maxlength="255"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'Coupon codes' ) ) ?>"
							name="<?= $enc->attr( $this->formparam( 'b_coupon' ) ) ?>"
							toolparamdescription="<?= $enc->attr( $this->translate( 'client', 'Coupon code to apply to the basket.' ) ) ?>"
							required="required"
						><!--
						--><button class="btn btn-primary" type="submit"><?= $enc->html( $this->translate( 'client', 'Apply' ) ) ?></button>
					</form>

					<?php if( !( $coupons = $this->standardBasket->getCoupons() )->isEmpty() ) : ?>
						<div class="coupon-detail row">
							<div class="col-6">
								<div class="name"><?= $enc->html( $this->translate( 'client', 'Coupons' ) ) ?>:</div>
							</div>
							<div class="col-6">
								<?php foreach( $coupons as $code => $products ) : $params = array( 'b_action' => 'coupon-delete' ) ?>
									<div class="coupon-codes">
										<span class="coupon-code"><?= $enc->html( $code ) ?></span>
										<button class="minibutton delete" type="submit"
											name="<?= $enc->attr( $this->formparam( 'b_coupon' ) ) ?>"
											value="<?= $enc->attr( $code ) ?>"
											form="basket-standard-update"
											formaction="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $params ) ) ?>"></button>
									</div>
								<?php endforeach ?>
							</div>
						</div>
					<?php endif ?>
				</div>
			</div>

			<div class="button-group">

				<?php if( isset( $this->standardBackUrl ) ) : ?>
					<a class="btn btn-default btn-lg btn-back" href="<?= $enc->attr( $this->standardBackUrl ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ) ?>
					</a>
				<?php endif ?>

				<button class="btn btn-default btn-lg btn-update" type="submit" form="basket-standard-update">
					<?= $enc->html( $this->translate( 'client', 'Update' ), $enc::TRUST ) ?>
				</button>

				<?php if( $this->get( 'standardCheckout', false ) === true ) : ?>
					<a class="btn btn-primary btn-lg btn-action"
						href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url' ) ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Checkout' ), $enc::TRUST ) ?>
					</a>
				<?php endif ?>

			</div>
		</div>
	</div>

<?php endif ?>
