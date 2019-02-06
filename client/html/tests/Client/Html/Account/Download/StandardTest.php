<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
 */


namespace Aimeos\Client\Html\Account\Download;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelperHtml::getView();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Download\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertEquals( '', $output );
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
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->object->process();
	}


	public function testProcessOK()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Download\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'checkAccess', 'checkDownload' ) )
			->getMock();
		$object->setView( $this->view );

		$object->expects( $this->once() )->method( 'checkAccess' )->will( $this->returnValue( true ) );
		$object->expects( $this->once() )->method( 'checkDownload' )->will( $this->returnValue( true ) );


		$attrManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Product\\Attribute\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'getItem' ) )
			->getMock();

		$attrManagerStub->expects( $this->once() )->method( 'getItem' )
			->will( $this->returnValue( $attrManagerStub->createItem() ) );

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

		$object->process();
	}


	public function testProcessCheckAccess()
	{
		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'checkAccess' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertFalse( $result );
	}


	public function testProcessCheckDownload()
	{
		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();
		$managerStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop::inject( 'customer/lists', $managerStub );


		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'checkDownload' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertTrue( $result );
	}


	public function testProcessCheckDownloadMaxCount()
	{
		$this->context->getConfig()->set( 'client/html/account/download/maxcount', 0 );

		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'checkDownload' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertFalse( $result );
	}


	public function testProcessGetListItem()
	{
		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'getListItem' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $result );
	}


	public function testAddDownload()
	{
		$fs = $this->context->getFilesystemManager()->get( 'fs-secure' );
		$fs->write( 'tmp/download/test.txt', 'test' );

		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->createItem();
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


		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'addDownload' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $item ) );
	}


	public function testAddDownloadRedirect()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->createItem();
		$item->setValue( 'http://localhost/dl/test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );


		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'addDownload' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $item ) );
	}


	public function testAddDownloadNotFound()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->createItem();
		$item->setValue( 'test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->never() )->method( 'withHeader' )->will( $this->returnSelf() );
		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnSelf() );

		$helper = new \Aimeos\MW\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );


		$class = new \ReflectionClass( \Aimeos\Client\Html\Account\Download\Standard::class );
		$method = $class->getMethod( 'addDownload' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $item ) );
	}
}