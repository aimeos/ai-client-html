<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Client\Html\Catalog\Stage;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;
	private $tags = [];
	private $expire;


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
		$output = $this->client( ['f_catid' => $this->getCatalogItem()->getId()] )->header();

		$this->assertNotNull( $output );
		$this->assertEquals( '2098-01-01 00:00:00', $this->expire );
		$this->assertCount( 3, $this->tags );
	}


	public function testBody()
	{
		$output = $this->client()->body();

		$this->assertStringContainsString( '<div class="section aimeos catalog-stage', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertMatchesRegularExpression( '#Back#smU', $output );

		$this->assertNull( $this->expire );
		$this->assertEmpty( $this->tags );
	}


	public function testBodyCatId()
	{
		$output = $this->client( ['f_catid' => $this->getCatalogItem()->getId()] )->body();

		$this->assertStringContainsString( '<div class="section aimeos catalog-stage home categories coffee"', $output );
		$this->assertStringContainsString( '<div class="catalog-stage-image', $output );
		$this->assertStringContainsString( '/path/to/folder/cafe/stage.jpg', $output );

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertMatchesRegularExpression( '#Root.*.Categories.*.Kaffee.*#smU', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $this->expire );
		$this->assertCount( 3, $this->tags );
	}


	public function testBodyProductId()
	{
		$prodId = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' )->getId();
		$output = $this->client( ['d_prodid' => $prodId] )->body();

		$this->assertMatchesRegularExpression( '#Root.*.Categories.*.Kaffee#smU', $output );
		$this->assertContains( 'product-' . $prodId, $this->tags );
	}


	public function testBodyProductName()
	{
		$output = $this->client( ['d_name' => 'cafe-noire-expresso'] )->body();

		$this->assertMatchesRegularExpression( '#Root.*.Categories.*.Kaffee#smU', $output );
	}


	public function testBodyProductListtype()
	{
		$this->context->config()->set( 'client/html/catalog/stage/listtype', 'internet' );

		$prodId = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' )->getId();
		$output = $this->client( ['d_prodid' => $prodId] )->body();

		$this->assertMatchesRegularExpression( '#Root.*.Groups.*.Nur online#smU', $output );
		$this->assertStringNotContainsString( 'Kaffee', $output );
	}


	public function testBodyProductPathUnavailable()
	{
		$output = $this->unavailablePath( fn() => $this->client( ['d_name' => 'cafe-noire-expresso'] )->body() );

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertStringNotContainsString( 'Kaffee', $output );
	}


	public function testBodyCatIdPathUnavailable()
	{
		$this->expectException( \Aimeos\Controller\Frontend\Exception::class );
		$this->unavailablePath( fn() => $this->client( ['f_catid' => $this->getCatalogItem()->getId()] ) );
	}


	public function testBodyProductUnknown()
	{
		$output = $this->client( ['d_name' => 'unknown'] )->body();

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertEmpty( $this->tags );
	}


	public function testBodyProductArrayParams()
	{
		$output = $this->client( ['d_prodid' => ['1'], 'd_name' => ['x']] )->body();

		$this->assertStringContainsString( '<div class="catalog-stage-breadcrumb', $output );
		$this->assertEmpty( $this->tags );
	}


	public function testPrefixes()
	{
		$method = new \ReflectionMethod( $this->object, 'prefixes' );

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['d_name' => 'test'] ) );
		$this->assertEquals( ['d_name'], $method->invoke( $this->object, $this->view ) );

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['d_prodid' => '1', 'd_name' => 'test'] ) );
		$this->assertEquals( ['d_prodid'], $method->invoke( $this->object, $this->view ) );

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['f_catid' => '1', 'd_prodid' => '1'] ) );
		$this->assertEquals( ['f_catid'], $method->invoke( $this->object, $this->view ) );
	}


	public function testBodyProductCached()
	{
		$this->expectCacheSet( $this->once() );
		$this->client( ['d_name' => 'cafe-noire-expresso'] )->body();
	}


	public function testBodyProductUnknownUncached()
	{
		$this->expectCacheSet( $this->never() );
		$this->client( ['d_name' => 'unknown'] )->body();
	}


	public function testBodyProductIdVariantUncached()
	{
		$prodId = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' )->getId();

		$this->expectCacheSet( $this->never() );
		$this->client( ['d_prodid' => $prodId . 'x'] )->body();
	}


	public function testBodyProductNameVariantUncached()
	{
		$this->expectCacheSet( $this->never() );
		$this->client( ['d_name' => 'Cafe-Noire-Expresso'] )->body();
	}


	public function testBodyCatIdCached()
	{
		$this->expectCacheSet( $this->once() );
		$this->client( ['f_catid' => $this->getCatalogItem()->getId()] )->body();
	}


	public function testBodyCatIdVariantUncached()
	{
		$this->expectCacheSet( $this->never() );
		$this->client( ['f_catid' => '0' . $this->getCatalogItem()->getId()] )->body();
	}


	public function testBodyCacheKeyExactNames()
	{
		$keys = [];
		$cache = $this->getMockBuilder( \Aimeos\Base\Cache\None::class )->onlyMethods( ['set'] )->getMock();
		$cache->expects( $this->exactly( 2 ) )->method( 'set' )->willReturnCallback( function( $key ) use ( &$keys ) {
			return (bool) $keys[] = $key;
		} );
		$this->context->setCache( $cache );

		$catId = $this->getCatalogItem()->getId();
		$this->client( ['f_catid' => $catId] )->body();

		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Standard( $this->context );
		$this->client( ['f_catid' => $catId, 'f_catidX' => 'x', 'd_name' => 'x'] )->body();

		$this->assertEquals( $keys[0], $keys[1] );
	}


	public function testBodyEscapesCategoryName()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create()
			->setLabel( '<img src=x onerror=alert(1)></script><script>alert(2)</script>' );
		$this->view->stageCatPath = map( [$item] );

		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '~<a [^>]+>\s*alert\(2\)</a>~', $output );
		$this->assertStringContainsString( '\\u003Cimg src=x onerror=alert(1)\\u003E', $output );
		$this->assertStringNotContainsString( '<img src=x onerror=alert(1)>', $output );
		$this->assertStringNotContainsString( '</script><script>alert(2)</script>', $output );
	}


	protected function client( array $params = [] ) : \Aimeos\Client\Html\Iface
	{
		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params ) );
		return $this->object->setView( $this->object->data( $this->view, $this->tags, $this->expire ) );
	}


	protected function expectCacheSet( $rule ) : void
	{
		$cache = $this->getMockBuilder( \Aimeos\Base\Cache\None::class )->onlyMethods( ['set'] )->getMock();
		$cache->expects( $rule )->method( 'set' );
		$this->context->setCache( $cache );
	}


	protected function unavailablePath( \Closure $fcn )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$item = $manager->find( 'categories' );
		$status = $item->getStatus();

		$manager->save( $item->setStatus( 0 ) );

		try {
			return $fcn();
		} finally {
			$manager->save( $item->setStatus( $status ) );
		}
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
