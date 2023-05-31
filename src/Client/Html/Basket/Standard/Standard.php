<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Basket\Standard;


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Basket\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/basket/standard/name
	 * Class name of the used basket standard client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Basket\Standard\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Basket\Standard\Mybasket
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/basket/standard/name = Mybasket
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
		$context = $this->context();
		$site = $context->locale()->getSiteItem()->getCode();

		$view->standardBackUrl = $context->session()->get( 'aimeos/catalog/last/' . $site );
		$view->standardBasket = \Aimeos\Controller\Frontend::create( $this->context(), 'basket' )->get();

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();
		$controller = \Aimeos\Controller\Frontend::create( $context, 'basket' );

		try
		{
			switch( $view->param( 'b_action' ) )
			{
				case 'add':
					$this->addProducts( $view );
					break;
				case 'coupon-delete':
					$this->deleteCoupon( $view );
					break;
				case 'delete':
					$this->deleteProducts( $view );
					break;
				case 'save':
					$this->saveBasket( $view );
					break;
				default:
					$this->updateProducts( $view );
					$this->addCoupon( $view );
			}

			/** client/html/basket/standard/check
			 * Alters the behavior of the product checks before continuing with the checkout
			 *
			 * By default, the product related checks are performed every time the basket
			 * is shown. They test if there are any products in the basket and execute all
			 * basket plugins that have been registered for the "check.before" and "check.after"
			 * events.
			 *
			 * Using this configuration setting, you can either disable all checks completely
			 * (0) or display a "Check" button instead of the "Checkout" button (2). In the
			 * later case, customers have to click on the "Check" button first to perform
			 * the checks and if everything is OK, the "Checkout" button will be displayed
			 * that allows the customers to continue the checkout process. If one of the
			 * checks fails, the customers have to fix the related basket item and must click
			 * on the "Check" button again before they can continue.
			 *
			 * Available values are:
			 *  0 = no product related checks
			 *  1 = checks are performed every time when the basket is displayed
			 *  2 = checks are performed only when clicking on the "check" button
			 *
			 * @param integer One of the allowed values (0, 1 or 2)
			 * @since 2016.08
			 */
			$check = (int) $view->config( 'client/html/basket/standard/check', 1 );

			switch( $check )
			{
				case 2: if( $view->param( 'b_check', 0 ) == 0 ) { break; }
				case 1: $controller->get()->check( ['order/product'] );
				default: $view->standardCheckout = true;
			}
		}
		catch( \Exception $e )
		{
			$controller->save();
			throw $e;
		}

		$controller->save();
	}


	/**
	 * Adds the coupon specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function addCoupon( \Aimeos\Base\View\Iface $view )
	{
		if( ( $coupon = $view->param( 'b_coupon' ) ) != '' )
		{
			$context = $this->context();
			$cntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );
			$code = $cntl->get()->getCoupons()->keys()->first();

			/** client/html/basket/standard/coupon/overwrite
			 * Replace previous coupon codes each time the user enters a new one
			 *
			 * If you want to allow only one coupon code per order and replace a
			 * previously entered one automatically, this configuration option
			 * should be set to true.
			 *
			 * @param boolean True to overwrite a previous coupon, false to keep them
			 * @since 2020.04
			 */
			if( $code && $context->config()->get( 'client/html/basket/standard/coupon/overwrite', false ) ) {
				$cntl->deleteCoupon( $code );
			}

			$cntl->addCoupon( $coupon );
			$this->clearCached();
		}
	}


	/**
	 * Adds the products specified by the view parameters to the basket.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function addProducts( \Aimeos\Base\View\Iface $view )
	{
		$context = $this->context();
		$domains = ['attribute', 'catalog', 'media', 'price', 'product', 'text', 'locale/site'];

		$basketCntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );
		$productCntl = \Aimeos\Controller\Frontend::create( $context, 'product' )->uses( $domains );

		if( ( $prodid = $view->param( 'b_prodid', '' ) ) !== '' && $view->param( 'b_quantity', 0 ) > 0 )
		{
			$basketCntl->addProduct(
				$productCntl->get( $prodid ),
				(float) $view->param( 'b_quantity', 0 ),
				(array) $view->param( 'b_attrvarid', [] ),
				$this->getAttributeMap( $view->param( 'b_attrconfid', [] ) ),
				array_filter( (array) $view->param( 'b_attrcustid', [] ) ),
				(string) $view->param( 'b_stocktype', 'default' ),
				$view->param( 'b_siteid' )
			);
		}
		else
		{
			foreach( (array) $view->param( 'b_prod', [] ) as $values )
			{
				if( ( $values['prodid'] ?? null ) && ( $values['quantity'] ?? 0 ) > 0 )
				{
					$basketCntl->addProduct( $productCntl->get( $values['prodid'] ),
						(float) ( $values['quantity'] ?? 0 ),
						array_filter( (array) ( $values['attrvarid'] ?? [] ) ),
						$this->getAttributeMap( (array) ( $values['attrconfid'] ?? [] ) ),
						array_filter( (array) ( $values['attrcustid'] ?? [] ) ),
						(string) ( $values['stocktype'] ?? 'default' ),
						$values['siteid'] ?? null
					);
				}
			}
		}

		$this->clearCached();
	}


	/**
	 * Removes the coupon specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function deleteCoupon( \Aimeos\Base\View\Iface $view )
	{
		if( ( $coupon = $view->param( 'b_coupon' ) ) != '' )
		{
			\Aimeos\Controller\Frontend::create( $this->context(), 'basket' )->deleteCoupon( $coupon );
			$this->clearCached();
		}
	}


	/**
	 * Removes the products specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function deleteProducts( \Aimeos\Base\View\Iface $view )
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context(), 'basket' );
		$products = (array) $view->param( 'b_position', [] );

		foreach( $products as $position ) {
			$controller->deleteProduct( $position );
		}

		$this->clearCached();
	}


	/**
	 * Returns the configurable attribute values as ID/quantity pairs
	 *
	 * @param array $values Associative list which "id" and "qty" keys
	 * @return array Pairs of config attribute ID/quantity pairs
	 */
	protected function getAttributeMap( array $values )
	{
		$list = [];
		$confIds = ( isset( $values['id'] ) ? array_filter( (array) $values['id'] ) : [] );
		$confQty = ( isset( $values['qty'] ) ? array_filter( (array) $values['qty'] ) : [] );

		foreach( $confIds as $idx => $id )
		{
			if( isset( $confQty[$idx] ) && $confQty[$idx] > 0 ) {
				$list[$id] = $confQty[$idx];
			}
		}

		return $list;
	}


	/**
	 * Saves the basket of the user permanently
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function saveBasket( \Aimeos\Base\View\Iface $view )
	{
		$context = $this->context();

		if( ( $userId = $context->user() ) === null )
		{
			$msg = $view->translate( 'client', 'You must log in first' );
			$view->errors = array_merge( $view->get( 'errors', [] ), [$msg] );

			return;
		}

		$manager = \Aimeos\MShop::create( $context, 'order/basket' );

		$item = $manager->create()->setId( md5( microtime( true ) . getmypid() . rand() ) )
			->setCustomerId( $userId )->setName( $view->param( 'b_name', date( 'Y-m-d H:i:s' ) ) )
			->setItem( \Aimeos\Controller\Frontend::create( $context, 'basket' )->get() );

		$manager->save( $item );

		$msg = $view->translate( 'client', 'Basket saved sucessfully' );
		$view->infos = array_merge( $view->get( 'infos', [] ), [$msg] );
	}


	/**
	 * Edits the products specified by the view parameters to the basket.
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function updateProducts( \Aimeos\Base\View\Iface $view )
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context(), 'basket' );
		$products = (array) $view->param( 'b_prod', [] );

		if( ( $position = $view->param( 'b_position', '' ) ) !== '' )
		{
			$products[] = array(
				'position' => $position,
				'quantity' => $view->param( 'b_quantity', 1 )
			);
		}

		foreach( $products as $values )
		{
			$controller->updateProduct(
				( isset( $values['position'] ) ? (int) $values['position'] : 0 ),
				( isset( $values['quantity'] ) ? (float) $values['quantity'] : 1 )
			);
		}

		$this->clearCached();
	}

	/** client/html/basket/standard/template-body
	 * Relative path to the HTML body template of the basket standard client.
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
	 * @see client/html/basket/standard/template-header
	 */

	/** client/html/basket/standard/template-header
	 * Relative path to the HTML header template of the basket standard client.
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
	 * @see client/html/basket/standard/template-body
	 */

	/** client/html/basket/standard/decorators/excludes
	 * Excludes decorators added by the "common" option from the basket standard html client
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
	 *  client/html/basket/standard/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/standard/decorators/global
	 * @see client/html/basket/standard/decorators/local
	 */

	/** client/html/basket/standard/decorators/global
	 * Adds a list of globally available decorators only to the basket standard html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/basket/standard/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/standard/decorators/excludes
	 * @see client/html/basket/standard/decorators/local
	 */

	/** client/html/basket/standard/decorators/local
	 * Adds a list of local decorators only to the basket standard html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Basket\Decorator\*") around the html client.
	 *
	 *  client/html/basket/standard/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Basket\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2014.05
	 * @see client/html/common/decorators/default
	 * @see client/html/basket/standard/decorators/excludes
	 * @see client/html/basket/standard/decorators/global
	 */
}
