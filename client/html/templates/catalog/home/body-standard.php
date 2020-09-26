<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */

$enc = $this->encoder();


?>
<section class="aimeos catalog-home" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( isset( $this->homeErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->homeErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if( isset( $this->homeTree ) ) : ?>

		<div class="home-item <?= $enc->attr( $this->homeTree->getCode() ) ?>">

			<?php if( !( $mediaItems = $this->homeTree->getRefItems( 'media', 'stage', 'default') )->isEmpty() ) : ?>
				<div class="home-stage">
					<?php foreach( $mediaItems as $mediaItem ) : ?>
						<a class="stage-item" href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', ['f_catid' => $this->homeTree->getId(), 'f_name' => $this->homeTree->getName( 'url' )] ) ) ?>">
							<img class="stage-image lazy-image"
								src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEEAAEALAAAAAABAAEAAAICTAEAOw=="
								data-src="<?= $enc->attr( $mediaItem->getPreview() ) ?>"
								data-srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
								alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>" />
						</a>
					<?php endforeach ?>
				</div>
			<?php endif ?>

			<?php if( !( $products = $this->homeTree->getRefItems( 'product', null, 'promotion' ) )->isEmpty() ) : ?>
				<div class="home-product">
					<h2 class="featured"><?= $enc->html( $this->translate( 'client', 'Featured items' ), $enc::TRUST ) ?></h2>
					<?= $this->partial( $this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ), [
						'require-stock' => (bool) $this->config( 'client/html/basket/require-stock', true ),
						'basket-add' => $this->config( 'client/html/catalog/home/basket-add', false ),
						'products' => $products
					] ) ?>
				</div>
			<?php endif ?>

		</div>

		<?php foreach( $this->homeTree->getChildren() as $child ) : ?>

			<div class="home-item <?= $enc->attr( $child->getCode() ) ?>">

				<?php if( !( $mediaItems = $child->getRefItems( 'media', 'stage', 'default') )->isEmpty() ) : ?>
					<div class="home-stage">
						<?php foreach( $mediaItems as $mediaItem ) : ?>
							<a class="stage-item" href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', ['f_catid' => $child->getId(), 'f_name' => $child->getName( 'url' )] ) ) ?>">
								<img class="stage-image lazy-image"
									src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEEAAEALAAAAAABAAEAAAICTAEAOw=="
									data-src="<?= $enc->attr( $mediaItem->getPreview() ) ?>"
									data-srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
									alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>" />
							</a>
						<?php endforeach ?>
					</div>
				<?php endif ?>

				<?php if( !( $products = $child->getRefItems( 'product', null, 'promotion' ) )->isEmpty() ) : ?>
					<div class="home-product">
						<h2 class="featured"><?= $enc->html( $this->translate( 'client', 'Featured items' ), $enc::TRUST ) ?></h2>
						<?= $this->partial( $this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ), [
							'require-stock' => (bool) $this->config( 'client/html/basket/require-stock', true ),
							'basket-add' => $this->config( 'client/html/catalog/home/basket-add', false ),
							'products' => $products
						] ) ?>
					</div>
				<?php endif ?>

			</div>

		<?php endforeach ?>

	<?php endif ?>

</section>
