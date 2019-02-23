<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address\Billing;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		\Aimeos\Controller\Frontend::cache( true );

		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Billing\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		\Aimeos\Controller\Frontend::cache( false );

		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-billing', $output );
		$this->assertRegexp( '/form-item form-group city.*form-item form-group postal/smU', $output );

		$this->assertGreaterThan( 0, count( $view->billingMandatory ) );
		$this->assertGreaterThan( 0, count( $view->billingOptional ) );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessNewAddress()
	{
		$view = \TestHelperHtml::getView();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'payment', 0 )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = \TestHelperHtml::getView();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 3, count( $view->billingError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->billingError );
			$this->assertArrayHasKey( 'order.base.address.email', $view->billingError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->billingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessNewAddressUnknown()
	{
		$view = \TestHelperHtml::getView();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'test', $basket->getAddress( 'payment', 0 )->getFirstName() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = \TestHelperHtml::getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20AB',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 1, count( $view->billingError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->billingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessExistingAddress()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001', ['customer/address'] );
		$id = current( $customer->getAddressItems() )->getId();

		$view = \TestHelperHtml::getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['ca_billingoption' => $id] );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( ['store'] )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnSelf() );

		\Aimeos\Controller\Frontend::inject( 'customer', $customerStub );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'payment', 0 )->getCompany() );
	}
}
