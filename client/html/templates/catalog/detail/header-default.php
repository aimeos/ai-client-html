<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/** client/html/common/template/baseurl
 * Path to the template directory or template base URL to a server
 *
 * This option must point to the base path or URL of the used template. This
 * directory must contain a "css/, "js/" and "images/" subdirectory with the
 * necessary files referenced in the HTML code.
 *
 * If you use an absolute URL prefer the https protocol to avoid issues with mixed
 * content. Browsers avoid to load files via http if the page was retrieved via
 * https.
 *
 * @param string Relative path or absolute URL
 * @since 2014.03
 * @see client/html/common/content/baseurl
 */
$templateUrl = $this->config( 'client/html/common/template/baseurl' );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );


/** client/html/catalog/detail/stock/enable
 * Enables or disables displaying product stock levels in product detail view
 *
 * This configuration option allows shop owners to display product
 * stock levels for each product in the detail views or to disable
 * fetching product stock information.
 *
 * The stock information is fetched via AJAX and inserted via Javascript.
 * This allows to cache product items by leaving out such highly
 * dynamic content like stock levels which changes with each order.
 *
 * @param boolean Value of "1" to display stock levels, "0" to disable displaying them
 * @since 2014.03
 * @category User
 * @category Developer
 * @see client/html/catalog/lists/stock/enable
 * @see client/html/catalog/stock/url/target
 * @see client/html/catalog/stock/url/controller
 * @see client/html/catalog/stock/url/action
 * @see client/html/catalog/stock/url/config
 */
$stock = $this->config( 'client/html/catalog/detail/stock/enable', true );

$stockTarget = $this->config( 'client/html/catalog/stock/url/target' );
$stockCntl = $this->config( 'client/html/catalog/stock/url/controller', 'catalog' );
$stockAction = $this->config( 'client/html/catalog/stock/url/action', 'stock' );
$stockConfig = $this->config( 'client/html/catalog/stock/url/config', array() );


?>
<?php if( isset( $this->detailProductItem ) ) : ?>
	<title><?php echo $enc->html( $this->detailProductItem->getName() ); ?></title>

	<?php foreach( $this->detailProductItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
		<meta name="keywords" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

	<?php foreach( $this->detailProductItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
		<meta name="description" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
	<?php endforeach; ?>

	<?php $params = array( 'd_name' => $this->detailProductItem->getName( 'url' ), 'd_prodid' => $this->detailProductItem->getId() ); ?>
	<link rel="canonical" href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>" />

	<?php if( $stock == true ) : ?>
		<?php
			$stockIds = array_keys( $this->detailProductItem->getRefItems( 'product', null, 'default' ) );
			$stockIds[] = $this->detailProductItem->getId();
			$url = $this->url( $stockTarget, $stockCntl, $stockAction, array( 's_prodid' => $stockIds ), array(), $stockConfig );
		?>
		<script type="text/javascript" defer="defer" src="<?php echo $enc->attr( $url ); ?>"></script>
	<?php endif; ?>

<?php endif; ?>

<meta name="application-name" content="Aimeos" />

<?php echo $this->get( 'detailHeader' ); ?>
