<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

/* Available data:
 * - productItem : Product item the attributes are associated with (optional)
 * - attributeConfigItems : List of configuration attributes
 * - attributeCustomItems : List of custom attributes
 * - attributeHiddenItems : List of hidden attributes
 */


$enc = $this->encoder();

$attributeConfigItems = [];
foreach( $this->get( 'attributeConfigItems', [] ) as $id => $attribute ) {
	$attributeConfigItems[$attribute->getType()][$id] = $attribute;
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

		<li class="select-item <?= $enc->attr( $layout ) . ' ' . $enc->attr( $code ); ?>">
			<div class="select-name"><?= $enc->html( $this->translate( 'client/code', $code ) ); ?></div>

			<?php $hintcode = $code . '-hint'; $hint = $enc->html( $this->translate( 'client/code', $hintcode ) ); ?>
			<?php if( !empty( $hint ) && $hint !== $hintcode ) : ?>
				<div class="select-hint"><?= $hint; ?></div>
			<?php endif; ?>

			<div class="select-value">

				<?php if( $layout === 'input' ) : ?>

					<ul class="select-list">
						<?php foreach( $attributes as $attrId => $attribute ) : ?>

							<li class="input-group select-entry">
								<input type="hidden" value="<?= $enc->attr( $attrId ); ?>"
									name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid', 'id', '' ) ) ); ?>"
								/>
								<input class="form-control select-option" id="option-<?= $enc->attr( $attrId ); ?>" type="number" value="0" step="1" min="0"
									name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid', 'qty', '' ) ) ); ?>"
								/><label class="form-control select-label" for="option-<?= $enc->attr( $attrId ); ?>">

									<?php $priceItems = $attribute->getRefItems( 'price', 'default', 'default' ); ?>
									<?php if( ( $priceItem = reset( $priceItems ) ) !== false ) : ?>
										<?php $value = $priceItem->getValue() + $priceItem->getCosts(); ?>
										<?= $enc->html( sprintf( /// Configurable product attribute name (%1$s) with sign (%4$s, +/-), price value (%2$s) and currency (%3$s)
											$this->translate( 'client', '%1$s ( %4$s%2$s%3$s )' ),
											$attribute->getName(),
											$this->number( abs( $value ) ),
											$this->translate( 'currency', $priceItem->getCurrencyId() ),
											( $value < 0 ? '−' : '+' )
										), $enc::TRUST ); ?>
									<?php else : ?>
										<?= $enc->html( $attribute->getName(), $enc::TRUST ); ?>
									<?php endif; ?>

								</label>
							</li>

						<?php endforeach; ?>
					</ul>

				<?php else : ?>

					<input type="hidden" value="1"
						name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid', 'qty', '' ) ) ); ?>"
					/>

					<select class="form-control select-list" name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid', 'id', '' ) ) ); ?>">
						<?php if( $preselect === false ) : ?>
							<option class="select-option" value=""><?= $enc->html( $this->translate( 'client', 'none' ) ); ?></option>
						<?php endif; ?>
						<?php foreach( $attributes as $id => $attribute ) : ?>
							<option class="select-option" value="<?= $enc->attr( $id ); ?>">

								<?php $priceItems = $attribute->getRefItems( 'price', 'default', 'default' ); ?>
								<?php if( ( $priceItem = reset( $priceItems ) ) !== false ) : ?>
									<?php $value = $priceItem->getValue() + $priceItem->getCosts(); ?>
									<?= $enc->html( sprintf( /// Configurable product attribute name (%1$s) with sign (%4$s, +/-), price value (%2$s) and currency (%3$s)
										$this->translate( 'client', '%1$s ( %4$s%2$s%3$s )' ),
										$attribute->getName(),
										$this->number( abs( $value ) ),
										$this->translate( 'currency', $priceItem->getCurrencyId() ),
										( $value < 0 ? '−' : '+' )
									), $enc::TRUST ); ?>
								<?php else : ?>
									<?= $enc->html( $attribute->getName(), $enc::TRUST ); ?>
								<?php endif; ?>

							</option>
						<?php endforeach; ?>
					</select>

				<?php endif; ?>

			</div>
		</li>

	<?php endforeach; ?>
</ul>

<ul class="selection">
	<?php foreach( $this->get( 'attributeCustomItems', [] ) as $id => $attribute ) : ?>
		<li class="select-item <?= $enc->attr( $attribute->getCode() ); ?>">
			<div class="select-name"><?= $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ); ?></div>

			<?php $hintcode = $attribute->getType() . '-hint'; $hint = $enc->html( $this->translate( 'client/code', $hintcode ) ); ?>
			<?php if( !empty( $hint ) && $hint !== $hintcode ) : ?>
				<div class="select-hint"><?= $hint; ?></div>
			<?php endif; ?>

			<div class="select-value">
				<?php switch( $attribute->getType() ) : case 'price': ?>
					<input class="form-control" type="number" min="0.01" step="0.01"
						name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrcustid', $id ) ) ); ?>"
						<?php if( isset( $this->productItem ) && ( $prices = $this->productItem->getRefItems( 'price', 'default', 'default' ) ) !== [] && ( $price = reset( $prices ) ) !== false ) : ?>
							value="<?= $enc->attr( $price->getValue() ); ?>"
						<?php endif; ?>
					/>
				<?php break; case 'date': ?>
					<input class="form-control" type="date" name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrcustid', $id ) ) ); ?>" />
				<?php break; default: ?>
					<input class="form-control" type="text" name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrcustid', $id ) ) ); ?>" />
				<?php endswitch; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
