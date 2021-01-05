<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog;


/**
 * Common methods for the catalog HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
{
	/**
	 * Returns the URL for retrieving the stock levels
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with helper
	 * @param \Aimeos\MShop\Product\Item\Iface[] List of products with their IDs as keys
	 * @return \Aimeos\Map URLs to retrieve the stock levels for the given products
	 */
	protected function getStockUrl( \Aimeos\MW\View\Iface $view, \Aimeos\Map $products ) : \Aimeos\Map
	{
		/** client/html/catalog/stock/url/target
		 * Destination of the URL where the controller specified in the URL is known
		 *
		 * The destination can be a page ID like in a content management system or the
		 * module of a software development framework. This "target" must contain or know
		 * the controller that should be called by the generated URL.
		 *
		 * @param string Destination of the URL
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/config
		 * @see client/html/catalog/stock/url/max-items
		 */
		$target = $view->config( 'client/html/catalog/stock/url/target' );

		/** client/html/catalog/stock/url/controller
		 * Name of the controller whose action should be called
		 *
		 * In Model-View-Controller (MVC) applications, the controller contains the methods
		 * that create parts of the output displayed in the generated HTML page. Controller
		 * names are usually alpha-numeric.
		 *
		 * @param string Name of the controller
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/config
		 * @see client/html/catalog/stock/url/max-items
		 */
		$cntl = $view->config( 'client/html/catalog/stock/url/controller', 'catalog' );

		/** client/html/catalog/stock/url/action
		 * Name of the action that should create the output
		 *
		 * In Model-View-Controller (MVC) applications, actions are the methods of a
		 * controller that create parts of the output displayed in the generated HTML page.
		 * Action names are usually alpha-numeric.
		 *
		 * @param string Name of the action
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/config
		 * @see client/html/catalog/stock/url/max-items
		 */
		$action = $view->config( 'client/html/catalog/stock/url/action', 'stock' );

		/** client/html/catalog/stock/url/config
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
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/max-items
		 */
		$config = $view->config( 'client/html/catalog/stock/url/config', [] );

		/** client/html/catalog/stock/url/max-items
		 * Maximum number of product stock levels per request
		 *
		 * To avoid URLs that exceed the maximum amount of characters (usually 8190),
		 * each request contains only up to the configured amount of product codes.
		 * If more product codes are available, several requests will be sent to the
		 * server.
		 *
		 * @param integer Maximum number of product codes per request
		 * @since 2018.10
		 * @category Developer
		 * @see client/html/catalog/stock/url/target
		 * @see client/html/catalog/stock/url/controller
		 * @see client/html/catalog/stock/url/action
		 * @see client/html/catalog/stock/url/config
		 */
		$max = $view->config( 'client/html/catalog/stock/url/max-items', 100 );


		$urls = [];
		$ids = $products->getId()->sort();

		while( !( $list = $ids->splice( -$max ) )->isEmpty() ) {
			$urls[] = $view->url( $target, $cntl, $action, ['st_pid' => $list->toArray()], [], $config );
		}

		return map( $urls )->reverse();
	}
}
