<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

/* Available data:
 * - detailProductItem : Product item incl. referenced items
 */


$enc = $this->encoder();

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );
$basketSite = $this->config( 'client/html/basket/standard/url/site' );


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


?>
<section class="aimeos catalog-detail" itemscope="" itemtype="http://schema.org/Product" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ); ?>">

	<?php if( isset( $this->detailErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->detailErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<?php if( isset( $this->detailProductItem ) ) : ?>

		<article class="product row <?= $this->detailProductItem->getConfigValue( 'css-class' ) ?>" data-id="<?= $this->detailProductItem->getId(); ?>">

			<div class="col-sm-6">
				<?= $this->partial(
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
					$this->config( 'client/html/catalog/detail/partials/image', 'catalog/detail/image-partial-standard' ),
					['mediaItems' => $this->get( 'detailMediaItems', map() ), 'params' => $this->param()]
				); ?>

			</div>

			<div class="col-sm-6">

				<div class="catalog-detail-basic">
					<h1 class="name" itemprop="name"><?= $enc->html( $this->detailProductItem->getName(), $enc::TRUST ); ?></h1>
					<p class="code">
						<span class="name"><?= $enc->html( $this->translate( 'client', 'Article no.' ), $enc::TRUST ); ?>: </span>
						<span class="value" itemprop="sku"><?= $enc->html( $this->detailProductItem->getCode() ); ?></span>
					</p>

					<?php foreach( $this->detailProductItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
						<p class="short" itemprop="description"><?= $enc->html( $textItem->getContent(), $enc::TRUST ); ?></p>
					<?php endforeach; ?>

				</div>


				<div class="catalog-detail-basket" data-reqstock="<?= $reqstock; ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

					<div class="price-list">
						<div class="articleitem price price-actual"
							data-prodid="<?= $enc->attr( $this->detailProductItem->getId() ); ?>"
							data-prodcode="<?= $enc->attr( $this->detailProductItem->getCode() ); ?>">

							<?= $this->partial(
								$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
								['prices' => $this->detailProductItem->getRefItems( 'price', null, 'default' )]
							); ?>

						</div>

						<?php if( $this->detailProductItem->getType() === 'select' ) : ?>
							<?php foreach( $this->detailProductItem->getRefItems( 'product', 'default', 'default' ) as $prodid => $product ) : ?>
								<?php if( !( $prices = $product->getRefItems( 'price', null, 'default' ) )->isEmpty() ) : ?>

									<div class="articleitem price"
										data-prodid="<?= $enc->attr( $prodid ); ?>"
										data-prodcode="<?= $enc->attr( $product->getCode() ); ?>">

										<?= $this->partial(
											$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
											['prices' => $prices]
										); ?>

									</div>

								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>

					</div>


					<?= $this->block()->get( 'catalog/detail/service' ); ?>


					<form method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, ( $basketSite ? ['site' => $basketSite] : [] ), [], $basketConfig ) ); ?>">
						<!-- catalog.detail.csrf -->
						<?= $this->csrf()->formfield(); ?>
						<!-- catalog.detail.csrf -->

						<?php if( $basketSite ) : ?>
							<input type="hidden" name="<?= $this->formparam( 'site' ) ?>" value="<?= $enc->attr( $basketSite ) ?>" />
						<?php endif ?>

						<?php if( $this->detailProductItem->getType() === 'select' ) : ?>

							<div class="catalog-detail-basket-selection">

								<?= $this->partial(
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
									 * be relative to the templates/ folder, e.g. "partials/selection-standard.php".
									 *
									 * @param string Relative path to the template file
									 * @since 2015.04
									 * @category Developer
									 * @see client/html/common/partials/attribute
									 */
									$this->config( 'client/html/common/partials/selection', 'common/partials/selection-standard' ),
									['productItems' => $this->detailProductItem->getRefItems( 'product', 'default', 'default' )]
								); ?>

							</div>

						<?php endif; ?>

						<div class="catalog-detail-basket-attribute">

							<?= $this->partial(
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
								 * be relative to the templates/ folder, e.g. "partials/attribute-standard.php".
								 *
								 * @param string Relative path to the template file
								 * @since 2016.01
								 * @category Developer
								 * @see client/html/common/partials/selection
								 */
								$this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-standard' ),
								['productItem' => $this->detailProductItem]
							); ?>

						</div>


						<div class="stock-list">
							<div class="articleitem stock-actual"
								data-prodid="<?= $enc->attr( $this->detailProductItem->getId() ); ?>"
								data-prodcode="<?= $enc->attr( $this->detailProductItem->getCode() ); ?>">
							</div>

							<?php foreach( $this->detailProductItem->getRefItems( 'product', null, 'default' ) as $articleId => $articleItem ) : ?>

								<div class="articleitem"
									data-prodid="<?= $enc->attr( $articleId ); ?>"
									data-prodcode="<?= $enc->attr( $articleItem->getCode() ); ?>">
								</div>

							<?php endforeach; ?>

						</div>


						<?php if( !$this->detailProductItem->getRefItems( 'price', 'default', 'default' )->empty() ) : ?>
							<div class="addbasket">
								<div class="input-group">
									<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ); ?>" />
									<input type="hidden"
										name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'prodid'] ) ); ?>"
										value="<?= $enc->attr( $this->detailProductItem->getId() ); ?>"
									/>
									<input type="number" class="form-control input-lg" <?= !$this->detailProductItem->isAvailable() ? 'disabled' : '' ?>
										name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'quantity'] ) ); ?>"
										min="<?= $this->detailProductItem->getScale() ?>" max="2147483647"
										step="<?= $this->detailProductItem->getScale() ?>" maxlength="10"
										value="<?= $this->detailProductItem->getScale() ?>" required="required"
									/>
									<button class="btn btn-primary btn-lg" type="submit" value="" <?= !$this->detailProductItem->isAvailable() ? 'disabled' : '' ?> >
										<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?>
									</button>
								</div>
							</div>
						<?php endif ?>

					</form>

				</div>


				<?= $this->partial(
					/** client/html/catalog/partials/actions
					 * Relative path to the catalog actions partial template file
					 *
					 * Partials are templates which are reused in other templates and generate
					 * reoccuring blocks filled with data from the assigned values. The actions
					 * partial creates an HTML block for the product actions (pin, like and watch
					 * products).
					 *
					 * @param string Relative path to the template file
					 * @since 2017.04
					 * @category Developer
					 */
					$this->config( 'client/html/catalog/partials/actions', 'catalog/actions-partial-standard' ),
					['productItem' => $this->detailProductItem]
				); ?>


				<?= $this->partial(
					/** client/html/catalog/partials/social
					 * Relative path to the social partial template file
					 *
					 * Partials are templates which are reused in other templates and generate
					 * reoccuring blocks filled with data from the assigned values. The social
					 * partial creates an HTML block for links to social platforms in the
					 * catalog components.
					 *
					 * @param string Relative path to the template file
					 * @since 2017.04
					 * @category Developer
					 */
					$this->config( 'client/html/catalog/partials/social', 'catalog/social-partial-standard' ),
					['productItem' => $this->detailProductItem]
				); ?>

			</div>


			<div class="col-sm-12">

				<?php if( $this->detailProductItem->getType() === 'bundle' && !( $products = $this->detailProductItem->getRefItems( 'product', null, 'default' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-bundle">
						<h2 class="header"><?= $this->translate( 'client', 'Bundled products' ); ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
							['products' => $products, 'itemprop' => 'isRelatedTo']
						); ?>

					</section>

				<?php endif; ?>


				<div class="catalog-detail-additional">

					<?php if( !( $textItems = $this->detailProductItem->getRefItems( 'text', 'long' ) )->isEmpty() ) : ?>

						<div class="additional-box">
							<h2 class="header description"><?= $enc->html( $this->translate( 'client', 'Description' ), $enc::TRUST ); ?></h2>
							<div class="content description">

								<?php foreach( $textItems as $textItem ) : ?>
									<div class="long item"><?= $enc->html( $textItem->getContent(), $enc::TRUST ); ?></div>
								<?php endforeach; ?>

							</div>
						</div>

					<?php endif; ?>


					<?php if( !( $attrMap = $this->get( 'detailAttributeMap', map() ) )->isEmpty() ) : ?>

						<div class="additional-box">
							<h2 class="header attributes"><?= $enc->html( $this->translate( 'client', 'Characteristics' ), $enc::TRUST ); ?></h2>
							<div class="content attributes">
								<table class="attributes">
									<tbody>

										<?php foreach( $attrMap as $type => $attrItems ) : ?>
											<?php foreach( $attrItems as $attrItem ) : ?>

												<tr class="item <?= ( $id = $attrItem->get( 'parent' ) ) ? 'subproduct subproduct-' . $id : '' ?>">
													<td class="name"><?= $enc->html( $this->translate( 'client/code', $type ), $enc::TRUST ); ?></td>
													<td class="value">
														<div class="media-list">

															<?php foreach( $attrItem->getListItems( 'media', 'icon' ) as $listItem ) : ?>
																<?php if( ( $refitem = $listItem->getRefItem() ) !== null ) : ?>
																	<?= $this->partial(
																		$this->config( 'client/html/common/partials/media', 'common/partials/media-standard' ),
																		['item' => $refitem, 'boxAttributes' => ['class' => 'media-item']]
																	); ?>
																<?php endif; ?>
															<?php endforeach; ?>

														</div>
														<span class="attr-name"><?= $enc->html( $attrItem->getName() ); ?></span>

														<?php foreach( $attrItem->getRefItems( 'text', 'short' ) as $textItem ) : ?>
															<div class="attr-short"><?= $enc->html( $textItem->getContent() ); ?></div>
														<?php endforeach ?>

														<?php foreach( $attrItem->getRefItems( 'text', 'long' ) as $textItem ) : ?>
															<div class="attr-long"><?= $enc->html( $textItem->getContent() ); ?></div>
														<?php endforeach ?>

													</td>
												</tr>

											<?php endforeach; ?>
										<?php endforeach; ?>

									</tbody>
								</table>
							</div>
						</div>

					<?php endif; ?>


					<?php if( !( $propMap = $this->get( 'detailPropertyMap', map() ) )->isEmpty() ) : ?>

						<div class="additional-box">
							<h2 class="header properties"><?= $enc->html( $this->translate( 'client', 'Properties' ), $enc::TRUST ); ?></h2>
							<div class="content properties">
								<table class="properties">
									<tbody>

										<?php foreach( $propMap as $type => $propItems ) : ?>
											<?php foreach( $propItems as $propItem ) : ?>

												<tr class="item <?= ( $id = $propItem->get( 'parent' ) ) ? 'subproduct subproduct-' . $id : '' ?>">
													<td class="name"><?= $enc->html( $this->translate( 'client/code', $propItem->getType() ), $enc::TRUST ); ?></td>
													<td class="value"><?= $enc->html( $propItem->getValue() ); ?></td>
												</tr>

											<?php endforeach; ?>
										<?php endforeach; ?>

									</tbody>
								</table>
							</div>
						</div>

					<?php endif; ?>


					<?php if( !( $mediaItems = $this->detailProductItem->getRefItems( 'media', 'download' ) )->isEmpty() ) : ?>

						<div class="additional-box">
							<h2 class="header downloads"><?= $enc->html( $this->translate( 'client', 'Downloads' ), $enc::TRUST ); ?></h2>
							<ul class="content downloads">

								<?php foreach( $mediaItems as $id => $item ) : ?>

									<li class="item">
										<a href="<?= $this->content( $item->getUrl() ); ?>" title="<?= $enc->attr( $item->getName() ); ?>">
											<img class="media-image"
												src="<?= $this->content( $item->getPreview() ); ?>"
												alt="<?= $enc->attr( $item->getName() ); ?>"
											/>
											<span class="media-name"><?= $enc->html( $item->getName() ); ?></span>
										</a>
									</li>

								<?php endforeach; ?>

							</ul>
						</div>

					<?php endif; ?>

				</div>


				<?php if( !( $products = $this->detailProductItem->getRefItems( 'product', null, 'suggestion' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-suggest">
						<h2 class="header"><?= $this->translate( 'client', 'Suggested products' ); ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
							['products' => $products, 'itemprop' => 'isRelatedTo']
						); ?>

					</section>

				<?php endif; ?>


				<?php if( !( $products = $this->detailProductItem->getRefItems( 'product', null, 'bought-together' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-bought">
						<h2 class="header"><?= $this->translate( 'client', 'Other customers also bought' ); ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
							['products' => $products, 'itemprop' => 'isRelatedTo']
						); ?>

					</section>

				<?php endif; ?>


				<?= $this->block()->get( 'catalog/detail/supplier' ); ?>

			</div>

		</article>

	<?php endif; ?>

</section>
