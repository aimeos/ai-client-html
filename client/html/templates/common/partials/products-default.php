<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

/* Expected data:
 * - products : List of product items
 * - mediaItems : List of media items incl. referenced items (optional)
 * - productItems : List of product variants incl. referenced items (optional)
 * - attributeItems : List of attribute items incl. referenced items (optional)
 * - basket-add : True to display "add to basket" button, false if not (optional)
 * - require-stock : True if the stock level should be displayed (optional)
 * - itemprop : Schema.org property for the product items (optional)
 * - position : Position is product list to start from (optional)
 */

$enc = $this->encoder();
$position = $this->get( 'position' );
$productItems = $this->get( 'productItems', array() );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );

if( $this->get( 'basket-add', false ) )
{
	$basketTarget = $this->config( 'client/html/basket/standard/url/target' );
	$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );
	$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );
	$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );
}


?>
<ul class="list-items"><!--

	<?php foreach( $this->get( 'products', array() ) as $id => $productItem ) : $firstImage = true; ?>
		<?php
			$conf = $productItem->getConfig(); $css = ( isset( $conf['css-class'] ) ? $conf['css-class'] : '' );
			$params = array( 'd_name' => $productItem->getName( 'url' ), 'd_prodid' => $id );
			if( $position !== null ) { $params['d_pos'] = $position++; }
		?>

		--><li class="product <?php echo $enc->attr( $css ); ?>"
			data-reqstock="<?php echo (int) $this->get( 'require-stock', true ); ?>"
			itemprop="<?php echo $this->get( 'itemprop' ); ?>"
			itemtype="http://schema.org/Product"
			itemscope="" >


			<a href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>">

				<div class="media-list">
					<?php foreach( $productItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
						<?php $mediaUrl = $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>
						<?php if( $firstImage === true ) : $firstImage = false; ?>
							<noscript>
								<div class="media-item" style="background-image: url('<?php echo $mediaUrl; ?>')" itemscope="" itemtype="http://schema.org/ImageObject">
									<meta itemprop="contentUrl" content="<?php echo $mediaUrl; ?>" />
								</div>
							</noscript>
							<div class="media-item lazy-image" data-src="<?php echo $mediaUrl; ?>"></div>
						<?php else : ?>
							<div class="media-item" data-src="<?php echo $mediaUrl; ?>"></div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>

				<div class="text-list">
					<h2 itemprop="name"><?php echo $enc->html( $productItem->getName(), $enc::TRUST ); ?></h2>
					<?php foreach( $productItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
						<div class="text-item" itemprop="description">
							<?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?><br/>
					</div>
					<?php endforeach; ?>
				</div>

			</a>


			<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

				<div class="stock-list">
					<div class="articleitem stock-actual"
						data-prodid="<?php echo $enc->attr( $productItem->getId() ); ?>"
						data-prodcode="<?php echo $enc->attr( $productItem->getCode() ); ?>">
					</div>
					<?php foreach( $productItem->getRefItems( 'product', null, 'default' ) as $articleId => $articleItem ) : ?>
						<div class="articleitem"
							data-prodid="<?php echo $enc->attr( $articleId ); ?>"
							data-prodcode="<?php echo $enc->attr( $articleItem->getCode() ); ?>">
						</div>
					<?php endforeach; ?>
				</div>

				<div class="price-list">
					<div class="articleitem price-actual"
						data-prodid="<?php echo $enc->attr( $productItem->getId() ); ?>"
						data-prodcode="<?php echo $enc->attr( $productItem->getCode() ); ?>">
						<?php echo $this->partial(
							/** client/html/common/partials/price
							 * Relative path to the price partial template file
							 *
							 * Partials are templates which are reused in other templates and generate
							 * reoccuring blocks filled with data from the assigned values. The price
							 * partial creates an HTML block for a list of price items.
							 *
							 * The partial template files are usually stored in the templates/partials/ folder
							 * of the core or the extensions. The configured path to the partial file must
							 * be relative to the templates/ folder, e.g. "partials/price-default.php".
							 *
							 * @param string Relative path to the template file
							 * @since 2015.04
							 * @category Developer
							 */
							$this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ),
							array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) )
						); ?>
					</div>
				</div>

			</div>


			<?php if( $this->get( 'basket-add', false ) ) : ?>
				<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
					<!-- catalog.lists.items.csrf -->
					<?php echo $this->csrf()->formfield(); ?>
					<!-- catalog.lists.items.csrf -->

					<?php if( $productItem->getType() === 'select' ) : ?>
						<div class="items-selection">
							<?php echo $this->partial(
								$this->config( 'client/html/common/partials/selection', 'common/partials/selection-default.php' ),
								array(
									'products' => $productItem->getRefItems( 'product', 'default', 'default' ),
									'attributeItems' => $this->get( 'attributeItems', array() ),
									'productItems' => $this->get( 'productItems', array() ),
									'mediaItems' => $this->get( 'mediaItems', array() ),
								)
							); ?>
						</div>
					<?php endif; ?>

					<div class="items-attribute">
						<?php echo $this->partial(
							$this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-default.php' ),
							array(
								'attributeItems' => $this->get( 'attributeItems', array() ),
								'attributeConfigItems' => $productItem->getRefItems( 'attribute', null, 'config' ),
								'attributeCustomItems' => $productItem->getRefItems( 'attribute', null, 'custom' ),
								'attributeHiddenItems' => $productItem->getRefItems( 'attribute', null, 'hidden' ),
							)
						); ?>
					</div>

					<div class="addbasket">
						<div class="group">
							<input type="hidden" value="add"
								name="<?php echo $enc->attr( $this->formparam( 'b_action' ) ); ?>"
							/>
							<inputtype="hidden" value="<?php echo $id; ?>"
								name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ); ?>"
							/>
							<input type="number" value="1"
								 min="1" max="2147483647" maxlength="10" step="1" required="required"
								name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ); ?>"
							/>
							<button class="standardbutton btn-action" type="submit" value="">
								<?php echo $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?>
							</button>
						</div>
					</div>

				</form>
			<?php endif; ?>


		</li><!--

	<?php endforeach; ?>

--></ul>
