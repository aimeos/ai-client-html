<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/* Available data:
 * - summaryTaxRates : Calculated taxes grouped by the tax rates
 * - summaryNamedTaxes : Calculated taxes grouped by the tax names
 * - summaryBasket : Order base item (basket) including products, addresses, services, etc.
 * - summaryShowDownloadAttributes : True if links to downloads should be shown, false if not (optional)
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
 * @category Developer
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/action
 * @see client/html/account/download/url/config
 */
$dlTarget = $this->config( 'client/html/account/download/url/target' );

/** client/html/account/download/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2016.02
 * @category Developer
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/action
 * @see client/html/account/download/url/config
 */
$dlController = $this->config( 'client/html/account/download/url/controller', 'account' );

/** client/html/account/download/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2016.02
 * @category Developer
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/config
 */
$dlAction = $this->config( 'client/html/account/download/url/action', 'download' );

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
 * @category Developer
 * @see client/html/account/download/url/target
 * @see client/html/account/download/url/controller
 * @see client/html/account/download/url/action
 */
$dlConfig = $this->config( 'client/html/account/download/url/config', array( 'absoluteUri' => 1 ) );

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
 * @category Developer
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

$unhide = $this->get( 'summaryShowDownloadAttributes', false );
$modify = $this->get( 'summaryEnableModify', false );
$errors = $this->get( 'summaryErrorCodes', [] );


