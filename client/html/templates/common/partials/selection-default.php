<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014-2016
 */

/* Available data:
 * - products : List of variant product items without references
 * - productItems : List of product items including the referenced items like texts, attributes, etc.
 * - attributeItems : List of attribute items including the referenced items like texts, images, etc.
 * - mediaItems : List of media items including the referenced items like texts, images, etc.
 */


$index = 0;
$enc = $this->encoder();
$attrTypeDeps = $attrDeps = $prodDeps = array();

$articleItems = $this->get( 'products', array() );
$productItems = $this->get( 'productItems', array() );
$attributeItems = $this->get( 'attributeItems', array() );

foreach( $articleItems as $articleId => $articleItem )
{
	if( isset( $productItems[$articleId] ) )
	{
		foreach( $productItems[$articleId]->getRefItems( 'attribute', null, 'variant' ) as $attrId => $attrItem )
		{
			$attrTypeDeps[$attrItem->getType()][$attrId] = $attrItem->getPosition();
			$attrDeps[$attrId][] = $articleId;
			$prodDeps[$articleId][] = $attrId;
		}
	}
}

ksort( $attrTypeDeps );


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
 * @category Developer
 * @category User
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
 * @category Developer
 * @category User
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

?>
<?php foreach( $this->get( 'products', array() ) as $prodid => $product ) : ?>
	<?php $prices = $product->getRefItems( 'price', null, 'default' ); ?>

	<?php if( !empty( $prices ) ) : ?>
		<div class="price price-prodid-<?php echo $prodid; ?>">
			<?php echo $this->partial(
				$this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ),
				array( 'prices' => $prices )
			); ?>
		</div>
	<?php endif; ?>

<?php endforeach; ?>


<ul class="selection"
	data-proddeps="<?php echo $enc->attr( json_encode( $prodDeps ) ); ?>"
	data-attrdeps="<?php echo $enc->attr( json_encode( $attrDeps ) ); ?>">

	<?php foreach( $attrTypeDeps as $code => $positions ) : asort( $positions ); ?>
		<?php $layout = $this->config( 'client/html/catalog/selection/type/' . $code, 'select' ); ?>
		<?php $preselect = (bool) $this->config( 'client/html/catalog/selection/preselect/' . $code, false ); ?>

		<li class="select-item <?php echo $enc->attr( $layout ) . ' ' . $enc->attr( $code ); ?>">
			<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $code ) ); ?></div>
			<div class="select-value">

				<?php if( $layout === 'radio' ) : $first = true; ?>

					<ul class="select-list" data-index="<?php echo $index++; ?>" data-type="<?php echo $enc->attr( $code ); ?>">
						<?php foreach( $positions as $attrId => $position ) : ?>
							<?php if( isset( $attributeItems[$attrId] ) ) : ?>

								<li class="select-entry">
									<input class="select-option" type="radio"
										id="option-<?php echo $enc->attr( $attrId ); ?>"
										name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrvarid', $code ) ) ); ?>"
										value="<?php echo $enc->attr( $attrId ); ?>"
										<?php echo ( $preselect && $first ? 'checked="checked"' : '' ); $first = false ?>
									/>
									<label class="select-label" for="option-<?php echo $enc->attr( $attrId ); ?>"><!--

										<?php foreach( $attributeItems[$attrId]->getListItems( 'media', 'icon' ) as $listItem ) : ?>
											<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
												<?php echo '-->' . $this->partial( $this->config(
													'client/html/common/partials/media', 'common/partials/media-default.php' ),
													array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) )
												) . '<!--'; ?>
											<?php endif; ?>
										<?php endforeach; ?>

										--><span><?php echo $enc->html( $attributeItems[$attrId]->getName() ); ?></span><!--
									--></label>
								</li>

							<?php endif; ?>
						<?php endforeach; ?>
					</ul>

				<?php else : ?>

					<select class="select-list"
						name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrvarid', $code ) ) ); ?>"
						data-index="<?php echo $index++; ?>" data-type="<?php echo $enc->attr( $code ); ?>" >

						<?php if( $preselect === false ) : ?>
							<option class="select-option" value="">
								<?php echo $enc->attr( $this->translate( 'client', 'Please select' ) ); ?>
							</option>
						<?php endif; ?>

						<?php foreach( $positions as $attrId => $position ) : ?>
							<?php if( isset( $attributeItems[$attrId] ) ) : ?>
								<option class="select-option" value="<?php echo $enc->attr( $attrId ); ?>">
									<?php echo $enc->html( $attributeItems[$attrId]->getName() ); ?>
								</option>
							<?php endif; ?>
						<?php endforeach; ?>

					</select>

				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
