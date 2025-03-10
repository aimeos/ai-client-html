<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2025
 */

$enc = $this->encoder();


?>
<?php $this->block()->start( 'checkout/standard/payment' ) ?>
<div class="section checkout-standard-payment">

	<h1><?= $enc->html( $this->translate( 'client', 'payment' ), $enc::TRUST ) ?></h1>
	<p class="note"><?= $enc->html( $this->translate( 'client', 'Please choose your payment method' ), $enc::TRUST ) ?></p>

	<?php foreach( $this->get( 'paymentServices', [] ) as $id => $service ) : ?>
		<div id="c_payment-<?= $enc->attr( $id ) ?>" class="item item-service row">

			<div class="col-sm-6">
				<label class="description" for="c_paymentoption-<?= $enc->attr( $id ) ?>">

					<input class="option" type="radio"
						id="c_paymentoption-<?= $enc->attr( $id ) ?>"
						name="<?= $enc->attr( $this->formparam( ['c_paymentoption'] ) ) ?>"
						value="<?= $enc->attr( $id ) ?>"
						<?= $id != $this->get( 'paymentOption' ) ?: 'checked="checked"' ?>
					>

					<div class="icons">
						<?php foreach( $service->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
							<?= $this->partial(
								$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
								array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'icon' ) )
							) ?>
						<?php endforeach ?>
					</div>

					<h2><?= $enc->html( $service->getName(), $enc::TRUST ) ?></h2>

					<?php if( $price = $service->price ) : ?>
						<?php if( $price->getValue() > 0 ) : ?>
							<div class="price-value">
								<?= $enc->html( sprintf( /// Service fee value (%1$s) and shipping cost value (%2$s) with currency (%3$s)
									$this->translate( 'client', '%1$s%3$s + %2$s%3$s' ),
									$this->number( $price->getValue(), $price->getPrecision() ),
									$this->number( $price->getCosts() > 0 ? $price->getCosts() : 0, $price->getPrecision() ),
									$this->translate( 'currency', $price->getCurrencyId() )
								) ) ?>
							</div>
						<?php elseif( $price->getCosts() > 0 ) : ?>
							<div class="price-value">
								<?php $pricetype = 'price:default' ?>
								<?= $enc->html( sprintf(
									/// Price format with price value (%1$s) and currency (%2$s)
									$this->translate( 'client/code', $pricetype, null, 0, false ) ?: $this->translate( 'client', '%1$s %2$s' ),
									$this->number( $price->getCosts() > 0 ? $price->getCosts() : 0, $price->getPrecision() ),
									$this->translate( 'currency', $price->getCurrencyId() )
								) ) ?>
							</div>
						<?php endif ?>
					<?php endif ?>

					<div class="text">
						<?php foreach( $service->getRefItems( 'text', null, 'default' ) as $textItem ) : ?>
							<?php if( ( $type = $textItem->getType() ) !== 'name' ) : ?>
								<p class="<?= $enc->attr( $type ) ?>"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></p>
							<?php endif ?>
						<?php endforeach ?>
					</div>

				</label>

			</div>

			<div class="col-sm-6">

				<?php if( $attributes = $service->attributes ) : ?>
					<?= $this->partial(
						/** client/html/checkout/standard/partials/serviceattr
						 * Relative path to the checkout service attribute partial template file
						 *
						 * Partials are templates which are reused in other templates and generate reoccuring
						 * blocks filled with data from the assigned values. The service attribute partial creates
						 * an HTML block for the checkout delivery/payment option input/select fields.
						 *
						 * This is a very generic way to generate the list of service attribute pairs that will be
						 * added as order service attributes in the basket. Depending on the type of the attribute,
						 * it will create an input field, a select box or a list of selectable items. What attributes
						 * are available to the customer depends on the definitions in the service providers and the
						 * decorators wrapped around them.
						 *
						 * If you want to adapt the output to your own project and you know you only have a specific
						 * list of attributes, you can create the input and selections in a non-generic, straight
						 * forward way. The $serviceAttributes[$id] array contains an associative list of codes as
						 * keys (e.g. "directdebit.bankcode") and items implementing \Aimeos\Base\Criteria\Attribute\Iface
						 * as values, e.g.
						 *   directdebit.bankcode => \Aimeos\Base\Criteria\Attribute\Iface (
						 *	   code => 'directdebit.bankcode',
						 *	   internalcode => 'bankcode',
						 *	   label => 'Bank code',
						 *	   type => 'string',
						 *	   internaltype => 'string',
						 *	   default => '',
						 *	   required => true
						 *   )
						 *
						 * @param string Relative path to the template file
						 * @since 2017.07
						 */
						$this->config( 'client/html/checkout/standard/partials/serviceattr', 'checkout/standard/serviceattr-partial' ),
						['attributes' => $attributes, 'type' => 'payment', 'id' => $id]
					) ?>

				<?php endif ?>

			</div>

		</div>
	<?php endforeach ?>


	<div class="button-group">
		<a class="btn btn-default btn-lg btn-back" href="<?= $enc->attr( $this->get( 'standardUrlBack' ) ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ) ?>
		</a>
		<button type="submit" class="btn btn-primary btn-lg btn-action">
			<?= $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ) ?>
		</button>
	</div>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/payment' ) ?>
