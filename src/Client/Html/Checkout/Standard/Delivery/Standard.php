<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Delivery;


// Strings for translation
sprintf( 'delivery' );


/**
 * Default implementation of checkout delivery HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$view = $this->view();
		$step = $view->get( 'standardStepActive' );
		$onepage = $view->config( 'client/html/checkout/standard/onepage', [] );

		if( $step != 'delivery' && !( in_array( 'delivery', $onepage ) && in_array( $step, $onepage ) ) ) {
			return '';
		}

		return parent::body( $uid );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();

		try
		{
			$basketCtrl = \Aimeos\Controller\Frontend::create( $context, 'basket' );
			$servCtrl = \Aimeos\Controller\Frontend::create( $context, 'service' )->uses( ['media', 'price', 'text'] );

			// only start if there's something to do
			if( ( $serviceIds = $view->param( 'c_deliveryoption', null ) ) !== null )
			{
				$basketCtrl->deleteService( 'delivery' );

				foreach( (array) $serviceIds as $idx => $id )
				{
					try
					{
						$basketCtrl->addService( $servCtrl->get( $id ), $view->param( 'c_delivery/' . $id, [] ), $idx );
					}
					catch( \Aimeos\Controller\Frontend\Basket\Exception $e )
					{
						$view->deliveryError = $e->getErrors();
						$view->errors = array_merge( $view->get( 'errors', [] ), $e->getErrors() );

						throw $e;
					}
				}
			}


			parent::init();


			if( !isset( $view->standardStepActive ) && !$this->call( 'isAvailable', $basketCtrl->get() ) ) {
				$view->standardStepActive = 'delivery';
			}
		}
		catch( \Exception $e )
		{
			$view->standardStepActive = 'delivery';
			throw $e;
		}
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
		$domains = ['media', 'price', 'text'];

		/** client/html/checkout/standard/delivery/domains
		 * List of domain names whose items should be available in the checkout payment templates
		 *
		 * The templates rendering checkout delivery related data usually add the
		 * images, prices and texts associated to each item. If you want to display
		 * additional content like the attributes, you can configure your own list
		 * of domains (attribute, media, price, text, etc. are domains) whose items
		 * are fetched from the storage.
		 *
		 * @param array List of domain names
		 * @since 2019.04
		 * @see client/html/checkout/standard/payment/domains
		 */
		$domains = $context->config()->get( 'client/html/checkout/standard/delivery/domains', $domains );

		$basketCntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );
		$serviceCntl = \Aimeos\Controller\Frontend::create( $context, 'service' );

		$services = [];
		$basket = $basketCntl->get();
		$providers = $serviceCntl->uses( $domains )->type( 'delivery' )->getProviders();
		$orderServices = map( $basket->getService( 'delivery' ) )->col( null, 'order.service.serviceid' );

		foreach( $providers as $id => $provider )
		{
			if( $provider->isAvailable( $basket ) === true )
			{
				$attr = $provider->getConfigFE( $basket );

				if( $oservice = $orderServices->get( $id ) )
				{
					foreach( $attr as $key => $item )
					{
						$value = is_array( $item->getDefault() ) ? key( $item->getDefault() ) : $item->getDefault();
						$value = $oservice->getAttribute( $key, 'delivery' ) ?: $value;
						$item->value = $oservice->getAttribute( $key . '/hidden', 'delivery' ) ?: $value;
					}
				}
				else
				{
					foreach( $attr as $key => $item ) {
						$item->value = is_array( $item->getDefault() ) ? key( $item->getDefault() ) : $item->getDefault();
					}
				}

				$services[$id] = $provider->getServiceItem()->set( 'attributes', $attr )
					->set( 'price', $provider->calcPrice( $basket ) );
			}
		}

		$view->deliveryServices = $services;
		$view->deliveryOption = $view->param( 'c_deliveryoption', $orderServices->firstKey() ?: key( $services ) );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Tests if an item is available and the step can be skipped
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @return bool TRUE if step can be skipped, FALSE if not
	 */
	protected function isAvailable( \Aimeos\MShop\Order\Item\Iface $basket ) : bool
	{
		return !empty( $basket->getService( 'delivery' ) );
	}


	/** client/html/checkout/standard/delivery/template-body
	 * Relative path to the HTML body template of the checkout standard delivery client.
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
	 * @see client/html/checkout/standard/delivery/template-header
	 */
}
