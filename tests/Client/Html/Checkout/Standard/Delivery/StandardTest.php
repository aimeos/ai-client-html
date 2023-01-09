<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Delivery;


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

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Delivery\Standard( $this->context );
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
		$this->view->standardStepActive = 'delivery';
		$this->view->standardSteps = array( 'before', 'delivery', 'after' );
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<div class="section checkout-standard-delivery">', $output );

		$this->assertGreaterThan( 0, count( $this->view->deliveryServices ) );
	}


	public function testBodyOtherStep()
	{
		$this->view = \TestHelper::view();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEquals( 'delivery', $this->view->get( 'standardStepActive' ) );
	}


	public function testInitExistingId()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'service' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitdeliverycode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$this->view = \TestHelper::view();

		$param = array(
			'c_deliveryoption' => $service->getId(),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend::create( $this->context, 'basket' )->get();
		$this->assertEquals( 'unitdeliverycode', $basket->getService( 'delivery', 0 )->getCode() );
	}


	public function testInitInvalidId()
	{
		$this->view = \TestHelper::view();

		$param = array( 'c_deliveryoption' => -1 );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->expectException( '\\Aimeos\\MShop\\Exception' );
		$this->object->init();
	}


	public function testInitNotExistingAttributes()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'service' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitdeliverycode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$this->view = \TestHelper::view();

		$param = array(
			'c_deliveryoption' => $service->getId(),
			'c_delivery' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->expectException( '\\Aimeos\\Controller\\Frontend\\Basket\\Exception' );
		$this->object->init();
	}
}
