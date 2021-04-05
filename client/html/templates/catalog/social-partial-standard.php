<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/* Available data:
 * - productItem : Product item incl. referenced items
 */

$enc = $this->encoder();


/** client/html/catalog/social/list
 * List of social network names that should be displayed in the catalog views
 *
 * Users can share product links in several social networks. The order of the
 * social network names in the configuration determines the order of the links
 * on the catalog pages.
 *
 * You can add more social links only by configuration if you define a
 * corresponding URL for the added social network. For example, if you would
 * like to add Tumblr as social network, you also need to configure a link with
 * the placeholder for the URL:
 *
 *  client/html/catalog/social/list = array( ..., 'tumblr' )
 *  client/html/catalog/social/url/tumblr = http://www.tumblr.com/share/link?url=%1$s&name=%2$s
 *
 * Possible placeholders and replaced values are:
 *
 * * %1$s : Shop URL of the product detail page
 * * %2$s : Name of the product
 * * %3$s : URL of the first product image
 *
 * @param array List of social network names
 * @since 2017.04
 * @category User
 * @category Developer
 * @see client/html/catalog/social/url/facebook
 * @see client/html/catalog/social/url/twitter
 * @see client/html/catalog/social/url/pinterest
 */
$list = $this->config( 'client/html/catalog/social/list', array( 'facebook', 'twitter', 'pinterest' ) );

$urls = array(
	/** client/html/catalog/social/url/whatsapp
	 * URL for sharing product links over WhatsApp
	 *
	 * Users can share product links over WhatsApp. This requires a URL defined
	 * by WhatsApp that accepts the transmitted product page URL. This URL must
	 * contain at least the "%1$s" placeholder for the URL to the product detail
	 * page of the shop.
	 *
	 * Possible placeholders and replaced values are:
	 *
	 * * %1$s : Shop URL of the product detail page
	 * * %2$s : Name of the product
	 * * %3$s : URL of the first product image
	 *
	 * @param string URL to share products on Facebook
	 * @since 2020.01
	 * @category User
	 * @category Developer
	 * @see client/html/catalog/social/list
	 */
	'whatsapp' => 'https://wa.me/?text=%2$s+%1$s',

	/** client/html/catalog/social/url/facebook
	 * URL for sharing product links on Facebook
	 *
	 * Users can share product links on Facebook. This requires a URL defined
	 * by Facebook that accepts the transmitted product page URL. This URL must
	 * contain at least the "%1$s" placeholder for the URL to the product detail
	 * page of the shop.
	 *
	 * Possible placeholders and replaced values are:
	 *
	 * * %1$s : Shop URL of the product detail page
	 * * %2$s : Name of the product
	 * * %3$s : URL of the first product image
	 *
	 * @param string URL to share products on Facebook
	 * @since 2017.04
	 * @category User
	 * @category Developer
	 * @see client/html/catalog/social/list
	 */
	'facebook' => 'https://www.facebook.com/sharer.php?u=%1$s&t=%2$s',

	/** client/html/catalog/social/url/twitter
	 * URL for sharing product links on Twitter
	 *
	 * Users can share product links on Twitter. This requires a URL defined
	 * by Twitter that accepts the transmitted product page URL. This URL must
	 * contain at least the "%1$s" placeholder for the URL to the product detail
	 * page of the shop.
	 *
	 * Possible placeholders and replaced values are:
	 *
	 * * %1$s : Shop URL of the product detail page
	 * * %2$s : Name of the product
	 * * %3$s : URL of the first product image
	 *
	 * @param string URL to share products on Twitter
	 * @since 2017.04
	 * @category User
	 * @category Developer
	 * @see client/html/catalog/social/list
	 */
	'twitter' => 'https://twitter.com/share?url=%1$s&text=%2$s',

	/** client/html/catalog/social/url/pinterest
	 * URL for sharing product links on Pinterest
	 *
	 * Users can share product links on Pinterest. This requires a URL defined
	 * by Pinterest that accepts the transmitted product page URL. This URL must
	 * contain the "%1$s", "%2$s" and "%3$s" placeholders for the URL to the
	 * product detail page, the product name and the product image to be useful.
	 *
	 * Possible placeholders and replaced values are:
	 *
	 * * %1$s : Shop URL of the product detail page
	 * * %2$s : Name of the product
	 * * %3$s : URL of the first product image
	 *
	 * @param string URL to share products on Pinterest
	 * @since 2017.04
	 * @category User
	 * @category Developer
	 * @see client/html/catalog/social/list
	 */
	'pinterest' => 'https://pinterest.com/pin/create/button/?url=%1$s&description=%2$s&media=%3$s',
);

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );
$detailConfig['absoluteUri'] = true;

$params = array_diff_key( ['d_name' => $this->productItem->getName( 'url' ), 'd_prodid' => $this->productItem->getId(), 'd_pos' => '0'], $detailFilter );


?>
<div class="catalog-social">
<?php foreach( $list as $entry ) : $default = ( isset( $urls[$entry] ) ? $urls[$entry] : null ) ?>
	<?php if( ( $link = $this->config( 'client/html/catalog/social/url/' . $entry, $default ) ) !== null ) : ?>
		<a class="social-button social-button-<?= $enc->attr( $entry ) ?>" rel="noopener"
			href="<?= $enc->attr( sprintf( $link,
				$enc->url( $this->url( $detailTarget, $detailController, $detailAction, $params, [], $detailConfig ) ),
				$this->productItem->getName(),
				$this->content( $this->productItem->getRefItems( 'media', 'default', 'default' )->getPreview( true )->first() )
			) ) ?>"
			title="<?= $enc->attr( $entry ) ?>"
			target="_blank"
		></a>

	<?php endif ?>
<?php endforeach ?>
</div>
