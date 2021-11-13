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
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Test supplier | Aimeos</title>', $output );
		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testHeaderException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['f_supid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->header();
	}


	public function testBody()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos supplier-detail"', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-basic">', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-image', $output );

		$this->assertStringContainsString( '<div class="supplier-detail-description', $output );
		$this->assertStringContainsString( 'supplier-detail-address', $output );

		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBodyDefaultId()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/supplier/detail/supid-default', $this->getSupplierItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<h1 class="name" itemprop="name">Test supplier</h1>', $output );
	}


	public function testBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['f_supid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->body();
	}


	public function testBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['f_supid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->body();
	}


	public function testBodyMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['f_supid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->body();
	}


	public function testBodyException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Supplier\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['f_supid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->body();
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
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'detailErrorList' ) );
	}


	protected function getSupplierItem( $code = 'unitSupplier001', $domains = [] )
	{
		return \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context )->find( $code, $domains );
	}
}
