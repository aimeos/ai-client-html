<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Stage;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertNotNull( $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Stage\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertEquals( null, $object->header() );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-stage', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb">', $output );
		$this->assertRegExp( '#Your search result#smU', $output );

		$this->assertEquals( null, $expire );
		$this->assertEquals( 0, count( $tags ) );
	}


	public function testBodyCatId()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-stage home categories coffee"', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-image', $output );
		$this->assertStringContainsString( '/path/to/folder/cafe/stage.jpg', $output );

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb">', $output );
		$this->assertRegExp( '#Root.*.Categories.*.Kaffee.*#smU', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Stage\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Stage\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Stage\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Stage\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'A non-recoverable error occured', $object->body() );
	}


	public function testModifyBody()
	{
		$this->assertEquals( 'test', $this->object->modifyBody( 'test', 1 ) );
	}


	public function testModifyHeader()
	{
		$this->assertEquals( 'test', $this->object->modifyHeader( 'test', 1 ) );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'navigator', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
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

		$this->assertEmpty( $this->view->get( 'stageErrorList' ) );
	}


	protected function getCatalogItem()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		if( ( $item = $catalogManager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No catalog item with code "cafe" found' );
		}

		return $item;
	}
}
