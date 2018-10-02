<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

		unset( $this->object, $this->context );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'process';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderSkip()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'process';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-process">', $output );
		$this->assertEquals( 'http://baseurl/checkout/index/?c_step=payment', $view->standardUrlPayment );
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
		$view = $this->object->getView();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Standard\Process\Standard' )
			->setConstructorArgs( [$this->context, \TestHelperHtml::getHtmlTemplatePaths()] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['addItem', 'block'] )
			->getMock();

		$form = new \Aimeos\MShop\Common\Item\Helper\Form\Standard( 'url', 'POST', [], true );
		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();
		$prodId = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->findItem( 'CNE' )->getId();
		$servId = \Aimeos\MShop\Factory::createManager( $this->context, 'service' )->findItem( 'paypalexpress' )->getId();

		$basketMock->addProduct( $prodId );
		$basketMock->addService( 'payment', $servId );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( $form ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get() ) );
		$orderMock->expects( $this->once() )->method( 'addItem' )->will( $this->returnValue( $orderItem ) );
		$orderMock->expects( $this->once() )->method( 'block' );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->process();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'url', $view->standardUrlNext );
		$this->assertEquals( 'POST', $view->standardMethod );
		$this->assertEquals( [], $view->standardProcessParams );
		$this->assertEquals( true, $view->standardUrlExternal );
	}


	public function testProcessNoPayment()
	{
		$view = $this->object->getView();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Standard\Process\Standard' )
			->setConstructorArgs( [$this->context, \TestHelperHtml::getHtmlTemplatePaths()] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['addItem', 'block', 'saveItem'] )
			->getMock();

		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();

		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get() ) );
		$orderMock->expects( $this->once() )->method( 'addItem' )->will( $this->returnValue( $orderItem ) );
		$orderMock->expects( $this->once() )->method( 'saveItem' );
		$orderMock->expects( $this->once() )->method( 'block' );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->process();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertEquals( 'http://baseurl/checkout/confirm/', $view->standardUrlNext );
	}


	public function testProcessNoService()
	{
		$view = $this->object->getView();
		$param = array( 'c_step' => 'process', 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Standard\Process\Standard' )
			->setConstructorArgs( [$this->context, \TestHelperHtml::getHtmlTemplatePaths()] )
			->setMethods( ['processPayment'] )
			->getMock();
		$object->setView( $view );

		$basketMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Basket\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['store'] )
			->getMock();

		$orderMock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['addItem', 'block'] )
			->getMock();

		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();
		$prodId = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->findItem( 'CNE' )->getId();
		$servId = \Aimeos\MShop\Factory::createManager( $this->context, 'service' )->findItem( 'paypalexpress' )->getId();

		$basketMock->addProduct( $prodId );
		$basketMock->addService( 'payment', $servId );
		$object->expects( $this->once() )->method( 'processPayment' )->will( $this->returnValue( null ) );
		$basketMock->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $basketMock->get() ) );
		$orderMock->expects( $this->once() )->method( 'addItem' )->will( $this->returnValue( $orderItem ) );
		$orderMock->expects( $this->once() )->method( 'block' );

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', $basketMock );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $orderMock );

		$object->process();

		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );
		\Aimeos\Controller\Frontend\Basket\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Basket\\Standard', null );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', [] ) ) );
		$this->assertTrue( isset( $view->standardUrlNext ) );
		$this->assertEquals( 'POST', $view->standardMethod );
	}


	public function testProcessNoStep()
	{
		$this->assertNull( $this->object->process() );
	}


	public function testProcessHtmlException()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cp_payment' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->disableOriginalConstructor()
			->setMethods( ['getItem'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'getItem' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );

		$this->assertInternalType( 'array', $view->standardErrorList );
	}


	public function testProcessFrontendException()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cp_payment' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->disableOriginalConstructor()
			->setMethods( ['getItem'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'getItem' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );

		$this->assertInternalType( 'array', $view->standardErrorList );
	}


	public function testProcessMShopException()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cp_payment' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->disableOriginalConstructor()
			->setMethods( ['getItem'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'getItem' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );

		$this->assertInternalType( 'array', $view->standardErrorList );
	}


	public function testProcessException()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['c_step' => 'process', 'cp_payment' => 1] );
		$view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Order\Standard' )
			->disableOriginalConstructor()
			->setMethods( ['getItem'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'getItem' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Order\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Order\\Standard', null );

		$this->assertInternalType( 'array', $view->standardErrorList );
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}
