<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$params = $this->get( 'listParams', array() );
$catPath = $this->get( 'listCatPath', array() );

$target = $this->config( 'client/html/catalog/lists/url/target' );
$cntl = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$config = $this->config( 'client/html/catalog/lists/url/config', array() );


$classes = '';
foreach( (array) $this->get( 'listCatPath', array() ) as $cat )
{
	$config = $cat->getConfig();
	if( isset( $config['css-class'] ) ) {
		$classes .= ' ' . $config['css-class'];
	}
}


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
$textTypes = $this->config( 'client/html/catalog/lists/head/text-types', array( 'short', 'long' ) );


$quoteItems = array();
if( $catPath !== array() && ( $catItem = end( $catPath ) ) !== false ) {
	$quoteItems = $catItem->getRefItems( 'text', 'quote', 'default' );
}


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
$pagination = $this->partial(
	$this->config( 'client/html/catalog/lists/partials/pagination', 'catalog/lists/pagination-default.php' ),
	array(
		'params' => $params,
		'total' => $this->get( 'listProductTotal', 0 ),
		'current' => $this->get( 'listProductCurr', 0 ),
		'size' => $this->get( 'listProductSize', 48 )
	)
);


?>
<section class="aimeos catalog-list<?php echo $enc->attr( $classes ); ?>">

	<?php if( isset( $this->listErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->listErrorList as $errmsg ) : ?>
				<li class="error-item"><?php echo $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<?php if( ( $catItem = end( $catPath ) ) !== false ) : ?>
		<div class="catalog-list-head">

			<div class="imagelist-default">
				<?php foreach( $catItem->getRefItems( 'media', 'head', 'default' ) as $mediaItem ) : ?>
					<img class="<?php echo $enc->attr( $mediaItem->getType() ); ?>"
						src="<?php echo $this->content( $mediaItem->getUrl() ); ?>"
					/>
				<?php endforeach; ?>
			</div>

			<h1><?php echo $enc->html( $catItem->getName() ); ?></h1>
			<?php foreach( (array) $textTypes as $textType ) : ?>
				<?php foreach( $catItem->getRefItems( 'text', $textType, 'default' ) as $textItem ) : ?>
					<div class="<?php echo $enc->attr( $textItem->getType() ); ?>">
						<?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?>
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>


	<?php if( count( $quoteItems ) > 0 ) : ?>
		<div class="catalog-list-quote">

			<div class="content">
				<?php foreach( $quoteItems as $quoteItem ) : ?>
					<article><?php echo $enc->html( $quoteItem->getContent() ); ?></article>
				<?php endforeach; ?>
				<a href="#"><?php echo $enc->html( $this->translate( 'client', 'Show all quotes' ), $enc::TRUST ); ?></a>
			</div>

		</div>
	<?php endif; ?>


	<?php echo $this->block()->get( 'catalog/lists/promo' ); ?>


	<div class="catalog-list-type">
		<a class="type-item type-grid" href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'grid' ) + $params, array(), $config ) ); ?>"></a>
		<a class="type-item type-list" href="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array( 'l_type' => 'list' ) + $params, array(), $config ) ); ?>"></a>
	</div>


	<?php echo $pagination; ?>


	<?php if( ( $searchText = $this->param( 'f_search', null ) ) != null ) : ?>
		<div class="list-search">

			<?php if( ( $total = $this->get( 'listProductTotal', 0 ) ) > 0 ) : ?>
				<?php echo $enc->html( sprintf(
					$this->translate(
						'client',
						'Search result for <span class="searchstring">"%1$s"</span> (%2$d article)',
						'Search result for <span class="searchstring">"%1$s"</span> (%2$d articles)',
						$total
					),
					$enc->html( $searchText ),
					$total
				), $enc::TRUST ); ?>
			<?php else : ?>
				<?php echo $enc->html( sprintf(
					$this->translate(
						'client',
						'No articles found for <span class="searchstring">"%1$s"</span>. Please try again with a different keyword.'
					),
					$enc->html( $searchText )
				), $enc::TRUST ); ?>
			<?php endif; ?>

		</div>
	<?php endif; ?>


	<?php echo $this->block()->get( 'catalog/lists/items' ); ?>


	<?php echo $pagination; ?>


</section>
