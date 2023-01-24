<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */

$enc = $this->encoder();

/** client/html/basket/bulk/rows
 * Number or rows shown in the product bulk order form by default
 *
 * The product bulk order form shows a new line for adding a product to the basket
 * by default. You can change the number of empty input lines shown by default
 * using this configuration setting.
 *
 * @param int Number of lines shown
 * @since 2020.07
 */
$rows = (int) $this->config( 'client/html/basket/bulk/rows', 1 );


?>
<div class="section aimeos basket-bulk" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
	<div class="container-xxl">

		<h2><?= $enc->html( $this->translate( 'client', 'Bulk order' ), $enc::TRUST ) ?></h2>

		<form method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
			<!-- basket.bulk.csrf -->
			<?= $this->csrf()->formfield() ?>
			<!-- basket.bulk.csrf -->

			<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">

			<div class="bulk-main">
				<div class="row g-0 headline">
					<div class="col-sm-6 product-item"><?= $enc->html( $this->translate( 'client', 'Article' ) ) ?></div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-4 quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ) ) ?></div>
							<div class="col-4 price"><?= $enc->html( $this->translate( 'client', 'Price' ) ) ?></div>
							<div class="col-4 buttons"><div class="minibutton add"></div></div>
						</div>
					</div>
				</div>
				<div class="bulk-content">
					<?php for( $idx = 0; $idx < $rows; $idx++ ) : ?>
						<div class="row g-0 details">
							<div class="col-sm-6 product-item">
								<input type="hidden" class="attrvarid"
									name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'attrvarid', '_type_'] ) ) ?>"
								>
								<input type="hidden" class="productid"
									name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'prodid'] ) ) ?>"
								>
								<input type="text" class="value search" tabindex="1"
									placeholder="<?= $enc->attr( $this->translate( 'client', 'SKU or article name' ) ) ?>"
								>
								<div class="vattributes"></div>
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-4 quantity">
										<input type="number" class="value" tabindex="1"
											name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'quantity'] ) ) ?>"
											min="1" max="2147483647" step="1" required="required" value="1"
										>
									</div>
									<div class="col-4 price"></div>
									<div class="col-4 buttons"><div class="minibutton delete" tabindex="1"></div></div>
								</div>
							</div>
						</div>
					<?php endfor ?>

					<div class="row g-0 details prototype">
						<div class="col-sm-6 product-item">
							<input type="hidden" class="attrvarid" disabled="disabled"
								name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'attrvarid', '_type_'] ) ) ?>"
							>
							<input type="hidden" class="productid" disabled="disabled"
								name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'prodid'] ) ) ?>"
							>
							<input type="text" class="value search" tabindex="1" disabled="disabled"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'SKU or article name' ) ) ?>"
							>
							<div class="vattributes"></div>
						</div>
						<div class="col-sm-6">
							<div class="row">
								<div class="col-4 quantity">
									<input type="number" class="value" tabindex="1" disabled="disabled"
										name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'quantity'] ) ) ?>"
										min="1" max="2147483647" step="1" required="required" value="1"
									>
								</div>
								<div class="col-4 price"></div>
								<div class="col-4 buttons"><div class="minibutton delete"></div></div>
							</div>
						</div>
					</div>
				</div>
			<div>

			<div class="button-group">
				<button class="btn btn-primary btn-lg btn-action" type="submit" value="" tabindex="1">
					<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
				</button>
			</div>

		</form>
	</div>
</div>
