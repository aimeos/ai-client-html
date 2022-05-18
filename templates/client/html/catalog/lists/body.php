<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();
$key = $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';


/** client/html/catalog/lists/pagination
 * Enables or disables pagination in list views
 *
 * Pagination is automatically hidden if there are not enough products in the
 * category or search result. But sometimes you don't want to show the pagination
 * at all, e.g. if you implement infinite scrolling by loading more results
 * dynamically using AJAX.
 *
 * @param boolean True for enabling, false for disabling pagination
 * @since 2019.04
 */


?>
<section class="aimeos catalog-list <?= $enc->attr( $this->get( 'listCatPath', map() )->getConfigValue( 'css-class', '' )->join( ' ' ) ) ?>"
	data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<div class="container-xxl">

		<?php if( ( $catItem = $this->get( 'listCatPath', map() )->last() ) !== null ) : ?>

			<div class="catalog-list-head">

				<?php foreach( $catItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>

					<div class="head-image">
						<img class="<?= $enc->attr( $mediaItem->getType() ) ?>"
							src="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ), $mediaItem->getFileSystem() ) ) ?>"
							srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews(), $mediaItem->getFileSystem() ) ) ?>"
							alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
						>
					</div>

				<?php endforeach ?>

				<h1><?= $enc->html( $catItem->getName() ) ?></h1>

			</div>

		<?php endif ?>


		<?php if( $this->get( 'listProductTotal', 0 ) > 0 ) : ?>

			<div class="catalog-list-type">
				<a class="type-item type-grid" title="<?= $enc->attr( $this->translate( 'client', 'Grid view' ) ) ?>"
					href="<?= $enc->attr( $this->link( $key, map( $this->get( 'listParams', [] ) )->remove( 'l_type' )->all() ) ) ?>"></a>
				<a class="type-item type-list" title="<?= $enc->attr( $this->translate( 'client', 'List view' ) ) ?>"
					href="<?= $enc->attr( $this->link( $key, ['l_type' => 'list'] + $this->get( 'listParams', [] ) ) ) ?>"></a>
			</div>

		<?php endif ?>


		<?php if( $searchText = $this->param( 'f_search' ) ) : ?>

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

		<?php endif ?>


		<?php if( $this->get( 'listProductTotal', 0 ) > 0 && $this->config( 'client/html/catalog/lists/pagination', true ) ) : ?>
			<?= $this->partial( 'catalog/lists/pagination', [
					'params' => $this->get( 'listParams', [] ),
					'size' => $this->get( 'listPageSize', 48 ),
					'total' => $this->get( 'listProductTotal', 0 ),
					'current' => $this->get( 'listPageCurr', 0 ),
					'prev' => $this->get( 'listPagePrev', 0 ),
					'next' => $this->get( 'listPageNext', 0 ),
					'last' => $this->get( 'listPageLast', 0 ),
				] )
			?>
		<?php endif ?>


		<?= $this->partial(
			$this->get( 'listPartial', 'catalog/lists/items' ),
			array(
				'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
				'basket-add' => $this->config( 'client/html/catalog/lists/basket-add', false ),
				'products' => $this->get( 'listProductItems', map() ),
				'position' => $this->get( 'listPosition' ),
			)
		) ?>


		<?php if( $this->get( 'listProductTotal', 0 ) > 0 && $this->config( 'client/html/catalog/lists/pagination', true ) ) : ?>
			<?= $this->partial( 'catalog/lists/pagination', [
					'params' => $this->get( 'listParams', [] ),
					'size' => $this->get( 'listPageSize', 48 ),
					'total' => $this->get( 'listProductTotal', 0 ),
					'current' => $this->get( 'listPageCurr', 0 ),
					'prev' => $this->get( 'listPagePrev', 0 ),
					'next' => $this->get( 'listPageNext', 0 ),
					'last' => $this->get( 'listPageLast', 0 ),
				] )
			?>
		<?php endif ?>


		<?php if( $this->get( 'listPageCurr', 0 ) <= 1 && ( $catItem = $this->get( 'listCatPath', map() )->last() ) !== null ) : ?>
			<div class="catalog-list-footer">
				<?php foreach( $catItem->getRefItems( 'text', 'long', 'default' ) as $textItem ) : ?>
					<div class="footer-text">
						<?= $enc->html( $textItem->getContent(), $enc::TRUST ) ?>
					</div>
				<?php endforeach ?>
			</div>
		<?php endif ?>

	</div>
</section>
