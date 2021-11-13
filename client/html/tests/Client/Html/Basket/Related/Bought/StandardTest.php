<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Basket\Related\Bought;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Basket\Related\Bought\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );

		$basket = $controller->get();
		$basket->addProduct( $this->getOrderProductItem( 'CNE' ) );

		$this->view->relatedBasket = $basket;
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="basket-related-bought', $output );
		$this->assertStringContainsString( 'Cafe Noire Cappuccino', $output );
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


	/**
	 * @param string $code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	protected function getOrderProductItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$manager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderItem = $manager->create()->copyFrom( $item )->setStockType( 'default' );

		return $orderItem;
	}
}
