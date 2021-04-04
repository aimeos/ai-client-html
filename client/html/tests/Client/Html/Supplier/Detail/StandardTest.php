<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Client\Html\Supplier\Detail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->getHeader();

		$this->assertStringContainsString( '<title>Test supplier | Aimeos</title>', $output );
		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetHeaderException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['f_supid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getHeader();
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos supplier-detail"', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-basic">', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-image', $output );

		$this->assertStringContainsString( '<div class="supplier-detail-description', $output );
		$this->assertStringContainsString( 'supplier-detail-address', $output );

		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetBodyDefaultId()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/supplier/detail/supid-default', $this->getSupplierItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $context );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, [] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( '<h1 class="name" itemprop="name">Test supplier</h1>', $output );
	}


	public function testGetBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['f_supid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['f_supid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['f_supid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['f_supid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getBody();
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

		$this->assertEmpty( $this->object->getView()->get( 'detailErrorList' ) );
	}


	protected function getSupplierItem( $code = 'unitSupplier001', $domains = [] )
	{
		return \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context )->find( $code, $domains );
	}
}
