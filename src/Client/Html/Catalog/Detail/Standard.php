<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
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
	private $tags = [];
	private $expire;
	private $view;


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
		 * to the templates directory (usually in client/html/templates).
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
		 * in client/html/templates).
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
		$context = $this->context();
		$session = $context->session();

		$site = $context->locale()->getSiteItem()->getCode();
		$params = $this->getClientParams( $this->view()->param() );

		$session->set( 'aimeos/catalog/detail/params/last/' . $site, $params );
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
		$domains = [
			'attribute', 'attribute/property', 'catalog', 'media', 'media/property', 'price',
			'product', 'product/property', 'supplier', 'supplier/address', 'text'
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

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )->uses( $domains );
		$productItem = ( $id ? $cntl->get( $id ) : ( $code ? $cntl->find( $code ) : $cntl->resolve( $view->param( 'd_name' ) ) ) );

		$propItems = $productItem->getPropertyItems();
		$supItems = $productItem->getRefItems( 'supplier', null, 'default' );
		$attrItems = $productItem->getRefItems( 'attribute', null, 'default' );
		$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );

		$this->addMetaItems( $productItem, $expire, $tags );
		$this->addMetaItems( $supItems, $expire, $tags );

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

		if( (bool) $view->config( 'client/html/catalog/detail/stock/enable', true ) === true )
		{
			$products = $productItem->getRefItems( 'product', null, 'default' )->push( $productItem );
			$view->detailStockUrl = $this->getStockUrl( $view, $products );
		}


		$view->detailMediaItems = $mediaItems;
		$view->detailProductItem = $productItem;
		$view->detailAttributeMap = $attrItems->groupBy( 'attribute.type' )->ksort();
		$view->detailPropertyMap = $propItems->groupBy( 'product.property.type' )->ksort();

		$this->call( 'seen', $productItem );

		return parent::data( $view, $tags, $expire );
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
					$param = ['d_name' => $product->getName( 'url ' ), 'd_prodid' => $product->getId(), 'd_pos' => $pos - 1];
					$view->navigationPrev = $view->link( 'client/html/catalog/detail/url', $param );
				}

				if( ( $pos === 0 || $count === 3 ) && ( $product = $products->last() ) !== null )
				{
					$param = ['d_name' => $product->getName( 'url ' ), 'd_prodid' => $product->getId(), 'd_pos' => $pos + 1];
					$view->navigationNext = $view->link( 'client/html/catalog/detail/url', $param );
				}
			}

			$config = $context->config();
			$template = $config->get( 'client/html/catalog/detail/template-navigator', 'catalog/detail/navigator' );

			return $view->render( $template );
		}

		return '';
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
			 * Relative path to the HTML body template of the catalog detail seen client.
			 *
			 * The template file contains the HTML code and processing instructions
			 * to generate the result shown in the body of the frontend. The
			 * configuration string is the path to the template file relative
			 * to the templates directory (usually in client/html/templates).
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
			$lastSeen->put( $id, $html )->slice( -$max );
		}

		$session->set( 'aimeos/catalog/session/seen/list', $lastSeen->put( $id, $lastSeen->pull( $id ) )->all() );
	}
}
