<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $this->context );
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

		$this->assertStringContainsString( '<title>Kaffee | Aimeos</title>', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testHeaderSearch()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_search' => '<b>Search result</b>' ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertRegexp( '#<title>[^>]*Search result[^<]* | Aimeos</title>#', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee"', $output );

		$this->assertStringContainsString( '<div class="catalog-list-head">', $output );
		$this->assertRegExp( '#<h1>Kaffee</h1>#', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testBodyPagination()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['l_size' => 2] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list', $output );
		$this->assertStringContainsString( '<nav class="pagination">', $output );
	}


	public function testBodyNoDefaultCat()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list', $output );
		$this->assertNotRegExp( '#.*U:TESTPSUB01.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB03.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB04.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB05.*#smu', $output );
	}


	public function testBodyDefaultCat()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/lists/catid-default', $this->getCatalogItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyMultipleDefaultCat()
	{
		$context = clone $this->context;
		$catid = $this->getCatalogItem()->getId();
		$context->getConfig()->set( 'client/html/catalog/lists/catid-default', array( $catid, $catid ) );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyMultipleDefaultCatString()
	{
		$context = clone $this->context;
		$catid = $this->getCatalogItem()->getId();
		$context->getConfig()->set( 'client/html/catalog/lists/catid-default', $catid . ',' . $catid );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyCategoryLevels()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem( 'root' )->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smu', $output );
		$this->assertRegExp( '#.*Cafe Noire Expresso.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Bundle.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Test priced Selection.*#smu', $output );
	}


	public function testBodySearchText()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_search' => '<b>Search result</b>' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list', $output );
		$this->assertStringContainsString( '&lt;b&gt;Search result&lt;/b&gt;', $output );
	}


	public function testBodySearchAttribute()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_attrid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list', $output );
	}


	public function testBodySearchSupplier()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_supid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-list', $output );
	}


	public function testBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'A non-recoverable error occured', $object->body() );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'items', 'Standard' );
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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'l_type' => 'list' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEmpty( $this->view->get( 'listErrorList' ) );
	}


	public function testInitHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'text exception' ) ) );

		$object->setView( $this->view );

		$object->init();

		$this->assertIsArray( $this->view->listErrorList );
	}


	public function testInitFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'text exception' ) ) );

		$object->setView( $this->view );

		$object->init();

		$this->assertIsArray( $this->view->listErrorList );
	}


	public function testInitMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'text exception' ) ) );

		$object->setView( $this->view );

		$object->init();

		$this->assertIsArray( $this->view->listErrorList );
	}


	public function testInitException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Lists\Standard::class )
		->setConstructorArgs( array( $this->context, [] ) )
		->setMethods( array( 'getClientParams' ) )
		->getMock();

		$object->expects( $this->once() )->method( 'getClientParams' )
		->will( $this->throwException( new \RuntimeException( 'text exception' ) ) );

		$object->setView( $this->view );

		$object->init();

		$this->assertIsArray( $this->view->listErrorList );
	}


	protected function getCatalogItem( $code = 'cafe' )
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		if( ( $item = $catalogManager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No catalog item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
