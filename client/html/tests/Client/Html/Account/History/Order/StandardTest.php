<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Account\History\Order;


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

		$this->object = new \Aimeos\Client\Html\Account\History\Order\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$customer = $this->getCustomerItem( 'test@example.com' );
		$this->context->setUserId( $customer->getId() );

		$this->view = \TestHelperHtml::view();
		$param = array(
			'his_action' => 'order',
			'his_id' => $this->getOrderItem( $customer->getId() )->getId()
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="account-history-order common-summary', $output );

		$this->assertStringContainsString( 'Our Unittest', $output );
		$this->assertStringContainsString( 'Example company', $output );

		$this->assertStringContainsString( '<h4>unitdeliverycode</h4>', $output );
		$this->assertStringContainsString( '<h4>unitpaymentcode</h4>', $output );

		$this->assertStringContainsString( '>5678<', $output );
		$this->assertStringContainsString( 'This is a comment', $output );

		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
		$this->assertStringContainsString( 'Cafe Noire Cappuccino', $output );
		$this->assertStringContainsString( 'Unittest: Monetary rebate', $output );
		$this->assertStringContainsString( '<td class="price">55.00 EUR</td>', $output );
		$this->assertStringContainsString( '<td class="quantity">14 articles</td>', $output );
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
	 */
	protected function getCustomerItem( $code )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getOrderItem( $customerid )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$search = $manager->filter( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'order.base.customerid', $customerid )
		);
		$search->setConditions( $search->and( $expr ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No order item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
