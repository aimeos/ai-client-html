<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014-2022
 */

/* Available data:
 * - productItem : Selection product for the variant products
 * - productItems : List of product items including the referenced items like texts, attributes, etc.
 */


/** client/html/catalog/selection/preselect
 * Pre-select first item in selection list
 *
 * No option of the available selections for a product is pre-selected by
 * default. This setting removes the hint to select an option, so the first one
 * is selected by default.
 *
 * The key for each value must be the type code of the attribute, e.g. "width",
 * "length", "color" or similar types. You can set the layout for all
 * attributes at once using e.g.
 *
 *  client/html/catalog/selection/preselect = array(
 *      'width' => false,
 *      'color' => true,
 *  )
 *
 * Similarly, you can set the pre-selection for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/selection/preselect/color = true
 *
 * @param boolean True to select the first option by default, false to display the select hint
 * @since 2016.07
 */

/** client/html/catalog/selection/type
 * List of layout types for the variant attributes
 *
 * Selection products will contain variant attributes and this configuration
 * setting allows you to change how these attributs will be displayed, either
 * as drop-down menu (value: "select") or as list of radio buttons (value:
 * "radio").
 *
 * The key for each value must be the type code of the attribute, e.g. "width",
 * "length", "color" or similar types. You can set the layout for all
 * attributes at once using e.g.
 *
 *  client/html/catalog/selection/type = array(
 *      'width' => 'select',
 *      'color' => 'radio',
 *  )
 *
 * Similarly, you can set the layout type for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/selection/type/color = radio
 *
 * Note: Up to 2016.10 this option was available as
 * client/html/catalog/detail/basket/selection/type
 *
 * @param array List of attribute types as key and layout types as value, e.g. "select" or "radio"
 * @since 2015.10
 * @see client/html/catalog/attribute/type
 */

/** client/html/catalog/selection/type/length
 * Layout types for the length selection
 *
 * @see client/html/catalog/selection/type
 */

/** client/html/catalog/selection/type/width
 * Layout types for the width selection
 *
 * @see client/html/catalog/selection/type
 */


$enc = $this->encoder();
$attrDeps = $prodDeps = [];
$attrItems = map();
$index = 0;

foreach( $this->get( 'productItems', [] ) as $prodId => $product )
{
	$attrItems->replace( $product->getRefItems( 'attribute', null, ['default', 'variant'] ) );

	foreach( $product->getRefItems( 'attribute', null, 'variant' ) as $attrId => $attrItem )
	{
		$attrDeps[$attrId][] = $prodId;
		$prodDeps[$prodId][] = $attrId;
	}
}

$sortfcn = function( $itemA, $itemB ) {
	return $itemA->getPosition() <=> $itemB->getPosition() ?: $itemA->getName() <=> $itemB->getName();
};


?>
<ul class="selection"
	data-proddeps="<?= $enc->attr( json_encode( $prodDeps ) ) ?>"
	data-attrdeps="<?= $enc->attr( json_encode( $attrDeps ) ) ?>">

	<?php foreach( $attrItems->uasort( $sortfcn )->groupBy( 'attribute.type' )->ksort() as $code => $list ) : ?>

		<li class="select-item <?= $enc->attr( $code . ' ' . $this->config( 'client/html/catalog/selection/type/' . $code, 'select' ) ) ?>">
			<label class="select-name"><?= $enc->html( $this->translate( 'client/code', $code ) ) ?></label>

			<?php if( $hint = $this->translate( 'client/code', $code . '-hint', null, 0, false ) ) : ?>
				<div class="select-hint"><?= $enc->html( $hint ) ?></div>
			<?php endif ?>

			<div class="select-value">

				<?php if( $this->config( 'client/html/catalog/selection/type/' . $code, 'select' ) === 'radio' ) : $first = true ?>

					<ul id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $code ) ?>" class="select-list" data-index="<?= $index++ ?>" data-type="<?= $enc->attr( $code ) ?>">

						<?php foreach( $list as $attrId => $attrItem ) : ?>

							<li class="select-entry">
								<input class="select-option" type="radio"
									id="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>"
									name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrvarid', $code] ) ) ?>"
									value="<?= $enc->attr( $attrId ) ?>"
									<?= ( $first && $this->config( 'client/html/catalog/selection/preselect/' . $code, false ) ? 'checked="checked"' : '' ); $first = false ?>
								>
								<label class="select-label" for="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>"><!--

									<?php foreach( $attrItem->getListItems( 'media', 'default', 'icon' ) as $listItem ) : ?>
										<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>

											<?= '-->' . $this->partial( $this->config(
												'client/html/common/partials/media', 'common/partials/media' ),
												['item' => $item, 'boxAttributes' => ['class' => 'media-item']]
											) . '<!--' ?>

										<?php endif ?>
									<?php endforeach ?>

									--><span><?= $enc->html( $attrItem->getName() ) ?></span><!--
								--></label>
							</li>

						<?php endforeach ?>

					</ul>

				<?php else : ?>

					<select id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $code ) ?>" class="form-control select-list"
						name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrvarid', $code] ) ) ?>"
						data-index="<?= $index++ ?>" data-type="<?= $enc->attr( $code ) ?>"
					>

						<?php if( !$this->config( 'client/html/catalog/selection/preselect/' . $code, false ) ) : ?>

							<option class="select-option" value="">
								<?= $enc->attr( $this->translate( 'client', 'Please select' ) ) ?>
							</option>

						<?php endif ?>

						<?php foreach( $list as $attrId => $attrItem ) : ?>

							<option class="select-option" value="<?= $enc->attr( $attrId ) ?>">
								<?= $enc->html( $attrItem->getName() ) ?>
							</option>

						<?php endforeach ?>

					</select>

				<?php endif ?>

			</div>
		</li>

	<?php endforeach ?>

</ul>
