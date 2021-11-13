<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Detail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $this->getProductItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Cafe Noire Expresso | Aimeos</title>', $output );
		$this->assertStringContainsString( '<script defer src="http://baseurl/catalog/stock/?st_pid', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );
	}


	public function testHeaderException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->header();
	}


	public function testBody()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $this->getProductItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos catalog-detail"', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-basic', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-image', $output );

		$this->assertStringContainsString( '<div class="catalog-social">', $output );
		$this->assertRegExp( '/.*facebook.*/', $output );

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

		$this->assertStringContainsString( '<section class="catalog-detail-suggest', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertStringContainsString( '<section class="catalog-detail-bought', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertStringContainsString( '<div class="catalog-detail-service', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-supplier', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );
	}


	public function testBodyByName()
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_name' => 'Cafe-Noire-Expresso' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyDefaultId()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/detail/prodid-default', $this->getProductItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyDefaultCode()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/detail/prodcode-default', 'CNE' );

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context );
		$this->object->setView( \TestHelperHtml::view() );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testBodyCsrf()
	{
		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$this->view->detailProductItem = $this->getProductItem();

		$output = $this->object->body( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new"', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value"', $output );
	}


	public function testBodyAttributes()
	{
		$product = $this->getProductItem( 'U:TESTP', array( 'attribute' ) );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $product->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$configAttr = $product->getRefItems( 'attribute', null, 'config' );

		$this->assertGreaterThan( 0, count( $configAttr ) );

		$output = $this->object->body();
		$this->assertStringContainsString( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option".*value="' . $id . '">#smU', $output );
		}
	}


	public function testBodySelection()
	{
		$prodId = $this->getProductItem( 'U:TEST' )->getId();

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_prodid' => $prodId ) );
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
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		foreach( $variantAttr2 as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		$this->assertEquals( null, $expire );
		$this->assertEquals( 6, count( $tags ) );
	}


	public function testBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->body();
	}


	public function testBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->body();
	}


	public function testBodyMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->body();
	}


	public function testBodyException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$this->view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['d_prodid' => -1] ) );
		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->body();
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'service', 'Standard' );
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

		$this->assertEmpty( $this->view->get( 'detailErrorList' ) );
	}


	public function testInitClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::view() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->init();
	}


	public function testInitControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::view() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->init();
	}


	public function testInitMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::view() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->init();
	}


	public function testInitException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::view() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->init();
	}


	protected function getProductItem( $code = 'CNE', $domains = [] )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search, $domains )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
