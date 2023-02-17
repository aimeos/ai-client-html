<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
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
		$this->context->setUserId( $customer->getId() );

		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="account-profile-address', $output );
		$this->assertMatchesRegularExpression( '#id="address-payment-salutation"#', $output );

		foreach( $customer->getAddressItems() as $idx => $item ) {
			$this->assertMatchesRegularExpression( '#id="address-delivery-salutation-' . $idx . '"#', $output );
		}
	}


	public function testInit()
	{
		$this->object->init();
	}
}
