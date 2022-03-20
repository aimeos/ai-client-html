<?php

namespace Aimeos\Client\Html\Checkout\Confirm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setEditor( 'test@example.com' );

		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->view );
	}


	public function testHeader()
	{
		$order = $this->getOrder( '2011-09-17 16:14:32' );
		$this->context->session()->set( 'aimeos/orderid', $order->getId() );

		$this->view->confirmOrderItem = $order;
		$this->view->summaryBasket = $order->getBaseItem();

		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Confirmation | Aimeos</title>', $output );
	}


	public function testBody()
	{
		$order = $this->getOrder( '2011-09-17 16:14:32' );
		$this->context->session()->set( 'aimeos/orderid', $order->getId() );

		$this->view->confirmOrderItem = $order;
		$this->view->summaryBasket = $order->getBaseItem();

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="aimeos checkout-confirm"', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-intro">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-retry">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-basic">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-detail', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-detail common-summary">', $output );
		$this->assertRegExp( '#<span class="value">.*' . $order->getId() . '.*</span>#smU', $output );

		$this->assertStringContainsString( 'mr Our Unittest', $output );
		$this->assertStringContainsString( 'Example company', $output );

		$this->assertStringContainsString( 'unitdeliverycode', $output );
		$this->assertStringContainsString( 'paypal', $output );

		$this->assertStringContainsString( 'This is a comment', $output );
	}


	public function testInit()
	{
		$orderId = $this->getOrder( '2011-09-17 16:14:32' )->getId();
		$this->context->session()->set( 'aimeos/orderid', $orderId );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['code' => 'paypalexpress', 'orderid' => $orderId] );
		$this->view->addHelper( 'param', $helper );

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view->addHelper( 'request', $helper );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->init();
	}


	public function testInitNoCode()
	{
		$this->expectException( \Aimeos\Client\Html\Exception::class );
		$this->object->init();
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$domains = ['order/base', 'order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$search = $manager->filter()->add( 'order.datepayment', '==', $date );

		return $manager->search( $search, $domains )->first( new \RuntimeException( 'No order found' ) );
	}
}
