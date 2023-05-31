<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Detail;


/**
 * Default implementation of catalog detail section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/detail/name
	 * Class name of the used catalog detail client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Detail\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Detail\Mydetail
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/detail/name = Mydetail
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyDetail"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
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
		$view = $this->view();
		$config = $this->context()->config();
		$prefixes = ['d_prodid', 'd_name'];

		$code = $config->get( 'client/html/catalog/detail/prodcode-default' );
		$id = $config->get( 'client/html/catalog/detail/prodid-default', $code );

		if( !$view->param( 'd_name', $view->param( 'd_prodid', $id ) ) ) {
			return '';
		}

		/** client/html/catalog/detail/cache
		 * Enables or disables caching only for the catalog detail component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @see client/html/catalog/filter/cache
		 * @see client/html/catalog/lists/cache
		 * @see client/html/catalog/stage/cache
		 */

		/** client/html/catalog/detail
		 * All parameters defined for the catalog detail component and its subparts
		 *
		 * This returns all settings related to the detail component.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/catalog#detail
		 */
		$confkey = 'client/html/catalog/detail';

		if( $html = $this->cached( 'body', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/detail/template-body
		 * Relative path to the HTML body template of the catalog detail client.
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
		 * @since 2014.03
		 * @see client/html/catalog/detail/template-header
		 * @see client/html/catalog/detail/404
		 */
		$template = $config->get( 'client/html/catalog/detail/template-body', 'catalog/detail/body' );

		$view = $this->view = $this->view ?? $this->object()->data( $view, $this->tags, $this->expire );
		$html = $this->modify( $view->render( $template ), $uid );

		return $this->cache( 'body', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$view = $this->view();
		$config = $this->context()->config();
		$prefixes = ['d_prodid', 'd_name'];
		$confkey = 'client/html/catalog/detail';

		$code = $config->get( 'client/html/catalog/detail/prodcode-default' );
		$id = $config->get( 'client/html/catalog/detail/prodid-default', $code );

		if( !$view->param( 'd_name', $view->param( 'd_prodid', $id ) ) ) {
			return '';
		}

		if( $html = $this->cached( 'header', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/detail/template-header
		 * Relative path to the HTML header template of the catalog detail client.
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
		 * @since 2014.03
		 * @see client/html/catalog/detail/template-body
		 * @see client/html/catalog/detail/404
		 */
		$template = $config->get( 'client/html/catalog/detail/template-header', 'catalog/detail/header' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->cache( 'header', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
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
		$content = $this->replaceSection( $content, $this->navigator(), 'catalog.detail.navigator' );
		return $this->replaceSection( $content, $this->view()->csrf()->formfield(), 'catalog.detail.csrf' );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();
		$session = $context->session();

		$params = $view->param();
		$site = $context->locale()->getSiteItem()->getCode();

		$session->set( 'aimeos/catalog/last/' . $site, $view->link( 'client/html/catalog/detail/url', $params ) );
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
		$productItem = $this->product( $view );

		$propItems = $productItem->getPropertyItems();
		$attrItems = $productItem->getRefItems( 'attribute', null, 'default' );
		$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );

		$this->addMetaItems( $productItem, $expire, $tags, ['product'] );

		if( in_array( $productItem->getType(), ['bundle', 'select'] ) )
		{
			\Aimeos\Map::method( 'attrparent', function( $subProdId ) {
				foreach( $this->list as $item ) {
					$item->parent = array_merge( $item->parent ?? [], [$subProdId] );
				}
				return $this;
			} );

			foreach( $productItem->getRefItems( 'product', null, 'default' ) as $subProdId => $subProduct )
			{
				$propItems->merge( $subProduct->getPropertyItems()->assign( ['parent' => $subProdId] ) );
				$mediaItems->merge( $subProduct->getRefItems( 'media', 'default', 'default' ) );
				$attrItems->replace(
					$subProduct->getRefItems( 'attribute', null, ['default', 'variant'] )->attrparent( $subProdId )
				);
			}
		}

		$view->detailMediaItems = $mediaItems;
		$view->detailProductItem = $productItem;
		$view->detailAttributeMap = $attrItems->groupBy( 'attribute.type' )->ksort();
		$view->detailPropertyMap = $propItems->groupBy( 'product.property.type' )->ksort();
		$view->detailStockTypes = $productItem->getStockItems()->getType();
		$view->detailStockUrl = $this->stockUrl( $productItem );

		$this->call( 'seen', $productItem );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the data domains fetched along with the products
	 *
	 * @return array List of domain names
	 */
	protected function domains() : array
	{
		$context = $this->context();
		$config = $context->config();

		$domains = [
			'attribute', 'attribute/property', 'catalog', 'media', 'media/property', 'price',
			'product', 'product/property', 'supplier', 'supplier/address', 'text', 'stock'
		];

		/** client/html/catalog/domains
		 * A list of domain names whose items should be available in the catalog view templates
		 *
		 * @see client/html/catalog/detail/domains
		 */
		$domains = $config->get( 'client/html/catalog/domains', $domains );

		/** client/html/catalog/detail/domains
		 * A list of domain names whose items should be available in the product detail view template
		 *
		 * The templates rendering product details usually add the images,
		 * prices, texts, attributes, products, etc. associated to the product
		 * item. If you want to display additional or less content, you can
		 * configure your own list of domains (attribute, media, price, product,
		 * text, etc. are domains) whose items are fetched from the storage.
		 * Please keep in mind that the more domains you add to the configuration,
		 * the more time is required for fetching the content!
		 *
		 * Since version 2014.05 this configuration option overwrites the
		 * "client/html/catalog/domains" option that allows to configure the
		 * domain names of the items fetched for all catalog related data.
		 *
		 * @param array List of domain names
		 * @since 2014.03
		 * @see client/html/catalog/domains
		 * @see client/html/catalog/lists/domains
		 */
		$domains = $config->get( 'client/html/catalog/detail/domains', $domains );

		return $domains;
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function navigator() : string
	{
		$view = $this->view();
		$context = $this->context();

		if( is_numeric( $pos = $view->param( 'd_pos' ) ) )
		{
			if( $pos < 1 ) {
				$pos = $start = 0; $size = 2;
			} else {
				$start = $pos - 1; $size = 3;
			}

			$site = $context->locale()->getSiteItem()->getCode();
			$params = $context->session()->get( 'aimeos/catalog/lists/params/last/' . $site, [] );
			$level = $view->config( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

			$catids = $view->value( $params, 'f_catid', $view->config( 'client/html/catalog/lists/catid-default' ) );
			$sort = $view->value( $params, 'f_sort', $view->config( 'client/html/catalog/lists/sort', 'relevance' ) );

			$products = \Aimeos\Controller\Frontend::create( $context, 'product' )
				->sort( $sort ) // prioritize user sorting over the sorting through relevance and category
				->allOf( $view->value( $params, 'f_attrid', [] ) )
				->oneOf( $view->value( $params, 'f_optid', [] ) )
				->oneOf( $view->value( $params, 'f_oneid', [] ) )
				->text( $view->value( $params, 'f_search' ) )
				->category( $catids, 'default', $level )
				->slice( $start, $size )
				->uses( ['text'] )
				->search();

			if( ( $count = count( $products ) ) > 1 )
			{
				if( $pos > 0 && ( $product = $products->first() ) !== null )
				{
					$param = ['d_name' => $product->getName( 'url' ), 'd_prodid' => $product->getId(), 'd_pos' => $pos - 1];
					$view->navigationPrev = $view->link( 'client/html/catalog/detail/url', $param );
				}

				if( ( $pos === 0 || $count === 3 ) && ( $product = $products->last() ) !== null )
				{
					$param = ['d_name' => $product->getName( 'url' ), 'd_prodid' => $product->getId(), 'd_pos' => $pos + 1];
					$view->navigationNext = $view->link( 'client/html/catalog/detail/url', $param );
				}
			}

			/** client/html/catalog/detail/template-navigator
			 * Relative path to the HTML template of the catalog detail navigator partial.
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
			 * @param string Relative path to the template creating the HTML fragment
			 * @since 2022.10
			 */
			$template = $context->config()->get( 'client/html/catalog/detail/template-navigator', 'catalog/detail/navigator' );

			return $view->render( $template );
		}

		return '';
	}


	/**
	 * Returns the product item
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @return \Aimeos\MShop\Product\Item\Iface Product item
	 */
	protected function product( \Aimeos\Base\View\Iface $view ) : \Aimeos\MShop\Product\Item\Iface
	{
		$context = $this->context();
		$config = $context->config();

		/** client/html/catalog/detail/prodid-default
		 * The default product ID used if none is given as parameter
		 *
		 * To display a product detail view or a part of it for a specific
		 * product, you can configure its ID using this setting. This is
		 * most useful in a CMS where the product ID can be configured
		 * separately for each content node.
		 *
		 * @param string Product ID
		 * @since 2016.01
		 * @see client/html/catalog/detail/prodid-default
		 * @see client/html/catalog/lists/catid-default
		 */
		$id = $view->param( 'd_prodid', $config->get( 'client/html/catalog/detail/prodid-default' ) );

		/** client/html/catalog/detail/prodcode-default
		 * The default product code used if none is given as parameter
		 *
		 * To display a product detail view or a part of it for a specific
		 * product, you can configure its code using this setting. This is
		 * most useful in a CMS where the product code can be configured
		 * separately for each content node.
		 *
		 * @param string Product code
		 * @since 2019.10
		 * @see client/html/catalog/detail/prodid-default
		 * @see client/html/catalog/lists/catid-default
		 */
		$code = $config->get( 'client/html/catalog/detail/prodcode-default' );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )->uses( $this->domains() );
		return ( $id ? $cntl->get( $id ) : ( $code ? $cntl->find( $code ) : $cntl->resolve( $view->param( 'd_name' ) ) ) );
	}


	/**
	 * Adds the product to the list of last seen products.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item
	 */
	protected function seen( \Aimeos\MShop\Product\Item\Iface $product )
	{
		$id = $product->getId();
		$context = $this->context();
		$session = $context->session();
		$lastSeen = map( $session->get( 'aimeos/catalog/session/seen/list', [] ) );

		if( !$lastSeen->has( $id ) )
		{
			$config = $context->config();

			/** client/html/catalog/detail/partials/seen
			 * Relative path to the HTML template of the catalog detail seen partial.
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
			 * @param string Relative path to the template creating the HTML fragment
			 * @since 2014.03
			 */
			$template = $config->get( 'client/html/catalog/detail/partials/seen', 'catalog/detail/seen' );

			/** client/html/catalog/session/seen/maxitems
			 * Maximum number of products displayed in the "last seen" section
			 *
			 * This option limits the number of products that are shown in the
			 * "last seen" section after the user visited their detail pages. It
			 * must be a positive integer value greater than 0.
			 *
			 * @param integer Number of products
			 * @since 2014.03
			 */
			$max = $config->get( 'client/html/catalog/session/seen/maxitems', 6 );

			$html = $this->view()->set( 'product', $product )->render( $template );
			$lastSeen = $lastSeen->put( $id, $html )->slice( -$max );
		}

		$session->set( 'aimeos/catalog/session/seen/list', $lastSeen->put( $id, $lastSeen->pull( $id ) )->all() );
	}


	/**
	 * Returns the URL for fetching stock levels
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item
	 * @return \Aimeos\Map List of stock URLs
	 */
	protected function stockUrl( \Aimeos\MShop\Product\Item\Iface $productItem ) : \Aimeos\Map
	{
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
		 * @see client/html/catalog/lists/stock/enable
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/config
		 */
		if( !$this->context()->config()->get( 'client/html/catalog/detail/stock/enable', true ) ) {
			return map();
		}

		$products = $productItem->getRefItems( 'product', null, 'default' )->push( $productItem );

		return $this->getStockUrl( $this->view(), $products );
	}


	/** client/html/catalog/detail/decorators/excludes
	 * Excludes decorators added by the "common" option from the catalog detail html client
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
	 *  client/html/catalog/detail/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/detail/decorators/global
	 * @see client/html/catalog/detail/decorators/local
	 */

	/** client/html/catalog/detail/decorators/global
	 * Adds a list of globally available decorators only to the catalog detail html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/detail/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/detail/decorators/excludes
	 * @see client/html/catalog/detail/decorators/local
	 */

	/** client/html/catalog/detail/decorators/local
	 * Adds a list of local decorators only to the catalog detail html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/detail/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/detail/decorators/excludes
	 * @see client/html/catalog/detail/decorators/global
	 */
}
