<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
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

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' );
		$basket = \Aimeos\Controller\Frontend::create( $context, 'basket' )->get();

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
		$size = $config->get( 'client/html/basket/related/bought/limit', 6 );

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
		if( $view->config( 'client/html/basket/related/basket-add', false ) ) {
			$domains = array_merge_recursive( $domains, ['product' => ['default'], 'attribute' => ['variant', 'custom', 'config']] );
		}

		$prodIds = $basket->getProducts()
			->concat( $basket->getProducts()->getProducts() )
			->col( 'order.base.product.parentproductid' )
			->unique()->all();

		$view->boughtItems = $cntl->uses( $domains )->product( $prodIds )->search()
			->getListItems( 'product', 'bought-together' )
			->flat( 1 )
			->usort( function( $a, $b ) {
				return $a->getPosition() <=> $b->getPosition();
			} )
			->getRefItem()
			->filter()
			->slice( 0, $size )
			->col( null, 'product.id' );

		return parent::data( $view, $tags, $expire );
	}


	/** client/html/basket/related/template-body
	 * Relative path to the HTML body template of the basket related client.
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
	 * @see client/html/basket/related/template-header
	 */

	/** client/html/basket/related/template-header
	 * Relative path to the HTML header template of the basket related client.
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
	 * @see client/html/basket/related/template-body
	 */
}
