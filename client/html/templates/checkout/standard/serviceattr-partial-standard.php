<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */

/* Available data:
 * - id : ID of the service option (required)
 * - type : Type of the service attributes ("delivery" or "payment", required)
 * - attributes : Associative list of codes as keys and \Aimeos\MW\Criteria\Attribute\Iface objects as values (required)
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
 * $id is the unique ID of the delivery/payment option. The value for the input tag is
 * available as $item->value.
 *
 * For select tags and lists of options the customer can choose from, $item->getDefault()
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


?>
<ul class="form-list form-horizontal">

	<?php foreach( $this->get( 'attributes', [] ) as $key => $item ) : ?>

		<li class="row form-item form-group <?= $enc->attr( $key ) . ( $item->isRequired() ? ' mandatory' : '' ) ?>">

			<div class="col-md-5">
				<label class="form-item-label" for="<?= $enc->attr( $this->type . '-' . $key ) ?>">
					<?= $enc->html( $this->translate( 'client/code', $key ) ) ?>
				</label>
			</div>

			<?php switch( $item->getType() ) : case 'select': ?>

					<div class="col-md-7">
						<select class="form-control form-item-value" id="<?= $enc->attr( $this->type . '-' . $key ) ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>">

							<?php foreach( (array) $item->getDefault() as $option ) : $code = $key . ':' . $option ?>
								<option value="<?= $enc->attr( $option ) ?>">
									<?= $enc->html( $this->translate( 'client/code', $code ) ) ?>
								</option>
							<?php endforeach ?>
						</select>
					</div>

				<?php break; case 'list': ?>

					<div class="form-item-value col-md-7">
						<?php foreach( (array) $item->getDefault() as $code => $val ) : ?>
							<input class="form-control" type="radio" id="<?= $enc->attr( $this->type . '-' . $key . '-' . $code ) ?>"
								name="<?= $enc->attr( $this->formparam( ['c_' . $this->type, $this->id, $key] ) ) ?>" value="<?= $enc->attr( $code ) ?>"
								<?= $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) == $code ? 'checked="checked"' : '' ?>
							>
							<label for="<?= $enc->attr( $this->type . '-' . $key . '-' . $code ) ?>" class="attr-list-item">
								<?= nl2br( $enc->html( $this->translate( 'client/code', $val ) ) ) ?>
							</label>
						<?php endforeach ?>
					</div>

				<?php break; case 'boolean': ?>

					<div class="col-md-7">
						<input class="form-control form-item-value" type="checkbox" id="<?= $enc->attr( $this->type . '-' . $key ) ?>"
							name="<?= $enc->attr( $this->formparam( ['c_' . $this->type, $this->id, $key] ) ) ?>" value="1"
							<?= $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ? 'checked="checked"' : '' ?>
						>
					</div>

				<?php break; case 'integer': case 'number': ?>

					<div class="col-md-7">
						<input class="form-control form-item-value" type="number" id="<?= $enc->attr( $this->type . '-' . $key ) ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?>"
						>
					</div>

				<?php break; case 'date': ?>

					<div class="col-md-7">
						<input class="form-control" type="<?= $item->getType() ?>"
							id="<?= $enc->attr( $this->type . '-' . $key ) ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'YYYY-MM-DD' ) ) ?>"
						>
					</div>

				<?php break; case 'datetime': ?>

					<div class="col-md-7">
						<input class="form-control" type="<?= $item->getType() ?>"
							id="<?= $enc->attr( $this->type . '-' . $key ) ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'YYYY-MM-DD HH:mm' ) ) ?>"
						>
					</div>

				<?php break; case 'time': ?>

					<div class="col-md-7">
						<input class="form-control" type="<?= $item->getType() ?>"
							id="<?= $enc->attr( $this->type . '-' . $key ) ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client', 'HH:mm' ) ) ?>"
						>
					</div>

				<?php break; case 'text': ?>

					<div class="col-md-7">
						<textarea class="form-control"
							id="<?= $enc->attr( $this->type . '-' . $key ) ?>" class="form-item-value"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
						><?= $enc->html( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?></textarea>
					</div>

				<?php break; default: ?>

					<div class="col-md-7">
						<input class="form-control form-item-value" type="text" id="<?= $enc->attr( $this->type . '-' . $key ) ?>"
							name="<?= $enc->attr( $this->formparam( array( 'c_' . $this->type, $this->id, $key ) ) ) ?>"
							value="<?= $enc->attr( $this->param( 'c_' . $this->type . '/' . $this->id . '/' . $key, $item->value ?? null ) ) ?>"
						>
					</div>

			<?php endswitch ?>

		</li>
	<?php endforeach ?>

</ul>
