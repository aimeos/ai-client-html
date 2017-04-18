<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$services = $this->get( 'paymentServices', [] );
$servicePrices = $this->get( 'paymentServicePrices', [] );
$serviceAttributes = $this->get( 'paymentServiceAttributes', [] );

try
{
	$orderService = $this->standardBasket->getService( 'payment' );
	$orderServiceId = $orderService->getServiceId();
}
catch( Exception $e )
{
	$orderService = null;
	$orderServiceId = null;

	if( ( $service = reset( $services ) ) !== false ) {
		$orderServiceId = $service->getId();
	}
}

$serviceOption = $this->param( 'c_paymentoption', $orderServiceId );

$paymentCss = [];
foreach( $this->get( 'paymentError', [] ) as $name => $msg ) {
	$paymentCss[$name][] = 'error';
}

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


?>
<?php $this->block()->start( 'checkout/standard/payment' ); ?>
<section class="checkout-standard-payment">

	<h1><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ); ?></h1>
	<p class="note"><?= $enc->html( $this->translate( 'client', 'Please choose your payment method' ), $enc::TRUST ); ?></p>

	<?php foreach( $services as $id => $service ) : ?>
		<div id="c_payment-<?= $enc->attr( $id ); ?>" class="item item-service">
			<label class="description" for="c_paymentoption-<?= $enc->attr( $id ); ?>">

				<input class="option" type="radio"
					id="c_paymentoption-<?= $enc->attr( $id ); ?>"
					name="<?= $enc->attr( $this->formparam( array( 'c_paymentoption' ) ) ); ?>"
					value="<?= $enc->attr( $id ); ?>"
					<?= ( $id == $serviceOption ? 'checked="checked"' : '' ); ?>
				/>

				<?php if( isset( $servicePrices[$id] ) ) : ?>
					<?php $currency = $this->translate( 'client/currency', $servicePrices[$id]->getCurrencyId() ); ?>
					<?php if( $servicePrices[$id]->getValue() > 0 ) : ?>
						<span class="price-value">
							<?= $enc->html( sprintf( /// Service fee value (%1$s) and shipping cost value (%2$s) with currency (%3$s)
								$this->translate( 'client', '%1$s%3$s + %2$s%3$s' ),
								$this->number( $servicePrices[$id]->getValue() ),
								$this->number( $servicePrices[$id]->getCosts() ),
								$currency )
							); ?>
						</span>
					<?php elseif( $servicePrices[$id]->getCosts() > 0 ) : ?>
						<span class="price-value">
							<?= $enc->html( sprintf(
								$priceFormat,
								$this->number( $servicePrices[$id]->getCosts() ),
								$currency )
							); ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>

				<div class="icons">
					<?php foreach( $service->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
						<?= $this->partial(
							$this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ),
							array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'icon' ) )
						); ?>
					<?php endforeach; ?>
				</div>

				<h2><?= $enc->html( $service->getName() ); ?></h2>

				<?php foreach( $service->getRefItems( 'text', null, 'default' ) as $textItem ) : ?>
					<?php if( ( $type = $textItem->getType() ) !== 'name' ) : ?>
						<p class="<?= $enc->attr( $type ); ?>"><?= $enc->html( $textItem->getContent(), $enc::TRUST ); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>

			</label><!--


			--><?php if( isset( $serviceAttributes[$id] ) && !empty( $serviceAttributes[$id] ) ) : ?><!--
				--><ul class="form-list">

					<?php foreach( $serviceAttributes[$id] as $key => $attribute ) : ?>
						<?php
							if( !isset( $orderService ) || (
								( $value = $orderService->getAttribute( $key . '/hidden' ) ) === null
								&& ( $value = $orderService->getAttribute( $key ) ) === null )
							) {
								$value = $attribute->getDefault();
							}
						?>
						<?php $css = ( isset( $paymentCss[$key] ) ? ' ' . join( ' ', $paymentCss[$key] ) : '' ) . ( $attribute->isRequired() ? ' mandatory' : '' ); ?>

						<li class="form-item <?= $enc->attr( $key ) . $css; ?>">
							<label for="payment-<?= $enc->attr( $key ); ?>"><?= $enc->html( $this->translate( 'client/code', $key ) ); ?></label><!--

							--><?php switch( $attribute->getType() ) : case 'select': ?><!--

									--><select id="payment-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_payment', $id, $key ) ) ); ?>">

										<?php foreach( (array) $attribute->getDefault() as $option ) : $code = $key . ':' . $option; ?>
											<?php $string = ( !is_numeric( $option ) ? $this->translate( 'client/code', $code ) : $option ); ?>
											<option value="<?= $enc->attr( $option ); ?>">
												<?= $enc->html( $string ); ?>
											</option>
										<?php endforeach; ?>
									</select><!--

								--><?php break; case 'boolean': ?><!--
									--><input type="checkbox" id="payment-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_payment', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_payment/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; case 'integer': case 'number': ?><!--
									--><input type="number" id="payment-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_payment', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_payment/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; case 'date': case 'datetime': case 'time': ?><!--
									--><input type="<?= $attribute->getType(); ?>" id="payment-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_payment', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_payment/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

								--><?php break; default: ?><!--
									--><input type="text" id="payment-<?= $enc->attr( $key ); ?>"
										name="<?= $enc->attr( $this->formparam( array( 'c_payment', $id, $key ) ) ); ?>"
										value="<?= $enc->attr( $this->param( 'c_payment/' . $id . '/' . $key, $value ) ); ?>"
									/><!--

							--><?php endswitch; ?>

						</li>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>


	<div class="button-group">
		<a class="standardbutton btn-back" href="<?= $enc->attr( $this->get( 'standardUrlBack' ) ); ?>">
			<?= $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ); ?>
		</a>
		<button class="standardbutton btn-action">
			<?= $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ); ?>
		</button>
	</div>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'checkout/standard/payment' ); ?>
