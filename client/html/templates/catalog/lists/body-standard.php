<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


if( $this->param( 'f_catid' ) !== null )
{
	$target = $this->config( 'client/html/catalog/tree/url/target' );
	$cntl = $this->config( 'client/html/catalog/tree/url/controller', 'catalog' );
	$action = $this->config( 'client/html/catalog/tree/url/action', 'tree' );
	$config = $this->config( 'client/html/catalog/tree/url/config', [] );
}
else
{
	$target = $this->config( 'client/html/catalog/lists/url/target' );
	$cntl = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
	$action = $this->config( 'client/html/catalog/lists/url/action', 'list' );
	$config = $this->config( 'client/html/catalog/lists/url/config', [] );
}

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );


/** client/html/catalog/lists/head/text-types
 * The list of text types that should be rendered in the catalog list head section
 *
 * The head section of the catalog list view at least consists of the category
 * name. By default, all short and long descriptions of the category are rendered
 * as well.
 *
 * You can add more text types or remove ones that should be displayed by
 * modifying these list of text types, e.g. if you've added a new text type
 * and texts of that type to some or all categories.
 *
 * @param array List of text type names
 * @since 2014.03
 * @category User
 * @category Developer
 */
$textTypes = $this->config( 'client/html/catalog/lists/head/text-types', array( 'long' ) );


/** client/html/catalog/lists/pagination/enable
 * Enables or disables pagination in list views
 *
 * Pagination is automatically hidden if there are not enough products in the
 * category or search result. But sometimes you don't want to show the pagination
 * at all, e.g. if you implement infinite scrolling by loading more results
 * dynamically using AJAX.
 *
 * @param boolean True for enabling, false for disabling pagination
 * @since 2019.04
 * @category User
 * @category Developer
 */

/** client/html/catalog/lists/partials/pagination
 * Relative path to the pagination partial template file for catalog lists
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The pagination
 * partial creates an HTML block containing a page browser and sorting links
 * if necessary.
 *
 * @param string Relative path to the template file
 * @since 2017.01
 * @category Developer
 */


?>
<section class="aimeos catalog-list <?= $enc->attr( $this->get( 'listCatPath', map() )->getConfigValue( 'css-class', '' )->join( ' ' ) ) ?>" data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ) ?>">

	<?php if( isset( $this->listErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->listErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>


	<?php if( ( $catItem = $this->get( 'listCatPath', map() )->last() ) !== null ) : ?>
		<div class="catalog-list-head">

			<div class="imagelist-default">
				<?php foreach( $catItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
					<img class="<?= $enc->attr( $mediaItem->getType() ) ?>"
						src="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ) ) ) ?>"
						srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews() ) ) ?>"
						alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
					>
				<?php endforeach ?>
			</div>

			<h1><?= $enc->html( $catItem->getName() ) ?></h1>
		</div>

	<?php endif ?>

	<?= $this->block()->get( 'catalog/lists/promo' ) ?>

	<?php if( $this->get( 'listProductTotal', 0 ) > 0 ) : ?>
		<div class="catalog-list-type">
			<a class="type-item type-grid" title="<?= $enc->attr( $this->translate( 'client', 'Grid view' ) ) ?>"
				href="<?= $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'grid' ) + $this->get( 'listParams', [] ), [], $config ) ) ?>"></a>
			<a class="type-item type-list" title="<?= $enc->attr( $this->translate( 'client', 'List view' ) ) ?>"
				href="<?= $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'list' ) + $this->get( 'listParams', [] ), [], $config ) ) ?>"></a>
		</div>
	<?php endif ?>

	<?php if( $this->get( 'listProductTotal', 0 ) > 0 && $this->config( 'client/html/catalog/lists/pagination/enable', true ) ) : ?>
		<?= $this->partial(
				$this->config( 'client/html/catalog/lists/partials/pagination', 'catalog/lists/pagination-standard' ),
				array(
					'params' => $this->get( 'listParams', [] ),
					'size' => $this->get( 'listPageSize', 48 ),
					'total' => $this->get( 'listProductTotal', 0 ),
					'current' => $this->get( 'listPageCurr', 0 ),
					'prev' => $this->get( 'listPagePrev', 0 ),
					'next' => $this->get( 'listPageNext', 0 ),
					'last' => $this->get( 'listPageLast', 0 ),
				)
			)
		?>
	<?php endif ?>

	<?php if( ( $searchText = $this->param( 'f_search', null ) ) != null ) : ?>
		<div class="container">
			<div class="list-search">

				<?php if( ( $total = $this->get( 'listProductTotal', 0 ) ) > 0 ) : ?>
					<?= $enc->html( sprintf(
						$this->translate(
							'client',
							'Search result for <span class="searchstring">"%1$s"</span> (%2$d article)',
							'Search result for <span class="searchstring">"%1$s"</span> (%2$d articles)',
							$total
						),
						$searchText,
						$total
					), $enc::TRUST ) ?>
				<?php else : ?>
					<?= $enc->html( sprintf(
						$this->translate(
							'client',
							'No articles found for <span class="searchstring">"%1$s"</span>. Please try again with a different keyword.'
						),
						$searchText
					), $enc::TRUST ) ?>
				<?php endif ?>

			</div>
		</div>
	<?php endif ?>

	<?= $this->block()->get( 'catalog/lists/items' ) ?>

	<?php if( $this->get( 'listProductTotal', 0 ) > 0 && $this->config( 'client/html/catalog/lists/pagination/enable', true ) ) : ?>
		<?= $this->partial(
				$this->config( 'client/html/catalog/lists/partials/pagination', 'catalog/lists/pagination-standard' ),
				array(
					'params' => $this->get( 'listParams', [] ),
					'size' => $this->get( 'listPageSize', 48 ),
					'total' => $this->get( 'listProductTotal', 0 ),
					'current' => $this->get( 'listPageCurr', 0 ),
					'prev' => $this->get( 'listPagePrev', 0 ),
					'next' => $this->get( 'listPageNext', 0 ),
					'last' => $this->get( 'listPageLast', 0 ),
				)
			)
		?>
	<?php endif ?>

	<?php if( $this->get( 'listPageCurr', 0 ) <= 1 && ( $catItem = $this->get( 'listCatPath', map() )->last() ) !== null ) : ?>
		<div class="catalog-list-footer">
			<?php foreach( (array) $textTypes as $textType ) : ?>
				<?php foreach( $catItem->getRefItems( 'text', $textType, 'default' ) as $textItem ) : ?>
					<div class="<?= $enc->attr( $textItem->getType() ) ?>">
						<?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?>
					</div>
				<?php endforeach ?>
			<?php endforeach ?>
		</div>
	<?php endif ?>

</section>
