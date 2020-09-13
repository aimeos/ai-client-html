<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();

?>
<?php $this->block()->start( 'catalog/filter/price' ); ?>
<section class="catalog-filter-price">

	<h2><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ); ?></h2>
	<fieldset>
		<input type="range" class="price-slider" name="<?= $this->formparam( ['f_price'] )?>">
	</fieldset>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/filter/price' ); ?>
