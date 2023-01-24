<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */

/* Available data:
 * - productItem : Selection product for the variant products
 * - productItems : List of variant product items with referenced items
 */


$enc = $this->encoder();


?>
<div class="selection">

	<div class="row">
		<div class="col-3 select-media"></div>
		<div class="col-3 select-name"><?= $enc->html( $this->translate( 'client', 'Name' ) ) ?></div>
		<div class="col-3 select-attr"><?= $enc->html( $this->translate( 'client', 'Variant' ) ) ?></div>
		<div class="col-1 select-stock"><?= $enc->html( $this->translate( 'client', 'Stock' ) ) ?></div>
		<div class="col-2 select-quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ) ) ?></div>
	</div>

	<?php foreach( $this->get( 'productItems', [] ) as $id => $product ) : ?>

		<div class="row select-item">

			<div class="col-3">
				<?php if( ( $mediaItem = $product->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) : ?>
					<div class="select-media">
						<img class="media-image"
							src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
							srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews( true ), $mediaItem->getFileSystem() ) ) ?>"
							alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
							sizes="160px"
						>
					</div>
				<?php endif ?>
			</div>

			<div class="col-3 select-name">
				<h2><?= $enc->html( $product->getName() ) ?></h2>

				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( array( 'b_prod', $id, 'prodid' ) ) ) ?>"
					value="<?= $enc->attr( $id ) ?>"
				>
			</div>

			<div class="col-3 select-attr">
				<ul class="attr-list">
					<?php foreach( $product->getRefItems( 'attribute', null, 'variant' ) as $attrItem ) : ?>
						<li class="attr-item">
							<span class="name"><?= $enc->html( $this->translate( 'client/code', $attrItem->getType() ) ) ?></span>
							<span class="value"><?= $enc->html( $attrItem->getName() ) ?></span>

							<input type="hidden" value="<?= $enc->attr( $attrItem->getId() ) ?>"
								name="<?= $enc->attr( $this->formparam( ['b_prod', $id, 'attrvarid', $attrItem->getType()] ) ) ?>"
							>
						</li>
					<?php endforeach ?>
				</ul>
			</div>

			<div class="col-1 select-stock" data-prodid="<?= $enc->attr( $id ) ?>"></div>

			<div class="col-2 select-quantity">
				<input type="number" class="form-control" placeholder="0"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $id, 'quantity'] ) ) ?>"
					min="0" max="2147483647" step="1" value="">
			</div>

		</div>

	<?php endforeach ?>

</div>
