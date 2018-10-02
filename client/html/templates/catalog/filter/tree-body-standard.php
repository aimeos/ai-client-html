<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$params = $this->param();
$path = $this->get( 'treeCatalogPath', [] );
$counts = $this->config( 'client/html/catalog/count/enable', true );

$name = '';
if( ( $node = end( $path ) ) !== false ) {
	$name = $node->getName();
}

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
<?php $this->block()->start( 'catalog/filter/tree' ); ?>
<section class="catalog-filter-tree <?= ( $counts == true ? 'catalog-filter-count' : '' ); ?>">

	<?php if( $enforce ) : ?>
		<input type="hidden"
			name="<?= $enc->attr( $this->formparam( array( 'f_catid' ) ) ); ?>"
			value="<?= $enc->attr( $this->param( 'f_catid' ) ); ?>"
		/>
	<?php endif; ?>

	<h2><?= $enc->html( $this->translate( 'client', 'Categories' ), $enc::TRUST ); ?></h2>

	<?php if( isset( $params['f_catid'] ) ) : unset( $params['f_catid'], $params['f_name'] ); ?>
		<div class="category-selected">
			<span class="selected-intro"><?= $enc->html( $this->translate( 'client', 'Your choice' ), $enc::TRUST ); ?></span>
			<a class="selected-category" href="<?= $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, [], $listConfig ) ); ?>">
				<?= $enc->html( $name, $enc::TRUST ); ?>
			</a>
		</div>
	<?php endif; ?>

	<?php if( isset( $this->treeCatalogTree ) && $this->treeCatalogTree->getStatus() > 0 ) : ?>
		<?= $this->partial(
			$this->config( 'client/html/catalog/filter/partials/tree', 'catalog/filter/tree-standard.php' ),
			array( 'nodes' => array( $this->treeCatalogTree ), 'path' => $path, 'params' => $this->get( 'treeFilterParams', [] ) )
		); ?>
	<?php endif; ?>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/filter/tree' ); ?>
