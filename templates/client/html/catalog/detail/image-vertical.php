<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2014-2023
 */

/* Available data:
 * - mediaItems : Media items incl. referenced items
 */

$enc = $this->encoder();


?>
<div class="catalog-detail-image">

	<?php if( ( $thumbNum = count( $this->get( 'mediaItems', [] ) ) ) > 1 ) : ?>

		<div class="thumbs thumbs-vertical swiffy-slider slider-nav-dark slider-nav-sm slider-nav-outside slider-item-snapstart slider-nav-visible slider-nav-page slider-nav-outside-expand">
			<div class="slider-container">

				<?php $index = 0; foreach( $this->get( 'mediaItems', [] ) as $id => $mediaItem ) : ?>

					<div class="thumbnail">
						<img loading="lazy" class="item-thumb img-<?= $index ?>"  data-index="<?= $enc->attr( $index ) ?>"
							src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
							alt="<?= $enc->attr( $this->translate( 'client', 'Product image' ) ) ?>"
						>
					</div>

				<?php $index++; endforeach ?>

			</div>

			<?php if( $thumbNum > 4 ) : ?>
				<button type="button" class="slider-nav" aria-label="Go previous"></button>
				<button type="button" class="slider-nav slider-nav-next" aria-label="Go next"></button>
			<?php endif ?>

		</div>

	<?php endif ?>

	<?php if( ( $imgNum = count( $this->get( 'mediaItems', [] ) ) ) > 0 ) : ?>

		<div class="swiffy-slider slider-item-ratio slider-item-ratio-contain slider-nav-round slider-nav-animation-fadein">
			<div class="image-single slider-container" data-pswp="{bgOpacity: 0.75, shareButtons: false}">

				<?php foreach( $this->get( 'mediaItems', [] ) as $id => $mediaItem ) : ?>

					<div class="media-item">
						<?= $this->image( $mediaItem, $this->config( 'client/html/catalog/detail/imageset-sizes', '(min-width: 2000px) 1920px, (min-width: 500px) 960px, 100vw' ), true ) ?>
					</div>

				<?php endforeach ?>

			</div>

			<?php if( $imgNum > 1 ) : ?>
				<button type="button" class="slider-nav" aria-label="Go previous"></button>
				<button type="button" class="slider-nav slider-nav-next" aria-label="Go next"></button>
			<?php endif ?>

		</div>

	<?php endif ?>


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
						title="<?= $enc->attr( $this->translate( 'client', 'Close' ) ) ?>">
					</button>
					<!-- button class="pswp__button pswp__button--share"
						title="<?= $enc->attr( $this->translate( 'client', 'Share' ) ) ?>">
					</button -->
					<button class="pswp__button pswp__button--fs"
						title="<?= $enc->attr( $this->translate( 'client', 'Toggle fullscreen' ) ) ?>">
					</button>
					<button class="pswp__button pswp__button--zoom"
						title="<?= $enc->attr( $this->translate( 'client', 'Zoom in/out' ) ) ?>">
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
					title="<?= $enc->attr( $this->translate( 'client', 'Previous' ) ) ?>">
				</button>
				<button class="pswp__button pswp__button--arrow--right"
					title="<?= $enc->attr( $this->translate( 'client', 'Next' ) ) ?>">
				</button>

				<div class="pswp__caption"><div class="pswp__caption__center"></div></div>

			</div>
		</div>
	</div>

</div>
