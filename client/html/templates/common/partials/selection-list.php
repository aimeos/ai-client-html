<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */

/* Available data:
 * - products : List of variant product items with referenced items
 */


$enc = $this->encoder();


?>
<table class="selection">

	<?php foreach( $this->get( 'products', [] ) as $id => $product ) : ?>

		<tr class="select-item">

			<td class="select-media">
				<?php if( ( $mediaItem = current( $product->getRefItems( 'media', 'default', 'default' ) ) ) !== false ) : ?>
					<img class="media-image" src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>" />
				<?php endif; ?>
			</td>

			<td class="select-name">
				<h2><?= $enc->html( $product->getName() ); ?></h2>

				<input type="hidden" value="add"
					name="<?= $enc->attr( $this->formparam( 'b_action' ) ); ?>" />
				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( array( 'b_prod', $id, 'prodid' ) ) ); ?>"
					value="<?= $enc->attr( $this->detailProductItem->getId() ); ?>" />
			</td>

			<td class="select-attr">
				<?php foreach( $product->getRefItems( 'attribute', null, 'variant' ) as $attrItem ) : ?>

					<span class="attr-name"><?= $enc->html( $this->translate( 'client/code', $attrItem->getType() ) ) ?></span>
					<span class="attr-value"><?= $enc->html( $attrItem->getName() ) ?></span>

					<input type="hidden" value="<?= $enc->attr( $attrItem->getId() ); ?>"
						name="<?= $enc->attr( $this->formparam( ['b_prod', $id, 'attrvarid', $attrItem->getType()] ) ); ?>" />

				<?php endforeach; ?>
			</td>

			<td class="select-quantity">
				<input type="number" class="form-control"
					name="<?= $enc->attr( $this->formparam( ['b_prod', $id, 'quantity'] ) ); ?>"
					min="1" max="2147483647" maxlength="10" step="1" value="" />
			</td>

		</tr>

	<?php endforeach; ?>

</table>
