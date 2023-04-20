<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

$enc = $this->encoder();


/** client/html/basket/standard/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/filter
 */

/** client/html/basket/standard/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/filter
 */

/** client/html/basket/standard/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/filter
 */

/** client/html/basket/standard/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/filter
 */

/** client/html/basket/standard/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 */

$pricefmt = $this->translate( 'client/code', 'price:default' );
/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $pricefmt !== 'price:default' ? $pricefmt : $this->translate( 'client', '%1$s %2$s' );


?>

<div class="section aimeos basket-mini" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( ( $errors = $this->get( 'miniErrorList', [] ) ) !== [] ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

	<?php if( isset( $this->miniBasket ) ) : ?>
		<?php
			$priceItem = $this->miniBasket->getPrice();
			$priceCurrency = $this->translate( 'currency', $priceItem->getCurrencyId() );
			$quantity = $this->miniBasket->getProducts()->sum( 'order.product.quantity' );
		?>

		<div class="aimeos-overlay-offscreen"></div>

		<div class="basket-mini-main menu">
			<span class="quantity"><?= $enc->html( $quantity ) ?></span>
			<span class="value"><?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue() + $priceItem->getCosts(), $priceItem->getPrecision() ), $priceCurrency ) ) ?></span>
		</div>

		<div class="basket-mini-product zeynep">
			<div class="header row">
				<div class="col-2 close"></div>
				<div class="col-8 name"><?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ) ?></div>
				<div class="col-2"></div>
			</div>

			<div class="basket">
				<div class="basket-header row">
						<div class="col-5 name"><?= $enc->html( $this->translate( 'client', 'Product' ), $enc::TRUST ) ?></div>
						<div class="col-2 quantity"><?= $enc->html( $this->translate( 'client', 'Qty' ), $enc::TRUST ) ?></div>
						<div class="col-3 price"><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ) ?></div>
						<div class="col-2 action"></div>
				</div>
				<div class="basket-body">
					<?php foreach( $this->miniBasket->getProducts() as $pos => $product ) : ?>
						<?php
							$param = ['resource' => 'basket', 'id' => 'default', 'related' => 'product', 'relatedid' => $pos];
							$param[$this->csrf()->name()] = $this->csrf()->value();
						?>
						<div class="product-item row" data-url="<?= $enc->attr( $this->link( 'client/jsonapi/url', $param ) ) ?>">
							<div class="col-5 name">
								<?= $enc->html( $product->getName() ) ?>
							</div>
							<div class="col-2 quantity">
								<?= $enc->html( $product->getQuantity() ) ?>
							</div>
							<div class="col-3 price">
								<?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue(), $product->getPrice()->getPrecision() ), $priceCurrency ) ) ?>
							</div>
							<div class="col-2 action">
								<?php if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>
									<a class="delete" href="#" title="<?= $enc->attr( $this->translate( 'client', 'Delete' ) ) ?>"></a>
								<?php endif ?>
							</div>
						</div>
					<?php endforeach ?>
					<div class="product-item row prototype">
						<div class="col-5 name"></div>
						<div class="col-2 quantity"></div>
						<div class="col-3 price"></div>
						<div class="col-2 action"><a class="delete" href="#" title="<?= $enc->attr( $this->translate( 'client', 'Delete' ) ) ?>"></a></div>
					</div>
				</div>
				<div class="basket-footer">
					<div class="delivery row">
						<div class="col-7 name">
							<?= $enc->html( $this->translate( 'client', 'Shipping' ), $enc::TRUST ) ?>
						</div>
						<div class="col-3 price">
							<?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getCosts(), $priceItem->getPrecision() ), $priceCurrency ) ) ?>
						</div>
						<div class="col-2 action"></div>
					</div>
					<div class="total row">
						<div class="col-7 name">
							<?= $enc->html( $this->translate( 'client', 'Total' ), $enc::TRUST ) ?>
								</div>
						<div class="col-3 price">
							<?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue() + $priceItem->getCosts(), $priceItem->getPrecision() ), $priceCurrency ) ) ?>
						</div>
						<div class="col-2 action"></div>
					</div>
				</div>
			</div>
			<div class="to-basket">
				<a class="btn btn-primary" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url' ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ) ?>
				</a>
			</div>
		</div>
	<?php endif ?>
</div>
