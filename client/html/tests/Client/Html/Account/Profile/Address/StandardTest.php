<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 */


namespace Aimeos\Client\Html\Account\Profile\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Profile\Address\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context );
		$customer = $manager->findItem( 'UTC001', ['customer/address'] );

		$view = \TestHelperHtml::getView();
		$view->profileCustomerItem = $customer;
		$this->object->setView( $this->object->addData( $view ) );
		$this->context->setUserId( $customer->getId() );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<div class="account-profile-address">', $output );
		$this->assertRegExp( '#id="address-payment-salutation"#', $output );

		foreach( $customer->getAddressItems() as $idx => $item ) {
			$this->assertRegExp( '#id="address-delivery-salutation-' . $idx . '"#', $output );
		}
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
}
