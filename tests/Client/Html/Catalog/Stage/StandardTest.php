<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Stage;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Standard( $this->context );
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

		$this->assertNotNull( $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos catalog-stage', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertMatchesRegularExpression( '#Back#smU', $output );

		$this->assertEquals( null, $expire );
		$this->assertEquals( 0, count( $tags ) );
	}


	public function testBodyCatId()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos catalog-stage home categories coffee"', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-image', $output );
		$this->assertStringContainsString( '/path/to/folder/cafe/stage.jpg', $output );

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertMatchesRegularExpression( '#Root.*.Categories.*.Kaffee.*#smU', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	protected function getCatalogItem()
	{
		$catalogManager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		if( ( $item = $catalogManager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No catalog item with code "cafe" found' );
		}

		return $item;
	}
}
