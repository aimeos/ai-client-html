<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
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

/** client/html/catalog/lists/basket-add
 * Display the "add to basket" button for each product item
 *
 * Enables the button for adding products to the basket from the list view.
 * This works for all type of products, even for selection products with product
 * variants and product bundles. By default, also optional attributes are
 * displayed if they have been associated to a product.
 *
 * **Note:** To fetch the necessary product variants, you have to extend the
 * list of domains for "client/html/catalog/lists/domains", e.g.
 *
 *  client/html/catalog/lists/domains = array( 'attribute', 'media', 'price', 'product', 'text' )
 *
 * @param boolean True to display the button, false to hide it
 * @since 2016.01
 * @see client/html/catalog/home/basket-add
 * @see client/html/catalog/detail/basket-add
 * @see client/html/catalog/product/basket-add
 * @see client/html/basket/related/basket-add
 * @see client/html/catalog/domains
 */

/** client/html/catalog/lists/infinite-scroll
 * Enables infinite scrolling in product catalog list
 *
 * If set to true, products from the next page are loaded via XHR request
 * and added to the product list when the user reaches the list bottom.
 *
 * @param boolean True to use infinite scrolling, false to disable it
 * @since 2019.10
 */
$infiniteScroll = $this->config( 'client/html/catalog/lists/infinite-scroll', false );

$url = '';
if( $infiniteScroll && $this->get( 'listPageNext', 0 ) > $this->get( 'listPageCurr', 0 ) ) {
	$url = $this->link( 'client/html/catalog/lists/url', ['l_page' => $this->get( 'listPageNext' )] + $this->get( 'listParams', [] ) );
}

?>
<div class="catalog-list-items product-list" data-infiniteurl="<?= $url ?>"
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
