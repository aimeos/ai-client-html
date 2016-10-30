<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$enc = $this->encoder();
$products = $this->get( 'promoItems', array() );

?>
<?php $this->block()->start( 'catalog/lists/promo' ); ?>

<?php if( !empty( $products ) ) : ?>
	<section class="catalog-list-promo">
		<h2 class="header"><?php echo $this->translate( 'client', 'Top seller' ); ?></h2>
		<?php echo $this->partial(
			$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
			array( 'products' => $products )
		); ?>
	</section>
<?php endif; ?>

<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/lists/promo' ); ?>
