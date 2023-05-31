<?php

namespace Aimeos\Client\Html\Checkout\Confirm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setEditor( 'test@example.com' );

		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->context, $this->object, $this->view );
	}


	public function testHeader()
	{
		$order = $this->getOrder( '2011-09-17 16:14:32' );
		$this->context->session()->set( 'aimeos/orderid', $order->getId() );

		$this->view->confirmOrderItem = $order;
		$this->view->summaryBasket = $order;

		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Confirmation | Aimeos</title>', $output );
	}


	public function testBody()
	{
		$order = $this->getOrder( '2011-09-17 16:14:32' );
		$this->context->session()->set( 'aimeos/orderid', $order->getId() );

		$this->view->confirmOrderItem = $order;
		$this->view->summaryBasket = $order;

		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos checkout-confirm"', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-intro">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-retry">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-basic">', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-detail', $output );
		$this->assertStringContainsString( '<div class="checkout-confirm-detail common-summary">', $output );
		$this->assertMatchesRegularExpression( '#<span class="value">.*' . $order->getInvoiceNumber() . '.*</span>#smU', $output );

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
		$request->expects( $this->any() )->method( 'getQueryParams' )->willReturn( [] );
		$request->expects( $this->any() )->method( 'getAttributes' )->willReturn( [] );

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
		$domains = ['order', 'order/address', 'order/coupon', 'order/product', 'order/service'];
		$manager = \Aimeos\MShop::create( $this->context, 'order' );
		$search = $manager->filter()->add( 'order.datepayment', '==', $date );

		return $manager->search( $search, $domains )->first( new \RuntimeException( 'No order found' ) );
	}
}
