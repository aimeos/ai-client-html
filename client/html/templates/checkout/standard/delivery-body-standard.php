<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


?>
<?php $this->block()->start( 'checkout/standard/delivery' ) ?>
<section class="checkout-standard-delivery">

	<h1><?= $enc->html( $this->translate( 'client', 'delivery' ), $enc::TRUST ) ?></h1>
	<p class="note"><?= $enc->html( $this->translate( 'client', 'Please choose your delivery method' ), $enc::TRUST ) ?></p>

	<?php foreach( $this->get( 'deliveryServices', [] ) as $id => $service ) : ?>
		<div id="c_delivery-<?= $enc->attr( $id ) ?>" class="item item-service row">

			<div class="col-sm-6">
				<label class="description" for="c_deliveryoption-<?= $enc->attr( $id ) ?>">

					<input class="option" type="radio"
						id="c_deliveryoption-<?= $enc->attr( $id ) ?>"
						name="<?= $enc->attr( $this->formparam( ['c_deliveryoption'] ) ) ?>"
						value="<?= $enc->attr( $id ) ?>"
						<?= $id != $this->get( 'deliveryOption' ) ?: 'checked="checked"' ?>
					>


					<?php if( $price = $service->price ) : ?>

						<?php if( $price->getValue() > 0 ) : ?>
							<span class="price-value">
								<?= $enc->html( sprintf( /// Service fee value (%1$s) and shipping cost value (%2$s) with currency (%3$s)
									$this->translate( 'client', '%1$s%3$s + %2$s%3$s' ),
									$this->number( $price->getValue(), $price->getPrecision() ),
									$this->number( $price->getCosts() > 0 ? $price->getCosts() : 0, $price->getPrecision() ),
									$this->translate( 'currency', $price->getCurrencyId() )
								) ) ?>
							</span>
						<?php else : ?>
							<span class="price-value">
								<?= $enc->html( sprintf(
									/// Price format with price value (%1$s) and currency (%2$s)
									$this->translate( 'client/code', 'price:default', null, 0, false ) ?: $this->translate( 'client', '%1$s %2$s' ),
									$this->number( $price->getCosts() > 0 ? $price->getCosts() : 0, $price->getPrecision() ),
									$this->translate( 'currency', $price->getCurrencyId() )
								) ) ?>
							</span>
						<?php endif ?>

					<?php endif ?>


					<div class="icons">
						<?php foreach( $service->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
							<?= $this->partial(
								$this->config( 'client/html/common/partials/media', 'common/partials/media-standard' ),
								array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'icon' ) )
							) ?>
						<?php endforeach ?>
					</div>

					<h2><?= $enc->html( $service->getName() ) ?></h2>

					<?php foreach( $service->getRefItems( 'text', null, 'default' ) as $textItem ) : ?>
						<?php if( ( $type = $textItem->getType() ) !== 'name' ) : ?>
							<p class="<?= $enc->attr( $type ) ?>"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></p>
						<?php endif ?>
					<?php endforeach ?>

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
						 * keys (e.g. "time.hourminute") and items implementing \Aimeos\MW\Criteria\Attribute\Iface
						 * as values, e.g.
						 *   time.hourminute => \Aimeos\MW\Criteria\Attribute\Iface (
						 *	   code => 'time.hourminute',
						 *	   internalcode => 'hourminute',
						 *	   label => 'Delivery time',
						 *	   type => 'time',
						 *	   internaltype => 'time',
						 *	   default => '',
						 *	   required => true
						 *   )
						 *
						 * @param string Relative path to the template file
						 * @since 2017.07
						 * @category Developer
						 */
						$this->config( 'client/html/checkout/standard/partials/serviceattr', 'checkout/standard/serviceattr-partial-standard' ),
						['attributes' => $attributes, 'type' => 'delivery', 'id' => $id]
					) ?>
				<?php endif ?>

			</div>
		</div>

	<?php endforeach ?>


	<div class="button-group">
		<a class="btn btn-default btn-lg btn-back" href="<?= $enc->attr( $this->get( 'standardUrlBack' ) ) ?>">
			<?= $enc->html( $this->translate( 'client', 'Previous' ), $enc::TRUST ) ?>
		</a>
		<button class="btn btn-primary btn-lg btn-action">
			<?= $enc->html( $this->translate( 'client', 'Next' ), $enc::TRUST ) ?>
		</button>
	</div>

</section>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/delivery' ) ?>
