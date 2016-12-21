<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/** client/html/catalog/lists/metatags
 * Adds the title, meta and link tags to the HTML header
 *
 * By default, each instance of the catalog list component adds some HTML meta
 * tags to the page head section, like page title, meta keywords and description
 * as well as some link tags to support browser navigation. If several instances
 * are placed on one page, this leads to adding several title and meta tags used
 * by search engine. This setting enables you to suppress these tags in the page
 * header and maybe add your own to the page manually.
 *
 * @param boolean True to display the meta tags, false to hide it
 * @since 2017.01
 * @category Developer
 * @category User
 * @see client/html/catalog/detail/metatags
 */


$enc = $this->encoder();

$params = $this->get( 'listParams', array() );
$catItems = $this->get( 'listCatPath', array() );
$total = $this->get( 'listProductTotal', 1 ) / $this->get( 'listPageSize', 1 );
$current = $this->get( 'listPageCurr', 1 );

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', array() );

$params = $this->param();
unset( $params['f_sort'] );


?>
<?php if( (bool) $this->config( 'client/html/catalog/lists/metatags', true ) === true ) : ?>

	<?php if( ( $catItem = end( $catItems ) ) !== false ) : ?>

		<title><?php echo $enc->html( $catItem->getName() ); ?></title>

		<?php foreach( $catItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
		<meta name="keywords" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach; ?>

		<?php foreach( $catItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
		<meta name="description" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach; ?>

	<?php elseif( ( $search = $this->param( 'f_search', null ) ) != null ) : /// Product search hint with user provided search string (%1$s) ?>
		<title><?php echo $enc->html( sprintf( $this->translate( 'client', 'Result for "%1$s"' ), strip_tags( $search ) ) ); ?></title>
	<?php else : ?>
		<title><?php echo $enc->html( $this->translate( 'client', 'Our products' ) ); ?></title>
	<?php endif; ?>


	<?php if( $current > 1 ) : ?>
		<link rel="prev" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->pagiPagePrev ) + $params, array(), $listConfig ) ); ?>" />
	<?php endif; ?>


	<?php if( $current > 1 && $current < $total ) : // Optimization to avoid loading next page while the user is still filtering ?>
		<link rel="next prefetch" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->pagiPageNext ) + $params, array(), $listConfig ) ); ?>" />
	<?php endif; ?>


	<link rel="canonical" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, array(), $listConfig ) ); ?>" />
	<meta name="application-name" content="Aimeos" />

<?php endif; ?>


<?php if( ( $url = $this->get( 'listStockUrl' ) ) != null ) : ?>
	<script type="text/javascript" defer="defer" src="<?php echo $enc->attr( $url ); ?>"></script>
<?php endif; ?>


<?php echo $this->get( 'listHeader' ); ?>
