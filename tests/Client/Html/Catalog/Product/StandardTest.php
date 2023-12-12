<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */


namespace Aimeos\Client\Html\Catalog\Product;


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
		$this->context->config()->set( 'client/html/catalog/product/product-codes', ['CNE', 'ABCD', 'CNC'] );

		$this->object = new \Aimeos\Client\Html\Catalog\Product\Standard( $this->context );
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

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$filter = $manager->filter()->add( ['product.code' => ['CNE', 'ABCD', 'CNC']] );
		$map = $manager->search( $filter )->col( 'product.id', 'product.code' );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<script', $output );
		$prodCodeParam = '/st_pid%5B[0-9]%5D=';
		$this->assertMatchesRegularExpression( $prodCodeParam . $map['CNE'] . '/', $output );
		$this->assertMatchesRegularExpression( $prodCodeParam . $map['ABCD'] . '/', $output );
		$this->assertMatchesRegularExpression( $prodCodeParam . $map['CNC'] . '/', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}


	public function testBody()
	{

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$productNameCNE = '<h2 class="name" itemprop="name">Cafe Noire Expresso</h2>';
		$productNameABCD = '<h2 class="name" itemprop="name">Unterproduct 1</h2>';
		$productNameCNC = '<h2 class="name" itemprop="name">Cafe Noire Cappuccino</h2>';
		$this->assertStringContainsString( $productNameCNE, $output );
		$this->assertStringContainsString( $productNameABCD, $output );
		$this->assertStringContainsString( $productNameCNC, $output );

		$outputPosCNE = strpos( $output, $productNameCNE );
		$outputPosABCD = strpos( $output, $productNameABCD );
		$outputPosCNC = strpos( $output, $productNameCNC );
		$this->assertGreaterThan( $outputPosCNE, $outputPosABCD );
		$this->assertGreaterThan( $outputPosABCD, $outputPosCNC );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
	}
}
