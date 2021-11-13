<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Confirm\Order;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->view = \TestHelperHtml::view();
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();

		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Order\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );

		$this->view = \TestHelperHtml::view();
		$this->view->confirmOrderItem = $this->getOrderItem( $customer->getId() );
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="checkout-confirm-detail common-summary">', $output );
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


	protected function getOrderItem( $customerid )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );

		$search = $manager->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'order.base.customerid', $customerid ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No order item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
