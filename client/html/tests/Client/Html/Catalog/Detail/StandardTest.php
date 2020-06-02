<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Catalog\Detail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_prodid' => $this->getProductItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->getHeader();

		$this->assertStringContainsString( '<title>Cafe Noire Expresso</title>', $output );
		$this->assertStringContainsString( '<script type="text/javascript" defer="defer" src="http://baseurl/catalog/stock/?s_prodcode', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );
	}


	public function testGetHeaderException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getHeader();
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_prodid' => $this->getProductItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos catalog-detail"', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-basic">', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-image', $output );

		$this->assertStringContainsString( '<div class="catalog-social">', $output );
		$this->assertRegExp( '/.*facebook.*/', $output );

		$this->assertStringContainsString( '<div class="catalog-actions">', $output );
		$this->assertStringContainsString( 'actions-button-pin', $output );
		$this->assertStringContainsString( 'actions-button-watch', $output );
		$this->assertStringContainsString( 'actions-button-favorite', $output );

		$this->assertStringContainsString( '<div class="catalog-detail-additional">', $output );
		$this->assertStringContainsString( '<h2 class="header description">', $output );

		$this->assertStringContainsString( '<h2 class="header attributes">', $output );
		$this->assertStringContainsString( '<td class="name">size</td>', $output );
		$this->assertStringContainsString( '<span class="attr-name">XS</span>', $output );

		$this->assertStringContainsString( '<h2 class="header properties">', $output );
		$this->assertStringContainsString( '<td class="name">package-height</td>', $output );
		$this->assertStringContainsString( '<td class="value">10.0</td>', $output );

		$this->assertStringContainsString( '<h2 class="header downloads">', $output );
		$this->assertStringContainsString( '<span class="media-name">path/to/folder/example5.jpg</span>', $output );

		$this->assertStringContainsString( '<section class="catalog-detail-suggest">', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertStringContainsString( '<section class="catalog-detail-bought">', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertStringContainsString( '<div class="catalog-detail-service', $output );
		$this->assertStringContainsString( '<div class="catalog-detail-supplier', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 7, count( $tags ) );
	}


	public function testGetBodyByName()
	{
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_name' => 'Cafe-Noire-Expresso' ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testGetBodyDefaultId()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/detail/prodid-default', $this->getProductItem()->getId() );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, [] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testGetBodyDefaultCode()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/detail/prodcode-default', 'CNE' );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, [] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testGetBodyCsrf()
	{
		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$view->detailProductItem = $this->getProductItem();

		$output = $this->object->getBody( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new" />', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value" />', $output );
	}


	public function testGetBodyAttributes()
	{
		$product = $this->getProductItem( 'U:TESTP', array( 'attribute' ) );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_prodid' => $product->getId() ) );
		$view->addHelper( 'param', $helper );

		$configAttr = $product->getRefItems( 'attribute', null, 'config' );

		$this->assertGreaterThan( 0, count( $configAttr ) );

		$output = $this->object->getBody();
		$this->assertStringContainsString( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option".*value="' . $id . '">#smU', $output );
		}
	}


	public function testGetBodySelection()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_prodid' => $this->getProductItem( 'U:TEST' )->getId() ) );
		$view->addHelper( 'param', $helper );

		$variantAttr1 = $this->getProductItem( 'U:TESTSUB02', array( 'attribute' ) )->getRefItems( 'attribute', null, 'variant' );
		$variantAttr2 = $this->getProductItem( 'U:TESTSUB04', array( 'attribute' ) )->getRefItems( 'attribute', null, 'variant' );

		$this->assertGreaterThan( 0, count( $variantAttr1 ) );
		$this->assertGreaterThan( 0, count( $variantAttr2 ) );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody( 1, $tags, $expire );

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


	public function testGetBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$view = $this->object->getView();
		$view->addHelper( 'param', new \Aimeos\MW\View\Helper\Param\Standard( $view, ['d_prodid' => -1] ) );
		$mock->setView( $view );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getBody();
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


	public function testProcess()
	{
		$this->object->process();

		$this->assertEmpty( $this->object->getView()->get( 'detailErrorList' ) );
	}


	public function testProcessClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->process();
	}


	public function testProcessControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->process();
	}


	public function testProcessMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->process();
	}


	public function testProcessException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Detail\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'getClientParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'getClientParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->process();
	}


	protected function getProductItem( $code = 'CNE', $domains = [] )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->searchItems( $search, $domains )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
