<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Stock;


/**
 * Default implementation of catalog stock HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/stock/name
	 * Class name of the used catalog stock client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Stock\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Stock\Mystock
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/stock/name = Mystock
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyStock"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */


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
		$stockItemsByProducts = [];
		$context = $this->context();
		$prodIds = (array) $view->param( 'st_pid', [] );

		/** client/html/catalog/stock/sort
		 * Sortation key if stock levels for different types exist
		 *
		 * Products can be shipped from several warehouses with a different
		 * stock level for each one. The stock levels for each warehouse will
		 * be shown in the product detail page. To get a consistent sortation
		 * of this list, the configured key will be used by the stock manager.
		 *
		 * Possible keys for sorting are ("-stock.type" for descending order):
		 *
		 * * stock.productid
		 * * stock.stocklevel
		 * * stock.type
		 * * stock.dateback
		 *
		 * @param array List of key/value pairs for sorting
		 * @since 2017.01
		 * @see client/html/catalog/stock/level/low
		 */
		$sort = $context->config()->get( 'client/html/catalog/stock/sort', 'stock.type' );
		$type = $context->locale()->getSiteItem()->getConfigValue( 'stocktype' );

		$view->stockProductIds = $prodIds;
		$view->stockItemsByProducts = \Aimeos\Controller\Frontend::create( $context, 'stock' )
			->product( $prodIds )->type( $type )->sort( $sort )
			->search()
			->groupBy( 'stock.productid' );

		return parent::data( $view, $tags, $expire );
	}


	/** client/html/catalog/stock/template-body
	 * Relative path to the HTML body template of the catalog stock client.
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
	 * @see client/html/catalog/stock/template-header
	 */

	/** client/html/catalog/stock/template-header
	 * Relative path to the HTML header template of the catalog stock client.
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
	 * @see client/html/catalog/stock/template-body
	 */
}
