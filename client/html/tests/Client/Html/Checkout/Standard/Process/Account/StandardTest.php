<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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

		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Account\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		\Aimeos\Controller\Frontend::cache( false );

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
		$address = $customerItem->getPaymentAddress()->setEmail( 'unittest@aimeos.org' )->toArray();

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );
		$basketCntl->addAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $address );

		$this->view = \TestHelperHtml::view();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'cs_option_account' => 1 ) );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'add', 'get', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'add' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'get' )->will( $this->returnValue( $customerItem ) );

		\Aimeos\Controller\Frontend::inject( 'customer', $customerStub );

		$this->object->init();
	}
}
