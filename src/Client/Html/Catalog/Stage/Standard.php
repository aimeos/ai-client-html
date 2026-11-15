<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Stage;


/**
 * Default implementation of catalog stage section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/stage/name
	 * Class name of the used catalog stage client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Stage\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Stage\Mystage
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/stage/name = Mystage
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyStage"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */


	private array $tags = [];
	private ?string $expire = null;
	private bool $uncachable = false;
	private ?\Aimeos\Base\View\Iface $view = null;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$prefixes = $this->prefixes( $this->view() );
		$config = $this->context()->config();

		/** client/html/catalog/stage/cache
		 * Enables or disables caching only for the catalog stage component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @see client/html/catalog/detail/cache
		 * @see client/html/catalog/filter/cache
		 * @see client/html/catalog/lists/cache
		 */

		/** client/html/catalog/stage
		 * All parameters defined for the catalog stage component and its subparts
		 *
		 * This returns all settings related to the stage component.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 */
		$confkey = 'client/html/catalog/stage';

		if( $html = $this->cached( 'body', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/stage/template-body
		 * Relative path to the HTML body template of the catalog stage client.
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
		 * @see client/html/catalog/stage/template-header
		 */
		$template = $config->get( 'client/html/catalog/stage/template-body', 'catalog/stage/body' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->uncachable ? $html : $this->cache( 'body', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$prefixes = $this->prefixes( $this->view() );
		$config = $this->context()->config();
		$confkey = 'client/html/catalog/stage';

		if( $html = $this->cached( 'header', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/stage/template-header
		 * Relative path to the HTML header template of the catalog stage client.
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
		 * @see client/html/catalog/stage/template-body
		 */
		$template = $config->get( 'client/html/catalog/stage/template-header', 'catalog/stage/header' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->uncachable ? $html : $this->cache( 'header', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$context = $this->context();
		$config = $context->config();
		// @phpstan-ignore-next-line
		$params = map( $this->getClientParams( $view->param(), ['f_', 'l_type'] ) );

		/** client/html/catalog/stage/listtype
		 * List type of the product category used for the breadcrumb on the product detail page
		 *
		 * If no category ID is passed in the URL, the breadcrumb on the product
		 * detail page is built from the first category (by position) the product
		 * is assigned to using this list type. Use a dedicated list type like
		 * "breadcrumb" to define the canonical category of a product independently
		 * from the other category assignments.
		 *
		 * @param string List type code of the product/catalog relation
		 * @since 2026.10
		 * @see client/html/catalog/lists/catid-default
		 */
		$listtype = $config->get( 'client/html/catalog/stage/listtype', 'default' );
		$catid = $params->get( 'f_catid' );
		$product = $catid ? null : $this->product( $view, $listtype );

		if( $product ) {
			// @phpstan-ignore-next-line
			$this->addMetaItems( $product, $expire, $tags );
		}

		// don't cache output for unknown products or non-canonical parameters to prevent flooding the cache
		$this->uncachable = $product
			? !$this->canonical( $product, $view, $this->prefixes( $view ) )
			: !$catid && ( $view->param( 'd_prodid' ) || $view->param( 'd_name' ) );

		$prodCatId = $product?->getRefItems( 'catalog', null, $listtype )->first()?->getId();
		$catid = $catid ?: $prodCatId ?: $config->get( 'client/html/catalog/lists/catid-default' );

		if( $catid )
		{
			$controller = \Aimeos\Controller\Frontend::create( $context, 'catalog' );

			/** client/html/catalog/domains
			 * A list of domain names whose items should be available in the catalog view templates
			 *
			 * @see client/html/catalog/stage/domains
			 */
			$domains = ['attribute', 'media', 'media/property', 'text'];
			$domains = $config->get( 'client/html/catalog/domains', $domains );

			/** client/html/catalog/stage/domains
			 * A list of domain names whose items should be available in the catalog stage view template
			 *
			 * The templates rendering the catalog stage section use the texts and
			 * maybe images and attributes associated to the categories. You can
			 * configure your own list of domains (attribute, media, price, product,
			 * text, etc. are domains) whose items are fetched from the storage.
			 * Please keep in mind that the more domains you add to the configuration,
			 * the more time is required for fetching the content!
			 *
			 * This configuration option overwrites the "client/html/catalog/domains"
			 * option that allows to configure the domain names of the items fetched
			 * for all catalog related data.
			 *
			 * @param array List of domain names
			 * @since 2014.03
			 * @see client/html/catalog/domains
			 * @see client/html/catalog/detail/domains
			 * @see client/html/catalog/lists/domains
			 */
			$domains = $config->get( 'client/html/catalog/stage/domains', $domains );

			try {
				$stageCatPath = $controller->uses( $domains )->getPath( $catid );
			} catch( \Aimeos\Controller\Frontend\Exception $e ) {
				// categories derived from the product may have unavailable parents
				$stageCatPath = $prodCatId ? map() : throw $e;
			}

			if( $view->param( 'f_catid' ) ) {
				// IDs are cast to integers so "1x" or "01" would create additional cache entries
				$this->uncachable = $view->param( 'f_catid' ) !== (string) $stageCatPath->last()?->getId();
			}

			$mediaItems = $stageCatPath->getRefItems( 'media', 'stage', 'default' )->find( function( \Aimeos\Map $entry ) {
				return !$entry->isEmpty();
			}, true );

			// @phpstan-ignore-next-line
			$this->addMetaItems( $stageCatPath, $expire, $tags );

			$view->stageCurrentCatItem = $stageCatPath->last();
			$view->stageMediaItems = $mediaItems;
			$view->stageCatPath = $stageCatPath;
			$view->stageCatId = $catid;
		}

		$view->stageParams = $params->all();

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the names of the parameters the output depends on
	 *
	 * @param \Aimeos\Base\View\Iface $view View object with the request parameters
	 * @return string[] List of parameter names used for the cache key
	 */
	protected function prefixes( \Aimeos\Base\View\Iface $view ) : array
	{
		// only the parameter used for fetching the category or product is relevant
		return $view->param( 'f_catid' ) ? ['f_catid'] : ( $view->param( 'd_prodid' ) ? ['d_prodid'] : ['d_name'] );
	}


	/**
	 * Returns the product shown on the detail page including its categories of the given list type
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param string $listtype List type of the product/catalog relation
	 * @return \Aimeos\MShop\Product\Item\Iface|null Product item or null if no or no available product is shown
	 */
	protected function product( \Aimeos\Base\View\Iface $view, string $listtype ) : ?\Aimeos\MShop\Product\Item\Iface
	{
		// ignore manipulated parameters like "d_prodid[]=1" which would cause type errors
		$id = is_string( $id = $view->param( 'd_prodid' ) ) ? $id : null;
		$name = is_string( $name = $view->param( 'd_name' ) ) ? $name : null;

		if( !$id && !$name ) {
			return null;
		}

		$controller = \Aimeos\Controller\Frontend::create( $this->context(), 'product' )->uses( ['catalog' => [$listtype]] );

		try {
			return $id ? $controller->get( $id ) : $controller->resolve( $name );
		} catch( \Aimeos\Controller\Frontend\Exception | \Aimeos\MShop\Exception $e ) {
			return null;
		}
	}


	/** client/html/catalog/stage/decorators/excludes
	 * Excludes decorators added by the "common" option from the catalog stage html client
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
	 *  client/html/catalog/stage/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/global
	 * @see client/html/catalog/stage/decorators/local
	 */

	/** client/html/catalog/stage/decorators/global
	 * Adds a list of globally available decorators only to the catalog stage html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/stage/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/excludes
	 * @see client/html/catalog/stage/decorators/local
	 */

	/** client/html/catalog/stage/decorators/local
	 * Adds a list of local decorators only to the catalog stage html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/stage/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/excludes
	 * @see client/html/catalog/stage/decorators/global
	 */
}
