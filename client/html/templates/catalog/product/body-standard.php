<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );

?>
<section class="aimeos catalog-product" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ); ?>">

	<?php if( isset( $this->productErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->productErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="catalog-product-items">
		<?= $this->partial( $this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
			array(
				'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
				'basket-add' => $this->config( 'client/html/catalog/product/basket-add', false ),
				'productItems' => $this->get( 'productProductItems', [] ),
				'products' => $this->get( 'productItems', [] ),
			)
		); ?>
	</div>

</section>
