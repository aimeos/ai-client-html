<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Basket\Related;


/**
 * Default implementation of related basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Basket\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/basket/related/name
	 * Class name of the used basket related client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Basket\Related\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Basket\Related\Mybasket
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/basket/related/name = Mybasket
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyBasket"!
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
		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'product' );

		$view->boughtItems = $cntl->uses( $this->domains() )
			->product( $this->productIds()->all() )
			->search()
			->getListItems( 'product', 'bought-together' )
			->flat( 1 )
			->usort( function( $a, $b ) {
				return $a->getPosition() <=> $b->getPosition();
			} )
			->getRefItem()
			->filter()
			->slice( 0, $this->size() )
			->col( null, 'product.id' );

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

		/** client/html/basket/related/bought/domains
		 * The list of domain names whose items should be available in the template for the products
		 *
		 * The templates rendering product details usually add the images,
		 * prices and texts, etc. associated to the product
		 * item. If you want to display additional or less content, you can
		 * configure your own list of domains (attribute, media, price, product,
		 * text, etc. are domains) whose items are fetched from the storage.
		 * Please keep in mind that the more domains you add to the configuration,
		 * the more time is required for fetching the content!
		 *
		 * @param array List of domain names
		 * @since 2014.09
		 */
		$domains = ['catalog', 'media', 'media/property', 'price', 'supplier', 'text'];
		$domains = $config->get( 'client/html/basket/related/bought/domains', $domains );
		$domains['product'] = ['bought-together'];

		/** client/html/basket/related/basket-add
		 * Display the "add to basket" button for each product item
		 *
		 * Enables the button for adding products to the basket for the related products
		 * in the basket. This works for all type of products, even for selection products
		 * with product variants and product bundles. By default, also optional attributes
		 * are displayed if they have been associated to a product.
		 *
		 * @param boolean True to display the button, false to hide it
		 * @since 2020.10
		 * @see client/html/catalog/home/basket-add
		 * @see client/html/catalog/lists/basket-add
		 * @see client/html/catalog/detail/basket-add
		 * @see client/html/catalog/product/basket-add
		 */
		if( $config->get( 'client/html/basket/related/basket-add', false ) ) {
			$domains = array_merge_recursive( $domains, ['product' => ['default'], 'attribute' => ['variant', 'custom', 'config']] );
		}

		return $domains;
	}


	/**
	 * Returns the IDs of the products in the basket
	 *
	 * @return \Aimeos\Map List of product IDs
	 */
	protected function productIds() : \Aimeos\Map
	{
		$basket = \Aimeos\Controller\Frontend::create( $this->context(), 'basket' )->get();

		return $basket->getProducts()
			->concat( $basket->getProducts()->getProducts() )
			->col( 'order.product.parentproductid' )
			->unique();
	}


	/**
	 * Returns the number of products shown in the list
	 *
	 * @return int Number of products
	 */
	protected function size() : int
	{
		/** client/html/basket/related/bought/limit
		 * Number of items in the list of bought together products
		 *
		 * This option limits the number of suggested products in the
		 * list of bought together products. The suggested items are
		 * calculated using the products that are in the current basket
		 * of the customer.
		 *
		 * Note: You need to start the job controller for calculating
		 * the bought together products regularly to get up to date
		 * product suggestions.
		 *
		 * @param integer Number of products
		 * @since 2014.09
		 */
		return $this->context()->config()->get( 'client/html/basket/related/bought/limit', 6 );
	}


	/** client/html/basket/related/template-body
	 * Relative path to the HTML body template of the basket related client.
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
	 * @see client/html/basket/related/template-header
	 */

	/** client/html/basket/related/template-header
	 * Relative path to the HTML header template of the basket related client.
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
	 * @see client/html/basket/related/template-body
	 */

	/** client/html/basket/related/decorators/excludes
	 * Excludes decorators added by the "common" option from the basket related html client
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
	 *  client/html/basket/related/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/related/decorators/global
	 * @see client/html/basket/related/decorators/local
	 */

	/** client/html/basket/related/decorators/global
	 * Adds a list of globally available decorators only to the basket related html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/basket/related/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/related/decorators/excludes
	 * @see client/html/basket/related/decorators/local
	 */

	/** client/html/basket/related/decorators/local
	 * Adds a list of local decorators only to the basket related html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Basket\Decorator\*") around the html client.
	 *
	 *  client/html/basket/related/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Basket\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/related/decorators/excludes
	 * @see client/html/basket/related/decorators/global
	 */
}
