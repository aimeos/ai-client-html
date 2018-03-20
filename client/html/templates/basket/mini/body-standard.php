<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
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
 * @category Developer
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/site
 */
$basketTarget = $this->config( 'client/html/basket/standard/url/target' );

/** client/html/basket/standard/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/site
 */
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );

/** client/html/basket/standard/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/config
 * @see client/html/basket/standard/url/site
 */
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );

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
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/site
 * @see client/html/url/config
 */
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );

/** client/html/basket/standard/url/site
 * Locale site code where products will be added to the basket
 *
 * In more complex setups with several shop sites, this setting allows to to
 * define the shop site that will manage the basket of the customer. For example
 * in market place setups where all vendors have there own shop sites, the basket
 * site should be the site code of the market place ("default" by default).
 *
 * @param string Code of the locale site
 * @since 2018.04
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/config
 */
$basketSite = $this->config( 'client/html/basket/standard/url/site' );

$basketParams = ( $basketSite ? ['site' => $basketSite] : [] );


$jsonTarget = $this->config( 'client/jsonapi/url/target' );
$jsonController = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$jsonAction = $this->config( 'client/jsonapi/url/action', 'options' );
$jsonConfig = $this->config( 'client/jsonapi/url/config', [] );


/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


?>
<section class="aimeos basket-mini" data-jsonurl="<?= $enc->attr( $this->url( $jsonTarget, $jsonController, $jsonAction, $basketParams, [], $jsonConfig ) ); ?>">

	<?php if( ( $errors = $this->get( 'miniErrorList', [] ) ) !== [] ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?= $enc->html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<?php if( isset( $this->miniBasket ) ) : ?>
		<?php
			$quantity = 0;
			$priceItem = $this->miniBasket->getPrice();
			$priceCurrency = $this->translate( 'currency', $priceItem->getCurrencyId() );

			foreach( $this->miniBasket->getProducts() as $product ) {
				$quantity += $product->getQuantity();
			}
		?>

		<h1><?= $enc->html( $this->translate( 'client', 'Basket' ), $enc::TRUST ); ?></h1>

		<a href="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, $basketParams, [], $basketConfig ) ); ?>">
			<div class="basket-mini-main">
				<span class="quantity">
					<?= $enc->html( $quantity ); ?>
				</span>
				<span class="value">
					<?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue() + $priceItem->getCosts() ), $priceCurrency ) ); ?>
				</span>
			</div>
		</a>

		<div class="basket-mini-product">
			<span class="basket-toggle toggle-close"></span>
			<table class="basket">
				<thead class="basket-header">
					<tr>
						<th class="name"><?= $enc->html( $this->translate( 'client', 'Product' ), $enc::TRUST ); ?></th>
						<th class="quantity"><?= $enc->html( $this->translate( 'client', 'Qty' ), $enc::TRUST ); ?></th>
						<th class="price"><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ); ?></th>
						<th class="action"></th>
					</tr>
				</thead>
				<tbody class="basket-body">
					<tr class="product prototype">
						<td class="name"></td>
						<td class="quantity"></td>
						<td class="price"></td>
						<td class="action"><a class="delete" href="#"></a></td>
					</tr>
					<?php foreach( $this->miniBasket->getProducts() as $pos => $product ) : ?>
						<?php
							$param = ['resource' => 'basket', 'id' => 'default', 'related' => 'product', 'relatedid' => $pos];
							if( $basketSite ) { $param['site'] = $basketSite; }
						?>
						<tr class="product"
							data-url="<?= $enc->attr( $this->url( $jsonTarget, $jsonController, $jsonAction, $param, [], $jsonConfig ) ); ?>"
							data-urldata="<?= $enc->attr( $this->csrf()->name() . '=' . $this->csrf()->value() ); ?>"
							>
							<td class="name">
								<?= $enc->html( $product->getName() ) ?>
							</td>
							<td class="quantity">
								<?= $enc->html( $product->getQuantity() ) ?>
							</td>
							<td class="price">
								<?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue() ), $priceCurrency ) ); ?>
							</td>
							<td class="action">
								<?php if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>
									<a class="delete" href="#"></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="basket-footer">
					<tr class="delivery">
						<td class="name" colspan="2">
							<?= $enc->html( $this->translate( 'client', 'Shipping' ), $enc::TRUST ); ?>
						</td>
						<td class="price">
							<?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getCosts() ), $priceCurrency ) ); ?>
						</td>
						<td class="action"></td>
					</tr>
					<tr class="total">
						<td class="name" colspan="2">
							<?= $enc->html( $this->translate( 'client', 'Total' ), $enc::TRUST ); ?>
						</td>
						<td class="price">
							<?= $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue() + $priceItem->getCosts() ), $priceCurrency ) ); ?>
						</td>
						<td class="action"></td>
					</tr>
				</tfoot>
			</table>
		</div>

	<?php endif; ?>

</section>
