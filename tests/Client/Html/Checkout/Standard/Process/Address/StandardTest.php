<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Address;


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
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Address\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::create( $this->context, 'basket' )->clear();
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view = \TestHelper::view();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertNotNull( $output );
	}


	public function testInit()
	{
		$customerItem = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );
		$address = $customerItem->getPaymentAddress()->setId( '-1' )->toArray();

		$basketCntl = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$basketCntl->addAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY, $address );

		$this->context->setUserId( $customerItem->getId() );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'addAddressItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'addAddressItem' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $customerStub );
		$this->object->init();
	}
}
