<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\Client\Html\Checkout\Standard\Address\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );

		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Delivery\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		\Aimeos\Controller\Frontend::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery', $output );

		$this->assertGreaterThan( 0, count( $this->view->addressDeliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $this->view->addressDeliveryOptional ) );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'addressDeliveryError' ) );
	}


	public function testInitNewAddress()
	{
		$this->view = \TestHelperHtml::view();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'delivery', 0 )->getCity() );
	}


	public function testInitNewAddressMissing()
	{
		$this->view = \TestHelperHtml::view();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		try
		{
			$this->object->init();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 1, count( $this->view->addressDeliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.languageid', $this->view->addressDeliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testInitNewAddressUnknown()
	{
		$this->view = \TestHelperHtml::view();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );
		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'test', $basket->getAddress( 'delivery', 0 )->getFirstName() );
	}


	public function testInitNewAddressInvalid()
	{
		$this->view = \TestHelperHtml::view();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $this->view, $config );
		$this->view->addHelper( 'config', $helper );

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20AB',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@example.com',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		try
		{
			$this->object->init();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 1, count( $this->view->addressDeliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $this->view->addressDeliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testInitAddressDelete()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['customer/address'] );
		$id = $customer->getAddressItems()->first()->getId();

		$this->view = \TestHelperHtml::view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['ca_delivery_delete' => $id] );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'deleteAddressItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'deleteAddressItem' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );

		\Aimeos\Controller\Frontend::inject( 'customer', $customerStub );

		$this->expectException( \Aimeos\Client\Html\Exception::class );
		$this->object->init();
	}


	public function testInitExistingAddress()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['customer/address'] );

		$this->view = \TestHelperHtml::view();
		$param = array( 'ca_deliveryoption' => $customer->getAddressItems()->first()->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'delivery', 0 )->getCompany() );
	}


	public function testInitExistingAddressInvalid()
	{
		$this->view = \TestHelperHtml::view();
		$param = [
			'ca_deliveryoption' => -2,
			'ca_delivery_-2' => [
				'order.base.address.languageid' => 'de',
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'street',
				'order.base.address.postal' => '1234',
				'order.base.address.city' => 'test city',
			]
		];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'mr', $basket->getAddress( 'delivery', 0 )->getSalutation() );
	}


	public function testInitRemoveAddress()
	{
		$this->view = \TestHelperHtml::view();
		$param = array( 'ca_delivery_delete' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertCount( 0, $basket->getAddress( 'delivery' ) );
	}
}
