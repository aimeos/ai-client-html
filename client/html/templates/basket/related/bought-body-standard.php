<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$productItems = $this->get( 'boughtItems', [] );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );


?>
<?php $this->block()->start( 'basket/related/bought' ); ?>
	<?php if( !empty( $productItems ) ) : ?>

		<section class="basket-related-bought">
			<h2 class="header"><?= $this->translate( 'client', 'Products you might be also interested in' ); ?></h2>

			<?= $this->partial(
				$this->config( 'client/html/common/partials/products', 'common/partials/products-standard.php' ),
				array( 'products' => $productItems, 'itemprop' => 'isRelatedTo' )
			); ?>

		</section>

	<?php endif; ?>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'basket/related/bought' ); ?>
