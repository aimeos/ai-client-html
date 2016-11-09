<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

/* Expected data:
 * - products : List of product items
 * - selectionProductDependencies : List of product dependencies (optional)
 * - selectionAttributeDependencies : List of attribute dependencies (optional)
 * - selectionAttributeTypeDependencies : List of attribute type dependencies (optional)
 * - selectionProducts : List of product variants incl. referenced items (optional)
 * - attributeConfigItems : List of config attribute items incl. referenced items (optional)
 * - selectionAttributeItems : List of variant attribute items incl. referenced items (optional)
 * - basket-add : True to display "add to basket" button, false if not
 * - itemprop : Schema.org property for the product items
 */

$enc = $this->encoder();
$position = $this->get( 'position' );

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
			$params = array( 'd_name' => $productItem->getName( 'url' ), 'd_prodid' => $id );
			if( $position !== null ) { $params['l_pos'] = $position++; }
			$conf = $productItem->getConfig(); $css = ( isset( $conf['css-class'] ) ? $conf['css-class'] : '' );

			$itemProdDeps = $this->get( 'selectionProductDependencies', array() );
			$prodDeps = ( isset( $itemProdDeps[$id] ) ? json_encode( (array) $itemProdDeps[$id] ) : '{}' );

			$itemAttrDeps = $this->get( 'selectionAttributeDependencies', array() );
			$attrDeps = ( isset( $itemAttrDeps[$id] ) ? json_encode( (array) $itemAttrDeps[$id] ) : '{}' );

			$itemAttrTypeDeps = $this->get( 'selectionAttributeTypeDependencies', array() );
			$attrTypeDeps = ( isset( $itemAttrTypeDeps[$id] ) ? (array) $itemAttrTypeDeps[$id] : array() );

			$itemSubProducts = $this->get( 'selectionProducts', array() );
			$subProducts = ( isset( $itemSubProducts[$id] ) ? (array) $itemSubProducts[$id] : array() );

			$itemAttrConfigItems = $this->get( 'attributeConfigItems', array() );
			$attrConfigItems = ( isset( $itemAttrConfigItems[$id] ) ? (array) $itemAttrConfigItems[$id] : array() );

			$selectParams = array(
				'selectionProducts' => $subProducts,
				'selectionAttributeTypeDependencies' => $attrTypeDeps,
				'selectionAttributeItems' => $this->get( 'selectionAttributeItems', array() ),
			);

			$attributeParams = array(
				'attributeConfigItems' => $attrConfigItems,
				'attributeCustomItems' => $productItem->getRefItems( 'attribute', null, 'custom' ),
				'attributeHiddenItems' => $productItem->getRefItems( 'attribute', null, 'hidden' ),
			);
		?>

		--><li class="product <?php echo $enc->attr( $css ); ?>"data-reqstock="<?php echo (int) $this->get( 'require-stock', true ); ?>" itemprop="<?php echo $this->get( 'itemprop' ); ?>" itemscope="" itemtype="http://schema.org/Product">


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
				<div class="stock" data-prodid="<?php echo $enc->attr( implode( ' ', array_merge( array( $id ), array_keys( $subProducts ) ) ) ); ?>"></div>
				<div class="price-list price price-actual">
					<?php echo $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ), array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) ) ); ?>
				</div>
			</div>


			<?php if( $this->get( 'basket-add', false ) ) : ?>
				<form method="POST" action="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
					<!-- catalog.lists.items.csrf -->
					<?php echo $this->csrf()->formfield(); ?>
					<!-- catalog.lists.items.csrf -->

					<div class="items-selection" data-proddeps="<?php echo $enc->attr( $prodDeps ); ?>" data-attrdeps="<?php echo $enc->attr( $attrDeps ); ?>">
						<?php echo $this->partial( $this->config( 'client/html/common/partials/selection', 'common/partials/selection-default.php' ), $selectParams ); ?>
					</div>

					<div class="items-attribute">
						<?php echo $this->partial( $this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-default.php' ), $attributeParams ); ?>
					</div>

					<div class="addbasket">
						<div class="group">
							<input name="<?php echo $enc->attr( $this->formparam( 'b_action' ) ); ?>" type="hidden" value="add" />
							<input name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'prodid' ) ) ); ?>" type="hidden" value="<?php echo $id; ?>" />
							<input name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'quantity' ) ) ); ?>" type="number" min="1" max="2147483647" maxlength="10" step="1" required="required" value="1" />
							<button class="standardbutton btn-action" type="submit" value=""><?php echo $enc->html( $this->translate( 'client', 'Add to basket' ), $enc::TRUST ); ?></button>
						</div>
					</div>

				</form>
			<?php endif; ?>


		</li><!--

	<?php endforeach; ?>

--></ul>
