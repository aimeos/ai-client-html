<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address\Billing;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Billing\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::create( $this->context, 'basket' )->clear();
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view = \TestHelper::view();
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->view->addressPaymentItem = \Aimeos\MShop::create( $this->context, 'order/address' )->create();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-billing', $output );
		$this->assertMatchesRegularExpression( '/form-item form-group city.*form-item form-group postal/smU', $output );

		$this->assertGreaterThan( 0, count( $this->view->addressBillingMandatory ) );
		$this->assertGreaterThan( 0, count( $this->view->addressBillingOptional ) );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'addressBillingError' ) );
	}


	public function testInitNewAddress()
	{
		$this->view = \TestHelper::view();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.address.salutation' => 'mr',
				'order.address.firstname' => 'test',
				'order.address.lastname' => 'user',
				'order.address.address1' => 'mystreet 1',
				'order.address.postal' => '20000',
				'order.address.city' => 'hamburg',
				'order.address.email' => 'me@example.com',
				'order.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend::create( $this->context, 'basket' )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'payment', 0 )->getCity() );
	}


	public function testInitNewAddressMissing()
	{
		$this->view = \TestHelper::view();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.address.firstname' => 'test',
				'order.address.lastname' => 'user',
				'order.address.address1' => 'mystreet 1',
				'order.address.postal' => '20000',
				'order.address.city' => 'hamburg',
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		try
		{
			$this->object->init();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 2, count( $this->view->addressBillingError ) );
			$this->assertArrayHasKey( 'order.address.email', $this->view->addressBillingError );
			$this->assertArrayHasKey( 'order.address.languageid', $this->view->addressBillingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testInitNewAddressUnknown()
	{
		$this->view = \TestHelper::view();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.address.salutation' => 'mr',
				'order.address.firstname' => 'test',
				'order.address.lastname' => 'user',
				'order.address.address1' => 'mystreet 1',
				'order.address.postal' => '20000',
				'order.address.city' => 'hamburg',
				'order.address.email' => 'me@example.com',
				'order.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );
		$this->object->init();

		$basket = \Aimeos\Controller\Frontend::create( $this->context, 'basket' )->get();
		$this->assertEquals( 'test', $basket->getAddress( 'payment', 0 )->getFirstName() );
	}


	public function testInitNewAddressInvalid()
	{
		$this->view = \TestHelper::view();

		$config = $this->context->config();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $this->view, $config );
		$this->view->addHelper( 'config', $helper );

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.address.salutation' => 'mr',
				'order.address.firstname' => 'test',
				'order.address.lastname' => 'user',
				'order.address.address1' => 'mystreet 1',
				'order.address.postal' => '20AB',
				'order.address.city' => 'hamburg',
				'order.address.email' => 'me@example.com',
				'order.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		try
		{
			$this->object->init();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 1, count( $this->view->addressBillingError ) );
			$this->assertArrayHasKey( 'order.address.postal', $this->view->addressBillingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testInitExistingAddress()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['customer/address'] );
		$id = $customer->getAddressItems()->first()->getId();

		$this->view = \TestHelper::view();
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['ca_billingoption' => $id] );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( ['store'] )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnSelf() );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $customerStub );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend::create( $this->context, 'basket' )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'payment', 0 )->getCompany() );
	}
}
