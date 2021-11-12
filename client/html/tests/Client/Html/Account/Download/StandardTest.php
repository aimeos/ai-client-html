<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\Client\Html\Account\Download;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Account\Download\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testBody()
	{
		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testHeader()
	{
		$output = $this->object->header();
		$this->assertEquals( '', $output );
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
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->object->init();
	}


	public function testInitOK()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['dl_id' => '-1'] );
		$this->view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Download\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'checkAccess', 'checkDownload' ) )
			->getMock();
		$object->setView( $this->view );

		$object->expects( $this->once() )->method( 'checkAccess' )->will( $this->returnValue( true ) );
		$object->expects( $this->once() )->method( 'checkDownload' )->will( $this->returnValue( true ) );


		$attrManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Product\\Attribute\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( ['get'] )
			->getMock();

		$attrManagerStub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $attrManagerStub->create() ) );

		\Aimeos\MShop::inject( 'order/base/product/attribute', $attrManagerStub );


		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->exactly( 7 ) )->method( 'withHeader' )->will( $this->returnSelf() );

		$helper = $this->getMockBuilder( \Aimeos\MW\View\Helper\Response\Standard::class )
			->setConstructorArgs( array( $this->view, $response ) )
			->setMethods( array( 'createStream' ) )
			->getMock();
		$helper->expects( $this->once() )->method( 'createStream' )->will( $this->returnValue( $stream ) );
		$this->view->addHelper( 'response', $helper );

		$object->init();
	}


	public function testInitCheckAccess()
	{
		$this->assertFalse( $this->access( 'checkAccess' )->invokeArgs( $this->object, [-1, -2] ) );
	}


	public function testInitCheckDownload()
	{
		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'addListItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'addListItem' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );

		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $customerStub );
		$this->assertTrue( $this->access( 'checkDownload' )->invokeArgs( $this->object, [-1, -2] ) );
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}


	public function testInitCheckDownloadMaxCount()
	{
		$this->context->getConfig()->set( 'client/html/account/download/maxcount', 0 );

		$this->assertFalse( $this->access( 'checkDownload' )->invokeArgs( $this->object, [-1, -2] ) );
	}


	public function testAddDownload()
	{
		$fs = $this->context->getFilesystemManager()->get( 'fs-secure' );
		$fs->write( 'tmp/download/test.txt', 'test' );

		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create();
		$item->setValue( 'tmp/download/test.txt' );
		$item->setName( 'test download' );


		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->exactly( 7 ) )->method( 'withHeader' )->will( $this->returnSelf() );

		$helper = $this->getMockBuilder( \Aimeos\MW\View\Helper\Response\Standard::class )
			->setConstructorArgs( array( $this->view, $response ) )
			->setMethods( array( 'createStream' ) )
			->getMock();
		$helper->expects( $this->once() )->method( 'createStream' )->will( $this->returnValue( $stream ) );
		$this->view->addHelper( 'response', $helper );

		$this->access( 'addDownload' )->invokeArgs( $this->object, [$item] );
	}


	public function testAddDownloadRedirect()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create();
		$item->setValue( 'http://localhost/dl/test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->access( 'addDownload' )->invokeArgs( $this->object, [$item] );
	}


	public function testAddDownloadNotFound()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create();
		$item->setValue( 'test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->never() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->access( 'addDownload' )->invokeArgs( $this->object, [$item] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
