<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

$enc = $this->encoder();
$pos = 0;

/** client/html/catalog/home/imageset-sizes
 * Size hints for loading the appropriate catalog home image sizes
 *
 * Modern browsers can load images of different sizes depending on their viewport
 * size. This is also known as serving "responsive images" because on small
 * smartphone screens, only small images are loaded while full width images are
 * loaded on large desktop screens.
 *
 * A responsive image contains additional "srcset" and "sizes" attributes:
 *
 *  <img src="img.jpg"
 *  	srcset="img-small.jpg 240w, img-large.jpg 720w"
 *  	sizes="(max-width: 320px) 240px, 720px"
 *  >
 *
 * The images and their width in the "srcset" attribute are automatically added
 * based on the sizes of the generated preview images. The value of the "sizes"
 * attribute can't be determined by Aimeos because it depends on the used frontend
 * theme and the size of the images defined in the CSS file. This config setting
 * adds the required value for the "sizes" attribute.
 *
 * It's value consists of one or more comma separated rules with
 * - an optional CSS media query for the view port size
 * - the (max) width the image will be displayed within this viewport size
 *
 * Rules without a media query are independent of the view port size and must be
 * always at last because the rules are evaluated from left to right and the first
 * matching rule is used.
 *
 * The above example tells the browser:
 * - Up to 320px view port width use img-small.jpg
 * - Above 320px view port width use img-large.jpg
 *
 * For more information about the "sizes" attribute of the "img" HTML tag read:
 * {@link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#attr-sizes}
 *
 * @param string HTML image "sizes" attribute
 * @since 2021.04
 * @see client/html/common/imageset-sizes
 */


?>
<section class="aimeos catalog-home" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( isset( $this->homeErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->homeErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

	<?php if( isset( $this->homeTree ) ) : ?>

		<div class="home-gallery <?= $enc->attr( $this->homeTree->getCode() ) ?>">

			<?php if( !( $mediaItems = $this->homeTree->getRefItems( 'media', 'stage', 'default' ) )->isEmpty() ) : ?>
				<div class="home-item home-image <?= $enc->attr( $this->homeTree->getCode() ) ?>">
					<div class="home-stage catalog-stage-image">
						<?php foreach( $mediaItems as $mediaItem ) : ?>
							<a class="stage-item" href="<?= $enc->attr( $this->link( 'client/html/catalog/tree/url', ['f_catid' => $this->homeTree->getId(), 'f_name' => $this->homeTree->getName( 'url' )] ) ) ?>">
								<img class="stage-image"
									src="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ) ) ) ?>"
									srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
									alt="<?= $enc->attr( $mediaItem->getProperties( 'name' )->first() ) ?>"
								>
								<div class="stage-text">
									<div class="stage-short">
										<?php foreach( $this->homeTree->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
											<?= $textItem->getContent() ?>
										<?php endforeach ?>
									</div>
									<div class="btn"><?= $enc->html( $this->translate( 'client', 'Take a look' ) ) ?></div>
								</div>
							</a>
						<?php endforeach ?>
					</div>
				</div>
			<?php endif ?>

			<?php foreach( $this->homeTree->getChildren() as $child ) : ?>
				<?php if( !( $mediaItems = $child->getRefItems( 'media', 'stage', 'default' ) )->isEmpty() ) : ?>

					<div class="home-item cat-image <?= $enc->attr( $child->getCode() ) ?>">
						<div class="home-stage catalog-stage-image">

							<?php foreach( $mediaItems as $mediaItem ) : ?>

								<a class="stage-item row" href="<?= $enc->attr( $this->link( 'client/html/catalog/tree/url', ['f_catid' => $child->getId(), 'f_name' => $child->getName( 'url' )] ) ) ?>">
									<img class="stage-image"
										src="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ) ) ) ?>"
										srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
										alt="<?= $enc->attr( $mediaItem->getProperties( 'name' )->first() ) ?>"
									>
									<div class="stage-text">
										<div class="stage-short">
											<?php foreach( $child->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
												<?= $textItem->getContent() ?>
											<?php endforeach ?>
										</div>
										<div class="btn"><?= $enc->html( $this->translate( 'client', 'Take a look' ) ) ?></div>
									</div>
								</a>

							<?php endforeach ?>

						</div>
					</div>

				<?php endif ?>
			<?php endforeach ?>

		</div>

	<?php endif ?>

</section>
