<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */

$enc = $this->encoder();
$items = $this->get( 'supplierItems', [] );

?>
<?php $this->block()->start( 'catalog/detail/supplier' ); ?>
<?php if( !empty( $items ) ) : ?>
<div class="catalog-detail-supplier">

	<?php foreach( $items as $item ) : ?>

		<div class="supplier-item">

			<?php if( ( $mediaItem = current( $item->getRefItems( 'media', 'default', 'default' ) ) ) !== false ) : ?>
				<?php
					$srcset = [];
					foreach( $mediaItem->getPreviews() as $type => $path ) {
						$srcset[] = $this->content( $path ) . ' ' . $type . 'w';
					}
				?>
				<div class="media-item">
					<img class="lazy-image" data-src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>" data-srcset="<?= $enc->attr( join( ', ', $srcset ) ) ?>" alt="<?= $enc->attr( $mediaItem->getName() ); ?>" />
				</div>
			<?php endif; ?>

			<span class="supplier-name"><?= $enc->html( $item->getName() ); ?></span>
			<?php if( ( $addrItem = current( $item->getAddressItems() ) ) !== false ) : ?>
				<span class="supplier-address"><?= $enc->html( $addrItem->getCity() ); ?>, <?= $enc->html( $addrItem->getCountryId() ); ?></span>
			<?php endif ?>

			<?php foreach( $item->getRefItems( 'text', 'description', 'default' ) as $textItem ) : ?>
				<p class="supplier-description"><?= $enc->html( $textItem->getContent() ); ?></p>
			<?php endforeach; ?>

		</div>

	<?php endforeach; ?>

</div>
<?php endif; ?>
<?php $this->block()->stop(); ?>
