<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Catalog\Detail;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
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
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertContains( '<title>Cafe Noire Expresso</title>', $output );
		$this->assertContains( '<script type="text/javascript" defer="defer" src="http://baseurl/catalog/stock/?s_prodcode', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetHeaderException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'setViewParams' )
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
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-detail"', $output );
		$this->assertContains( '<div class="catalog-detail-basic">', $output );
		$this->assertContains( '<div class="catalog-detail-image', $output );

		$this->assertContains( '<div class="catalog-social">', $output );
		$this->assertRegExp( '/.*facebook.*/', $output );

		$this->assertContains( '<div class="catalog-actions">', $output );
		$this->assertContains( 'actions-button-pin', $output );
		$this->assertContains( 'actions-button-watch', $output );
		$this->assertContains( 'actions-button-favorite', $output );

		$this->assertContains( '<div class="catalog-detail-additional">', $output );
		$this->assertContains( '<h2 class="header description">', $output );

		$this->assertContains( '<h2 class="header attributes">', $output );
		$this->assertContains( '<td class="name">size</td>', $output );
		$this->assertContains( '<span class="attr-name">XS</span>', $output );

		$this->assertContains( '<h2 class="header properties">', $output );
		$this->assertContains( '<td class="name">package-height</td>', $output );
		$this->assertContains( '<td class="value">10.0</td>', $output );

		$this->assertContains( '<h2 class="header downloads">', $output );
		$this->assertContains( '<span class="media-name">example image 1</span>', $output );

		$this->assertContains( '<section class="catalog-detail-suggest">', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertContains( '<section class="catalog-detail-bought">', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );

		$this->assertContains( '<div class="catalog-detail-service', $output );

		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
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

		$output = $this->object->getBody();

		$this->assertContains( '<span class="value" itemprop="sku">CNE</span>', $output );
	}


	public function testGetBodyCsrf()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem();

		$output = $this->object->getBody( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertContains( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new" />', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertContains( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value" />', $output );
	}


	public function testGetBodyAttributes()
	{
		$product = $this->getProductItem( 'U:TESTP', array( 'attribute' ) );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'd_prodid' => $product->getId() ) );
		$view->addHelper( 'param', $helper );

		$configAttr = $product->getRefItems( 'attribute', null, 'config' );
		$hiddenAttr = $product->getRefItems( 'attribute', null, 'hidden' );

		$this->assertGreaterThan( 0, count( $configAttr ) );
		$this->assertGreaterThan( 0, count( $hiddenAttr ) );

		$output = $this->object->getBody();
		$this->assertContains( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option".*value="' . $id . '">#smU', $output );
		}

		foreach( $hiddenAttr as $id => $item ) {
			$this->assertRegexp( '#<input type="hidden".*value="' . $id . '".*/>#smU', $output );
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
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertContains( '<div class="catalog-detail-basket-selection', $output );

		foreach( $variantAttr1 as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		foreach( $variantAttr2 as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		$this->assertEquals( null, $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyMShopException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'setViewParams' )
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
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessClientHtmlException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
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
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
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
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
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
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Detail\Standard' )
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
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search, $domains );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
