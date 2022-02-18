<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();

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
 * @see client/html/catalog/detail/metatags
 */


?>
<?php if( (bool) $this->config( 'client/html/catalog/lists/metatags', true ) === true ) : ?>
	<?php if( ( $catItem = $this->get( 'listCatPath', map() )->last() ) !== null ) : ?>
		<title><?= $enc->html( strip_tags( $catItem->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

		<?php foreach( $catItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

		<?php foreach( $catItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

	<?php elseif( ( $search = $this->param( 'f_search', null ) ) != null ) : /// Product search hint with user provided search string (%1$s) ?>
		<title><?= $enc->html( sprintf( $this->translate( 'client', 'Result for "%1$s"' ), strip_tags( $search ) ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>
		<meta name="keywords" content="<?= $enc->attr( strip_tags( $search ) ) ?>">
		<meta name="description" content="<?= $enc->attr( strip_tags( $search ) ) ?>">
	<?php else : ?>
		<title><?= $enc->html( $this->translate( 'client', 'Our products' ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>
		<meta name="keywords" content="<?= $enc->attr( $this->translate( 'client', 'Our products' ) ) ?>">
		<meta name="description" content="<?= $enc->attr( $this->translate( 'client', 'Our products' ) ) ?>">
	<?php endif ?>


	<?php if( $this->get( 'listPageCurr', 0 ) > 1 ) : ?>
		<link rel="prev" href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', ['l_page' => $this->get( 'listPagePrev', 0 )] + $this->get( 'listParams', [] ) ) ) ?>">
	<?php endif ?>


	<?php if( $this->get( 'listPageCurr', 0 ) > 1 && $this->get( 'listPageCurr', 0 ) < $this->get( 'listPageLast', 0 ) ) : // Optimization to avoid loading next page while the user is still filtering ?>
		<link rel="next prefetch" href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', ['l_page' => $this->get( 'listPageNext', 0 )] + $this->get( 'listParams', [] ) ) ) ?>">
	<?php endif ?>


	<link rel="canonical" href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', $this->get( 'listParams', [] ), ['absoluteUri' => true] ) ) ?>">
	<meta name="application-name" content="Aimeos">

<?php endif ?>

<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-lists.css', 'fs-theme', true ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-lists.js', 'fs-theme', true ) ) ?>"></script>

<?php foreach( $this->get( 'listStockUrl', [] ) as $url ) : ?>
	<script class="items-stock" defer src="<?= $enc->attr( $url ) ?>"></script>
<?php endforeach ?>
