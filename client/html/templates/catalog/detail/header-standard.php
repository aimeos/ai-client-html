<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
 * @category Developer
 * @category User
 * @see client/html/catalog/lists/metatags
 */


?>
<?php if( (bool) $this->config( 'client/html/catalog/detail/metatags', true ) === true ) : ?>
	<?php if( isset( $this->detailProductItem ) ) : ?>
		<title><?= $enc->html( strip_tags( $this->detailProductItem->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

		<?php $params = array_diff_key( ['d_name' => $this->detailProductItem->getName( 'url' ), 'd_prodid' => $this->detailProductItem->getId(), 'd_pos' => ''], $detailFilter ) ?>
		<link rel="canonical" href="<?= $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, [], $detailConfig + ['absoluteUri' => true] ) ) ?>">

		<meta property="og:type" content="product">
		<meta property="og:title" content="<?= $enc->attr( strip_tags( $this->detailProductItem->getName() ) ) ?>">
		<meta property="og:url" content="<?= $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, [], $detailConfig + ['absoluteUri' => true] ) ) ?>">

		<?php foreach( $this->detailProductItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ) ) ) ?>">
			<meta name="twitter:card" content="summary_large_image">
		<?php endforeach ?>

		<?php foreach( $this->detailProductItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
			<meta property="og:description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

		<?php foreach( $this->detailProductItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

		<?php if( ( $priceItem = $this->detailProductItem->getRefItems( 'price', 'default', 'default' )->first() ) !== null ) : ?>
			<meta property="product:price:amount" content="<?= $enc->attr( $priceItem->getValue() ) ?>">
			<meta property="product:price:currency" content="<?= $enc->attr( $priceItem->getCurrencyId() ) ?>">
		<?php endif ?>

	<?php endif ?>

	<meta name="application-name" content="Aimeos">

<?php endif ?>

<?php if( isset( $this->detailStockUrl ) ) : ?>
	<?php foreach( $this->detailStockUrl as $url ) : ?>
		<script defer src="<?= $enc->attr( $url ) ?>"></script>
	<?php endforeach ?>
<?php endif ?>

<?= $this->get( 'detailHeader' ) ?>