?>
<table>

	<thead>
		<tr>
			<th class="details" colspan="3"></th>
			<th class="quantity"><?= $enc->html( $this->translate( 'client', 'Quantity' ), $enc::TRUST ) ?></th>
			<th class="unitprice"><?= $enc->html( $this->translate( 'client', 'Price' ), $enc::TRUST ) ?></th>
			<th class="price"><?= $enc->html( $this->translate( 'client', 'Sum' ), $enc::TRUST ) ?></th>
			<?php if( $modify ) : ?>
				<th class="action"></th>
			<?php endif ?>
		</tr>
	</thead>

	<tbody>

	<?php foreach( $this->summaryBasket->getProducts()->groupBy( 'order.base.product.supplierid' )->ksort() as $supId => $list ) : ?>
			<?php $sname = map( $list )->first()->getSupplierName() ?>

			<?php if( $supId && $sname ) : ?>
				<tr class="supplier">
					<td colspan="<?= $modify ? 7 : 6 ?>">
						<h3 class="supplier-name">
							<a class="supplier-link" href="<?= $enc->attr( $this->link( 'client/html/supplier/detail/url', ['f_supid' => $supId, 's_name' => $sname] ) ) ?>">
								<?= $enc->html( $sname ) ?>
							</a>
						</h3>
					</td>
				</tr>
			<?php endif ?>

			<?php foreach( $list as $position => $product ) : $totalQuantity += $product->getQuantity() ?>
				<tr class="product <?= ( isset( $errors['product'][$position] ) ? 'error' : '' ) ?>">

					<td class="status">
						<?php if( ( $status = $product->getStatus() ) >= 0 ) : $key = 'stat:' . $status ?>
							<?= $enc->html( $this->translate( 'mshop/code', $key ) ) ?>
						<?php endif ?>
					</td>

					<td class="image">
						<?php if( ( $url = $product->getMediaUrl() ) != '' ) : ?>
							<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
						<?php endif ?>
					</td>

					<td class="details">

						<?php
							$url = '#';

							if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) == 0 )
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

						<?php if( $unhide && ( $attribute = $product->getAttributeItem( 'download', 'hidden' ) ) !== null ) : ?>
							<ul class="attr-list attr-list-hidden">
								<li class="attr-item attr-code-<?= $enc->attr( $attribute->getCode() ) ?>">
									<span class="name"><?= $enc->html( $this->translate( 'client/code', $attribute->getCode() ) ) ?></span>
									<span class="value">
										<a href="<?= $enc->attr( $this->url( $dlTarget, $dlController, $dlAction, array( 'dl_id' => $attribute->getId() ), [], $dlConfig ) ) ?>">
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

					</td>


					<td class="quantity">

						<?php if( $modify && ( $product->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>

							<?php if( $product->getQuantity() > 1 ) : ?>
								<?php $basketParams = array( 'b_action' => 'edit', 'b_position' => $position, 'b_quantity' => $product->getQuantity() - 1 ) ?>
								<a class="minibutton change" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>">−</a>
							<?php else : ?>
								&nbsp;
							<?php endif ?>

							<input class="value" type="number"
								name="<?= $enc->attr( $this->formparam( array( 'b_prod', $position, 'quantity' ) ) ) ?>"
								value="<?= $enc->attr( $product->getQuantity() ) ?>" required="required"
							>
							<input type="hidden" type="text"
								name="<?= $enc->attr( $this->formparam( array( 'b_prod', $position, 'position' ) ) ) ?>"
								value="<?= $enc->attr( $position ) ?>"
							>

							<?php $basketParams = array( 'b_action' => 'edit', 'b_position' => $position, 'b_quantity' => $product->getQuantity() + 1 ) ?>
							<a class="minibutton change" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>">+</a>

						<?php else : ?>
							<?= $enc->html( $product->getQuantity() ) ?>
						<?php endif ?>
					</td>


					<?php if( $product->getPrice()->getValue() !== null ) : ?>
						<td class="unitprice"><?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue(), $precision ), $priceCurrency ) ) ?></td>
						<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $product->getPrice()->getValue() * $product->getQuantity(), $precision ), $priceCurrency ) ) ?></td>
					<?php else : ?>
						<td class="unitprice"></td>
						<td class="price"><?= $enc->html( $this->translate( 'client', 'on request' ) ) ?></td>
					<?php endif ?>


					<?php if( $modify ) : ?>
						<td class="action">
							<?php if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) == 0 ) : ?>
								<?php $basketParams = array( 'b_action' => 'delete', 'b_position' => $position ) ?>
								<a class="minibutton delete" href="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', $basketParams ) ) ?>"></a>
							<?php endif ?>
						</td>
					<?php endif ?>

				</tr>
			<?php endforeach ?>
		<?php endforeach ?>


		<?php foreach( $this->summaryBasket->getService( 'delivery' ) as $service ) : ?>
			<?php if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
				<?php $price = $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ) ?>
				<tr class="delivery">
					<td class="status"></td>
					<td class="image">
						<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
							<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
						<?php endif ?>
					</td>
					<td class="details"><?= $enc->html( $service->getName() ) ?></td>
					<td class="quantity">1</td>
					<td class="unitprice"><?= $price ?></td>
					<td class="price"><?= $price ?></td>
					<?php if( $modify ) : ?>
						<td class="action"></td>
					<?php endif ?>
				</tr>
			<?php endif ?>
		<?php endforeach ?>


		<?php foreach( $this->summaryBasket->getService( 'payment' ) as $service ) : ?>
			<?php if( $service->getPrice()->getValue() > 0 ) : $priceItem = $service->getPrice() ?>
				<?php $price = $enc->html( sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $priceItem->getCurrencyId() ) ) ?>
				<tr class="payment">
					<td class="status"></td>
					<td class="image">
						<?php if( ( $url = $service->getMediaUrl() ) != '' ) : ?>
							<img class="detail" src="<?= $enc->attr( $this->content( $url ) ) ?>">
						<?php endif ?>
					</td>
					<td class="details"><?= $enc->html( $service->getName() ) ?></td>
					<td class="quantity">1</td>
					<td class="unitprice"><?= $price ?></td>
					<td class="price"><?= $price ?></td>
					<?php if( $modify ) : ?>
						<td class="action"></td>
					<?php endif ?>
				</tr>
			<?php endif ?>
		<?php endforeach ?>

	</tbody>


	<tfoot>

		<?php if( $priceTaxflag === false || $this->summaryBasket->getPrice()->getCosts() > 0 ) : ?>
			<tr class="subtotal">
				<td colspan="5"><?= $enc->html( $this->translate( 'client', 'Sub-total' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue(), $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

		<?php if( ( $costs = $this->get( 'summaryCostsDelivery', 0 ) ) > 0 ) : ?>
			<tr class="delivery">
				<td colspan="5"><?= $enc->html( $this->translate( 'client', 'Shipping' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $costs, $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

		<?php if( ( $costs = $this->get( 'summaryCostsPayment', 0 ) ) > 0 ) : ?>
			<tr class="payment">
				<td colspan="5"><?= $enc->html( $this->translate( 'client', 'Payment costs' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $costs, $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

		<?php if( $priceTaxflag === true ) : ?>
			<tr class="total">
				<td colspan="3"></td>
				<td class="quantity"><?= $enc->html( sprintf( $this->translate( 'client', '%1$d article', '%1$d articles', $totalQuantity ), $totalQuantity ) ) ?></td>
				<td><?= $enc->html( $this->translate( 'client', 'Total' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts(), $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

		<?php foreach( $this->get( 'summaryNamedTaxes', [] ) as $taxName => $map ) : ?>
			<?php foreach( $map as $taxRate => $priceItem ) : ?>
				<?php if( ( $taxValue = $priceItem->getTaxValue() ) > 0 ) : ?>
					<tr class="tax">
						<td colspan="5"><?= $enc->html( sprintf( $priceTaxflag ? $taxFormatIncl : $taxFormatExcl, $this->number( $taxRate ), $this->translate( 'client/code', 'tax' . $taxName ) ) ) ?></td>
						<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $taxValue, $precision ), $priceCurrency ) ) ?></td>
						<?php if( $modify ) : ?>
							<td class="action"></td>
						<?php endif ?>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
		<?php endforeach ?>

		<?php if( $priceTaxflag === false ) : ?>
			<tr class="total">
				<td colspan="3"></td>
				<td class="quantity"><?= $enc->html( sprintf( $this->translate( 'client', '%1$d article', '%1$d articles', $totalQuantity ), $totalQuantity ) ) ?></td>
				<td><?= $enc->html( $this->translate( 'client', 'Total' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getValue() + $this->summaryBasket->getPrice()->getCosts() + $this->summaryBasket->getPrice()->getTaxValue(), $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

		<?php if( $this->summaryBasket->getPrice()->getRebate() > 0 ) : ?>
			<tr class="rebate">
				<td colspan="5"><?= $enc->html( $this->translate( 'client', 'Included rebates' ) ) ?></td>
				<td class="price"><?= $enc->html( sprintf( $priceFormat, $this->number( $this->summaryBasket->getPrice()->getRebate(), $precision ), $priceCurrency ) ) ?></td>
				<?php if( $modify ) : ?>
					<td class="action"></td>
				<?php endif ?>
			</tr>
		<?php endif ?>

	</tfoot>

</table>
