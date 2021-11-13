<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Address\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view = \TestHelperHtml::view();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertNotNull( $output );
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


	public function testInit()
	{
		$customerItem = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );
		$address = $customerItem->getPaymentAddress()->setId( '-1' )->toArray();

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );
		$basketCntl->addAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $address );

		$this->context->setUserId( $customerItem->getId() );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'addAddressItem', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'addAddressItem' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' );

		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $customerStub );
		$this->object->init();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}
}
