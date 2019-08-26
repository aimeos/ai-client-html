<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */

$enc = $this->encoder();

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );
$basketSite = $this->config( 'client/html/basket/standard/url/site' );

$basketParams = ( $basketSite ? ['site' => $basketSite] : [] );

$jsonTarget = $this->config( 'client/jsonapi/url/target' );
$jsonController = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$jsonAction = $this->config( 'client/jsonapi/url/action', 'options' );
$jsonConfig = $this->config( 'client/jsonapi/url/config', [] );


?>
<section class="aimeos basket-bulk" data-jsonurl="<?= $enc->attr( $this->url( $jsonTarget, $jsonController, $jsonAction, $basketParams, [], $jsonConfig ) ); ?>">

	<?php if( isset( $this->bulkErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->bulkErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<form class="container" method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, $basketParams, [], $basketConfig ) ); ?>">
		<!-- basket.bulk.csrf -->
		<?= $this->csrf()->formfield(); ?>
		<!-- basket.bulk.csrf -->

		<?php if( $basketSite ) : ?>
			<input type="hidden" name="<?= $this->formparam( 'site' ) ?>" value="<?= $enc->attr( $basketSite ) ?>" />
		<?php endif ?>

		<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ); ?>" />

		<table class="table table-striped">
			<thead>
				<tr class="header">
					<th class="product"><?= $enc->html( $this->translate( 'client', 'Article' ) ) ?></th>
					<th class="quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ) ) ?></th>
					<th class="buttons"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="details">
					<td class="product">
						<input type="hidden" name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ); ?>" />
						<input type="text" class="form-control" tabindex="1"
							name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'code' ) ) ); ?>"
						/>
					</td>
					<td class="quantity">
						<input type="number" class="form-control" tabindex="1"
							name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ); ?>"
							min="1" max="2147483647" maxlength="10" step="1" required="required" value="1"
						/>
					</td>
					<td class="buttons">
						<button class="btn act-add" type="button" value="" tabindex="1"></button>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr class="details prototype">
					<td class="product">
						<input type="hidden"
							name="<?= $enc->attr( $this->formparam( array( 'b_prod', '_idx_', 'prodid' ) ) ); ?>" />
						<input type="text" class="form-control" tabindex="1"
							name="<?= $enc->attr( $this->formparam( array( 'b_prod', '_idx_', 'code' ) ) ); ?>"
						/>
					</td>
					<td class="quantity">
						<input type="number" class="form-control" tabindex="1"
							name="<?= $enc->attr( $this->formparam( array( 'b_prod', '_idx_', 'quantity' ) ) ); ?>"
							min="1" max="2147483647" maxlength="10" step="1" required="required" value="1"
						/>
					</td>
					<td class="buttons">
						<button class="btn act-add" type="button" value="" tabindex="1"></button>
					</td>
				</tr>
			</tfoot>
		</table>

		<div class="button-group">
			<button class="btn btn-primary btn-lg btn-action" type="submit" value="" tabindex="1">
				<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?>
			</button>
		</div>

	</form>

</section>
