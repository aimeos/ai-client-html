<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 */


namespace Aimeos\Client\Html\Catalog\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->context->getConfig()->set( 'client/html/catalog/product/product-codes', ['CNE', 'IJKL', 'CNC'] );

		$this->object = new \Aimeos\Client\Html\Catalog\Product\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getHeader();

		$this->assertStringContainsString( '<script type="text/javascript"', $output );
		$prodCodeParam = '/s_prodcode%5B[0-9]%5D=';
		$this->assertRegExp( $prodCodeParam . 'CNE/', $output );
		$this->assertRegExp( $prodCodeParam . 'IJKL/', $output );
		$this->assertRegExp( $prodCodeParam . 'CNC/', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertNull( $object->getHeader() );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$productNameCNE = '<h2 itemprop="name">Cafe Noire Expresso</h2>';
		$productNameIJKL = '<h2 itemprop="name">Unterproduct 3</h2>';
		$productNameCNC = '<h2 itemprop="name">Cafe Noire Cappuccino</h2>';
		$this->assertStringContainsString( $productNameCNE, $output );
		$this->assertStringContainsString( $productNameIJKL, $output );
		$this->assertStringContainsString( $productNameCNC, $output );

		$outputPosCNE = strpos( $output, $productNameCNE );
		$outputPosIJKL = strpos( $output, $productNameIJKL );
		$outputPosCNC = strpos( $output, $productNameCNC );
		$this->assertGreaterThan( $outputPosCNE, $outputPosIJKL );
		$this->assertGreaterThan( $productNameIJKL, $outputPosCNC );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

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
		$this->object->process();

		$this->assertEmpty( $this->object->getView()->get( 'productErrorList' ) );
	}
}
