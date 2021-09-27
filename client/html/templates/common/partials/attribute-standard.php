<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/* Available data:
 * - productItem : Product item the attributes are associated to
 */


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


$enc = $this->encoder();


?>
<script type="text/javascript">
    function removeFile(event) {
        console.log($(event).parents('.upload-file-entry').remove());
    }
    function slugName(name) {
        return name.toLowerCase().replaceAll(' ', '_').replaceAll('.', '_');
    }
    function updateFileList() {
        $('input[type=file]').each((i, input) => {
            $('.upload-file-list').html('');
            if(input.files && input.files.length > 0) {
                $('.upload-view').addClass('bg-success');
                $('.upload-view').attr('data-title', $('.upload-view').attr('data-title').replace('hinzufügen', 'ändern'));
                for(let file of input.files) {
                    const localUrl = URL.createObjectURL(file);
                    $el = $('<div class="upload-file-entry"></div>');
                    $el.attr('id', `file-${slugName(file.name)}`);
                    $el.append(`<img src="${localUrl}" alt="image">`);
                    $el.append(`<span class="m-2">${file.name}</span>`);
                    $('.upload-file-list').append($el);
                }
            }
        })
    }
    if (window.fileListInterval) {
        clearInterval(window.fileListInterval);
        window.fileListInterval = null;
    }

</script>
<ul class="selection">

	<?php foreach( $this->typemap( $this->productItem->getRefItems( 'attribute', null, 'config' ) ) as $code => $attributes ) : ?>

		<li class="select-item <?= $enc->attr( $code . ' ' . $this->config( 'client/html/catalog/attribute/type/' . $code, 'select' ) ) ?>">
			<label for="select-<?= $enc->attr( $code ) ?>" class="select-name"><?= $enc->html( $this->translate( 'client/code', $code ) ) ?></label>

			<?php if( $hint = $this->translate( 'client/code', $code . '-hint', null, 0, false ) ) : ?>
				<div class="select-hint"><?= $enc->html( $hint ) ?></div>
			<?php endif ?>

			<div class="select-value">

				<?php if( $this->config( 'client/html/catalog/attribute/type/' . $code, 'select' ) === 'input' ) : ?>

					<ul id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $code ) ?>" class="select-list">

						<?php foreach( $attributes as $attrId => $attribute ) : ?>

							<li class="input-group select-entry">
								<input type="hidden" value="<?= $enc->attr( $attrId ) ?>"
									name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrconfid', 'id', ''] ) ) ?>"
								>
								<input class="form-control select-option" id="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>" type="number" value="0" step="1" min="0"
									name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrconfid', 'qty', ''] ) ) ?>"
								><!--
								--><label class="form-control select-label" for="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>">
									<?= $enc->html( $this->attrname( $attribute ) ) ?>
								</label>
							</li>

						<?php endforeach ?>

					</ul>

				<?php elseif( $this->config( 'client/html/catalog/attribute/type/' . $code, 'select' ) === 'radio' ) : ?>

					<ul id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $code ) ?>" class="select-list">

						<?php foreach( $attributes as $attrId => $attribute ) : ?>

							<li class="select-entry">
								<input class="select-option" type="radio"
									id="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>"
									name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrconfid', 'id', ''] ) ) ?>"
									value="<?= $enc->attr( $attrId ) ?>"
								>
								<label class="select-label" for="option-<?= $enc->attr( $this->productItem->getId() . '-' . $attrId ) ?>"><!--

									<?php foreach( $attribute->getListItems( 'media', 'default', 'icon' ) as $listItem ) : ?>
										<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>

											<?= '-->' . $this->partial( $this->config(
												'client/html/common/partials/media', 'common/partials/media-standard' ),
												['item' => $item, 'boxAttributes' => ['class' => 'media-item']]
											) . '<!--' ?>

										<?php endif ?>
									<?php endforeach ?>

									--><span><?= $enc->html( $attribute->getName() ) ?></span><!--
								--></label>
							</li>

						<?php endforeach ?>

					</ul>

				<?php else : ?>

					<input type="hidden" value="1" name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrconfid', 'qty', ''] ) ) ?>">
					<select id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $code ) ?>" class="form-control select-list"
						name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrconfid', 'id', ''] ) ) ?>">

						<?php if( $this->config( 'client/html/catalog/attribute/preselect/' . $code, false ) === false ) : ?>
							<option class="select-option" value=""><?= $enc->html( $this->translate( 'client', 'none' ) ) ?></option>
						<?php endif ?>

						<?php foreach( $attributes as $id => $attribute ) : ?>

							<option class="select-option" value="<?= $enc->attr( $id ) ?>">
								<?= $enc->html( $this->attrname( $attribute ) ) ?>
							</option>

						<?php endforeach ?>

					</select>

				<?php endif ?>

			</div>
		</li>

	<?php endforeach ?>

</ul>


<ul class="selection">

	<?php foreach( $this->productItem->getRefItems( 'attribute', null, 'custom' ) as $id => $attribute ) : $key = $attribute->getType() . '-' . $attribute->getCode() ?>
		<li class="select-item <?= $enc->attr( $key ) ?>">
            <?php if ( $attribute->getType() !== 'upload' ) { ?>
			    <label for="select-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>" class="select-name"><?= $enc->html( $this->translate( 'client/code', $attribute->getName() ) ) ?></label>
            <?php } ?>

			<?php if( $hint = $this->translate( 'client/code', $key . '-hint', null, 0, false ) ) : ?>
				<div class="select-hint"><?= $enc->html( $hint ) ?></div>
			<?php endif ?>

			<div class="select-value">

				<?php switch( $attribute->getType() ) : case 'price': ?>
					<input id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>" class="form-control" type="number" min="0.01" step="0.01"
						name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrcustid', $id] ) ) ?>"
						<?php if( $price = $this->productItem->getRefItems( 'price', 'default', 'default' )->first() ) : ?>
							value="<?= $enc->attr( $price->getValue() ) ?>"
						<?php endif ?>
					>
				<?php break; case 'date': ?>
					<input id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>" class="form-control" type="date" name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrcustid', $id] ) ) ?>">
                <?php break;case 'upload': ?>
                    <div class="upload-wrapper">
                        <div class="upload-view"
                             data-title="<?= $enc->html( $this->translate( 'client/code', $attribute->getName() ) ) ?> Klicken zum hinzufügen."
                             onclick="document.querySelector('#upload-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>').click()"
                        >
                        </div>
                        <input id="upload-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>"
                               class="form-control hidden"
                               type="file"
                               name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrcustid', $id] ) ) ?>[]"
                               multiple
                               onchange="updateFileList()"
                        >
                        <div class="upload-file-list p-1">
                        </div>
                    </div>
				<?php break; default: ?>
					<input id="select-<?= $enc->attr( $this->productItem->getId() . '-' . $key ) ?>" class="form-control" type="text" name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'attrcustid', $id] ) ) ?>">
				<?php endswitch ?>
			</div>
		</li>

	<?php endforeach ?>

</ul>
