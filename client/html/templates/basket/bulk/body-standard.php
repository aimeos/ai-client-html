<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

$enc = $this->encoder();

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );
$basketSite = $this->config( 'client/html/basket/standard/url/site' );

$jsonTarget = $this->config( 'client/jsonapi/url/target' );
$jsonController = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$jsonAction = $this->config( 'client/jsonapi/url/action', 'options' );
$jsonConfig = $this->config( 'client/jsonapi/url/config', [] );

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
<section class="aimeos basket-bulk" data-jsonurl="<?= $enc->attr( $this->url( $jsonTarget, $jsonController, $jsonAction, ( $basketSite ? ['site' => $basketSite] : [] ), [], $jsonConfig ) ) ?>">

	<?php if( isset( $this->bulkErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->bulkErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

	<h1><?= $enc->html( $this->translate( 'client', 'Bulk order' ), $enc::TRUST ) ?></h1>

	<form class="container" method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, ( $basketSite ? ['site' => $basketSite] : [] ), [], $basketConfig ) ) ?>">
		<!-- basket.bulk.csrf -->
		<?= $this->csrf()->formfield() ?>
		<!-- basket.bulk.csrf -->

		<?php if( $basketSite ) : ?>
			<input type="hidden" name="<?= $this->formparam( 'site' ) ?>" value="<?= $enc->attr( $basketSite ) ?>">
		<?php endif ?>

		<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">

		<table class="table">
			<thead>
				<tr class="header">
					<th class="product"><?= $enc->html( $this->translate( 'client', 'Article' ) ) ?></th>
					<th class="quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ) ) ?></th>
					<th class="price"><?= $enc->html( $this->translate( 'client', 'Price' ) ) ?></th>
					<th class="buttons"><a href="#" class="minibutton add">+</a></th>
				</tr>
			</thead>
			<tbody>
				<?php for( $idx = 0; $idx < $rows; $idx++ ) : ?>
					<tr class="details">
						<td class="product">
							<input type="hidden" class="attrvarid"
								name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'attrvarid', '_type_'] ) ) ?>"
							>
							<input type="hidden" class="productid"
								name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'prodid'] ) ) ?>"
							>
							<input type="text" class="form-control search" tabindex="1"
								placeholder="<?= $enc->attr( $this->translate( 'client', 'SKU or article name' ) ) ?>"
							>
							<div class="vattributes"></div>
						</td>
						<td class="quantity">
							<input type="number" class="form-control" tabindex="1"
								name="<?= $enc->attr( $this->formparam( ['b_prod', $idx, 'quantity'] ) ) ?>"
								min="1" max="2147483647" step="1" required="required" value="1"
							>
						</td>
						<td class="price"></td>
						<td class="buttons">
							<a href="#" class="minibutton delete"></a>
						</td>
					</tr>
				<?php endfor ?>
			</tbody>
			<tfoot>
			<tr class="details prototype">
					<td class="product">
						<input type="hidden" class="attrvarid" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'attrvarid', '_type_'] ) ) ?>"
						>
						<input type="hidden" class="productid" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'prodid'] ) ) ?>"
						>
						<input type="text" class="form-control search" tabindex="1" disabled="disabled">
						<div class="vattributes"></div>
					</td>
					<td class="quantity">
						<input type="number" class="form-control" tabindex="1" disabled="disabled"
							name="<?= $enc->attr( $this->formparam( ['b_prod', '_idx_', 'quantity'] ) ) ?>"
							min="1" max="2147483647" step="1" required="required" value="1"
						>
					</td>
					<td class="price"></td>
					<td class="buttons">
						<a href="#" class="btn minibutton delete"></a>
					</td>
				</tr>
			</tfoot>
		</table>

		<div class="button-group">
			<button class="btn btn-primary btn-lg btn-action" type="submit" value="" tabindex="1">
				<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
			</button>
		</div>

	</form>

</section>
