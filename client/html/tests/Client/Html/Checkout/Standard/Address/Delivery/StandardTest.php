<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

namespace Aimeos\Client\Html\Checkout\Standard\Address\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );

		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Delivery\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		\Aimeos\Controller\Frontend::cache( false );

		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery', $output );

		$this->assertGreaterThan( 0, count( $view->addressDeliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $view->addressDeliveryOptional ) );
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


	public function testProcess()
	{
		$this->object->process();

		$this->assertEmpty( $this->object->getView()->get( 'addressDeliveryError' ) );
	}


	public function testProcessNewAddress()
	{
		$view = \TestHelperHtml::getView();

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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'delivery', 0 )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = \TestHelperHtml::getView();

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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 2, count( $view->addressDeliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->addressDeliveryError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->addressDeliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessNewAddressUnknown()
	{
		$view = \TestHelperHtml::getView();

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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'test', $basket->getAddress( 'delivery', 0 )->getFirstName() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = \TestHelperHtml::getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
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
			$this->assertEquals( 1, count( $view->addressDeliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->addressDeliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessAddressDelete()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001', ['customer/address'] );
		$id = $customer->getAddressItems()->first()->getId();

		$view = \TestHelperHtml::getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['ca_delivery_delete' => $id] );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'deleteAddressItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'deleteAddressItem' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );

		\Aimeos\Controller\Frontend::inject( 'customer', $customerStub );

		$this->expectException( \Aimeos\Client\Html\Exception::class );
		$this->object->process();
	}


	public function testProcessExistingAddress()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001', ['customer/address'] );

		$view = \TestHelperHtml::getView();
		$param = array( 'ca_deliveryoption' => $customer->getAddressItems()->first()->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'delivery', 0 )->getCompany() );
	}


	public function testProcessExistingAddressInvalid()
	{
		$view = \TestHelperHtml::getView();
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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'mr', $basket->getAddress( 'delivery', 0 )->getSalutation() );
	}


	public function testProcessRemoveAddress()
	{
		$view = \TestHelperHtml::getView();
		$param = array( 'ca_delivery_delete' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertCount( 0, $basket->getAddress( 'delivery' ) );
	}
}
