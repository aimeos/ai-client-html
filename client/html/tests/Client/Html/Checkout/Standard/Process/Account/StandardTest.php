<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Account;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Account\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();
		\Aimeos\Controller\Frontend::cache( false );

		unset( $this->object, $this->object );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
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


	public function testProcess()
	{
		$customerItem = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' );
		$address = $customerItem->getPaymentAddress()->setEmail( 'unittest@aimeos.org' )->toArray();

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );
		$basketCntl->addAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $address );

		$view = \TestHelperHtml::getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'cs_option_account' => 1 ) );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$customerStub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'add', 'get', 'store' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'add' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'store' )->will( $this->returnValue( $customerStub ) );
		$customerStub->expects( $this->once() )->method( 'get' )->will( $this->returnValue( $customerItem ) );

		\Aimeos\Controller\Frontend::inject( 'customer', $customerStub );

		$this->object->process();
	}
}
