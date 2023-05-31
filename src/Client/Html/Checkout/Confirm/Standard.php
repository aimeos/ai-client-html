<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Confirm;


/**
 * Default implementation of confirm checkout HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/checkout/confirm/name
	 * Class name of the used checkout confirm client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Checkout\Confirm\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Checkout\Confirm\Myconfirm
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/checkout/confirm/name = Myconfirm
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyConfirm"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */


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

		$config = $context->config();
		$session = $context->session();

		if( ( $orderid = $session->get( 'aimeos/orderid' ) ) === null ) {
			throw new \Aimeos\Client\Html\Exception( 'No order ID available' );
		}

		$ref = $config->get( 'mshop/order/manager/subdomains', [] );
		$ref = $config->get( 'client/html/checkout/confirm/domains', $ref );
		$orderCntl = \Aimeos\Controller\Frontend::create( $context, 'order' )->uses( $ref );

		if( ( $code = $view->param( 'code' ) ) !== null )
		{
			$serviceCntl = \Aimeos\Controller\Frontend::create( $context, 'service' );
			$orderItem = $serviceCntl->updateSync( $view->request(), $code, $orderid );
		}
		else
		{
			$orderItem = $orderCntl->get( $orderid, false );
		}

		// update stock, coupons, etc.
		\Aimeos\Controller\Common\Order\Factory::create( $context )->update( $orderItem );

		parent::init();

		if( $orderItem->getStatusPayment() > \Aimeos\MShop\Order\Item\Base::PAY_REFUSED )
		{
			\Aimeos\Controller\Frontend::create( $context, 'basket' )->clear();
			$session->remove( array_keys( $session->get( 'aimeos/basket/cache', [] ) ) );
		}

		$orderCntl->save( $orderItem );
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

		if( ( $id = $context->session()->get( 'aimeos/orderid' ) ) === null )
		{
			$context->logger()->log( 'Lost session at confirmation page' . PHP_EOL . print_r( $_SERVER, true ) );
			throw new \Aimeos\Client\Html\Exception( $context->translate( 'client', 'No order ID available in session' ) );
		}

		/** client/html/checkout/confirm/domains
		 * List of domains to fetch items related to the order
		 *
		 * To adapt the order data loaded for displaying at the checkout confirmation
		 * page, add or remove the names of the domains using this setting. By default,
		 * all order sub-domains are included (order/address, order/coupon, order/product
		 * and order/service) and you can remove unused domains or add additional ones
		 * like "product" to get the original product items for the bought order products.
		 * You can also add domains related to e.g. products like "catalog" for the
		 * categories the products are assigned to.
		 *
		 * @param array List of domain names
		 * @since 2023.07
		 */
		$ref = $config->get( 'mshop/order/manager/subdomains', [] );
		$ref = $config->get( 'client/html/checkout/confirm/domains', $ref );
		$order = \Aimeos\Controller\Frontend::create( $context, 'order' )->uses( $ref )->get( $id, false );

		$view->confirmOrderItem = $order;
		$view->summaryBasket = $order;

		return parent::data( $view, $tags, $expire );
	}


	/** client/html/checkout/confirm/template-body
	 * Relative path to the HTML body template of the checkout confirm client.
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
	 * @see client/html/checkout/confirm/template-header
	 */

	/** client/html/checkout/confirm/template-header
	 * Relative path to the HTML header template of the checkout confirm client.
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
	 * @see client/html/checkout/confirm/template-body
	 */

	/** client/html/checkout/confirm/decorators/excludes
	 * Excludes decorators added by the "common" option from the checkout confirm html client
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
	 *  client/html/checkout/confirm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/checkout/confirm/decorators/global
	 * @see client/html/checkout/confirm/decorators/local
	 */

	/** client/html/checkout/confirm/decorators/global
	 * Adds a list of globally available decorators only to the checkout confirm html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/checkout/confirm/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/checkout/confirm/decorators/excludes
	 * @see client/html/checkout/confirm/decorators/local
	 */

	/** client/html/checkout/confirm/decorators/local
	 * Adds a list of local decorators only to the checkout confirm html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Checkout\Decorator\*") around the html client.
	 *
	 *  client/html/checkout/confirm/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Checkout\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/checkout/confirm/decorators/excludes
	 * @see client/html/checkout/confirm/decorators/global
	 */
}
