<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


?>
<?php $this->block()->start( 'catalog/lists/promo' ) ?>

<?php if( !$this->get( 'promoItems', map() )->isEmpty() ) : ?>
	<section class="catalog-list-promo">
		<h2 class="header"><?= $this->translate( 'client', 'Top seller' ) ?></h2>
		<?= $this->partial(
			/** client/html/common/partials/products
			 * Relative path to the products partial template file
			 *
			 * Partials are templates which are reused in other templates and generate
			 * reoccuring blocks filled with data from the assigned values. The products
			 * partial creates an HTML block for a product listing.
			 *
			 * @param string Relative path to the template file
			 * @since 2017.01
			 * @category Developer
			 */
			$this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
			['products' => $this->get( 'promoItems', map() )]
		) ?>
	</section>
<?php endif ?>

<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/lists/promo' ) ?>
