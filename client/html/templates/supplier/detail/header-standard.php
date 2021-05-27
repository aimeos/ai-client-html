<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/supplier/detail/url/target' );
$cntl = $this->config( 'client/html/supplier/detail/url/controller', 'supplier' );
$action = $this->config( 'client/html/supplier/detail/url/action', 'detail' );
$config = $this->config( 'client/html/supplier/detail/url/config', [] );


/** client/html/supplier/detail/metatags
 * Adds the title, meta and link tags to the HTML header
 *
 * By default, each instance of the supplier list component adds some HTML meta
 * tags to the page head section, like page title, meta keywords and description
 * as well as some link tags to support browser navigation. If several instances
 * are placed on one page, this leads to adding several title and meta tags used
 * by search engine. This setting enables you to suppress these tags in the page
 * header and maybe add your own to the page manually.
 *
 * @param boolean True to display the meta tags, false to hide it
 * @since 2021.01
 * @category Developer
 * @category User
 * @see client/html/supplier/lists/metatags
 */


?>
<?php if( (bool) $this->config( 'client/html/supplier/detail/metatags', true ) === true ) : ?>
	<?php if( isset( $this->detailSupplierItem ) ) : ?>
		<title><?= $enc->html( $this->detailSupplierItem->getName() ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

		<?php foreach( $this->detailSupplierItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

		<?php foreach( $this->detailSupplierItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
		<?php endforeach ?>

		<?php $params = ['s_name' => $this->detailSupplierItem->getName( 'url' ), 'f_supid' => $this->detailSupplierItem->getId()] ?>
		<link rel="canonical" href="<?= $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config + ['absoluteUri' => true] ) ) ?>">

		<meta property="og:type" content="product">
		<meta property="og:title" content="<?= $enc->html( $this->detailSupplierItem->getName() ) ?>">
		<meta property="og:url" content="<?= $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config + ['absoluteUri' => true] ) ) ?>">

		<?php foreach( $this->detailSupplierItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ) ) ) ?>">
			<meta name="twitter:card" content="summary_large_image">
		<?php endforeach ?>

		<?php foreach( $this->detailSupplierItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
			<meta property="og:description" content="<?= $enc->attr( $textItem->getContent() ) ?>">
		<?php endforeach ?>

	<?php endif ?>

	<meta name="application-name" content="Aimeos">

<?php endif ?>

<?= $this->get( 'detailHeader' ) ?>
