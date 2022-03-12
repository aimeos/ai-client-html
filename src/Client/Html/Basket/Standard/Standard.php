<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
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

		if( !empty( $params = $context->session()->get( 'aimeos/catalog/detail/params/last/' . $site ) ) ) {
			$view->standardBackUrl = $view->link( 'client/html/catalog/detail/url', array_filter( $params ) );
		} elseif( !empty( $params = $context->session()->get( 'aimeos/catalog/lists/params/last/' . $site, [] ) ) ) {
			$view->standardBackUrl = $view->link( 'client/html/catalog/lists/url', array_filter( $params ) );
		}

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
				case 1: $controller->get()->check( ['order/base/product'] );
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
				(string) $view->param( 'b_stocktype', 'default' )
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
						(string) ( $values['stocktype'] ?? 'default' )
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

	/** client/html/basket/template-body
	 * Relative path to the HTML body template of the basket standard client.
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
	 * @see client/html/basket/template-header
	 */

	/** client/html/basket/template-header
	 * Relative path to the HTML header template of the basket standard client.
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
	 * @see client/html/basket/template-body
	 */
}
