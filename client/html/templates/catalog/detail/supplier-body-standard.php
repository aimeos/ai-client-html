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

	<h2 class="header"><?= $this->translate( 'client', 'Supplier information' ); ?></h2>

	<?php foreach( $items as $item ) : ?>

		<div class="content supplier">

			<?php if( ( $mediaItem = current( $item->getRefItems( 'media', 'default', 'default' ) ) ) !== false ) : ?>
				<div class="media-item">
					<img class="lazy-image"
						data-src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>"
						data-srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
						alt="<?= $enc->attr( $mediaItem->getName() ); ?>"
					/>
				</div>
			<?php endif; ?>

			<h3 class="supplier-name">
				<?= $enc->html( $item->getName() ); ?>

				<?php if( ( $addrItem = current( $item->getAddressItems() ) ) !== false ) : ?>
					<span class="supplier-address">(<?= $enc->html( $addrItem->getCity() ); ?>, <?= $enc->html( $addrItem->getCountryId() ); ?>)</span>
				<?php endif ?>
			</h3>

			<?php foreach( $item->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
				<p class="supplier-short"><?= $enc->html( $textItem->getContent() ); ?></p>
			<?php endforeach; ?>

			<?php foreach( $item->getRefItems( 'text', 'long', 'default' ) as $textItem ) : ?>
				<p class="supplier-long"><?= $enc->html( $textItem->getContent() ); ?></p>
			<?php endforeach; ?>

		</div>

	<?php endforeach; ?>

</div>
<?php endif; ?>
<?php $this->block()->stop(); ?>
