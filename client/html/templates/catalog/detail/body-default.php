<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/* Available data:
 * - detailProductItem : Product item incl. referenced items
 * - detailProductItems : Referenced products (bundle, variant, suggested, bought, etc) incl. referenced items
 * - detailPropertyItems : Properties for all products
 * - detailAttributeItems : Attributes items incl. referenced items
 * - detailMediaItems : Media items incl. referenced items
 * - detailParams : Request parameters for this detail view
 * - detailUserId : User ID if logged in
 */


$getProductList = function( array $posItems, array $items )
{
	$list = array();

	foreach( $posItems as $id => $posItem )
	{
		if( isset( $items[$id] ) ) {
			$list[$id] = $items[$id];
		}
	}

	return $list;
};


$enc = $this->encoder();

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );


/** client/html/basket/require-stock
 * Customers can order products only if there are enough products in stock
 *
 * Checks that the requested product quantity is in stock before
 * the customer can add them to his basket and order them. If there
 * are not enough products available, the customer will get a notice.
 *
 * @param boolean True if products must be in stock, false if products can be sold without stock
 * @since 2014.03
 * @category Developer
 * @category User
 */
$reqstock = (int) $this->config( 'client/html/basket/require-stock', true );


$attrMap = $subAttrDeps = array();
$attrItems = $this->get( 'detailAttributeItems', array() );

foreach( $this->get( 'detailProductItems', array() ) as $subProdId => $subProduct )
{
	$subItems = $subProduct->getRefItems( 'attribute', null, 'default' );
	$subItems += $subProduct->getRefItems( 'attribute', null, 'variant' ); // show product variant attributes as well

	foreach( $subItems as $attrId => $attrItem )
	{
		if( isset( $attrItems[$attrId] ) )
		{
			$attrMap[ $attrItem->getType() ][$attrId] = $attrItems[$attrId];
			$subAttrDeps[$attrId][] = $subProdId;
		}
	}
}

$propMap = array();

foreach( $this->get( 'detailPropertyItems', array() ) as $propItem ) {
	$propMap[ $propItem->getType() ][] = $propItem;
}

ksort( $attrMap );
ksort( $propMap );


