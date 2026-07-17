<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2026
 */


namespace Aimeos\Client\Html\Account\Profile;


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

		$this->object = new \Aimeos\Client\Html\Account\Profile\Standard( $this->context );
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

		$this->assertStringContainsString( '<link rel="stylesheet"', $output );
		$this->assertStringContainsString( '<script defer', $output );
	}


	public function testBody()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$customer = $manager->find( 'test@example.com', ['customer/address'] );

		$this->view = \TestHelper::view();
		$this->view->profileCustomerItem = $customer;
		$this->object->setView( $this->object->data( $this->view ) );
		$this->context->setUser( $customer );

		$output = $this->object->body();
		$addressCount = count( $customer->getAddressItems() );

		$this->assertStringContainsString( '<div class="account-profile-address', $output );
		$this->assertMatchesRegularExpression( '#id="address-payment-salutation-#', $output );
		$this->assertSame( $addressCount + 2, substr_count( $output, 'class="address-save ' ) );
		$this->assertSame( $addressCount, substr_count( $output, 'class="address-delete ' ) );
		$this->assertSame( $addressCount * 2 + 2, substr_count( $output, '<form ' ) );
		preg_match_all( '/toolname="([^"]+)"/', $output, $tools );
		$this->assertCount( $addressCount * 2 + 2, $tools[1] );
		$this->assertSame( $tools[1], array_values( array_unique( $tools[1] ) ) );
		$this->assertContains( 'update_billing_address', $tools[1] );
		$this->assertContains( 'add_delivery_address', $tools[1] );
		$this->assertSame( count( $tools[1] ), substr_count( $output, 'tooldescription=' ) );
		$this->assertDoesNotMatchRegularExpression( '#<(?:input|select) class="form-control"[^>]* disabled#', $output );

		foreach( $customer->getAddressItems() as $idx => $item ) {
			$this->assertMatchesRegularExpression( '#id="address-delivery-salutation-' . $idx . '"#', $output );
			$this->assertContains( 'update_delivery_address_' . $idx, $tools[1] );
			$this->assertContains( 'delete_delivery_address_' . $idx, $tools[1] );
		}
	}


	public function testInit()
	{
		$this->object->init();
		$this->expectNotToPerformAssertions();
	}


	public function testInitDeliveryOnly()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['customer/address'] );
		$addresses = $customer->getAddressItems();
		$pos = $addresses->firstKey();
		$this->context->setUser( $customer );

		$params = ['address' => ['save' => 1, 'delivery' => [$pos => $addresses->get( $pos )->toArray()]]];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['add', 'addAddressItem', 'store'] )
			->getMock();

		$stub->expects( $this->never() )->method( 'add' );
		$stub->expects( $this->once() )->method( 'addAddressItem' )->willReturn( $stub );
		$stub->expects( $this->once() )->method( 'store' )->willReturn( $stub );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $stub );
		$this->object->init();
	}


	public function testInitDeleteOnly()
	{
		$customer = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['customer/address'] );
		$pos = $customer->getAddressItems()->firstKey();
		$this->context->setUser( $customer );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['address' => ['delete' => $pos]] );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['add', 'deleteAddressItem', 'store'] )
			->getMock();

		$stub->expects( $this->never() )->method( 'add' );
		$stub->expects( $this->once() )->method( 'deleteAddressItem' )->willReturn( $stub );
		$stub->expects( $this->once() )->method( 'store' )->willReturn( $stub );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $stub );
		$this->object->init();
	}
}
