<?php

namespace Aimeos\Client\Html\Catalog\Stock;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
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

		$this->object = new \Aimeos\Client\Html\Catalog\Stock\Standard( $this->context );
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
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$prodid = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'st_pid' => $prodid ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$this->assertMatchesRegularExpression( '/"' . $prodid . '".*stock-high/', $output );
	}


	public function testBodyEncodesStockType()
	{
		$item = ( new \Aimeos\MShop\Stock\Item\Standard( 'stock.' ) )->setProductId( '1' )
			->setStockLevel( 5 )->setType( "<img/src='x'/onerror='window.stockXss=1'>" );
		$this->view->stockItemsByProducts = ['1' => [$item]];

		$output = $this->view->render( 'catalog/stock/body' );
		$this->assertSame( 1, preg_match( '/var aimeosStockHtml = (.*);/', $output, $matches ) );
		$html = json_decode( $matches[1], true, 512, JSON_THROW_ON_ERROR )[1];

		$this->assertStringNotContainsString( '<img', $html );
		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( '<span class="stocktext">Stock: stocktype:, stock-low</span>', $html );
	}


	public function testBodyHexEncodesInlineJson()
	{
		$prodId = '</script><script>window.stockXss=1</script>';
		$item = ( new \Aimeos\MShop\Stock\Item\Standard( 'stock.' ) )->setProductId( $prodId )
			->setStockLevel( 5 )->setType( 'default' );
		$this->view->stockItemsByProducts = [$prodId => [$item]];

		$output = $this->view->render( 'catalog/stock/body' );
		$this->assertSame( 1, preg_match( '/var aimeosStockHtml = (.*);/', $output, $matches ) );
		$this->assertStringNotContainsString( '</script>', $matches[1] );
		$this->assertStringContainsString( '\\u003C', $matches[1] );
		$this->assertArrayHasKey( $prodId, json_decode( $matches[1], true, 512, JSON_THROW_ON_ERROR ) );
	}


	public function testBodyPreservesStockInformation()
	{
		$items = [];
		foreach( [null, 0, 2, 10] as $level ) {
			$items[] = ( new \Aimeos\MShop\Stock\Item\Standard( 'stock.' ) )->setProductId( '1' )
				->setStockLevel( $level )->setType( 'A&B' )->setDateBack( '2030-01-02 00:00:00' );
		}
		$this->view->stockItemsByProducts = ['1' => $items];

		$output = $this->view->render( 'catalog/stock/body' );
		$this->assertSame( 1, preg_match( '/var aimeosStockHtml = (.*);/', $output, $matches ) );
		$html = json_decode( $matches[1], true, 512, JSON_THROW_ON_ERROR )[1];

		foreach( ['unlimited', 'out', 'low', 'high'] as $level ) {
			$this->assertStringContainsString( 'Stock: stocktype:A&amp;B, stock-' . $level, $html );
		}
		$this->assertStringContainsString( 'back on 2030-01-02', $html );
		$this->assertStringNotContainsString( '&amp;amp;', $html );
	}
}
