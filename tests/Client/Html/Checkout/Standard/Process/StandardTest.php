<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process;


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

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Standard( $this->context );
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
		$this->view->standardStepActive = 'process';
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<div class="checkout-standard-process">', $output );
		$this->assertEquals( 'http://baseurl/checkout/index/?c_step=payment', $this->view->standardUrlPayment );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['processPayment'] )
			->getMock();
		$object->setView( $this->view );

		$basketMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Basket\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Order\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['save'] )
			->getMock();

		$form = new \Aimeos\MShop\Common\Helper\Form\Standard( 'url', 'POST', [], true );
		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['price'] );
		$service = \Aimeos\MShop::create( $this->context, 'service' )->find( 'paypalexpress' );

		$basketMock->addProduct( $product );
		$basketMock->addService( $service );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( $form ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( $orderItem->setId( '123' ) ) );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Basket\Standard::class, $basketMock );
		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Order\Standard::class, $orderMock );

		$object->init();

		$this->assertEquals( 0, count( $this->view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'url', $this->view->standardUrlNext );
		$this->assertEquals( 'POST', $this->view->standardMethod );
		$this->assertEquals( [], $this->view->standardProcessParams );
		$this->assertEquals( true, $this->view->standardUrlExternal );
	}


	public function testInitNoPayment()
	{
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['processPayment'] )
			->getMock();
		$object->setView( $this->view );

		$basketMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Basket\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Order\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['save'] )
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->exactly( 2 ) )->method( 'save' )->will( $this->returnValue( $orderItem ) );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Basket\Standard::class, $basketMock );
		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Order\Standard::class, $orderMock );

		$object->init();

		$this->assertEquals( 0, count( $this->view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'http://baseurl/checkout/confirm/', $this->view->standardUrlNext );
	}


	public function testInitNoService()
	{
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Process\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['processPayment'] )
			->getMock();
		$object->setView( $this->view );

		$basketMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Basket\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( \Aimeos\Controller\Frontend\Order\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['save'] )
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['price'] );
		$service = \Aimeos\MShop::create( $this->context, 'service' )->find( 'paypalexpress' );

		$basketMock->addProduct( $product );
		$basketMock->addService( $service );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( null ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get()->setId( '123' ) ) );
		$orderMock->expects( $this->exactly( 2 ) )->method( 'save' )->will( $this->returnValue( $orderItem ) );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Basket\Standard::class, $basketMock );
		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Order\Standard::class, $orderMock );

		$object->init();

		$this->assertEquals( 0, count( $this->view->get( 'standardErrorList', [] ) ) );
		$this->assertTrue( isset( $this->view->standardUrlNext ) );
		$this->assertEquals( 'POST', $this->view->standardMethod );
	}


	public function testInitNoStep()
	{
		$this->assertTrue( $this->object->init() );
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}
