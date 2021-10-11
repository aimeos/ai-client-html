<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


$enc = $this->encoder();
$prefix = !$this->get( 'standardUrlExternal', true );


/** client/html/checkout/standard/process/validate
 * List of regular expressions for validating the payment details
 *
 * To validate the payment input data of the customer, an individual Perl
 * compatible regular expression (http://php.net/manual/en/pcre.pattern.php)
 * can be applied to each field. Available fields are:
 *
 * * payment.cardno
 * * payment.cvv
 * * payment.expirymonthyear
 *
 * To validate e.g the CVV security code, you can define a regular expression
 * like this to allow only three digits:
 *
 *  client/html/checkout/standard/process/validate/payment.cvv = '^[0-9]{3}$'
 *
 * Several regular expressions can be defined line this:
 *
 *  client/html/checkout/standard/process/validate = array(
 *   'payment.cardno' = '^[0-9]{16,19}$',
 *   'payment.cvv' = '^[0-9]{3}$',
 *  )
 *
 * Don't add any delimiting characters like slashes (/) to the beginning or the
 * end of the regular expression. They will be added automatically. Any slashes
 * inside the expression must be escaped by backlashes, i.e. "/".
 *
 * @param array Associative list of field names and regular expressions
 * @since 2015.07
 * @category User
 * @category Developer
 * @see client/html/checkout/standard/address/validate
 */
$defaultRegex = array( 'payment.cardno' => '^[0-9]{16,19}$', 'payment.cvv' => '^[0-9]{3}$' );
$regex = $this->config( 'client/html/checkout/standard/process/validate', $defaultRegex );


?>
<?php $this->block()->start( 'checkout/standard/process' ) ?>
<div class="checkout-standard-process">
	<h2><?= $enc->html( $this->translate( 'client', 'Payment' ), $enc::TRUST ) ?></h2>

	<?php if( !empty( $this->get( 'standardErrorList', [] ) ) ) : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'Processing the payment failed' ), $enc::TRUST ) ?>
		</p>
	<?php elseif( !empty( $this->get( 'standardProcessPublic', [] ) ) ) : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'Please enter your payment details' ), $enc::TRUST ) ?>
		</p>
	<?php else : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'You will now be forwarded to the next step' ), $enc::TRUST ) ?>
		</p>
	<?php endif ?>


	<input type="hidden" name="<?php echo $enc->attr( $this->formparam( ['cp_payment'], $prefix ) ) ?>" value="1">

	<?php foreach( $this->get( 'standardProcessHidden', [] ) as $id => $item ) : ?>
		<?php foreach( (array) $item->getDefault() as $key => $value ) : ?>
			<?php if( is_array( $item->getDefault() ) ) : ?>
				<input type="hidden" id="process-<?= $id ?>" value="<?= $enc->attr( $value ) ?>"
					name="<?= $enc->attr( $this->formparam( [$item->getInternalCode(), $key], $prefix ) ) ?>"
				>
			<?php else : ?>
				<input type="hidden" id="process-<?= $id ?>" value="<?= $enc->attr( $value ) ?>"
					name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>"
				>
			<?php endif ?>
		<?php endforeach ?>
	<?php endforeach ?>


	<div class="form-list">
		<?php foreach( $this->get( 'standardProcessPublic', [] ) as $key => $item ) : ?>
			<div class="row form-item form-group <?= $key . ( $item->isRequired() ? ' mandatory' : ' optional' ) ?>" data-regex="<?= $regex[$key] ?? '' ?>">

				<div class="col-md-6">
					<label for="process-<?= $key ?>">
						<?= $enc->html( $this->translate( 'client/code', $item->getCode() ), $enc::TRUST ) ?>
					</label>
				</div>

				<div class="col-md-6">
					<?php switch( $item->getType() ) : case 'select': ?>
							<select id="process-<?= $key ?>" name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>">
								<option value=""><?= $enc->html( $this->translate( 'client', 'Please select' ) ) ?></option>
								<?php foreach( (array) $item->getDefault() as $option ) : ?>
									<option value="<?= $enc->attr( $option ) ?>"><?= $enc->html( $option ) ?></option>
								<?php endforeach ?>
							</select>

						<?php break; case 'container': ?>
							<div id="process-<?= $key ?>"></div>

						<?php break; case 'boolean': ?>
							<input type="checkbox" id="process-<?= $key ?>"
								name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>"
								value="<?= $enc->attr( $item->getDefault() ) ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ) ?>"
							>

						<?php break; case 'integer': case 'number': ?>
							<input type="number" id="process-<?= $key ?>"
								name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>"
								value="<?= $enc->attr( $item->getDefault() ) ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ) ?>"
							>

						<?php break; case 'date': case 'datetime': case 'time': ?>
							<input type="<?= $attribute->getType() ?>" id="process-<?= $key ?>"
								name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>"
								value="<?= $enc->attr( $item->getDefault() ) ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ) ?>"
							>

						<?php break; default: ?>
							<input type="text" id="process-<?= $key ?>"
								name="<?= $enc->attr( $this->formparam( $item->getInternalCode(), $prefix ) ) ?>"
								value="<?= $enc->attr( $item->getDefault() ) ?>"
								placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ) ?>"
							>

					<?php endswitch ?>
				</div>

			</div>

		<?php endforeach ?>
	</div>


	<?= $this->get( 'standardHtml', '' ); //Custom html from Provider ?>


	<div class="button-group">

		<?php if( !empty( $this->get( 'standardErrorList', [] ) ) ) : ?>

			<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->standardUrlPayment ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ) ?>
			</a>
			<button class="btn btn-primary btn-lg btn-action">
				<?= $enc->html( $this->translate( 'client', 'Try again' ), $enc::TRUST ) ?>
			</button>

		<?php elseif( !empty( $this->get( 'standardProcessPublic', [] ) ) ) : ?>

			<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->standardUrlPayment ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ) ?>
			</a>
			<button class="btn btn-primary btn-lg btn-action" id="payment-button">
				<?= $enc->html( $this->translate( 'client', 'Pay now' ), $enc::TRUST ) ?>
			</button>

		<?php elseif( $this->get( 'standardMethod', 'POST' ) === 'GET' ) : ?>

			<a class="btn btn-primary btn-lg btn-action" href="<?= $enc->attr( $this->standardUrlNext ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Proceed' ), $enc::TRUST ) ?>
			</a>

		<?php elseif( $this->get( 'standardMethod', 'POST' ) === '' ) : ?>

			<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->standardUrlPayment ) ?>">
				<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ) ?>
			</a>

		<?php else : ?>

			<button class="btn btn-primary btn-lg btn-action">
				<?= $enc->html( $this->translate( 'client', 'Proceed' ), $enc::TRUST ) ?>
			</button>

		<?php endif ?>

	</div>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/process' ) ?>
