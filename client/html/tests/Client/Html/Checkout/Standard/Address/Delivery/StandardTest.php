<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\Client\Html\Checkout\Standard\Address\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Delivery\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();

		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $view->deliveryOptional ) );
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
			$this->assertEquals( 2, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->deliveryError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->deliveryError );
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
			$this->assertEquals( 1, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->deliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessAddressDelete()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer address found' );
		}

		$item->setId( null );
		$item = $manager->saveItem( $item );

		$view = \TestHelperHtml::getView();
		$this->context->setUserId( $item->getParentId() );

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try {
			$this->object->process();
			$this->markTestFailed( 'Exception not thrown' );
		}
		catch( \Aimeos\Client\Html\Exception $e ) {}

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$manager->getItem( $item->getId() );
	}


	public function testProcessAddressDeleteUnknown()
	{
		$view = \TestHelperHtml::getView();

		$param = array( 'ca_delivery_delete' => '-1' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->process();
	}


	public function testProcessAddressDeleteNoLogin()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer address found' );
		}

		$view = \TestHelperHtml::getView();

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( \Aimeos\Controller\Frontend\Customer\Exception::class );
		$this->object->process();
	}


	public function testProcessExistingAddress()
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'Customer item not found' );
		}

		$customerAddressManager = $customerManager->getSubManager( 'address' );
		$search = $customerAddressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.parentid', $customer->getId() ) );
		$result = $customerAddressManager->searchItems( $search );

		if( ( $address = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'Customer address item not found' );
		}

		$this->context->setUserId( $customer->getId() );

		$view = \TestHelperHtml::getView();

		$param = array( 'ca_deliveryoption' => $address->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$this->context->setEditor( null );
		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'delivery', 0 )->getCompany() );
	}


	public function testProcessInvalidId()
	{
		$view = \TestHelperHtml::getView();

		$param = array( 'ca_deliveryoption' => 0 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->process();
	}
}
