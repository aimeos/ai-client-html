<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Client\Html\Catalog\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->getConfig()->set( 'client/html/catalog/product/product-codes', ['CNE', 'ABCD', 'CNC'] );

		$this->object = new \Aimeos\Client\Html\Catalog\Product\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$filter = $manager->filter()->add( ['product.code' => ['CNE', 'ABCD', 'CNC']] );
		$map = $manager->search( $filter )->col( 'product.id', 'product.code' );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<script', $output );
		$prodCodeParam = '/st_pid%5B[0-9]%5D=';
		$this->assertRegExp( $prodCodeParam . $map['CNE'] . '/', $output );
		$this->assertRegExp( $prodCodeParam . $map['ABCD'] . '/', $output );
		$this->assertRegExp( $prodCodeParam . $map['CNC'] . '/', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}


	public function testHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertEmpty( $object->header() );
	}


	public function testBody()
	{

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$productNameCNE = '<h2 itemprop="name">Cafe Noire Expresso</h2>';
		$productNameABCD = '<h2 itemprop="name">Unterproduct 1</h2>';
		$productNameCNC = '<h2 itemprop="name">Cafe Noire Cappuccino</h2>';
		$this->assertStringContainsString( $productNameCNE, $output );
		$this->assertStringContainsString( $productNameABCD, $output );
		$this->assertStringContainsString( $productNameCNC, $output );

		$outputPosCNE = strpos( $output, $productNameCNE );
		$outputPosABCD = strpos( $output, $productNameABCD );
		$outputPosCNC = strpos( $output, $productNameCNC );
		$this->assertGreaterThan( $outputPosCNE, $outputPosABCD );
		$this->assertGreaterThan( $outputPosABCD, $outputPosCNC );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}


	public function testBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Product\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'A non-recoverable error occured', $object->body() );
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

		$this->assertEmpty( $this->view->get( 'productErrorList' ) );
	}
}
