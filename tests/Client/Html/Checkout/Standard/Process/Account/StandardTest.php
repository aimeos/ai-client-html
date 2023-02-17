<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Account;


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

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Account\Standard( $this->context );
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
		$address = $customerItem->getPaymentAddress()->setEmail( 'unittest@aimeos.org' )->toArray();

		$basketCntl = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$basketCntl->addAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT, $address );

		$this->view = \TestHelper::view();
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'cs_option_account' => 1 ) );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'add', 'get', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'add' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'get' )->will( $this->returnValue( $customerItem ) );

		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $customerStub );

		$this->object->init();
	}
}
