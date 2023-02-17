<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Kaffee | Aimeos</title>', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 8, count( $tags ) );
	}


	public function testHeaderSearch()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => '<b>Search result</b>' ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertMatchesRegularExpression( '#<title>[^>]*Search result[^<]* | Aimeos</title>#', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testBody()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list home categories coffee"', $output );

		$this->assertStringContainsString( '<div class="catalog-list-head">', $output );
		$this->assertMatchesRegularExpression( '#<h1>Kaffee</h1>#', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 8, count( $tags ) );
	}


	public function testBodyPagination()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['l_size' => 2] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
		$this->assertStringContainsString( '<nav class="pagination">', $output );
	}


	public function testBodyDefaultAttribute()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$attrId = $manager->find( 'xs', [], 'product', 'size' )->getId();

		$context = clone $this->context;
		$context->config()->set( 'client/html/catalog/lists/attrid-default', $attrId );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
		$this->assertMatchesRegularExpression( '#.*Cafe Noire Cappuccino.*#smu', $output );
		$this->assertMatchesRegularExpression( '#.*Cafe Noire Expresso.*#smu', $output );
	}


	public function testBodyNoDefaultCat()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
		$this->assertDoesNotMatchRegularExpression( '#.*U:TESTPSUB01.*#smu', $output );
		$this->assertDoesNotMatchRegularExpression( '#.*U:TESTSUB03.*#smu', $output );
		$this->assertDoesNotMatchRegularExpression( '#.*U:TESTSUB04.*#smu', $output );
		$this->assertDoesNotMatchRegularExpression( '#.*U:TESTSUB05.*#smu', $output );
	}


	public function testBodyDefaultCat()
	{
		$context = clone $this->context;
		$context->config()->set( 'client/html/catalog/lists/catid-default', $this->getCatalogItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyMultipleDefaultCat()
	{
		$context = clone $this->context;
		$catid = $this->getCatalogItem()->getId();
		$context->config()->set( 'client/html/catalog/lists/catid-default', array( $catid, $catid ) );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyMultipleDefaultCatString()
	{
		$context = clone $this->context;
		$catid = $this->getCatalogItem()->getId();
		$context->config()->set( 'client/html/catalog/lists/catid-default', $catid . ',' . $catid );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list home categories coffee"', $output );
	}


	public function testBodyCategoryLevels()
	{
		$context = clone $this->context;
		$context->config()->set( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem( 'root' )->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#.*Cafe Noire Cappuccino.*#smu', $output );
		$this->assertMatchesRegularExpression( '#.*Cafe Noire Expresso.*#smu', $output );
		$this->assertMatchesRegularExpression( '#.*Unittest: Bundle.*#smu', $output );
		$this->assertMatchesRegularExpression( '#.*Unittest: Test priced Selection.*#smu', $output );
	}


	public function testBodySearchText()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => '<b>Search result</b>' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
		$this->assertStringContainsString( '&lt;b&gt;Search result&lt;/b&gt;', $output );
	}


	public function testBodySearchAttribute()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_attrid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
	}


	public function testBodySearchSupplier()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_supid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
	}


	public function testBodySearchPrice()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_price' => 20 ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
	}


	public function testBodySearchRadius()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_point' => [52.5, 10], 'f_dist' => 115 ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section aimeos catalog-list', $output );
	}


	public function testInit()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'l_type' => 'list' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEmpty( $this->view->get( 'errors' ) );
	}


	protected function getCatalogItem( $code = 'cafe' )
	{
		$catalogManager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		if( ( $item = $catalogManager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No catalog item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
