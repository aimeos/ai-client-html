<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

/* Available data:
 * - orderItem : Order item (optional, only available after checkout)
 * - summaryBasket : Order base item (basket) including products, addresses, services, etc.
 * - summaryEnableModify : True if users are allowed to change the basket content, false if not (optional)
 * - summaryErrorCodes : List of error codes including those for the products (optional)
 */


$totalQuantity = 0;
$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array( 'absoluteUri' => 1 ) );


/** client/html/account/download/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2016.02
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/action
 * @see client/html/account/download/url/config
 */

/** client/html/account/download/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2016.02
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/action
 * @see client/html/account/download/url/config
 */

/** client/html/account/download/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2016.02
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/config
 */

/** client/html/account/download/url/config
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
 * @since 2016.02
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/action
 */

/** client/html/common/summary/detail/product/attribute/types
 * List of attribute type codes that should be displayed in the basket along with their product
 *
 * The products in the basket can store attributes that exactly define an ordered
 * product or which are important for the back office. By default, the product
 * variant attributes are always displayed and the configurable product attributes
 * are displayed separately.
 *
 * Additional attributes for each ordered product can be added by basket plugins.
 * Depending on the attribute types and if they should be shown to the customers,
 * you need to extend the list of displayed attribute types ab adding their codes
 * to the configurable list.
 *
 * @param array List of attribute type codes
 * @since 2014.09
 */
$attrTypes = $this->config( 'client/html/common/summary/detail/product/attribute/types', ['variant', 'config', 'custom'] );


$price = $this->summaryBasket->getPrice();
$precision = $price->getPrecision();
$priceTaxflag = $price->getTaxFlag();
$priceCurrency = $this->translate( 'currency', $price->getCurrencyId() );


/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client/code', 'price:default', null, 0, false ) ?: $this->translate( 'client', '%1$s %2$s' );
/// Tax format with tax rate (%1$s) and tax name (%2$s)
$taxFormatIncl = $this->translate( 'client', 'Incl. %1$s%% %2$s' );
/// Tax format with tax rate (%1$s) and tax name (%2$s)
$taxFormatExcl = $this->translate( 'client', '+ %1$s%% %2$s' );

$modify = $this->get( 'summaryEnableModify', false );
$errors = $this->get( 'summaryErrorCodes', [] );


