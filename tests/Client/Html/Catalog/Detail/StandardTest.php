<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Detail;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context );
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
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['d_prodid' => $this->getProductItem()->getId()] );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Cafe Noire Expresso Test supplier | Aimeos</title>', $output );
		$this->assertStringContainsString( '<script defer src="http://baseurl/Catalog/stock/?st_pid', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );
	}


	public function testBody()
	{
		$params = ['d_prodid' => $this->getProductItem()->getId(), 'd_pos' => 1];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$this->view->navigationPrev = '#';
		$this->view->navigationNext = '#';

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<!-- catalog.detail.navigator -->', $output );
		$this->assertStringContainsString( '<a class="prev"', $output );
		$this->assertStringContainsString( '<a class="next"', $output );

		$this->assertStringContainsString( '<div class="aimeos catalog-detail', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-basic', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-image', $output );

		$this->assertStringContainsString( '<div class="catalog-social">', $output );
		$this->assertStringContainsString( 'facebook', $output );

		$this->assertStringContainsString( '<div class="catalog-actions', $output );
		$this->assertStringContainsString( 'actions-button-pin', $output );
		$this->assertStringContainsString( 'actions-button-watch', $output );
		$this->assertStringContainsString( 'actions-button-favorite', $output );

		$this->assertStringContainsString( 'catalog-detail-additional', $output );

		$this->assertStringContainsString( '<td class="name">size</td>', $output );
		$this->assertStringContainsString( '<span class="attr-name">XS</span>', $output );
		$this->assertStringContainsString( '<td class="name">package-height</td>', $output );
		$this->assertStringContainsString( '<td class="value">10.0</td>', $output );

		$this->assertStringContainsString( '<span class="media-name">Example image</span>', $output );

		$this->assertStringContainsString( '<div class="section catalog-detail-suggest', $output );
		$this->assertStringContainsString( 'Cappuccino', $output );

		$this->assertStringContainsString( '<div class="section catalog-detail-bought', $output );
		$this->assertStringContainsString( 'Cappuccino', $output );

		$this->assertStringContainsString( '<div class="catalog-detail-supplier', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );

		$result = $this->context->session()->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $result );
		$this->assertEquals( 1, count( $result ) );
	}


	public function testBodyByName()
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'd_name' => 'cafe-noire-expresso' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyDefaultId()
	{
		$context = clone $this->context;
		$context->config()->set( 'client/html/catalog/detail/prodid-default', $this->getProductItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyDefaultCode()
	{
		$context = clone $this->context;
		$context->config()->set( 'client/html/catalog/detail/prodcode-default', 'CNE' );

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyCsrf()
	{
		$item = $this->getProductItem();
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['d_prodid' => $item->getId()] );
		$this->view->addHelper( 'param', $helper );
		$this->view->detailProductItem = $item;

		$output = $this->object->body( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new"', $output );

		$output = $this->object->modify( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value"', $output );
	}


	public function testBodyAttributes()
	{
		$product = $this->getProductItem( 'U:TESTP', array( 'attribute' ) );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $product->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$configAttr = $product->getRefItems( 'attribute', null, 'config' );

		$this->assertGreaterThan( 0, count( $configAttr ) );

		$output = $this->object->body();
		$this->assertStringContainsString( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertMatchesRegularExpression( '#<option class="select-option".*value="' . $id . '">#smU', $output );
		}
	}


	public function testBodySelection()
	{
		$prodId = $this->getProductItem( 'U:TEST' )->getId();

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $prodId ) );
		$this->view->addHelper( 'param', $helper );

		$variantAttr1 = $this->getProductItem( 'U:TESTSUB02', array( 'attribute' ) )->getRefItems( 'attribute', null, 'variant' );
		$variantAttr2 = $this->getProductItem( 'U:TESTSUB04', array( 'attribute' ) )->getRefItems( 'attribute', null, 'variant' );

		$this->assertGreaterThan( 0, count( $variantAttr1 ) );
		$this->assertGreaterThan( 0, count( $variantAttr2 ) );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body( 1, $tags, $expire );

		$this->assertStringContainsString( '<div class="catalog-detail-basket-selection', $output );

		foreach( $variantAttr1 as $id => $item ) {
			$this->assertMatchesRegularExpression( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		foreach( $variantAttr2 as $id => $item ) {
			$this->assertMatchesRegularExpression( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 8, count( $tags ) );
	}


	public function testModify()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'd_pos' => 1 ) );
		$this->view->addHelper( 'param', $helper );

		$content = '<!-- catalog.detail.navigator -->test<!-- catalog.detail.navigator -->';
		$output = $this->object->modify( $content, 1 );

		$this->assertStringContainsString( '<div class="catalog-detail-navigator">', $output );
	}


	public function testInit()
	{
		$prodid = $this->getProductItem()->getId();

		$session = $this->context->session();
		$session->set( 'aimeos/catalog/session/seen/list', array( $prodid => 'test' ) );
		$session->set( 'aimeos/catalog/session/seen/cache', array( $prodid => 'test' ) );

		$param = array( 'd_prodid' => $prodid );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$str = $session->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $str );
	}


	protected function getProductItem( $code = 'CNE', $domains = [] )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search, $domains )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
