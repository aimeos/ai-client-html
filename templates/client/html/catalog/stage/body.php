<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();


?>
<div class="section aimeos catalog-stage <?= $enc->attr( $this->get( 'stageCatPath', map() )->getConfigValue( 'css-class', '' )->join( ' ' ) ) ?>"
	data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<?php if( ( $catItem = $this->get( 'stageCurrentCatItem' ) ) && !( $mediaItems = $catItem->getRefItems( 'media', 'stage', 'default' ) )->isEmpty() ) : ?>
		<div class="catalog-stage-image single-item">

			<?php foreach( $mediaItems as $mediaItem ) : ?>
				<div class="stage-item">
					<img alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
						src="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ), $mediaItem->getFileSystem() ) ) ?>"
						srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews( true ), $mediaItem->getFileSystem() ) ) ?>"
					>
				</div>
			<?php endforeach ?>

		</div>
	<?php endif ?>

	<div class="catalog-stage-breadcrumb container-xxl" aria-label="<?= $enc->attr( $this->translate( 'client', 'Breadcrumb navigation' ) ) ?>">

		<?php if( isset( $this->stageCatPath ) ) : ?>
			<?php
				$entries = []; $idx = 1;
				foreach( $this->get( 'stageCatPath', map() ) as $cat )
				{
					$entries[] = [
						'@type' => 'ListItem',
						'position' => $idx++,
						'name' => $cat->getName(),
						'item' => $this->link( 'client/html/catalog/tree/url', ['f_name' => $cat->getName( 'url' ), 'f_catid' => $cat->getId()], ['absoluteUri' => true] )
					];
				}
			?>
			<script type="application/ld+json">
				{
					"@context": "https://schema.org",
					"@type": "BreadcrumbList",
					"itemListElement": <?= json_encode( $entries ) ?>
				}
			</script>
		<?php endif ?>

		<nav class="breadcrumb">
			<span class="title"><?= $enc->html( $this->translate( 'client', 'You are here:' ), $enc::TRUST ) ?></span>
			<ol>

				<?php if( isset( $this->stageCatPath ) ) : ?>
					<?php foreach( $this->get( 'stageCatPath', map() ) as $cat ) : ?>
						<li>
							<a href="<?= $enc->attr( $this->link( 'client/html/catalog/tree/url', array_merge( $this->get( 'stageParams', [] ), ['f_name' => $cat->getName( 'url' ), 'f_catid' => $cat->getId()] ) ) ) ?>">
								<?= $enc->html( $cat->getName(), $enc::TRUST ) ?>
							</a>
						</li>
					<?php endforeach ?>
				<?php else : ?>
					<li>
						<a class="back" href="#">
							<?= $enc->html( $this->translate( 'client', 'Back' ), $enc::TRUST ) ?>
						</a>
					</li>
				<?php endif ?>

			</ol>
		</nav>
	</div>

</div>
