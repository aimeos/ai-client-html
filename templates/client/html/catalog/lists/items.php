<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();


/** client/html/common/partials/products
 * Relative path to the products partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The products
 * partial creates an HTML block for a product listing.
 *
 * @param string Relative path to the template file
 * @since 2017.01
 */


?>
<div class="catalog-list-items product-list"
	data-infiniteurl="<?= $enc->attr( $this->get( 'infinite-url' ) ) ?>"
	data-pinned="<?= $enc->attr( $this->session( 'aimeos/catalog/session/pinned/list', [] ) ) ?>">

	<?= $this->partial(
		$this->config( 'client/html/common/partials/products', 'common/partials/products' ),
		array(
			'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
			'basket-add' => $this->config( 'client/html/catalog/lists/basket-add', false ),
			'products' => $this->get( 'products', map() ),
			'position' => $this->get( 'position' ),
		)
	) ?>

</div>
