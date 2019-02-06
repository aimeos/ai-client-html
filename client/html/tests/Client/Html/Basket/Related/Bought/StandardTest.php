<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Basket\Related\Bought;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Basket\Related\Bought\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );

		$basket = $controller->get();
		$basket->addProduct( $this->getOrderProductItem( 'CNE' ) );

		$view = $this->object->getView();
		$view->relatedBasket = $basket;
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();

		$this->assertContains( '<section class="basket-related-bought', $output );
		$this->assertContains( 'Cafe Noire Cappuccino', $output );
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


	/**
	 * @param string $code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	protected function getOrderProductItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$manager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderItem = $manager->createItem();
		$orderItem->copyFrom( $item );

		return $orderItem;
	}
}
