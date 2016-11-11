<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$productItems = $this->get( 'boughtItems', array() );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );


?>
<?php $this->block()->start( 'basket/related/bought' ); ?>
	<?php if( !empty( $productItems ) ) : ?>

		<section class="basket-related-bought">
			<h2 class="header"><?php echo $this->translate( 'client', 'Products you might be also interested in' ); ?></h2>

			<?php echo $this->partial(
				$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
				array( 'products' => $productItems, 'itemprop' => 'isRelatedTo' )
			); ?>

		</section>

	<?php endif; ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'basket/related/bought' ); ?>
