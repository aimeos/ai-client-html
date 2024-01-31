<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */

$enc = $this->encoder();

/** client/html/catalog/home/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2020.10
 * @see client/html/catalog/home/url/controller
 * @see client/html/catalog/home/url/action
 * @see client/html/catalog/home/url/config
 * @see client/html/catalog/home/url/filter
 */

/** client/html/catalog/home/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2020.10
 * @see client/html/catalog/home/url/target
 * @see client/html/catalog/home/url/action
 * @see client/html/catalog/home/url/config
 * @see client/html/catalog/home/url/filter
 */

/** client/html/catalog/home/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2020.10
 * @see client/html/catalog/home/url/target
 * @see client/html/catalog/home/url/controller
 * @see client/html/catalog/home/url/config
 * @see client/html/catalog/home/url/filter
 */

/** client/html/catalog/home/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2020.10
 * @see client/html/catalog/home/url/target
 * @see client/html/catalog/home/url/controller
 * @see client/html/catalog/home/url/action
 * @see client/html/catalog/home/url/filter
 */

/** client/html/catalog/home/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/catalog/home/url/target
 * @see client/html/catalog/home/url/controller
 * @see client/html/catalog/home/url/action
 * @see client/html/catalog/home/url/config
 */


?>
<?php if( isset( $this->homeTree ) ) : ?>

	<title><?= $enc->html( strip_tags( $this->homeTree->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?= $enc->attr( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?>">
	<meta property="og:title" content="<?= $enc->attr( strip_tags( $this->homeTree->getName() ) ) ?>">
	<meta property="og:url" content="<?= $enc->attr( $this->link( 'client/html/catalog/home/url', [], ['absoluteUri' => true] ) ) ?>">

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

	<?php if( $mediaItem = $media->flat()->first() ) : ?>
		<link rel="preload" as="image"
			href="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
			imagesrcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews( true ), $mediaItem->getFileSystem() ) ) ?>">
	<?php endif ?>

<?php else : ?>

	<title><?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

<?php endif ?>

<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/slider.css', 'fs-theme', true ) ) ?>">
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-home.css', 'fs-theme', true ) ) ?>">

<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/slider.js', 'fs-theme', true ) ) ?>"></script>
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/catalog-home.js', 'fs-theme', true ) ) ?>"></script>

<meta name="application-name" content="Aimeos">
