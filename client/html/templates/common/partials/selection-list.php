<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */

/* Available data:
 * - productItem : Selection product for the variant products
 * - productItems : List of variant product items with referenced items
 */


$enc = $this->encoder();


?>
<table class="selection">

	<tr>
		<th class="select-media"></th>
		<th class="select-name"><?= $enc->html( $this->translate( 'client', 'Name' ) ) ?></th>
		<th class="select-attr"><?= $enc->html( $this->translate( 'client', 'Variant' ) ) ?></th>
		<th class="select-stock"><?= $enc->html( $this->translate( 'client', 'Stock' ) ) ?></th>
		<th class="select-quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ) ) ?></th>
	</tr>

	<?php foreach( $this->get( 'productItems', [] ) as $id => $product ) : ?>

		<tr class="select-item">

			<td class="select-media">
				<?php if( ( $mediaItem = $product->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) : ?>
					<img class="media-image"
						src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>"
						srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
						alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
					>
				<?php endif ?>
			</td>

			<td class="select-name">
				<h2><?= $enc->html( $product->getName() ) ?></h2>

				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( array( 'b_prod', $id, 'prodid' ) ) ) ?>"
					value="<?= $enc->attr( $this->productItem->getId() ) ?>"
				>
			</td>

			<td class="select-attr">
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
			</td>

			<td class="select-stock" data-prodid="<?= $enc->attr( $id ) ?>"></td>

			<td class="select-quantity">
				<input type="number" class="form-control" placeholder="0"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $id, 'quantity'] ) ) ?>"
					min="1" max="2147483647" step="1" value="">
			</td>

		</tr>

	<?php endforeach ?>

</table>
