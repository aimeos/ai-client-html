<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2022
 */

/* Available data:
 * - detailSupplierItem : Supplier item incl. referenced items
 */


$enc = $this->encoder();


?>
<?php if( isset( $this->detailSupplierItem ) ) : ?>

	<section class="aimeos supplier-detail" itemscope itemtype="http://schema.org/Organization" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="supplier container-xxl">
			<div class="row">
				<div class="supplier-detail-basic col-sm-6 col-md-7 col-lg-8">

					<h1 class="name" itemprop="name"><?= $enc->html( $this->detailSupplierItem->getName(), $enc::TRUST ) ?></h1>

					<?php foreach( $this->detailSupplierItem->getRefItems( 'text', 'long' ) as $textItem ) : ?>
						<div class="long item"><?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?></div>
					<?php endforeach ?>

				</div>
				<div class="supplier-detail-image col-sm-6 col-md-5 col-lg-4">
					<div class="image-single">

						<?php foreach( $this->detailSupplierItem->getRefItems( 'media', 'default', 'default' ) as $id => $mediaItem ) : ?>
							<div id="image-<?= $enc->attr( $id ) ?>" class="media-item">
								<?= $this->image( $mediaItem ) ?>
							</div>
						<?php endforeach ?>

					</div>
				</div>
			</div>
		</div>
	</section>

<?php endif ?>
