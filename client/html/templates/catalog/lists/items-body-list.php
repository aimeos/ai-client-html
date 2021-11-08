<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

$enc = $this->encoder();
$position = $this->get( 'itemPosition' );


$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );

$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
$basketConfig = $this->config( 'client/html/basket/standard/url/config', [] );
$basketSite = $this->config( 'client/html/basket/standard/url/site' );


?>
<?php $this->block()->start( 'catalog/lists/items' ) ?>
<div class="catalog-list-items">

	<div class="list-items list"><!--

		<?php foreach( $this->get( 'listProductItems', [] ) as $id => $productItem ) : $firstImage = true ?>
			<?php
				$params = array_diff_key( ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => $position !== null ? $position++ : ''], $detailFilter );
				$url = $this->url( ( $productItem->getTarget() ?: $detailTarget ), $detailController, $detailAction, $params, [], $detailConfig );
			?>

			--><div class="product" itemscope itemtype="http://schema.org/Product"
				data-reqstock="<?= (int) $this->config( 'client/html/basket/require-stock', true ) ?>">

				<div class="product-item <?= $enc->attr( $productItem->getConfigValue( 'css-class' ) ) ?>">
					<div class="badges">
						<span class="badge-item new"><?= $enc->html( $this->translate( 'client', 'New' ) ) ?></span>
						<span class="badge-item sale"><?= $enc->html( $this->translate( 'client', 'Sale' ) ) ?></span>
					</div>

					<a class="media-list" href="<?= $url ?>" title="<?= $enc->attr( $productItem->getName(), $enc::TRUST ) ?>">
						<?php if( ( $mediaItem = $productItem->getRefItems( 'media', 'default', 'default' )->first() ) !== null ) : ?>
							<noscript>
								<div class="media-item" itemscope itemtype="http://schema.org/ImageObject">
									<img alt="<?= $enc->attr( $mediaItem->getName() ) ?>"
										src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>"
										srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
										alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
									>
									<meta itemprop="contentUrl" content="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>">
								</div>
							</noscript>

							<?php foreach( $productItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
								<div class="media-item">
									<img class="lazy-image"
										src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEEAAEALAAAAAABAAEAAAICTAEAOw=="
										sizes="<?= $enc->attr( $this->config( 'client/html/common/imageset-sizes', '240px' ) ) ?>"
										data-src="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>"
										data-srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
										alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
									>
									<meta itemprop="contentUrl" content="<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>">
								</div>
							<?php endforeach ?>
						<?php endif ?>
					</a><!--

					--><div class="list-column"><!--
						--><div class="rating"><!--
							<?php if( $productItem->getRating() > 0 ) : ?>
								--><span class="stars"><?= str_repeat( '★', (int) round( $productItem->getRating() ) ) ?></span><!--
							<?php endif ?>
						--></div><!--


						--><a class="text-list" href="<?= $url ?>">
							<?php if( !( $suppliers = $productItem->getSupplierItems() )->isEmpty() ) : ?>
								<h3 class="supplier"><?= $enc->html( $suppliers->getName()->first(), $enc::TRUST ) ?></h3>
							<?php endif ?>

							<h2 itemprop="name"><?= $enc->html( $productItem->getName(), $enc::TRUST ) ?></h2>

							<?php foreach( $productItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
								<div class="text-item" itemprop="description">
									<?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?><br>
								</div>
							<?php endforeach ?>
						</a><!--


						--><div class="offer" itemscope itemprop="offers" itemtype="http://schema.org/Offer">
							<div class="stock-list">
								<div class="articleitem <?= !in_array( $productItem->getType(), ['group'] ) ? 'stock-actual' : '' ?>"
									data-prodid="<?= $enc->attr( $productItem->getId() ) ?>">
								</div>
								<?php foreach( $productItem->getRefItems( 'product', null, 'default' ) as $articleId => $articleItem ) : ?>
									<div class="articleitem" data-prodid="<?= $enc->attr( $articleId ) ?>"></div>
								<?php endforeach ?>
							</div>
							<div class="price-list">
								<div class="articleitem price price-actual" data-prodid="<?= $enc->attr( $id ) ?>">
									<?= $this->partial(
										$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
										['prices' => $productItem->getRefItems( 'price', null, 'default' )]
									) ?>
								</div>

								<?php if( $productItem->getType() === 'select' ) : ?>
									<?php foreach( $productItem->getRefItems( 'product', 'default', 'default' ) as $prodid => $product ) : ?>
										<?php if( !( $prices = $product->getRefItems( 'price', null, 'default' ) )->isEmpty() ) : ?>
											<div class="articleitem price" data-prodid="<?= $enc->attr( $prodid ) ?>">
												<?= $this->partial(
													$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
													['prices' => $prices]
												) ?>
											</div>
										<?php endif ?>
									<?php endforeach ?>
								<?php endif ?>
							</div>
						</div>


						<?php if( $this->config( 'client/html/catalog/lists/basket-add', false ) ) : ?>
							<form class="basket" method="POST" action="<?= $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, ( $basketSite ? ['site' => $basketSite] : [] ), [], $basketConfig ) ) ?>">
								<!-- catalog.lists.items.csrf -->
								<?= $this->csrf()->formfield() ?>
								<!-- catalog.lists.items.csrf -->

								<?php if( $basketSite ) : ?>
									<input type="hidden" name="<?= $this->formparam( 'site' ) ?>" value="<?= $enc->attr( $basketSite ) ?>">
								<?php endif ?>

								<?php if( $productItem->getType() === 'select' ) : ?>
									<div class="items-selection">
										<?= $this->partial( $this->config( 'client/html/common/partials/selection', 'common/partials/selection-standard' ), [
											'productItems' => $productItem->getRefItems( 'product', 'default', 'default' ),
											'productItem' => $productItem
										] ) ?>
									</div>
								<?php endif ?>

								<div class="items-attribute">
									<?= $this->partial(
										$this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-standard' ),
										['productItem' => $productItem]
									) ?>
								</div>

								<?php if( !$productItem->getRefItems( 'price', 'default', 'default' )->empty() ) : ?>
									<div class="addbasket">
										<div class="input-group">
											<input type="hidden"
												name="<?= $enc->attr( $this->formparam( 'b_action' ) ) ?>"
												value="add"
											>
											<input type="hidden"
												name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ) ?>"
												value="<?= $id ?>"
											>
											<input type="number" max="2147483647"
												value="<?= $enc->attr( $productItem->getScale() ) ?>"
												min="<?= $enc->attr( $productItem->getScale() ) ?>"
												step="<?= $enc->attr( $productItem->getScale() ) ?>"
												required="required" <?= !$productItem->isAvailable() ? 'disabled' : '' ?>
												name="<?= $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ) ?>"
												title="<?= $enc->attr( $this->translate( 'client', 'Quantity' ), $enc::TRUST ) ?>"
											><!--
											--><button class="btn btn-primary" type="submit"
												title="<?= $enc->attr( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ) ?>"
												<?= !$productItem->isAvailable() ? 'disabled' : '' ?> >
											</button><!--
											--><a class="btn-pin"
												href="<?= $enc->attr( $this->link( 'client/html/catalog/session/pinned/url', ['pin_action' => 'add', 'pin_id' => $id, 'd_name' => $productItem->getName( 'url' )] ) ) ?>"
												data-rmurl="<?= $enc->attr( $this->link( 'client/html/catalog/session/pinned/url', ['pin_action' => 'delete', 'pin_id' => $id, 'd_name' => $productItem->getName( 'url' )] ) ) ?>"
												title="<?= $enc->attr( $this->translate( 'client', 'Pin product' ), $enc::TRUST ) ?>">
											</a>
										</div>
									</div>
								<?php endif ?>

							</form>
						<?php endif ?>

					</div>
				</div>
			</div><!--

		<?php endforeach ?>
	--></div>

</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/lists/items' ) ?>
