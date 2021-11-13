<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Lists\Items;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$config = $this->context->getConfig();
		$config->set( 'client/html/catalog/lists/basket-add', true );

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Items\Standard( $this->context );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		if( ( $catItem = $catalogManager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$domains = array( 'media', 'price', 'text', 'attribute', 'product' );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $productManager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'U:TEST', 'U:BUNDLE' ) ) );
		$total = 0;


		$this->view = \TestHelperHtml::view( 'unittest', $config );

		$this->view->listProductItems = $productManager->search( $search, $domains, $total );
		$this->view->listProductTotal = $total;
		$this->view->listPageSize = 100;
		$this->view->listPageCurr = 1;
		$this->view->listParams = [];
		$this->view->listCatPath = array( $catalogManager->create(), $catItem );

		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="catalog-list-items"', $output );

		$this->assertStringContainsString( '<div class="price-item', $output );
		$this->assertStringContainsString( '<span class="quantity"', $output );
		$this->assertStringContainsString( '<span class="value">', $output );
		$this->assertStringContainsString( '<span class="costs">', $output );
		$this->assertStringContainsString( '<span class="taxrate">', $output );
	}


	public function testBodyCsrf()
	{
		$output = $this->object->body( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new"', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value"', $output );
	}


	public function testBodyTemplate()
	{
		$this->object->setView( $this->object->data( $this->view ) );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'l_type' => 'list' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="catalog-list-items">', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
