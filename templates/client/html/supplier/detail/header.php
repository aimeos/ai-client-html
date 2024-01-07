<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */

$enc = $this->encoder();

/** client/html/supplier/detail/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2020.10
 * @see client/html/supplier/detail/url/controller
 * @see client/html/supplier/detail/url/action
 * @see client/html/supplier/detail/url/config
 * @see client/html/supplier/detail/url/filter
 */

/** client/html/supplier/detail/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2020.10
 * @see client/html/supplier/detail/url/target
 * @see client/html/supplier/detail/url/action
 * @see client/html/supplier/detail/url/config
 * @see client/html/supplier/detail/url/filter
 */

/** client/html/supplier/detail/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2020.10
 * @see client/html/supplier/detail/url/target
 * @see client/html/supplier/detail/url/controller
 * @see client/html/supplier/detail/url/config
 * @see client/html/supplier/detail/url/filter
 */

/** client/html/supplier/detail/url/config
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
 * @see client/html/supplier/detail/url/target
 * @see client/html/supplier/detail/url/controller
 * @see client/html/supplier/detail/url/action
 * @see client/html/supplier/detail/url/filter
 */

/** client/html/supplier/detail/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/supplier/detail/url/target
 * @see client/html/supplier/detail/url/controller
 * @see client/html/supplier/detail/url/action
 * @see client/html/supplier/detail/url/config
 */


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
		<link rel="canonical" href="<?= $enc->attr( $this->link( 'client/html/supplier/detail/url', $params, ['absoluteUri' => true] ) ) ?>">

		<meta property="og:type" content="website">
		<meta property="og:title" content="<?= $enc->html( $this->detailSupplierItem->getName() ) ?>">
		<meta property="og:url" content="<?= $enc->attr( $this->link( 'client/html/supplier/detail/url', $params, ['absoluteUri' => true] ) ) ?>">

		<?php foreach( $this->detailSupplierItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getPreview( true ), $mediaItem->getFileSystem() ) ) ?>">
			<meta name="twitter:card" content="summary_large_image">
		<?php endforeach ?>

		<?php foreach( $this->detailSupplierItem->getRefItems( 'text', 'short', 'default' ) as $textItem ) : ?>
			<meta property="og:description" content="<?= $enc->attr( $textItem->getContent() ) ?>">
		<?php endforeach ?>

	<?php endif ?>

	<meta name="application-name" content="Aimeos">

<?php endif ?>

<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/supplier-detail.css', 'fs-theme', true ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSiteTheme', 'default' ) . '/supplier-detail.js', 'fs-theme', true ) ) ?>"></script>
