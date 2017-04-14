<?php

namespace Aimeos\Client\Html\Checkout\Confirm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->view = \TestHelperHtml::getView();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setEditor( 'UTC001' );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Standard( $this->context, $paths );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object, $this->view );
	}


	public function testGetHeader()
	{
		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'customer' )->findItem( 'UTC001' );

		$this->context->setUserId( $item->getId() );
		$this->context->getSession()->set( 'aimeos/orderid', $this->getOrder( '2011-09-17 16:14:32' )->getId() );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Confirm\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'customer' )->findItem( 'UTC001' );

		$this->context->setUserId( $item->getId() );
		$orderid = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderid );

		$output = $this->object->getBody();

		$this->assertContains( '<section class="aimeos checkout-confirm">', $output );
		$this->assertContains( '<div class="checkout-confirm-retry">', $output );
		$this->assertContains( '<div class="checkout-confirm-basic">', $output );
		$this->assertContains( '<div class="checkout-confirm-detail', $output );
		$this->assertRegExp( '#<span class="value">.*' . $orderid . '.*</span>#smU', $output );

		$this->assertContains( 'mr  Our Unittest', $output );
		$this->assertContains( 'Example company', $output );

		$this->assertContains( 'solucia', $output );
		$this->assertContains( 'paypal', $output );

		$this->assertContains( 'This is a comment', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Confirm\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Confirm\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Confirm\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Confirm\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$this->assertContains( 'A non-recoverable error occured', $object->getBody() );
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
		$orderId = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderId );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => $orderId] );
		$this->view->addHelper( 'param', $helper );

		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view->addHelper( 'request', $helper );

		$this->object->process();
	}


	public function testProcessNoCode()
	{
		$this->object->process();
	}


	public function testProcessClientException()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => -1] );
		$this->view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Service\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['updateSync'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'updateSync' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', null );

		$this->assertEquals( 1, count( $this->view->get( 'confirmErrorList', [] ) ) );
	}


	public function testProcessControllerException()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => -1] );
		$this->view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Service\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['updateSync'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'updateSync' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', null );

		$this->assertEquals( 1, count( $this->view->get( 'confirmErrorList', [] ) ) );
	}


	public function testProcessMShopException()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => -1] );
		$this->view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Service\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['updateSync'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'updateSync' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', null );

		$this->assertEquals( 1, count( $this->view->get( 'confirmErrorList', [] ) ) );
	}


	public function testProcessException()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => -1] );
		$this->view->addHelper( 'param', $helper );

		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Service\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['updateSync'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'updateSync' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', null );

		$this->assertEquals( 1, count( $this->view->get( 'confirmErrorList', [] ) ) );
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
