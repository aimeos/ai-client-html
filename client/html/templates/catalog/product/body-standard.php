<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

$enc = $this->encoder();

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );

/** client/html/catalog/product/basket-add
 * Display the "add to basket" button for each product item
 *
 * Enables the button for adding products to the basket for the listed products.
 * This works for all type of products, even for selection products with product
 * variants and product bundles. By default, also optional attributes are
 * displayed if they have been associated to a product.
 *
 * **Note:** To fetch the necessary product variants, you have to extend the
 * list of domains for "client/html/catalog/product/domains", e.g.
 *
 *  client/html/catalog/product/domains = ['attribute', 'media', 'price', 'product', 'text']
 *
 * @param boolean True to display the button, false to hide it
 * @since 2019.10
 * @category Developer
 * @category User
 * @see client/html/catalog/home/basket-add
 * @see client/html/catalog/lists/basket-add
 * @see client/html/catalog/detail/basket-add
 * @see client/html/basket/related/basket-add
 */

?>
<section class="aimeos catalog-product" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ) ?>">

	<?php if( isset( $this->productErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->productErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

	<div class="catalog-product-items">
		<?= $this->partial( $this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
			array(
				'require-stock' => (bool) $this->config( 'client/html/basket/require-stock', true ),
				'basket-add' => $this->config( 'client/html/catalog/product/basket-add', false ),
				'productItems' => $this->get( 'productProductItems', [] ),
				'products' => $this->get( 'productItems', [] ),
			)
		) ?>
	</div>

</section>
