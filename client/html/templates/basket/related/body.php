<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<section class="aimeos basket-related" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( !$this->get( 'boughtItems', map() )->isEmpty() ) : ?>

		<section class="basket-related-bought">
			<h2 class="header"><?= $this->translate( 'client', 'Products you might be also interested in' ) ?></h2>

			<?= $this->partial(
				$this->config( 'client/html/common/partials/products', 'common/partials/products' ),
				[
					'require-stock' => (bool) $this->config( 'client/html/basket/require-stock', true ),
					'basket-add' => $this->config( 'client/html/basket/related/basket-add', false ),
					'products' => $this->get( 'boughtItems', map() ),
					'itemprop' => 'isRelatedTo'
				]
			) ?>

		</section>

	<?php endif ?>

</section>
