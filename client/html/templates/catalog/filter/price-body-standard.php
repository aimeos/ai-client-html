<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/catalog/lists/url/target' );
$cntl = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$config = $this->config( 'client/html/catalog/lists/url/config', [] );


?>
<?php $this->block()->start( 'catalog/filter/price' ); ?>
<section class="catalog-filter-price">

	<h2><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ); ?></h2>
	<fieldset>
		<input type="number" class="price-low" name="<?= $this->formparam( ['f_price', 0] )?>"
			min="0" max="<?= $enc->html( $this->get( 'priceHigh', 0 ) ) ?>" step="1" value="0">
		<input type="number" class="price-high" name="<?= $this->formparam( ['f_price', 1] )?>"
			min="0" max="<?= $enc->html( $this->get( 'priceHigh', 0 ) ) ?>" step="1"
			value="<?= $enc->html( $this->param( 'f_price/1', $this->get( 'priceHigh', 0 ) ) ) ?>">
		<input type="range" class="price-slider" name="<?= $this->formparam( ['f_price', 1] )?>"
			min="0" max="<?= $enc->html( $this->get( 'priceHigh', $this->param( 'f_price/1', 0 ) ) ) ?>" step="1"
			value="<?= $enc->html( $this->param( 'f_price/1', $this->get( 'priceHigh', 0 ) ) ) ?>">
		<a class="btn btn-secondary" href="<?= $enc->attr( $this->url( $target, $cntl, $action, $this->get( 'priceResetParams', [] ), [], $config ) ); ?>">
			<?= $enc->html( $this->translate( 'client', 'Reset' ) ) ?>
		</a>
		<button type="submit" class="btn btn-primary"><?= $enc->html( $this->translate( 'client', 'Save' ) ) ?></button>
	</fieldset>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/filter/price' ); ?>
