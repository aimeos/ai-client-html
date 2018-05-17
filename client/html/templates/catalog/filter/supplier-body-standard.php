<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

$enc = $this->encoder();

$contentUrl = $this->config( 'client/html/common/content/baseurl' );

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );

$suppliers = $this->get( 'supplierList', [] );
$supIds = $this->param( 'f_supid', [] );
$params = $this->param();


?>
<?php $this->block()->start( 'catalog/filter/supplier' ); ?>
<section class="catalog-filter-supplier">

	<?php if( !empty( $suppliers ) ) : ?>

		<h2><?= $enc->html( $this->translate( 'client', 'Suppliers' ), $enc::TRUST ); ?></h2>

		<fieldset class="supplier-lists">
			<ul class="attr-list"><!--

				<?php foreach( $suppliers as $id => $supplier ) : ?>

					--><li class="attr-item" data-id="<?= $enc->attr( $id ); ?>">

						<input class="attr-item" type="checkbox"
							id="attr-<?= $enc->attr( $id ); ?>"
							name="<?= $enc->attr( $this->formparam( ['f_supid', ''] ) ); ?>"
							value="<?= $enc->attr( $id ); ?>"
							<?= ( in_array( $id, $supIds ) ? 'checked="checked"' : '' ); ?>
						/>

						<label class="attr-name" for="attr-<?= $enc->attr( $id ); ?>"><!--
							--><div class="media-list"><!--

								<?php foreach( $supplier->getListItems( 'media', 'icon' ) as $listItem ) : ?>
									<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
										<?= '-->' . $this->partial(
											$this->config( 'client/html/common/partials/media', 'common/partials/media-standard.php' ),
											array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) )
										) . '<!--'; ?>
									<?php endif; ?>
								<?php endforeach; ?>

							--></div>
							<span><?= $enc->html( $supplier->getName(), $enc::TRUST ); ?></span><!--
						--></label>
					</li><!--

				<?php endforeach; ?>
			--></ul>
		</fieldset>

	<?php endif; ?>


	<?php if( $this->config( 'client/html/catalog/filter/standard/button', true ) ) : ?>
		<noscript>
			<button class="filter btn btn-primary" type="submit">
				<?= $enc->html( $this->translate( 'client', 'Show' ), $enc::TRUST ); ?>
			</button>
		</noscript>
	<?php endif; ?>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/filter/supplier' ); ?>