?>
<div>
	<div class="row g-0 headline">
		<div class="col-8 col-md-6 offset-4 offset-md-6">
			<div class="row g-0">
				<div class="col-4 quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ), $enc::TRUST ) ?></div>
				<div class="col-4 unitprice"><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ) ?></div>
				<div class="col-3 price"><?= $enc->html( $this->translate( 'client', 'Sum' ), $enc::TRUST ) ?></div>
				<?php if( $modify ) : ?>
					<div class="action col-1"></div>
				<?php endif ?>
			</div>
		</div>
	</div>

	<?php foreach( $this->summaryBasket->getProducts()->groupBy( 'order.product.vendor' )->ksort() as $vendor => $list ) : ?>

		<?php if( $vendor ) : ?>
			<div class="supplier">
				<h3 class="supplier-name"><?= $enc->html( $vendor ) ?></h3>
			</div>
		<?php endif ?>

		<?php foreach( $list as $position => $product ) : $totalQuantity += $product->getQuantity() ?>
			<div class="row g-0 product-item <?= ( isset( $errors['product'][$position] ) ? 'error' : '' ) ?>">
				<div class="col-4 col-md-6">
					<div class="row g-0">
						<div class="status col-1">
							<?php if( ( $status = $product->getStatusDelivery() ) >= 0 ) : $key = 'stat:' . $status ?>
								<?= $enc->html( $this->translate( 'mshop/code', $key ) ) ?>
							<?php endif ?>
						</div>
						<div class="image col-11 col-lg-3">
							<?php if( ( $url = $product->getMediaUrl() ) != '' ) : ?>
								<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
							<?php endif ?>
						</div>
						<div class="details col-12 col-lg-8">
							<?php
								$url = '#';

								if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE ) == 0 )
								{
									$params = ['d_name' => $product->getName( 'url' ), 'd_prodid' => $product->getParentProductId() ?: $product->getProductId(), 'd_pos' => ''];
									$url = $this->url( ( $product->getTarget() ?: $detailTarget ), $detailController, $detailAction, $params, [], $detailConfig );
								}
							?>
							<a class="product-name" href="<?= $enc->attr( $url ) ?>"><?= $enc->html( $product->getName(), $enc::TRUST ) ?></a>
							<p class="code">
								<span class="name"><?= $enc->html( $this->translate( 'client', 'Article no.' ), $enc::TRUST ) ?></span>
								<span class="value"><?= $product->getProductCode() ?></span>
							</p>
							<?php if( ( $desc = $product->getDescription() ) !== '' ) : ?>
								<p class="product-description"><?= $enc->html( $desc ) ?></p>
							<?php endif ?>
							<?php foreach( $attrTypes as $attrType ) : ?>
								<?php if( !( $attributes = $product->getAttributeItems( $attrType ) )->isEmpty() ) : ?>
									<ul class="attr-list attr-type-<?= $enc->attr( $attrType ) ?>">
										<?php foreach( $product->getAttributeItems( $attrType ) as $attribute ) : ?>
											<li class="attr-item attr-code-<?= $enc->attr( $attribute->getCode() ) ?>">
												<span class="name"><?= $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ) ?></span>
												<span class="value">
													<?php if( $attribute->getQuantity() > 1 ) : ?>
														<?= $enc->html( $attribute->getQuantity() ) ?>×
													<?php endif ?>
													<?= $enc->html( $attrType !== 'custom' && $attribute->getName() ? $attribute->getName() : $attribute->getValue() ) ?>
												</span>
											</li>
										<?php endforeach ?>
									</ul>
								<?php endif ?>
							<?php endforeach ?>
							<?php if( isset( $this->orderItem ) && $this->orderItem->getStatusPayment() >= \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED
									&& ( $product->getStatusPayment() < 0 || $product->getStatusPayment() >= \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED )
									&& ( $attribute = $product->getAttributeItem( 'download', 'hidden' ) ) ) : ?>
								<ul class="attr-list attr-list-hidden">
									<li class="attr-item attr-code-<?= $enc->attr( $attribute->getCode() ) ?>">
										<span class="name"><?= $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ) ?></span>
										<span class="value">
											<a href="<?= $enc->attr( $this->link( 'client/html/account/download/url', ['dl_id' => $attribute->getId()] ) ) ?>">
												<?= $enc->html( $attribute->getName() ) ?>
											</a>
										</span>
									</li>
								</ul>
							<?php endif ?>
							<?php if( ( $timeframe = $product->getTimeframe() ) !== '' ) : ?>
								<p class="timeframe">
									<span class="name"><?= $enc->html( $this->translate( 'client', 'Delivery within' ) ) ?></span>
									<span class="value"><?= $enc->html( $timeframe ) ?></span>
								</p>
							<?php endif ?>
						</div>
						</div>
					</div>
				<div class="col-8 col-md-6">
					<div class="row g-0">
						<div class="quantity col-4">

							<?php if( $modify && ( $product->getFlags() & \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>

								<?php if( $product->getQuantity() > 1 ) : ?>
									<?php $basketParams = array( 'b_action' => 'edit', 'b_position' => $position, 'b_quantity' => $product->getQuantity() - 1 ) ?>
									<a class="minibutton change down" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>">−</a>
								<?php else : ?>
									&nbsp;&nbsp;&nbsp;
								<?php endif ?>

								<input class="value" type="number" required="required"
									name="<?= $enc->attr( $this->formparam( array( 'b_prod', $position, 'quantity' ) ) ) ?>"
									value="<?= $enc->attr( $product->getQuantity() ) ?>"
									step="<?= $enc->attr( $product->getScale() ) ?>"
									min="<?= $enc->attr( $product->getScale() ) ?>"
									max="2147483647"
								>
								<input type="hidden" type="text"
									name="<?= $enc->attr( $this->formparam( array( 'b_prod', $position, 'position' ) ) ) ?>"
									value="<?= $enc->attr( $position ) ?>"
								>

								<?php $basketParams = array( 'b_action' => 'edit', 'b_position' => $position, 'b_quantity' => $product->getQuantity() + 1 ) ?>
								<a class="minibutton change up" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>">+</a>

							<?php else : ?>
								<?= $enc->html( $product->getQuantity() ) ?>
							<?php endif ?>
						</div>
						<div class="unitprice col-4"><?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue(), $precision ), $priceCurrency ) ) ?></div>
						<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue() * $product->getQuantity(), $precision ), $priceCurrency ) ) ?></div>
						<?php if( $modify ) : ?>
						<div class="action col-1">
							<?php if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>
								<?php $basketParams = array( 'b_action' => 'delete', 'b_position' => $position ) ?>
								<a class="minibutton delete" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>"></a>
							<?php endif ?>
						</div>
						<?php endif ?>

					</div>
				</div>
			</div>

		<?php endforeach ?>
	<?php endforeach ?>


	<?php foreach( $this->summaryBasket->getService( 'delivery' ) as $service ) : ?>
		<?php if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
			<?php $price = $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ) ?>
			<div class="delivery row g-0">
				<div class="col-7 col-md-6">
					<div class="row g-0">
						<div class="status col-1"></div>
						<div class="image col-11 col-lg-3">
							<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
								<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
							<?php endif ?>
						</div>
						<div class="details col-12 col-lg-8"><?= $enc->html( $service->getName() ) ?></div>
					</div>
				</div>
				<div class="col-5 col-md-6">
					<div class="row g-0">
						<div class="quantity col-4">1</div>
						<div class="unitprice col-4"><?= $price ?></div>
						<div class="price col-3"><?= $price ?></div>
						<?php if( $modify ) : ?>
							<div class="action col-1"></div>
						<?php endif ?>
					</div>
				</div>
			</div>
		<?php endif ?>
	<?php endforeach ?>

	<?php foreach( $this->summaryBasket->getService( 'payment' ) as $service ) : ?>
		<?php if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
			<?php $price = $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ) ?>
			<div class="payment row g-0">
				<div class="col-8 col-md-6">
					<div class="row g-0">
						<div class="status col-1"></div>
						<div class="image col-11 col-lg-3">
						<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
							<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
						<?php endif ?>
						</div>
						<div class="details col-12 col-lg-8"><?= $enc->html( $service->getName() ) ?></div>
					</div>
				</div>
				<div class="col-4 col-md-6">
					<div class="row g-0">
						<div class="quantity col-4">1</div>
						<div class="unitprice col-4"><?= $price ?></div>
						<div class="price col-3"><?= $price ?></div>
						<?php if( $modify ) : ?>
							<div class="action col-1"></div>
						<?php endif ?>
					</div>
				</div>
			</div>

		<?php endif ?>
	<?php endforeach ?>

	<?php if( $priceTaxflag === false || $this->summaryBasket->getPrice()->getCosts() > 0 ) : ?>
		<div class="subtotal row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0">
					<div class="col-8"><?= $enc->html( $this->translate( 'client', 'Sub-total' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue(), $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
						<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php if( ( $costs = $this->summaryBasket->getCosts( 'delivery' ) ) > 0 ) : ?>
		<div class="delivery row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0">
					<div class="col-8"><?= $enc->html( $this->translate( 'client', 'Shipping' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $costs, $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
					<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php if( ( $costs = $this->summaryBasket->getCosts( 'payment' ) ) > 0 ) : ?>
		<div class="payment row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0">
					<div class="col-8"><?= $enc->html( $this->translate( 'client', 'Payment costs' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $costs, $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
						<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php if( $priceTaxflag === true ) : ?>
		<div class="total row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0 price-total">
					<div class="quantity col-4"><?= $enc->html( sprintf( $this->translate( 'client', '%1$d article', '%1$d articles', $totalQuantity ), $totalQuantity ) ) ?></div>
					<div class="col-4 total-text"><?= $enc->html( $this->translate( 'client', 'Total' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts(), $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
						<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php foreach( $this->summaryBasket->getTaxes() as $taxName => $map ) : ?>
		<?php foreach( $map as $taxRate => $priceItem ) : ?>
			<?php if( ( $taxValue = $priceItem->getTaxValue() ) > 0 ) : ?>
				<div class="tax row g-0">
					<div class="col-8 col-md-6 offset-4 offset-md-6">
						<div class="row g-0">
							<div class="col-8"><?= $enc->html( sprintf( $priceTaxflag ? $taxFormatIncl : $taxFormatExcl, $this->number( $taxRate ), $this->translate( 'client/code', $taxName ) ) ) ?></div>
							<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $taxValue, $precision ), $priceCurrency ) ) ?></div>
							<?php if( $modify ) : ?>
								<div class="action col-1"></div>
							<?php endif ?>
						</div>
					</div>
				</div>
			<?php endif ?>
		<?php endforeach ?>
	<?php endforeach ?>

	<?php if( $priceTaxflag === false ) : ?>
		<div class="total row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0">
					<div class="quantity col-4"><?= $enc->html( sprintf( $this->translate( 'client', '%1$d article', '%1$d articles', $totalQuantity ), $totalQuantity ) ) ?></div>
					<div class="price col-4"><?= $enc->html( $this->translate( 'client', 'Total' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts() + $this->summaryBasket->getPrice()->getTaxValue(), $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
						<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php if( $this->summaryBasket->getPrice()->getRebate() > 0 ) : ?>
		<div class="rebate row g-0">
			<div class="col-8 col-md-6 offset-4 offset-md-6">
				<div class="row g-0">
					<div class="quantity col-8"><?= $enc->html( $this->translate( 'client', 'Included rebates' ) ) ?></div>
					<div class="price col-3"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getRebate(), $precision ), $priceCurrency ) ) ?></div>
					<?php if( $modify ) : ?>
						<div class="action col-1"></div>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php endif ?>

</div>
