<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2017
 */

/* Available data:
 * - attributeItems : List of attribute items including the referenced items like texts, images, etc.
 * - attributeConfigItems : List of configuration attributes
 * - attributeCustomItems : List of custom attributes
 * - attributeHiddenItems : List of hidden attributes
 */


$enc = $this->encoder();
$attrItems = $this->get( 'attributeItems', array() );

$attributeConfigItems = array();
foreach( $this->get( 'attributeConfigItems', array() ) as $id => $attribute )
{
	if( isset( $attrItems[$id] ) ) {
		$attributeConfigItems[$attribute->getType()][$id] = $attrItems[$id];
	}
}


/** client/html/catalog/attribute/preselect
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
 *  client/html/catalog/attribute/preselect = array(
 *      'width' => false,
 *      'color' => true,
 *  )
 *
 * Similarly, you can set the pre-selection for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/attribute/preselect/color = true
 *
 * @param boolean True to select the first option by default, false to display the select hint
 * @since 2017.04
 * @category Developer
 * @category User
 */

/** client/html/catalog/attribute/type
 * List of layout types for the optional attributes
 *
 * Each product can contain optional attributes and this configuration setting
 * allows you to change how these attributs will be displayed, either as
 * drop-down menu (value: "select") or as list of radio buttons (value:
 * "radio").
 *
 * The key for each value must be the type code of the attribute, e.g. "width",
 * "length", "color" or similar types. You can set the layout for all
 * attributes at once using e.g.
 *
 *  client/html/catalog/attribute/type = array(
 *      'width' => 'select',
 *      'color' => 'radio',
 *  )
 *
 * Similarly, you can set the layout type for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/attribute/type/color = radio
 *
 * Note: Up to 2015.10 this option was available as
 * client/html/catalog/detail/basket/attribute/type
 *
 * @param array List of attribute types as key and layout types as value, e.g. "select" or "radio"
 * @since 2015.10
 * @category Developer
 * @category User
 * @see client/html/catalog/selection/type
 */

/** client/html/catalog/attribute/type/color
 * Layout types for the color attribute
 *
 * @see client/html/catalog/attribute/type
 */

/** client/html/catalog/attribute/type/size
 * Layout types for the size attribute
 *
 * @see client/html/catalog/attribute/type
 */

?>
<ul class="selection">
	<?php foreach( $attributeConfigItems as $code => $attributes ) : ?>
		<?php $layout = $this->config( 'client/html/catalog/attribute/type/' . $code, 'select' ); ?>
		<?php $preselect = (bool) $this->config( 'client/html/catalog/attribute/preselect/' . $code, false ); ?>

		<li class="select-item <?php echo $enc->attr( $layout ) . ' ' . $enc->attr( $code ); ?>">
			<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $code ) ); ?></div>

			<?php $hintcode = $code . '-hint'; $hint = $enc->html( $this->translate( 'client/code', $hintcode ) ); ?>
			<?php if( !empty( $hint ) && $hint !== $hintcode ) : ?>
				<div class="select-hint"><?php echo $hint; ?></div>
			<?php endif; ?>

			<div class="select-value">

				<select class="select-list" name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid', $code ) ) ); ?>">
					<?php if( $preselect === false ) : ?>
						<option class="select-option" value=""><?php echo $enc->html( $this->translate( 'client', 'none' ) ); ?></option>
					<?php endif; ?>
					<?php foreach( $attributes as $id => $attribute ) : ?>
						<option class="select-option" value="<?php echo $enc->attr( $id ); ?>">

							<?php $priceItems = $attribute->getRefItems( 'price', 'default', 'default' ); ?>
							<?php if( ( $priceItem = reset( $priceItems ) ) !== false ) : ?>
								<?php $value = $priceItem->getValue() + $priceItem->getCosts(); ?>
								<?php echo $enc->html( sprintf( /// Configurable product attribute name (%1$s) with sign (%4$s, +/-), price value (%2$s) and currency (%3$s)
									$this->translate( 'client', '%1$s ( %4$s%2$s%3$s )' ),
									$attribute->getName(),
									$this->number( abs( $value ) ),
									$this->translate( 'client/currency', $priceItem->getCurrencyId() ),
									( $value < 0 ? '−' : '+' )
								), $enc::TRUST ); ?>
							<?php else : ?>
								<?php echo $enc->html( $attribute->getName(), $enc::TRUST ); ?>
							<?php endif; ?>

						</option>
					<?php endforeach; ?>
				</select>

			</div>
		</li>

	<?php endforeach; ?>
</ul>

<ul class="selection">
	<?php foreach( $this->get( 'attributeCustomItems', array() ) as $id => $attribute ) : ?>
		<li class="select-item <?php echo $enc->attr( $attribute->getCode() ); ?>">
			<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $attribute->getType() ) ); ?></div>

			<?php $hintcode = $attribute->getType() . '-hint'; $hint = $enc->html( $this->translate( 'client/code', $hintcode ) ); ?>
			<?php if( !empty( $hint ) && $hint !== $hintcode ) : ?>
				<div class="select-hint"><?php echo $hint; ?></div>
			<?php endif; ?>

			<div class="select-value">
				<input type="text" value=""
					name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrcustid', $id ) ) ); ?>"
					placeholder="<?php echo $enc->attr( $attribute->getName() ); ?>"
				/>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<?php foreach( $this->get( 'attributeHiddenItems', array() ) as $id => $attribute ) : ?>
	<input type="hidden"
		name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrhideid', $id ) ) ); ?>"
		value="<?php echo $enc->attr( $id ); ?>"
	/>
<?php endforeach; ?>
