<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/** client/html/catalog/lists/basket-add
 * Display the "add to basket" button for each product item
 *
 * Enables the button for adding products to the basket from the list view.
 * This works for all type of products, even for selection products with product
 * variants and product bundles. By default, also optional attributes are
 * displayed if they have been associated to a product.
 *
 * '''Note:''' To fetch the necessary product variants, you have to extend the
 * list of domains for "client/html/catalog/lists/domains", e.g.
 *
 *  client/html/catalog/lists/domains = array( 'attribute', 'media', 'price', 'product', 'text' )
 *
 * @param boolean True to display the button, false to hide it
 * @since 2016.01
 * @category Developer
 * @category User
 * @see client/html/catalog/domains
 */


?>
<?php $this->block()->start( 'catalog/lists/items' ); ?>
<div class="catalog-list-items">

	<?php echo $this->partial(
		$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
		array(
			'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
			'basket-add' => $this->config( 'client/html/catalog/lists/basket-add', false ),
			'attributeItems' => $this->get( 'itemsAttributeItems', [] ),
			'productItems' => $this->get( 'itemsProductItems', [] ),
			'mediaItems' => $this->get( 'itemsMediaItems', [] ),
			'products' => $this->get( 'listProductItems', [] ),
			'position' => $this->get( 'itemPosition', 0 ),
		)
	); ?>

</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/lists/items' ); ?>