?>
<section class="aimeos catalog-detail" itemscope="" itemtype="http://schema.org/Product">

	<?php if( isset( $this->detailErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->detailErrorList as $errmsg ) : ?>
				<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<?php if( isset( $this->detailProductItem ) ) : ?>
		<?php $conf = $this->detailProductItem->getConfig(); ?>

		<article class="product <?php echo ( isset( $conf['css-class'] ) ? $conf['css-class'] : '' ); ?>" data-id="<?php echo $this->detailProductItem->getId(); ?>">

			<?php echo $this->partial(
				/** client/html/catalog/detail/partials/image
				 * Relative path to the detail image partial template file
				 *
				 * Partials are templates which are reused in other templates and generate
				 * reoccuring blocks filled with data from the assigned values. The image
				 * partial creates an HTML block for the catalog detail images.
				 *
				 * @param string Relative path to the template file
				 * @since 2017.01
				 * @category Developer
				 */
				$this->config( 'client/html/catalog/detail/partials/image', 'catalog/detail/image-partial-bottom.php' ),
				array(
					'product' => $this->detailProductItem,
					'params' => $this->get( 'detailParams', array() ),
					'mediaItems' => $this->get( 'detailMediaItems', array() )
				)
			); ?>


			<div class="catalog-detail-basic">
				<h1 class="name" itemprop="name"><?php echo $enc->html( $this->detailProductItem->getName(), $enc::TRUST ); ?></h1>
				<p class="code">
					<span class="name"><?php echo $enc->html( $this->translate( 'client', 'Article no.:' ), $enc::TRUST ); ?></span>
					<span class="value" itemprop="sku"><?php echo $enc->html( $this->detailProductItem->getCode() ); ?></span>
				</p>
				<?php foreach( $this->detailProductItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
					<p class="short" itemprop="description"><?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?></p>
				<?php endforeach; ?>
			</div>


			<div class="catalog-detail-basket" data-reqstock="<?php echo $reqstock; ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

				<?php if( isset( $this->detailProductItem ) ) : ?>
					<div class="price-list">
						<div class="articleitem price-actual"
							data-prodid="<?php echo $enc->attr( $this->detailProductItem->getId() ); ?>"
							data-prodcode="<?php echo $enc->attr( $this->detailProductItem->getCode() ); ?>">
							<?php echo $this->partial(
								$this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ),
								array( 'prices' => $this->detailProductItem->getRefItems( 'price', null, 'default' ) )
							); ?>
						</div>
					</div>
				<?php endif; ?>


				<?php echo $this->block()->get( 'catalog/detail/service' ); ?>


				<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
					<!-- catalog.detail.csrf -->
					<?php echo $this->csrf()->formfield(); ?>
					<!-- catalog.detail.csrf -->

					<?php if( $this->detailProductItem->getType() === 'select' ) : ?>
						<div class="catalog-detail-basket-selection">
							<?php echo $this->partial(
								/** client/html/common/partials/selection
								 * Relative path to the variant attribute partial template file
								 *
								 * Partials are templates which are reused in other templates and generate
								 * reoccuring blocks filled with data from the assigned values. The selection
								 * partial creates an HTML block for a list of variant product attributes
								 * assigned to a selection product a customer must select from.
								 *
								 * The partial template files are usually stored in the templates/partials/ folder
								 * of the core or the extensions. The configured path to the partial file must
								 * be relative to the templates/ folder, e.g. "partials/selection-default.php".
								 *
								 * @param string Relative path to the template file
								 * @since 2015.04
								 * @category Developer
								 * @see client/html/common/partials/attribute
								 */
								$this->config( 'client/html/common/partials/selection', 'common/partials/selection-default.php' ),
								array(
									'products' => $this->detailProductItem->getRefItems( 'product', 'default', 'default' ),
									'mediaItems' => $this->get( 'detailMediaItems', array() ),
									'productItems' => $this->get( 'detailProductItems', array() ),
									'attributeItems' => $this->get( 'detailAttributeItems', array() ),
								)
							); ?>
						</div>
					<?php endif; ?>

					<div class="catalog-detail-basket-attribute">
						<?php echo $this->partial(
							/** client/html/common/partials/attribute
							 * Relative path to the product attribute partial template file
							 *
							 * Partials are templates which are reused in other templates and generate
							 * reoccuring blocks filled with data from the assigned values. The attribute
							 * partial creates an HTML block for a list of optional product attributes a
							 * customer can select from.
							 *
							 * The partial template files are usually stored in the templates/partials/ folder
							 * of the core or the extensions. The configured path to the partial file must
							 * be relative to the templates/ folder, e.g. "partials/attribute-default.php".
							 *
							 * @param string Relative path to the template file
							 * @since 2016.01
							 * @category Developer
							 * @see client/html/common/partials/selection
							 */
							$this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-default.php' ),
							array(
								'attributeItems' => $this->get( 'detailAttributeItems', array() ),
								'attributeConfigItems' => $this->detailProductItem->getRefItems( 'attribute', null, 'config' ),
								'attributeCustomItems' => $this->detailProductItem->getRefItems( 'attribute', null, 'custom' ),
								'attributeHiddenItems' => $this->detailProductItem->getRefItems( 'attribute', null, 'hidden' ),
							)
						); ?>
					</div>


					<div class="stock-list">
						<div class="articleitem stock-actual"
							data-prodid="<?php echo $enc->attr( $this->detailProductItem->getId() ); ?>"
							data-prodcode="<?php echo $enc->attr( $this->detailProductItem->getCode() ); ?>">
						</div>
						<?php foreach( $this->detailProductItem->getRefItems( 'product', null, 'default' ) as $articleId => $articleItem ) : ?>
							<div class="articleitem"
								data-prodid="<?php echo $enc->attr( $articleId ); ?>"
								data-prodcode="<?php echo $enc->attr( $articleItem->getCode() ); ?>">
							</div>
						<?php endforeach; ?>
					</div>


					<div class="addbasket">
						<div class="group">
							<input type="hidden" value="add"
								name="<?php echo $enc->attr( $this->formparam( 'b_action' ) ); ?>"
							/>
							<input type="hidden"
								name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ); ?>"
								value="<?php echo $enc->attr( $this->detailProductItem->getId() ); ?>"
							/>
							<input type="number"
								name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ); ?>"
								min="1" max="2147483647" maxlength="10" step="1" required="required" value="1"
							/>
							<button class="standardbutton btn-action" type="submit" value="">
								<?php echo $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?>
							</button>
						</div>
					</div>

				</form>

			</div>


			<?php echo $this->block()->get( 'catalog/detail/actions' ); ?>


			<?php echo $this->partial(
				/** client/html/catalog/detail/partials/social
				 * Relative path to the social partial template file
				 *
				 * Partials are templates which are reused in other templates and generate
				 * reoccuring blocks filled with data from the assigned values. The social
				 * partial creates an HTML block for links to social platforms in the
				 * catalog detail component.
				 *
				 * @param string Relative path to the template file
				 * @since 2017.01
				 * @category Developer
				 */
				$this->config( 'client/html/catalog/detail/partials/social', 'catalog/detail/social-partial-default.php' ),
				array( 'product' => $this->detailProductItem )
			); ?>


			<?php if( $this->detailProductItem->getType() === 'bundle'
				&& ( $posItems = $this->detailProductItem->getRefItems( 'product', null, 'default' ) ) !== array()
				&& ( $products = $getProductList( $posItems, $this->get( 'detailProductItems', array() ) ) ) !== array() ) : ?>

				<section class="catalog-detail-bundle">
					<h2 class="header"><?php echo $this->translate( 'client', 'Bundled products' ); ?></h2>
					<?php echo $this->partial(
						$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
						array( 'products' => $products, 'itemprop' => 'isRelatedTo' )
					); ?>
				</section>

			<?php endif; ?>


			<div class="catalog-detail-additional">

				<?php if( ( $textItems = $this->detailProductItem->getRefItems( 'text', 'long' ) ) !== array() ) : ?>
					<div class="additional-box">
						<h2 class="header description"><?php echo $enc->html( $this->translate( 'client', 'Description' ), $enc::TRUST ); ?></h2>
						<div class="content description">
							<?php foreach( $textItems as $textItem ) : ?>
								<div class="long item"><?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?></div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if( count( $attrMap ) > 0 ) : ?>
					<div class="additional-box">
						<h2 class="header attributes"><?php echo $enc->html( $this->translate( 'client', 'Characteristics' ), $enc::TRUST ); ?></h2>
						<div class="content attributes">
							<table class="attributes">
								<tbody>
									<?php foreach( $attrMap as $type => $attrItems ) : ?>
										<?php foreach( $attrItems as $attrItem ) : $classes = ""; ?>
											<?php if( isset( $subAttrDeps[ $attrItem->getId() ] ) ) : ?>
												<?php $classes .= ' subproduct'; ?>
												<?php foreach( $subAttrDeps[ $attrItem->getId() ] as $prodid ) { $classes .= ' subproduct-' . $prodid; } ?>
											<?php endif; ?>
											<tr class="item<?php echo $classes; ?>">
												<td class="name"><?php echo $enc->html( $this->translate( 'client/code', $type ), $enc::TRUST ); ?></td>
												<td class="value">
													<div class="media-list">
														<?php foreach( $attrItem->getListItems( 'media', 'icon' ) as $listItem ) : ?>
															<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
																<?php echo $this->partial(
																	$this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ),
																	array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) )
																); ?>
															<?php endif; ?>
														<?php endforeach; ?>
													</div>
													<span class="attr-name"><?php echo $enc->html( $attrItem->getName() ); ?></span>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>

				<?php if( count( $propMap ) > 0 ) : ?>
					<div class="additional-box">
						<h2 class="header properties"><?php echo $enc->html( $this->translate( 'client', 'Properties' ), $enc::TRUST ); ?></h2>
						<div class="content properties">
							<table class="properties">
								<tbody>
									<?php foreach( $propMap as $type => $propItems ) : ?>
										<?php foreach( $propItems as $propertyItem ) : ?>
											<tr class="item">
												<td class="name"><?php echo $enc->html( $this->translate( 'client/code', $propertyItem->getType() ), $enc::TRUST ); ?></td>
												<td class="value"><?php echo $enc->html( $propertyItem->getValue() ); ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>

				<?php $mediaList = $this->get( 'detailMediaItems', array() ); ?>
				<?php if( ( $mediaItems = $this->detailProductItem->getRefItems( 'media', null, 'download' ) ) !== array() ) : ?>
					<div class="additional-box">
						<h2 class="header downloads"><?php echo $enc->html( $this->translate( 'client', 'Downloads' ), $enc::TRUST ); ?></h2>
						<ul class="content downloads">
							<?php foreach( $mediaItems as $id => $item ) : ?>
								<?php if( isset( $mediaList[$id] ) ) { $item = $mediaList[$id]; } ?>
								<li class="item">
									<a href="<?php echo $this->content( $item->getUrl() ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>">
										<img class="media-image"
											src="<?php echo $this->content( $item->getPreview() ); ?>"
											alt="<?php echo $enc->attr( $item->getName() ); ?>"
										/>
										<span class="media-name"><?php echo $enc->html( $item->getName() ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

			</div>


			<?php if( ( $posItems = $this->detailProductItem->getRefItems( 'product', null, 'suggestion' ) ) !== array()
				&& ( $products = $getProductList( $posItems, $this->get( 'detailProductItems', array() ) ) ) !== array() ) : ?>

				<section class="catalog-detail-suggest">
					<h2 class="header"><?php echo $this->translate( 'client', 'Suggested products' ); ?></h2>
					<?php echo $this->partial(
						$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
						array( 'products' => $products, 'itemprop' => 'isRelatedTo' )
					); ?>
				</section>

			<?php endif; ?>


			<?php if( ( $posItems = $this->detailProductItem->getRefItems( 'product', null, 'bought-together' ) ) !== array()
				&& ( $products = $getProductList( $posItems, $this->get( 'detailProductItems', array() ) ) ) !== array() ) : ?>

				<section class="catalog-detail-bought">
					<h2 class="header"><?php echo $this->translate( 'client', 'Other customers also bought' ); ?></h2>
					<?php echo $this->partial(
						$this->config( 'client/html/common/partials/products', 'common/partials/products-default.php' ),
						array( 'products' => $products, 'itemprop' => 'isRelatedTo' )
					); ?>
				</section>

			<?php endif; ?>


		</article>
	<?php endif; ?>

</section>
