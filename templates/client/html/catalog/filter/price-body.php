<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2022
 */

$enc = $this->encoder();

$linkKey = $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';


?>
<?php $this->block()->start( 'catalog/filter/price' ) ?>
<?php if( $this->get( 'priceHigh', 0 ) ) : ?>
	<section class="catalog-filter-price">
		<h2><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ) ?></h2>

		<div class="price-lists">
			<fieldset>
				<div class="price-input">
					<input type="number" class="price-low" name="<?= $this->formparam( ['f_price', 0] )?>"
						min="0" max="<?= $enc->html( $this->get( 'priceHigh', 0 ) ) ?>" step="1"
						value="<?= $enc->html( $this->param( 'f_price/0', 0 ) ) ?>"
						title="<?= $enc->attr( $this->translate( 'client', 'Lowest price' ) ) ?>">
					<input type="number" class="price-high" name="<?= $this->formparam( ['f_price', 1] )?>"
						min="0" max="<?= $enc->html( $this->get( 'priceHigh', 0 ) ) ?>" step="1"
						value="<?= $enc->html( $this->param( 'f_price/1', $this->get( 'priceHigh', 0 ) ) ) ?>"
						title="<?= $enc->attr( $this->translate( 'client', 'Highest price' ) ) ?>">
					<input type="range" class="price-slider" name="<?= $this->formparam( ['f_price', 1] )?>"
						min="0" max="<?= $enc->html( $this->get( 'priceHigh', $this->param( 'f_price/1', 0 ) ) ) ?>" step="1"
						value="<?= $enc->html( $this->param( 'f_price/1', $this->get( 'priceHigh', 0 ) ) ) ?>"
						title="<?= $enc->attr( $this->translate( 'client', 'Price range' ) ) ?>">
				</div>
				<button type="submit" class="btn btn-primary"><?= $enc->html( $this->translate( 'client', 'Save' ) ) ?></button>
				<?php if( $this->param( 'f_price' ) ) : ?>
					<a class="btn" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'priceResetParams', [] ) ) ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Reset' ) ) ?>
					</a>
				<?php endif ?>
			</fieldset>
		</div>
	</section>
<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/price' ) ?>
