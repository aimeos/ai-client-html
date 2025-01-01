<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2025
 */


namespace Aimeos\Client\Html\Account\Download;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setUser( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' ) );

		$this->object = new \Aimeos\Client\Html\Account\Download\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->view, $this->context );
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


	public function testInit()
	{
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->willReturnSelf();
		$response->expects( $this->once() )->method( 'withStatus' )->willReturnSelf();

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->object->init();
	}


	public function testInitOK()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['dl_id' => '-1'] );
		$this->view->addHelper( 'param', $helper );

		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Download\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( array( 'checkAccess', 'checkDownload' ) )
			->getMock();
		$object->setView( $this->view );

		$object->expects( $this->once() )->method( 'checkAccess' )->willReturn( true );
		$object->expects( $this->once() )->method( 'checkDownload' )->willReturn( true );


		$attrManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Product\\Attribute\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( ['get'] )
			->getMock();

		$attrManagerStub->expects( $this->once() )->method( 'get' )
			->willReturn( $attrManagerStub->create() );

		\Aimeos\MShop::inject( '\\Aimeos\\MShop\\Order\\Manager\\Product\\Attribute\\Standard', $attrManagerStub );


		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->exactly( 7 ) )->method( 'withHeader' )->willReturnSelf();

		$helper = $this->getMockBuilder( \Aimeos\Base\View\Helper\Response\Standard::class )
			->setConstructorArgs( array( $this->view, $response ) )
			->onlyMethods( ['createStream', 'withBody'] )
			->getMock();
		$helper->expects( $this->once() )->method( 'createStream' )->willReturn( $stream );
		$helper->expects( $this->once() )->method( 'withBody' )->willReturnSelf();
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
			->onlyMethods( array( 'addListItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'addListItem' )->willReturn( $customerStub );
		$customerStub->expects( $this->once() )->method( 'store' )->willReturn( $customerStub );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $customerStub );
		$this->assertTrue( $this->access( 'checkDownload' )->invokeArgs( $this->object, [-1, -2] ) );
	}


	public function testInitCheckDownloadMaxCount()
	{
		$this->context->config()->set( 'client/html/account/download/maxcount', 0 );

		$this->assertFalse( $this->access( 'checkDownload' )->invokeArgs( $this->object, [-1, -2] ) );
	}


	public function testAddDownload()
	{
		$fs = $this->context->fs( 'fs-secure' );
		$fs->write( 'tmp/download/test.txt', 'test' );

		$item = \Aimeos\MShop::create( $this->context, 'order/product/attribute' )->create();
		$item->setValue( 'tmp/download/test.txt' );
		$item->setName( 'test download' );


		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->exactly( 7 ) )->method( 'withHeader' )->willReturnSelf();

		$helper = $this->getMockBuilder( \Aimeos\Base\View\Helper\Response\Standard::class )
			->setConstructorArgs( array( $this->view, $response ) )
			->onlyMethods( ['createStream', 'withBody'] )
			->getMock();
		$helper->expects( $this->once() )->method( 'createStream' )->willReturn( $stream );
		$helper->expects( $this->once() )->method( 'withBody' )->willReturnSelf();
		$this->view->addHelper( 'response', $helper );

		$this->access( 'addDownload' )->invokeArgs( $this->object, [$item] );
	}


	public function testAddDownloadRedirect()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/product/attribute' )->create();
		$item->setValue( 'http://localhost/dl/test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->once() )->method( 'withHeader' )->willReturnSelf();
		$response->expects( $this->once() )->method( 'withStatus' )->willReturnSelf();

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $this->view, $response );
		$this->view->addHelper( 'response', $helper );

		$this->access( 'addDownload' )->invokeArgs( $this->object, [$item] );
	}


	public function testAddDownloadNotFound()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/product/attribute' )->create();
		$item->setValue( 'test.txt' );
		$item->setName( 'test download' );


		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();
		$response->expects( $this->never() )->method( 'withHeader' )->willReturnSelf();
		$response->expects( $this->once() )->method( 'withStatus' )->willReturnSelf();

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $this->view, $response );
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
