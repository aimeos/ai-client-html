<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

/* Available data:
 * - detailProductItem : Product item incl. referenced items
 */


$enc = $this->encoder();

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
 */
$reqstock = (int) $this->config( 'client/html/basket/require-stock', true );

/** client/html/catalog/detail/basket-add
 * Display the "add to basket" button for each suggested/bought-together product item
 *
 * Enables the button for adding products to the basket for the related products
 * in the basket. This works for all type of products, even for selection products
 * with product variants and product bundles. By default, also optional attributes
 * are displayed if they have been associated to a product.
 *
 * To fetch the variant articles of selection products too, add this setting to
 * your configuration:
 *
 * mshop/common/manager/maxdepth = 3
 *
 * @param boolean True to display the button, false to hide it
 * @since 2021.04
 * @see client/html/catalog/home/basket-add
 * @see client/html/catalog/lists/basket-add
 * @see client/html/catalog/product/basket-add
 * @see client/html/basket/related/basket-add
 */


?>
<section class="aimeos catalog-detail" itemscope itemtype="http://schema.org/Product" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( isset( $this->detailProductItem ) ) : ?>

		<!-- catalog.detail.navigator -->
		<!-- navigator template added by client -->
		<!-- catalog.detail.navigator -->

		<article class="product row <?= $this->detailProductItem->getConfigValue( 'css-class' ) ?>"
			data-id="<?= $this->detailProductItem->getId() ?>" data-reqstock="<?= $reqstock ?>">

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
					 */
					$this->config( 'client/html/catalog/detail/partials/image', 'catalog/detail/image-partial' ),
					['mediaItems' => $this->get( 'detailMediaItems', map() ), 'params' => $this->param()]
				) ?>

			</div>

			<div class="col-sm-6">

				<div class="catalog-detail-basic">
					<?php if( !( $suppliers = $this->detailProductItem->getRefItems( 'supplier' ) )->isEmpty() ) : $name = $suppliers->getName()->first() ?>
						<p class="supplier">
							<a href="<?= $enc->attr( $this->link( 'client/html/supplier/detail/url', ['f_supid' => $suppliers->firstKey(), 's_name' => $name] ) ) ?>">
								<?= $enc->html( $name, $enc::TRUST ) ?>
							</a>
						</p>
					<?php elseif( $this->get( 'contextSite' ) !== 'default' ) : ?>
						<p class="site"><?= $enc->html( $this->get( 'contextSiteLabel' ) ) ?></p>
					<?php endif ?>

					<h1 class="name" itemprop="name"><?= $enc->html( $this->detailProductItem->getName(), $enc::TRUST ) ?></h1>

					<p class="code">
						<span class="name"><?= $enc->html( $this->translate( 'client', 'Article no.' ), $enc::TRUST ) ?>: </span>
						<span class="value" itemprop="sku"><?= $enc->html( $this->detailProductItem->getCode() ) ?></span>
					</p>

					<?php if( $this->detailProductItem->getRating() > 0 ) : ?>
						<div class="rating" itemscope itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating">
							<span class="stars"><?= str_repeat( '★', (int) round( $this->detailProductItem->getRating() ) ) ?></span>
							<span class="rating-value" itemprop="ratingValue"><?= $enc->html( $this->detailProductItem->getRating() ) ?></span>
							<span class="ratings" itemprop="reviewCount"><?= (int) $this->detailProductItem->getRatings() ?></span>
						</div>
					<?php endif ?>

					<?php foreach( $this->detailProductItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
						<div class="short" itemprop="description"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></div>
					<?php endforeach ?>

				</div>


				<div class="catalog-detail-basket" itemscope itemprop="offers" itemtype="http://schema.org/Offer">

					<div class="price-list">
						<div class="articleitem price price-actual" data-prodid="<?= $enc->attr( $this->detailProductItem->getId() ) ?>">

							<?= $this->partial(
								$this->config( 'client/html/common/partials/price', 'common/partials/price' ),
								['prices' => $this->detailProductItem->getRefItems( 'price', null, 'default' )]
							) ?>

						</div>

						<?php if( $this->detailProductItem->getType() === 'select' ) : ?>
							<?php foreach( $this->detailProductItem->getRefItems( 'product', 'default', 'default' ) as $prodid => $product ) : ?>
								<?php if( !( $prices = $product->getRefItems( 'price', null, 'default' ) )->isEmpty() ) : ?>

									<div class="articleitem price" data-prodid="<?= $enc->attr( $prodid ) ?>">

										<?= $this->partial(
											$this->config( 'client/html/common/partials/price', 'common/partials/price' ),
											['prices' => $prices]
										) ?>

									</div>

								<?php endif ?>
							<?php endforeach ?>
						<?php endif ?>

					</div>


					<div class="catalog-detail-service">

						<?php if( !$this->get( 'serviceItems', map() )->isEmpty() ) : ?>
							<span class="service-intro"><?= $enc->html( $this->translate( 'client', '+ shipping costs' ) ) ?></span>
							<ul class="service-list">

								<?php foreach( $this->get( 'serviceItems', [] ) as $item ) : ?>
									<li class="service-item">
										<span class="service-name"><?= $enc->html( $item->getName() ) ?></span>

										<?= $this->partial(
											$this->config( 'client/html/common/partials/price', 'common/partials/price' ),
											array( 'prices' => $item->getRefItems( 'price', null, 'default' ), 'costsItem' => false, 'all' => true )
										) ?>

										<?php foreach( $item->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
											<span class="service-short"><?= $enc->html( $textItem->getContent() ) ?></span>
										<?php endforeach ?>
									</li>

								<?php endforeach ?>

							</ul>
						<?php endif ?>

					</div>


					<form class="basket" method="POST" action="<?= $enc->attr( $this->link( 'client/html/basket/standard/url', ( $basketSite ? ['site' => $basketSite] : [] ) ) ) ?>">
						<!-- catalog.detail.csrf -->
						<?= $this->csrf()->formfield() ?>
						<!-- catalog.detail.csrf -->

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
									 * be relative to the templates/ folder, e.g. "common/partials/selection".
									 *
									 * @param string Relative path to the template file
									 * @since 2015.04
									 * @see client/html/common/partials/attribute
									 */
									$this->config( 'client/html/common/partials/selection', 'common/partials/selection' ),
									[
										'productItems' => $this->detailProductItem->getRefItems( 'product', null, 'default' ),
										'productItem' => $this->detailProductItem
									]
								) ?>

							</div>

						<?php elseif( $this->detailProductItem->getType() === 'group' ) : ?>

							<div class="catalog-detail-basket-selection">

								<?= $this->partial(
									/** client/html/common/partials/group
									 * Relative path to the group product partial template file
									 *
									 * Partials are templates which are reused in other templates and generate
									 * reoccuring blocks filled with data from the assigned values. The group
									 * partial creates an HTML block for a list of sub-products assigned to a
									 * group product a customer can select from.
									 *
									 * The partial template files are usually stored in the templates/partials/ folder
									 * of the core or the extensions. The configured path to the partial file must
									 * be relative to the templates/ folder, e.g. "common/partials/selection-list".
									 *
									 * @param string Relative path to the template file
									 * @since 2021.07
									 * @see client/html/common/partials/attribute
									 */
									$this->config( 'client/html/common/partials/group', 'common/partials/selection-list' ),
									[
										'productItems' => $this->detailProductItem->getRefItems( 'product', null, 'default' ),
										'productItem' => $this->detailProductItem
									]
								) ?>

							</div>

						<?php endif ?>

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
								 * be relative to the templates/ folder, e.g. "partials/attribute.php".
								 *
								 * @param string Relative path to the template file
								 * @since 2016.01
								 * @see client/html/common/partials/selection
								 */
								$this->config( 'client/html/common/partials/attribute', 'common/partials/attribute' ),
								['productItem' => $this->detailProductItem]
							) ?>

						</div>


						<div class="stock-list">
							<div class="articleitem <?= !in_array( $this->detailProductItem->getType(), ['select', 'group'] ) ? 'stock-actual' : '' ?>"
								data-prodid="<?= $enc->attr( $this->detailProductItem->getId() ) ?>">
							</div>

							<?php foreach( $this->detailProductItem->getRefItems( 'product', null, 'default' ) as $articleId => $articleItem ) : ?>

								<div class="articleitem" data-prodid="<?= $enc->attr( $articleId ) ?>"></div>

							<?php endforeach ?>

						</div>


						<?php if( !$this->detailProductItem->getRefItems( 'price', 'default', 'default' )->empty() ) : ?>
							<div class="addbasket">
								<div class="input-group">
									<input type="hidden" value="add" name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>">
									<input type="hidden"
										name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'prodid'] ) ) ?>"
										value="<?= $enc->attr( $this->detailProductItem->getId() ) ?>"
									>
									<input type="hidden"
										name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'supplier'] ) ) ?>"
										value="<?= $enc->attr( $this->detailProductItem->getRefItems( 'supplier' )->getId()->first() ) ?>"
									>
									<?php if( $basketSite ) : ?>
										<input type="hidden"
											name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'siteid'] ) ) ?>"
											value="<?= $enc->attr( $basketSite ) ?>"
										>
									<?php endif ?>
									<?php if( $this->detailProductItem->getType() !== 'group' ) : ?>
										<input type="number" class="form-control input-lg" <?= !$this->detailProductItem->isAvailable() ? 'disabled' : '' ?>
											name="<?= $enc->attr( $this->formparam( ['b_prod', 0, 'quantity'] ) ) ?>"
											step="<?= $this->detailProductItem->getScale() ?>"
											min="<?= $this->detailProductItem->getScale() ?>" max="2147483647"
											value="<?= $this->detailProductItem->getScale() ?>" required="required"
											title="<?= $enc->attr( $this->translate( 'client', 'Quantity' ) ) ?>"
										>
									<?php endif ?>
									<button class="btn btn-primary btn-lg btn-action" type="submit" value="" <?= !$this->detailProductItem->isAvailable() ? 'disabled' : '' ?>>
										<?= $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>
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
					 */
					$this->config( 'client/html/catalog/partials/actions', 'catalog/actions-partial' ),
					['productItem' => $this->detailProductItem]
				) ?>


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
					 */
					$this->config( 'client/html/catalog/partials/social', 'catalog/social-partial' ),
					['productItem' => $this->detailProductItem]
				) ?>

			</div>

			<div class="col-sm-12">
				<div class="catalog-detail-additional content-block">
					<nav>
						<div class="nav nav-tabs" id="nav-tab" role="tablist">

						    <?php if( !( $textItems = $this->detailProductItem->getRefItems( 'text', 'long' ) )->isEmpty() ) : ?>
							    <a class="nav-link active" id="nav-description-tab" data-bs-toggle="tab" data-bs-target="#nav-description" type="button" role="tab" aria-controls="nav-description" aria-selected="true">
								    <?= $enc->html( $this->translate( 'client', 'Description' ), $enc::TRUST ) ?>
							    </a>
						    <?php endif ?>

						    <?php if( !$this->get( 'detailAttributeMap', map() )->isEmpty() || !$this->get( 'detailPropertyMap', map() )->isEmpty() ) : ?>
							    <a class="nav-link nav-attribute" id="nav-attribute-tab" data-bs-toggle="tab" data-bs-target="#nav-attribute" type="button" role="tab" aria-controls="nav-attribute">
								    <?= $enc->html( $this->translate( 'client', 'Characteristics' ), $enc::TRUST ) ?>
							    </a>
						    <?php endif ?>

						    <?php if( !( $mediaItems = $this->detailProductItem->getRefItems( 'media', 'download' ) )->isEmpty() ) : ?>
							    <a class="nav-link nav-characteristics" id="nav-characteristics-tab" data-bs-toggle="tab" data-bs-target="#nav-characteristics" type="button" role="tab" aria-controls="nav-characteristics">
								    <h2 class="header downloads"><?= $enc->html( $this->translate( 'client', 'Downloads' ), $enc::TRUST ) ?></h2>
							    </a>
						    <?php endif ?>

						    <a class="nav-link nav-review" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review">
							    <?= $enc->html( $this->translate( 'client', 'Reviews' ), $enc::TRUST ) ?>
							    <span class="ratings"><?= $enc->html( $this->detailProductItem->getRatings() ) ?></span>
						    </a>
						</div>
					</nav>

					<div class="tab-content" id="nav-tabContent">

						<div class="tab-pane fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">

							<?php if( !( $textItems = $this->detailProductItem->getRefItems( 'text', 'long' ) )->isEmpty() ) : ?>

								<div class="block description">

									<?php foreach( $textItems as $textItem ) : ?>
										<div class="long item"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></div>
									<?php endforeach ?>

								</div>

							<?php endif ?>

						</div>

						<div class="tab-pane fade" id="nav-attribute" role="tabpanel" aria-labelledby="nav-attribute-tab">

							<?php if( !$this->get( 'detailAttributeMap', map() )->isEmpty() || !$this->get( 'detailPropertyMap', map() )->isEmpty() ) : ?>

								<div class="block attributes">
									<table class="attributes">
										<tbody>

											<?php foreach( $this->get( 'detailAttributeMap', map() ) as $type => $attrItems ) : ?>
												<?php foreach( $attrItems as $attrItem ) : ?>

													<tr class="item <?= ( $ids = $attrItem->get( 'parent' ) ) ? 'subproduct ' . map( $ids )->prefix( 'subproduct-' )->join( ' ' ) : '' ?>">
														<td class="name"><?= $enc->html( $this->translate( 'client/code', $type ), $enc::TRUST ) ?></td>
														<td class="value">
															<div class="media-list">

																<?php foreach( $attrItem->getListItems( 'media', 'icon' ) as $listItem ) : ?>
																	<?php if( ( $refitem = $listItem->getRefItem() ) !== null ) : ?>
																		<?= $this->partial(
																			$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
																			['item' => $refitem, 'boxAttributes' => ['class' => 'media-item']]
																		) ?>
																	<?php endif ?>
																<?php endforeach ?>

															</div><!--
															--><span class="attr-name"><?= $enc->html( $attrItem->getName() ) ?></span>

															<?php foreach( $attrItem->getRefItems( 'text', 'short' ) as $textItem ) : ?>
																<div class="attr-short"><?= $enc->html( $textItem->getContent() ) ?></div>
															<?php endforeach ?>

															<?php foreach( $attrItem->getRefItems( 'text', 'long' ) as $textItem ) : ?>
																<div class="attr-long"><?= $enc->html( $textItem->getContent() ) ?></div>
															<?php endforeach ?>

														</td>
													</tr>

												<?php endforeach ?>
											<?php endforeach ?>

											<?php foreach( $this->get( 'detailPropertyMap', map() ) as $type => $propItems ) : ?>
												<?php foreach( $propItems as $propItem ) : ?>

													<tr class="item <?= ( $id = $propItem->get( 'parent' ) ) ? 'subproduct subproduct-' . $id : '' ?>">
														<td class="name"><?= $enc->html( $this->translate( 'client/code', $propItem->getType() ), $enc::TRUST ) ?></td>
														<td class="value"><?= $enc->html( $propItem->getValue() ) ?></td>
													</tr>

												<?php endforeach ?>
											<?php endforeach ?>

										</tbody>
									</table>
								</div>

							<?php endif ?>
						</div>

						<div class="tab-pane fade" id="nav-characteristics" role="tabpanel" aria-labelledby="nav-characteristics-tab">
							<?php if( !( $mediaItems = $this->detailProductItem->getRefItems( 'media', 'download' ) )->isEmpty() ) : ?>

								<ul class="block downloads">

									<?php foreach( $mediaItems as $id => $mediaItem ) : ?>

										<li class="item">
											<a href="<?= $this->content( $mediaItem->getUrl(), $mediaItem->getFileSystem() ) ?>"
												title="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first( $mediaItem->getLabel() ) ) ?>">
												<img class="media-image"
													alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first( $mediaItem->getLabel() ) ) ?>"
													src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
													srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews(), $mediaItem->getFileSystem() ) ) ?>"
												>
												<span class="media-name"><?= $enc->html( $mediaItem->getProperties( 'title' )->first( $mediaItem->getLabel() ) ) ?></span>
											</a>
										</li>

									<?php endforeach ?>

								</ul>

							<?php endif ?>
						</div>

						<div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
							<div class="reviews container-fluid block" data-productid="<?= $enc->attr( $this->detailProductItem->getId() ) ?>">
								<div class="row">
									<div class="col-md-4 rating-list">
										<div class="rating-numbers">
											<div class="rating-num"><?= number_format( $this->detailProductItem->getRating(), 1 ) ?>/5</div>
											<div class="rating-total"><?= $enc->html( sprintf( $this->translate( 'client', '%1$d review', '%1$d reviews', $this->detailProductItem->getRatings() ), $this->detailProductItem->getRatings() ) ) ?></div>
											<div class="rating-stars"><?= str_repeat( '★', (int) round( $this->detailProductItem->getRating() ) ) ?></div>
										</div>
										<table class="rating-dist" data-ratings="<?= $enc->attr( $this->detailProductItem->getRatings() ) ?>">
											<tr>
												<td class="rating-label"><label for="rating-5">★★★★★</label></td>
												<td class="rating-percent"><progress id="rating-5" value="0" max="100">0</progress></td>
											</tr>
											<tr>
												<td class="rating-label"><label for="rating-4">★★★★</label></td>
												<td class="rating-percent"><progress id="rating-4" value="0" max="100">0</progress></td>
											</tr>
											<tr>
												<td class="rating-label"><label for="rating-3">★★★</label></td>
												<td class="rating-percent"><progress id="rating-3" value="0" max="100">0</progress></td>
											</tr>
											<tr>
												<td class="rating-label"><label for="rating-2">★★</label></td>
												<td class="rating-percent"><progress id="rating-2" value="0" max="100">0</progress></td>
											</tr>
											<tr>
												<td class="rating-label"><label for="rating-1">★</label></td>
												<td class="rating-percent"><progress id="rating-1" value="0" max="100">0</progress></td>
											</tr>
										</table>
									</div>
									<div class="col-md-8 review-list">
										<div class="sort">
											<span><?= $enc->html( $this->translate( 'client', 'Sort by:' ), $enc::TRUST ) ?></span>
											<ul>
												<li>
													<a class="sort-option option-ctime active" href="<?= $enc->attr( $this->link( 'client/jsonapi/url', ['resource' => 'review', 'filter' => ['f_refid' => $this->detailProductItem->getId()], 'sort' => '-ctime'] ) ) ?>">
														<?= $enc->html( $this->translate( 'client', 'Latest' ), $enc::TRUST ) ?>
													</a>
												</li>
												<li>
													<a class="sort-option option-rating" href="<?= $enc->attr( $this->link( 'client/jsonapi/url', ['resource' => 'review', 'filter' => ['f_refid' => $this->detailProductItem->getId()], 'sort' => '-rating,-ctime'] ) ) ?>">
														<?= $enc->html( $this->translate( 'client', 'Rating' ), $enc::TRUST ) ?>
													</a>
												</li>
											</ul>
										</div>
										<div class="review-items">
											<div class="review-item prototype">
												<div class="review-name"></div>
												<div class="review-ctime"></div>
												<div class="review-rating">★</div>
												<div class="review-comment"></div>
												<div class="review-response">
													<div class="review-vendor"><?= $enc->html( $this->translate( 'client', 'Vendor response' ) ) ?></div>
												</div>
												<div class="review-show"><a href="#"><?= $enc->html( $this->translate( 'client', 'Show' ) ) ?></a></div><!--
											--></div>
										</div>
										<a class="btn btn-primary more" href="#"><?= $enc->html( $this->translate( 'client', 'More reviews' ), $enc::TRUST ) ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<?php if( $this->detailProductItem->getType() === 'bundle' && !( $products = $this->detailProductItem->getRefItems( 'product', null, 'default' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-bundle content-block">
						<h2 class="header"><?= $this->translate( 'client', 'Bundled products' ) ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products' ),
							['products' => $products, 'itemprop' => 'isRelatedTo']
						) ?>

					</section>

				<?php endif ?>


				<?php if( !( $products = $this->detailProductItem->getRefItems( 'product', null, 'suggestion' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-suggest content-block">
						<h2 class="header"><?= $this->translate( 'client', 'Suggested products' ) ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products' ), [
								'basket-add' => $this->config( 'client/html/catalog/detail/basket-add', false ),
								'products' => $products, 'itemprop' => 'isRelatedTo'
							] )
						?>

					</section>

				<?php endif ?>


				<?php if( !( $products = $this->detailProductItem->getRefItems( 'product', null, 'bought-together' ) )->isEmpty() ) : ?>

					<section class="catalog-detail-bought content-block">
						<h2 class="header"><?= $this->translate( 'client', 'Other customers also bought' ) ?></h2>

						<?= $this->partial(
							$this->config( 'client/html/common/partials/products', 'common/partials/products' ), [
								'basket-add' => $this->config( 'client/html/catalog/detail/basket-add', false ),
								'products' => $products, 'itemprop' => 'isRelatedTo'
							] )
						?>

					</section>

				<?php endif ?>

				<?php if( !( $supplierItems = $this->detailProductItem->getRefItems( 'supplier', null, 'default' ) )->isEmpty() ) : ?>
					<div class="catalog-detail-supplier content-block">

						<h2 class="header"><?= $this->translate( 'client', 'Supplier information' ) ?></h2>

						<?php foreach( $supplierItems as $supplierItem ) : ?>

							<div class="content supplier">

								<?php if( ( $mediaItem = $supplierItem->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) : ?>
									<div class="media-item">
										<img class="lazy-image"
											alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
											data-src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
											data-srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews(), $mediaItem->getFileSystem() ) ) ?>"
											sizes="<?= $enc->attr( $this->config( 'client/html/common/imageset-sizes', '240px' ) ) ?>"
										>
									</div>
								<?php endif ?>

								<h3 class="supplier-name">
									<?= $enc->html( $supplierItem->getName(), $enc::TRUST ) ?>

									<?php if( ( $addrItem = $supplierItem->getAddressItems()->first() ) !== null ) : ?>
										<span class="supplier-address">(<?= $enc->html( $addrItem->getCity() ) ?>, <?= $enc->html( $addrItem->getCountryId() ) ?>)</span>
									<?php endif ?>
								</h3>

								<?php foreach( $supplierItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
									<p class="supplier-short"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></p>
								<?php endforeach ?>

								<?php foreach( $supplierItem->getRefItems( 'text', 'long', 'default' ) as $textItem ) : ?>
									<p class="supplier-long"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></p>
								<?php endforeach ?>

							</div>

						<?php endforeach ?>

					</div>
					<?php endif ?>

			</div>

		</article>

	<?php endif ?>

</section>