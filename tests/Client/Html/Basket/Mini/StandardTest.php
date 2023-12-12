<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Basket\Mini;


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

		$this->object = new \Aimeos\Client\Html\Basket\Mini\Standard( $this->context );
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
		$output = $this->object->body();
		$miniBasket = $this->view->miniBasket;

		$this->assertTrue( $miniBasket instanceof \Aimeos\MShop\Order\Item\Iface );
		$this->assertStringContainsString( '<div class="section aimeos basket-mini', $output );
		$this->assertStringContainsString( '<div class="basket-mini-main', $output );
		$this->assertStringContainsString( '<div class="basket-mini-product', $output );
	}


	public function testBodyAddedOneProduct()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );

		$productItem = $this->getProductItem( 'CNE' );


		$controller->addProduct( $productItem, 9 );
		$this->view->miniBasket = $controller->get();

		$output = $this->object->body();

		$controller->clear();

		$this->assertStringContainsString( '<div class="basket-mini-product', $output );
		$this->assertMatchesRegularExpression( '#9#smU', $output );
		$this->assertMatchesRegularExpression( '#171.00#smU', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'miniErrorList' ) );
	}


	/**
	 * @param string $code
	 */
	protected function getProductItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search, ['price'] )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
