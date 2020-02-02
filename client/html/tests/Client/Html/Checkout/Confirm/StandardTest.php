<?php

namespace Aimeos\Client\Html\Checkout\Confirm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::getView();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setEditor( 'UTC001' );

		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->view );
	}


	public function testGetHeader()
	{
		$orderid = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderid );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Confirm\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$orderid = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderid );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<section class="aimeos checkout-confirm"', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-retry">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-basic">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-detail', $output );
		$this->assertRegExp( '#<span class="value">.*' . $orderid . '.*</span>#smU', $output );

		$this->assertStringContainsString( 'mr Our Unittest', $output );
		$this->assertStringContainsString( 'Example company', $output );

		$this->assertStringContainsString( 'solucia', $output );
		$this->assertStringContainsString( 'paypal', $output );

		$this->assertStringContainsString( 'This is a comment', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Confirm\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Confirm\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Confirm\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Confirm\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$this->assertStringContainsString( 'A non-recoverable error occured', $object->getBody() );
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
		$orderId = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderId );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => $orderId] );
		$this->view->addHelper( 'param', $helper );

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view->addHelper( 'request', $helper );

		$this->object->process();

		$this->assertNotEmpty( $this->object->getView()->get( 'confirmErrorList' ) );
	}


	public function testProcessNoCode()
	{
		$this->object->process();

		$this->assertNotEmpty( $this->object->getView()->get( 'confirmErrorList' ) );
	}


	public function testProcessClientException()
	{
		$this->context->getSession()->set( 'aimeos/orderid', -1 );

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
		$this->context->getSession()->set( 'aimeos/orderid', -1 );

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
		$this->context->getSession()->set( 'aimeos/orderid', -1 );

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
		$this->context->getSession()->set( 'aimeos/orderid', -1 );

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
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		if( ( $item = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}
