<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->standardBasket ) ) : ?>

	<div class="section aimeos basket-standard" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
				<?= $this->csrf()->formfield() ?>

				<div class="row header">
					<h2 class="col-12 col-sm-6"><?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ) ?></h2>

					<?php if( $this->get( 'contextUserId' ) ) : ?>
						<div class="col-12 col-sm-6">
							<div class="input-group basket-save">
								<input class="form-control basket-name" type="text" maxlength="255"
									placeholder="<?= $enc->attr( $this->translate( 'client', 'Basket name' ) ) ?>"
									name="<?= $enc->attr( $this->formparam( 'b_name' ) ) ?>"
								>
								<button class="btn" type="submit"
									formaction="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', ['b_action' => 'save'] ) ) ?>">
									<?= $enc->attr( $this->translate( 'client', 'Save' ) ) ?>
								</button>
							</div>
						</div>
					<?php endif ?>
				</div>

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

				<div class="basket-standard-coupon">
					<div class="header">
						<h2><?= $enc->html( $this->translate( 'client', 'Coupon codes' ) ) ?></h2>
					</div>

					<div class="content">

						<div class="input-group coupon-new">
							<input class="form-control coupon-code" type="text" maxlength="255"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'Coupon codes' ) ) ?>"
								name="<?= $enc->attr( $this->formparam( 'b_coupon' ) ) ?>"
							><!--
							--><button class="btn btn-primary" type="submit"><?= $enc->html( $this->translate( 'client', 'Apply' ) ) ?></button>
						</div>

						<?php if( !( $coupons = $this->standardBasket->getCoupons() )->isEmpty() ) : ?>
							<div class="coupon-detail">
								<p class="name"><?= $enc->html( $this->translate( 'client', 'Coupons' ) ) ?>:</p>
								<ul class="attr-list">
									<?php foreach( $coupons as $code => $products ) : $params = array( 'b_action' => 'coupon-delete', 'b_coupon' => $code ) ?>
									<li class="attr-item">
										<span class="coupon-code"><?= $enc->html( $code ) ?></span>
										<a class="minibutton delete" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $params ) ) ?>"></a>
									</li>
									<?php endforeach ?>
								</ul>
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

					<button class="btn btn-default btn-lg btn-update" type="submit">
						<?= $enc->html( $this->translate( 'client', 'Update' ), $enc::TRUST ) ?>
					</button>

					<?php if( $this->get( 'standardCheckout', false ) === true ) : ?>
						<a class="btn btn-primary btn-lg btn-action"
							href="<?= $enc->attr( $this->link( 'client/html/checkout/standard/url' ) ) ?>">
							<?= $enc->html( $this->translate( 'client', 'Checkout' ), $enc::TRUST ) ?>
						</a>
					<?php else : ?>
						<input type="hidden" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>" value="1">
						<button class="btn btn-primary btn-lg btn-action" type="submit">
							<?= $enc->html( $this->translate( 'client', 'Check' ), $enc::TRUST ) ?>
						</button>
					<?php endif ?>

				</div>
			</form>
		</div>
	</div>

<?php endif ?>
