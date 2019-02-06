<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

if( $this->get( 'standardUrlExternal', true ) )
{
	$namefcn = function( $view, $key ) {

		$key = (array) $key;
		$name = array_shift( $key );

		foreach( $key as $part ) {
			$name .= '[' . $part . ']';
		}

		return $name;
	};
}
else
{
	$namefcn = function( $view, $key ) {
		return $view->formparam( (array) $key );
	};
}

$testfcn = function( $list, $key, $default = '' ) {
	return ( isset( $list[$key] ) ? $list[$key] : $default );
};


$enc = $this->encoder();
$public = $hidden = [];
$errors = $this->get( 'standardErrorList', [] );
$params = $this->get( 'standardProcessParams', [] );

foreach( $params as $key => $item )
{
	if( $item->isPublic() ) {
		$public[$key] = $item;
	} else {
		$hidden[$key] = $item;
	}
}


/** client/html/checkout/standard/process/validate
 * List of regular expressions for validating the payment details
 *
 * To validate the payment input data of the customer, an individual Perl
 * compatible regular expression (http://php.net/manual/en/pcre.pattern.php)
 * can be applied to each field. Available fields are:
 * * payment.cardno
 * * payment.cvv
 * * payment.expirymonthyear
 *
 * To validate e.g the CVV security code, you can define a regular expression
 * like this to allow only three digits:
 *  client/html/checkout/standard/process/validate/payment.cvv = '^[0-9]{3}$'
 *
 * Several regular expressions can be defined line this:
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
<?php $this->block()->start( 'checkout/standard/process' ); ?>
<div class="checkout-standard-process">
	<h2><?= $enc->html( $this->translate( 'client', 'Payment' ), $enc::TRUST ); ?></h2>

	<?php if( !empty( $errors ) ) : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'Processing the payment failed' ), $enc::TRUST ); ?>
		</p>
	<?php elseif( !empty( $public ) ) : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'Please enter your payment details' ), $enc::TRUST ); ?>
		</p>
	<?php else : ?>
		<p class="order-notice">
			<?= $enc->html( $this->translate( 'client', 'You will now be forwarded to the next step' ), $enc::TRUST ); ?>
		</p>
	<?php endif; ?>


	<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'cp_payment' ) ) ); ?>" value="1" />

	<?php foreach( $hidden as $key => $item ) : ?>
		<?php if( is_array( $item->getDefault() ) ) : ?>

			<?php foreach( (array) $item->getDefault() as $key2 => $value ) : ?>
				<input type="hidden"
					name="<?= $enc->attr( $namefcn( $this, array( $item->getInternalCode(), $key2 ) ) ); ?>"
					value="<?= $enc->attr( $value ); ?>"
				/>
			<?php endforeach; ?>

		<?php else : ?>

			<input type="hidden"
				name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>"
				value="<?= $enc->attr( $item->getDefault() ); ?>"
			/>

		<?php endif; ?>
	<?php endforeach; ?>


	<ul class="form-list">
		<?php foreach( $public as $key => $item ) : ?>

			<li class="form-item <?= $key . ( $item->isRequired() ? ' mandatory' : ' optional' ); ?>"
				data-regex="<?= $testfcn( $regex, $key ); ?>">

				<label for="process-<?= $key; ?>">
					<?= $enc->html( $this->translate( 'client/code', $item->getCode() ), $enc::TRUST ); ?>
				</label>

				<?php switch( $item->getType() ) : case 'select': ?>
						<select id="process-<?= $key; ?>" name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>">
							<option value=""><?= $enc->html( $this->translate( 'client', 'Please select' ) ); ?></option>
							<?php foreach( (array) $item->getDefault() as $option ) : ?>
								<option value="<?= $enc->attr( $option ); ?>"><?= $enc->html( $option ); ?></option>
							<?php endforeach; ?>
						</select>

					<?php break; case 'boolean': ?>
						<input type="checkbox" id="process-<?= $key; ?>"
							name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>"
							value="<?= $enc->attr( $item->getDefault() ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ); ?>" />

					<?php break; case 'integer': case 'number': ?>
						<input type="number" id="process-<?= $key; ?>"
							name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>"
							value="<?= $enc->attr( $item->getDefault() ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ); ?>" />

					<?php break; case 'date': case 'datetime': case 'time': ?>
						<input type="<?= $attribute->getType(); ?>" id="process-<?= $key; ?>"
							name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>"
							value="<?= $enc->attr( $item->getDefault() ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ); ?>" />

					<?php break; default: ?>
						<input type="text" id="process-<?= $key; ?>"
							name="<?= $enc->attr( $namefcn( $this, $item->getInternalCode() ) ); ?>"
							value="<?= $enc->attr( $item->getDefault() ); ?>"
							placeholder="<?= $enc->attr( $this->translate( 'client/code', $key ) ); ?>" />

				<?php endswitch; ?>

			</li>

		<?php endforeach; ?>
	</ul>

	<?= $this->get( 'standardHtml', '' ); //Custom html from Provider ?>


	<div class="button-group">

		<?php if( !empty( $errors ) ) : ?>

			<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->standardUrlPayment ); ?>">
				<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ); ?>
			</a>
			<button class="btn btn-primary btn-lg btn-action">
				<?= $enc->html( $this->translate( 'client', 'Try again' ), $enc::TRUST ); ?>
			</button>

		<?php elseif( !empty( $public ) ) : ?>

			<a class="btn btn-default btn-lg" href="<?= $enc->attr( $this->standardUrlPayment ); ?>">
				<?= $enc->html( $this->translate( 'client', 'Change payment' ), $enc::TRUST ); ?>
			</a>
			<button class="btn btn-primary btn-lg btn-action" id="payment-button">
				<?= $enc->html( $this->translate( 'client', 'Pay now' ), $enc::TRUST ); ?>
			</button>

		<?php elseif( $this->get( 'standardMethod', 'POST' ) === 'GET' ) : ?>
			<?php
				$urlParams = [];
				$url = $this->get( 'standardUrlNext' );

				foreach( $params as $key => $item )
				{
					if( is_array( $item->getDefault() ) )
					{
						foreach( (array) $item->getDefault() as $key2 => $value ){
							$urlParams[] = $namefcn( $this, array( $item->getInternalCode(), $key2 ) ) . '=' . urlencode( $value );
						}
					}
					else
					{
						$urlParams[] = $namefcn( $this, $item->getInternalCode() ) . '=' . urlencode( $item->getDefault() );
					}
				}

				$char = ( strpos( $url, '?' ) === false ? '?' : '&' );
				$url .= $char . implode( '&', $urlParams );
			?>

			<a class="btn btn-primary btn-lg btn-action" href="<?= $enc->attr( $url ); ?>">
				<?= $enc->html( $this->translate( 'client', 'Proceed' ), $enc::TRUST ); ?>
			</a>

		<?php else : ?>

			<button class="btn btn-primary btn-lg btn-action">
				<?= $enc->html( $this->translate( 'client', 'Proceed' ), $enc::TRUST ); ?>
			</button>

		<?php endif; ?>

	</div>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/process' ); ?>
