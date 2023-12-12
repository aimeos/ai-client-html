<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Product;

/**
 * Implementation of catalog product section HTML clients for a configurable list of products.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/product/name
	 * Class name of the used catalog product client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Product\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Product\Myproduct
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/product/name = Myproduct
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyProduct"!
	 *
	 * @param string Last part of the class name
	 * @since 2019.06
	 */


	private array $tags = [];
	private ?string $expire = null;
	private ?\Aimeos\Base\View\Iface $view = null;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		/** client/html/catalog/product/cache
		 * Enables or disables caching only for the catalog product component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @see client/html/catalog/detail/cache
		 * @see client/html/catalog/filter/cache
		 * @see client/html/catalog/stage/cache
		 * @see client/html/catalog/list/cache
		 */

		/** client/html/catalog/product
		 * All parameters defined for the catalog product component and its subparts
		 *
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/catalog#product
		 */
		$confkey = 'client/html/catalog/product';

		if( $html = $this->cached( 'body', $uid, [], $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		$config = $this->context()->config();
		$template = $config->get( 'client/html/catalog/product/template-body', 'catalog/product/body' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->cache( 'body', $uid, [], $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$confkey = 'client/html/catalog/product';

		if( $html = $this->cached( 'header', $uid, [], $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		$config = $this->context()->config();
		$template = $config->get( 'client/html/catalog/product/template-header', 'catalog/product/header' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->cache( 'header', $uid, [], $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Modifies the cached content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified content
	 */
	public function modify( string $content, string $uid ) : string
	{
		return $this->replaceSection( $content, $this->view()->csrf()->formfield(), 'catalog.lists.items.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$context = $this->context();
		$config = $context->config();

		/** client/html/catalog/product/product-codes
		 * List of codes of products to load for the current list.
		 * Should be set dynamically through some integration plugin,
		 * to allow a list of products with configurable products.
		 *
		 * @param string List of codes of products to load for the current list
		 * @since 2019.06
		 */
		$productCodes = $config->get( 'client/html/catalog/product/product-codes', [] );

		$products = \Aimeos\Controller\Frontend::create( $context, 'product' )
			->compare( '==', 'product.code', $productCodes )
			->slice( 0, count( $productCodes ) )
			->uses( $this->domains() )
			->search();

		// Sort products by the order given in the configuration "client/html/catalog/product/product-codes".
		$productCodesOrder = array_flip( $productCodes );
		$products->usort( function( $a, $b ) use ( $productCodesOrder ) {
			return $productCodesOrder[$a->getCode()] - $productCodesOrder[$b->getCode()];
		} );

		$productItems = $products->copy();

		if( $config->get( 'client/html/catalog/product/basket-add', false ) )
		{
			foreach( $products as $product )
			{
				if( $product->getType() === 'select' ) {
					$productItems->union( $product->getRefItems( 'product', 'default', 'default' ) );
				}
			}
		}

		$this->addMetaItems( $products, $expire, $tags, ['product'] );

		$view->productItems = $products;
		$view->productTotal = count( $products );
		$view->itemsStockUrl = $this->stockUrl( $productItems );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the data domains fetched along with the products
	 *
	 * @return array List of domain names
	 */
	protected function domains() : array
	{
		$config = $this->context()->config();

		/** client/html/catalog/product/domains
		 * A list of domain names whose items should be available in the catalog product view template
		 *
		 * The templates rendering product lists usually add the images, prices
		 * and texts associated to each product item. If you want to display additional
		 * content like the product attributes, you can configure your own list of
		 * domains (attribute, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is required
		 * for fetching the content!
		 *
		 * This configuration option overwrites the "client/html/catalog/domains"
		 * option that allows to configure the domain names of the items fetched
		 * for all catalog related data.
		 *
		 * @param array List of domain names
		 * @since 2019.06
		 * @see client/html/catalog/domains
		 * @see client/html/catalog/detail/domains
		 * @see client/html/catalog/stage/domains
		 * @see client/html/catalog/lists/domains
		 */
		$domains = ['catalog', 'media', 'media/property', 'price', 'supplier', 'text'];
		$domains = $config->get( 'client/html/catalog/domains', $domains );
		$domains = $config->get( 'client/html/catalog/product/domains', $domains );

		if( $config->get( 'client/html/catalog/product/basket-add', false ) ) {
			$domains = array_merge_recursive( $domains, ['product' => ['default'], 'attribute' => ['variant', 'custom', 'config']] );
		}

		return $domains;
	}


	/**
	 * Returns the list of stock URLs for the given products
	 *
	 * @param \Aimeos\Map $products List of products
	 * @return \Aimeos\Map List of stock URLs
	 */
	protected function stockUrl( \Aimeos\Map $products ) : \Aimeos\Map
	{
		$config = $this->context()->config();

		/** client/html/catalog/product/stock/enable
		 * Enables or disables displaying product stock levels in product list views
		 *
		 * This configuration option allows shop owners to display product
		 * stock levels for each product in list views or to disable
		 * fetching product stock information.
		 *
		 * The stock information is fetched via AJAX and inserted via Javascript.
		 * This allows to cache product items by leaving out such highly
		 * dynamic content like stock levels which changes with each order.
		 *
		 * @param boolean Value of "1" to display stock levels, "0" to disable displaying them
		 * @since 2019.06
		 * @see client/html/catalog/detail/stock/enable
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/config
		 */
		$enabled = $config->get( 'client/html/catalog/product/stock/enable', true );

		if( !$enabled || $products->isEmpty() ) {
			return map();
		}

		return $this->getStockUrl( $this->view(), $products );
	}


	/** client/html/catalog/product/template-body
	 * Relative path to the HTML body template of the catalog product client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the result shown in the body of the frontend. The
	 * configuration string is the path to the template file relative
	 * to the templates directory (usually in templates/client/html).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but suffixed by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, it
	 * should be suffixed by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page body
	 * @since 2019.06
	 * @see client/html/catalog/product/template-header
	 */

	/** client/html/catalog/product/template-header
	 * Relative path to the HTML header template of the catalog product client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the HTML code that is inserted into the HTML page header
	 * of the rendered page in the frontend. The configuration string is the
	 * path to the template file relative to the templates directory (usually
	 * in templates/client/html).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but suffixed by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, it
	 * should be suffixed by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page head
	 * @since 2019.06
	 * @see client/html/catalog/product/template-body
	 */

	/** client/html/catalog/product/decorators/excludes
	 * Excludes decorators added by the "common" option from the catalog product html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "client/html/common/decorators/default" before they are wrapped
	 * around the html client.
	 *
	 *  client/html/catalog/product/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/product/decorators/global
	 * @see client/html/catalog/product/decorators/local
	 */

	/** client/html/catalog/product/decorators/global
	 * Adds a list of globally available decorators only to the catalog product html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/product/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/product/decorators/excludes
	 * @see client/html/catalog/product/decorators/local
	 */

	/** client/html/catalog/product/decorators/local
	 * Adds a list of local decorators only to the catalog product html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/product/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/product/decorators/excludes
	 * @see client/html/catalog/product/decorators/global
	 */
}
