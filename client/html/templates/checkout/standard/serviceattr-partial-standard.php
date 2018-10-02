<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */

/* Available data:
 * - id : ID of the service option (required)
 * - type : Type of the service attributes ("delivery" or "payment", required)
 * - attributes : Associative list of codes as keys and \Aimeos\MW\Criteria\Attribute\Iface objects as values
 * - orderService : Order service item that contains the available attributes
 * - css : List of CSS classes for the attribute fields
 */

/*
 * Displays the required and optional service attributes depending on their type
 *
 * Only "code", "type", "default" and "required" are relevant as the rest is for internal use.
 * Dump the array for each service option by using print_r() to see what's available. See also
 * https://github.com/aimeos/aimeos-core/blob/master/lib/mwlib/src/MW/Criteria/Attribute/Iface.php
 *
 * The key (e.g. "time.hourminute") must be in the name property of the input/select field:
 * - $this->formparam( ['c_delivery', $id, 'time.hourminute'] ) or
 * - $this->formparam( ['c_payment', $id, 'directdebit.name'] )
 *
 * $id is the unique ID of the delivery/payment option. The value for the input tag should be
 * the value that was entered by the user before (in case of errors), the value that is already
 * stored in the basket as order service attribute the attribute default value:
 * - $value = ($orderService->getAttribute( $key ) ?: $attribute->getDefault())
 * - $this->param( 'c_delivery/' . $id . '/' . $key, $value )
 *
 * For select tags and lists of options the customer can choose from, $attribute->getDefault()
 * returns a list of available options as code/value pairs.
 *
 * The label of the attribute item is only for internal use. To be able to translate all strings
 * to different languages, you should use the attribute codes resp. the select list codes and
 * pass them to
 * - $this->translate( 'client/code', $code )
 *
 * If you have values that should be named differently depending on the attribute, you can prefix
 * them with an arbitrary string or the code of the attribute.
 */

$enc = $this->encoder();

$id = $this->id;
$type = $this->type;
$css = $this->get( 'css', [] );
$orderService = $this->get( 'orderService' );


?>
<ul class="form-list form-horizontal">

	<?php foreach( $this->get( 'attributes', [] ) as $key => $attribute ) : ?>
		<?php
			if( !isset( $orderService ) || (
				( $value = $orderService->getAttribute( $key . '/hidden' ) ) === null
				&& ( $value = $orderService->getAttribute( $key ) ) === null )
			) {
				$value = $attribute->getDefault();
			}
		?>
		<?php $css = ( isset( $css[$key] ) ? ' ' . join( ' ', $css[$key] ) : '' ) . ( $attribute->isRequired() ? ' mandatory' : '' ); ?>

		<li class="form-item form-group <?= $enc->attr( $key ) . $css; ?>">

			<label class="col-md-5 form-item-label" for="<?= $enc->attr( $type . '-' . $key ); ?>">
				<?= $enc->html( $this->translate( 'client/code', $key ) ); ?>
			</label>

			<?php switch( $attribute->getType() ) : case 'select': ?>

					<div class="col-md-7">
						<select class="form-control form-item-value" id="<?= $enc->attr( $type . '-' . $key ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>">

							<?php foreach( (array) $attribute->getDefault() as $option ) : $code = $key . ':' . $option; ?>
								<option value="<?= $enc->attr( $option ); ?>">
									<?= $enc->html( $this->translate( 'client/code', $code ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

				<?php break; case 'list': ?>

					<div class="form-item-value col-md-7">
						<?php $checked = 'checked="checked"'; ?>
						<?php foreach( $attribute->getDefault() as $code => $name ) : ?>
							<input class="form-control" type="radio" id="<?= $enc->attr( $type . '-' . $key . '-' . $code ); ?>"
								name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>"
								selected="<?= ( $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) === $code ? 'selected' : '' ); ?>"
								value="<?= $code ?>" <?= $checked; ?>
							/>
							<label for="<?= $enc->attr( $type . '-' . $key . '-' . $code ); ?>" class="attr-list-item">
								<?= nl2br( $enc->html( $name ) ); ?>
							</label>
							<?php $checked = ''; ?>
						<?php endforeach; ?>
					</div>

				<?php break; case 'boolean': ?>

					<div class="col-md-7">
						<input class="form-control col-md-7 form-item-value" type="checkbox" id="<?= $enc->attr( $type . '-' . $key ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>" value="1"
							<?= $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) ? 'checked="checked"' : '' ?>
						/>
					</div>

				<?php break; case 'integer': case 'number': ?>

					<div class="col-md-7">
						<input class="form-control col-md-7 form-item-value" type="number" id="<?= $enc->attr( $type . '-' . $key ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) ); ?>"
						/>
					</div>

				<?php break; case 'date': case 'datetime': case 'time': ?>

					<div class="col-md-7">
						<input class="form-control col-md-7" type="<?= $attribute->getType(); ?>"
							id="<?= $enc->attr( $type . '-' . $key ); ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) ); ?>"
						/>
					</div>

				<?php break; case 'text': ?>

					<div class="col-md-7">
						<textarea class="form-control col-md-7"
							id="<?= $enc->attr( $type . '-' . $key ); ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>"
						><?= $enc->html( $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) ); ?></textarea>
					</div>

				<?php break; default: ?>

					<div class="col-md-7">
						<input class="form-control col-md-7 form-item-value" type="text" id="<?= $enc->attr( $type . '-' . $key ); ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $type, $id, $key ) ) ); ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $type . '/' . $id . '/' . $key, $value ) ); ?>"
						/>
					</div>

			<?php endswitch; ?>

		</li>
	<?php endforeach; ?>

</ul>
