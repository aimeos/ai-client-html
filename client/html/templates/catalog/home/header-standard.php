<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/catalog/home/url/target' );
$cntl = $this->config( 'client/html/catalog/home/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/home/url/action', 'home' );
$config = $this->config( 'client/html/catalog/home/url/config', [] );


?>
<?php if( isset( $this->homeTree ) ) : ?>

	<title><?= $enc->html( strip_tags( $this->homeTree->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?= $enc->attr( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?>">
	<meta property="og:title" content="<?= $enc->attr( strip_tags( $this->homeTree->getName() ) ) ?>">
	<meta property="og:url" content="<?= $enc->attr( $this->url( $target, $cntl, $action, [], [], $config + ['absoluteUri' => true] ) ) ?>">

	<?php if( $icon = $this->get( 'contextSiteIcon' ) ) : ?>
		<meta property="og:image" content="<?= $enc->attr( $this->content( $icon ) ) ?>">
		<meta name="twitter:card" content="summary_large_image">
	<?php endif ?>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
		<meta property="og:description" content="<?= $enc->attr( $textItem->getContent() ) ?>">
		<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
	<?php endforeach ?>

	<?php foreach( $this->homeTree->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
		<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ) ?>">
	<?php endforeach ?>

	<?php
		if( ( $media = $this->homeTree->getRefItems( 'media', 'stage', 'default' ) )->isEmpty() ) {
			$media = $this->homeTree->getChildren()->getRefItems( 'media', 'stage', 'default' );
		}
	?>

	<?php if( $previews = $media->flat()->getPreviews()->first() ) : ?>
		<link rel="preload" imagesrcset="<?= $enc->attr( $this->imageset( $previews ) ) ?>">
	<?php endif ?>

<?php else : ?>

	<title><?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<?php endif ?>

<?php if( isset( $this->homeStockUrl ) ) : ?>
	<?php foreach( $this->homeStockUrl as $url ) : ?>
		<script defer src="<?= $enc->attr( $url ) ?>"></script>
	<?php endforeach ?>
<?php endif ?>

<meta name="application-name" content="Aimeos">
