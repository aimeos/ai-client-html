<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2024
 */

$enc = $this->encoder();

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );


/** client/html/catalog/detail/metatags
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
 * @see client/html/catalog/lists/metatags
 */


?>
<?php if( (bool) $this->config( 'client/html/catalog/detail/metatags', true ) === true ) : ?>
	<?php if( isset( $this->detailProductItem ) ) : ?>
		<?php if( $title = $this->detailProductItem->getRefItems( 'text', 'meta-title', 'default' )->getContent()->first() ) : ?>
			<title><?= $enc->html( $title ) ?></title>
		<?php else : ?>
			<title><?= $enc->html( strip_tags( $this->detailProductItem->getName() ) ) ?>
				<?= $enc->html( strip_tags( ' ' . $this->detailProductItem->getRefItems( 'supplier' )->getName()->first() ) ) ?>
				<?= $enc->html( ' | ' . $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?>
			</title>
		<?php endif ?>

		<?php $name = $this->detailProductItem->getName( 'url' ) ?>
		<?php $params = array_diff_key( ['path' => $name, 'd_name' => $name, 'd_prodid' => $this->detailProductItem->getId(), 'd_pos' => ''], $detailFilter ) ?>
		<link rel="canonical" href="<?= $enc->attr( $this->url( $this->detailProductItem->getTarget() ?: $detailTarget, $detailController, $detailAction, $params, [], $detailConfig + ['absoluteUri' => true] ) ) ?>">

		<meta property="og:type" content="product">
		<meta property="og:title" content="<?= $enc->attr( strip_tags( $this->detailProductItem->getName() ) ) ?>">
		<meta property="og:url" content="<?= $enc->attr( $this->url( $this->detailProductItem->getTarget() ?: $detailTarget, $detailController, $detailAction, $params, [], $detailConfig + ['absoluteUri' => true] ) ) ?>">

		<?php if( $mediaItem = $this->detailProductItem->getRefItems( 'media', 'default', 'default' )->first() ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ), $mediaItem->getFileSystem() ) ) ?>">
			<meta name="twitter:card" content="summary_large_image">
		<?php endif ?>

		<?php if( $textItem = $this->detailProductItem->getRefItems( 'text', 'meta-description', 'default' )->first() ) : ?>
			<meta property="og:description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endif ?>

		<?php if( $textItem = $this->detailProductItem->getRefItems( 'text', 'meta-keyword', 'default' )->first() ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endif ?>

		<?php if( ( $priceItem = $this->detailProductItem->getRefItems( 'price', 'default', 'default' )->first() ) !== null ) : ?>
			<meta property="product:price:amount" content="<?= $enc->attr( $priceItem->getValue() ) ?>">
			<meta property="product:price:currency" content="<?= $enc->attr( $priceItem->getCurrencyId() ) ?>">
		<?php endif ?>

	<?php endif ?>

	<meta name="application-name" content="Aimeos">

<?php endif ?>

<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/slider.css', 'fs-theme', true ) ) ?>">
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-detail.css', 'fs-theme', true ) ) ?>">

<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/slider.js', 'fs-theme', true ) ) ?>"></script>
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-detail.js', 'fs-theme', true ) ) ?>"></script>

<?php if( isset( $this->detailStockUrl ) ) : ?>
	<?php foreach( $this->detailStockUrl as $url ) : ?>
		<script defer src="<?= $enc->attr( $url ) ?>"></script>
	<?php endforeach ?>
<?php endif ?>

<?= $this->get( 'detailHeader' ) ?>
