<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

$enc = $this->encoder();


$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );


/** client/html/catalog/filter/tree/force-search
 * Use the current category in full text searches
 *
 * Normally, a full text search finds all products that match the entered string
 * regardless of the category the user is currently in. This is also the standard
 * behavior of other shops.
 *
 * If it's desired, setting this configuration option to "1" will limit the full
 * text search to the current category only, so only products that match the text
 * and which are in the current category are found.
 *
 * @param boolean True to enforce current category for search, false for full text search only
 * @since 2015.10
 * @category Developer
 * @category User
 */
$enforce = $this->config( 'client/html/catalog/filter/tree/force-search', false );

/** client/html/catalog/filter/partials/tree
 * Relative path to the category tree partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The tree
 * partial creates an HTML block of nested lists for category trees.
 *
 * @param string Relative path to the template file
 * @since 2017.01
 * @category Developer
 */


?>
<?php $this->block()->start( 'catalog/filter/tree' ) ?>
<?php if( isset( $this->treeCatalogTree ) && $this->treeCatalogTree->getStatus() > 0 && !$this->treeCatalogTree->getChildren()->isEmpty() ) : ?>

	<div class="aimeos-overlay-offscreen"></div>
	<div class="open-menu"></div>

	<section class="catalog-filter-tree <?= ( $this->config( 'client/html/catalog/count/enable', true ) ? 'catalog-filter-count' : '' ); ?>">
		<div class="category-lists zeynep">

			<div class="menu-head row">
				<h2 class="col-10"><?= $enc->html( $this->translate( 'client', 'Categories' ), $enc::TRUST ) ?></h2>
				<div class="menu-close col-2"></div>
			</div>

			<?php if( $this->param( 'f_catid' ) ) : ?>
				<a class="btn btn-secondary category-selected" href="<?= $enc->attr( $this->url( $listTarget, $listController, $listAction, map( $this->treeFilterParams )->remove( ['f_catid', 'f_name'] )->toArray(), [], $listConfig ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Reset' ), $enc::TRUST ) ?>
				</a>
			<?php endif ?>

			<?php if( $enforce ) : ?>
				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( ['f_catid'] ) ) ?>"
					value="<?= $enc->attr( $this->param( 'f_catid' ) ) ?>"
				>
				<input type="hidden"
					name="<?= $enc->attr( $this->formparam( ['f_name'] ) ) ?>"
					value="<?= $enc->attr( $this->get( 'treeCatalogPath', map() )->getName()->get( $this->param( 'f_catid' ) ) ) ?>"
				>
			<?php endif ?>

			<fieldset>
				<?= $this->partial(
					$this->config( 'client/html/catalog/filter/partials/tree', 'catalog/filter/tree-partial-standard' ), [
						'nodes' => $this->treeCatalogTree->getChildren(),
						'path' => $this->get( 'treeCatalogPath', map() ),
						'params' => $this->get( 'treeFilterParams', [] ),
						'level' => 1
					] ) ?>
			</fieldset>
		</div>
	</section>

<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/tree' ) ?>
