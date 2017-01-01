<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2014-2016
 */

$enc = $this->encoder();

$getVariantData = function( $mediaId, array $mediaItems ) use ( $enc )
{
	$string = '';

	if( isset( $mediaItems[$mediaId] ) )
	{
		foreach( $mediaItems[$mediaId]->getRefItems( 'attribute', null, 'variant' ) as $id => $item ) {
			$string .= ' data-variant-' . $item->getType() . '="' . $enc->attr( $id ) . '"';
		}
	}

	return $string;
};


$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );

$url = $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $this->get( 'params', array() ), array(), $detailConfig ) );
$media = $this->get( 'mediaItems', array() );


?>
<div class="catalog-detail-image">
	<?php $mediaItems = $this->product->getRefItems( 'media', 'default', 'default' ); ?>

	<div class="image-single" data-pswp="{bgOpacity: 0.75, shareButtons: false}">

		<?php foreach( $mediaItems as $id => $mediaItem ) : ?>
			<?php $mediaUrl = $enc->attr( $this->content( $mediaItem->getUrl() ) ); ?>
			<?php $previewUrl = $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>

			<figure id="image-<?php echo $enc->attr( $id ); ?>"
				class="item" style="background-image: url('<?php echo $mediaUrl; ?>')"
				itemprop="associatedMedia" itemscope="" itemtype="http://schema.org/ImageObject"
				data-image="<?php echo $previewUrl; ?>"
				<?php echo $getVariantData( $id, $media ); ?> >
				<a href="<?php echo $enc->attr( $mediaUrl ); ?>" itemprop="contentUrl"></a>
				<figcaption itemprop="caption description"><?php echo $enc->html( $mediaItem->getName() ); ?></figcaption>
			</figure>

		<?php endforeach; ?>

	</div><!--

	--><?php if( count( $mediaItems ) > 1 ) : $class = 'item selected'; ?><!--
		--><div class="image-thumbs thumbs-horizontal" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
			<button type="button" class="slick-prev"><?php echo $enc->html( $this->translate( 'client', 'Previous' ) ); ?></button>
			<div class="thumbs">

				<?php foreach( $mediaItems as $id => $mediaItem ) : ?>
					<?php $previewUrl = $enc->attr( $this->content( $mediaItem->getPreview() ) ); ?>

					<a class="<?php echo $class; ?>" style="background-image: url('<?php echo $previewUrl; ?>')"
						href="<?php echo $url . '#image-' . $enc->attr( $id ); ?>"
					></a>

					<?php $class = 'item'; ?>
				<?php endforeach; ?>

			</div>
			<button type="button" class="slick-next"><?php echo $enc->html( $this->translate( 'client', 'Next' ) ); ?></button>
		</div>
	<?php endif; ?>


	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="pswp__bg"></div>
	    <div class="pswp__scroll-wrap">

	        <!-- Container that holds slides. Don't modify these 3 pswp__item elements, data is added later on. -->
	        <div class="pswp__container">
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	        </div>

	        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
	        <div class="pswp__ui pswp__ui--hidden">
	            <div class="pswp__top-bar">

	                <div class="pswp__counter"></div>

	                <button class="pswp__button pswp__button--close"
	                	title="<?php echo $enc->attr( $this->translate( 'client', 'Close' ) ); ?>">
	                </button>
	                <!-- button class="pswp__button pswp__button--share"
	                	title="<?php echo $enc->attr( $this->translate( 'client', 'Share' ) ); ?>">
	                </button -->
	                <button class="pswp__button pswp__button--fs"
	                	title="<?php echo $enc->attr( $this->translate( 'client', 'Toggle fullscreen' ) ); ?>">
	                </button>
	                <button class="pswp__button pswp__button--zoom"
	                	title="<?php echo $enc->attr( $this->translate( 'client', 'Zoom in/out' ) ); ?>">
	                </button>

	                <div class="pswp__preloader">
	                    <div class="pswp__preloader__icn">
	                      <div class="pswp__preloader__cut">
	                        <div class="pswp__preloader__donut"></div>
	                      </div>
	                    </div>
	                </div>
	            </div>

	            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
	                <div class="pswp__share-tooltip"></div>
	            </div>

	            <button class="pswp__button pswp__button--arrow--left"
                	title="<?php echo $enc->attr( $this->translate( 'client', 'Previous' ) ); ?>">
	            </button>
	            <button class="pswp__button pswp__button--arrow--right"
                	title="<?php echo $enc->attr( $this->translate( 'client', 'Next' ) ); ?>">
	            </button>

	            <div class="pswp__caption"><div class="pswp__caption__center"></div></div>

	        </div>
	    </div>
	</div>

</div>
