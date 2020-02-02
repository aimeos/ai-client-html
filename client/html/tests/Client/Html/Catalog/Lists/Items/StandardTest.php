<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Catalog\Lists\Items;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperHtml::getContext();

		$config = $context->getConfig();
		$config->set( 'client/html/catalog/lists/basket-add', true );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Items\Standard( $context, $paths );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		if( ( $catItem = $catalogManager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$domains = array( 'media', 'price', 'text', 'attribute', 'product' );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'U:TEST', 'U:BUNDLE' ) ) );
		$total = 0;


		$view = \TestHelperHtml::getView( 'unittest', $config );

		$view->listProductItems = $productManager->searchItems( $search, $domains, $total );
		$view->listProductTotal = $total;
		$view->listPageSize = 100;
		$view->listPageCurr = 1;
		$view->listParams = [];
		$view->listCatPath = array( $catalogManager->createItem(), $catItem );

		$this->object->setView( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->addData( $this->object->getView() ) );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="catalog-list-items"', $output );

		$this->assertStringContainsString( '<div class="price-item', $output );
		$this->assertStringContainsString( '<span class="quantity"', $output );
		$this->assertStringContainsString( '<span class="value">', $output );
		$this->assertStringContainsString( '<span class="costs">', $output );
		$this->assertStringContainsString( '<span class="taxrate">', $output );
	}


	public function testGetBodyCsrf()
	{
		$output = $this->object->getBody( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new" />', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value" />', $output );
	}


	public function testGetBodyTemplate()
	{
		$view = $this->object->getView();
		$this->object->setView( $this->object->addData( $view ) );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'l_type' => 'list' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="catalog-list-items">', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
