<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Controller\Jobs\Order\Email\Voucher;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$aimeos = \TestHelperJobs::getAimeos();
		$this->context = \TestHelperJobs::getContext();

		$codeManager = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Code\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$codeManager->expects( $this->any() )->method( 'save' );
		\Aimeos\MShop::inject( 'coupon/code', $codeManager );

		$this->object = new \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard( $this->context, $aimeos );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testGetName()
	{
		$this->assertEquals( 'Voucher related e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends the e-mail with the voucher to the customer';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$orderManagerStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( ['search'] )
			->getMock();

		\Aimeos\MShop::inject( 'order', $orderManagerStub );

		$orderItem = $orderManagerStub->create();

		$orderManagerStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderItem] ) ) );

		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'process' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'process' );

		$object->run();
	}


	public function testAddCouponCodes()
	{
		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Code\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$managerStub->expects( $this->once() )->method( 'save' );

		\Aimeos\MShop::inject( 'coupon/code', $managerStub );

		$this->access( 'addCouponCodes' )->invokeArgs( $this->object, [['test' => 1]] );
	}


	public function testAddOrderStatus()
	{
		$statusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$statusManagerStub->expects( $this->once() )->method( 'save' );

		\Aimeos\MShop::inject( 'order/status', $statusManagerStub );

		$this->access( 'addOrderStatus' )->invokeArgs( $this->object, array( -1, +1 ) );
	}


	public function testGetAddressItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base' );
		$addrManager = \Aimeos\MShop::create( $this->context, 'order/base/address' );

		$item = $manager->create();
		$item->addAddress( $addrManager->create(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$item->addAddress( $addrManager->create(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

		$result = $this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $item ) );

		$this->assertInstanceof( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $result );
	}


	public function testGetAddressItemNone()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base' );

		$this->expectException( \Aimeos\Controller\Jobs\Exception::class );
		$this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $manager->create() ) );
	}


	public function testGetCouponId()
	{
		$this->assertGreaterThan( 0, $this->access( 'getCouponId' )->invokeArgs( $this->object, [] ) );
	}


	public function testGetView()
	{
		$result = $this->access( 'view' )->invokeArgs( $this->object, array( $this->context, 'unittest', 'EUR', 'de' ) );
		$this->assertInstanceof( \Aimeos\MW\View\Iface::class, $result );
	}


	public function testProcess()
	{
		$orderBaseManagerStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( ['load', 'store'] )
			->getMock();

		$orderBaseItem = $orderBaseManagerStub->create();

		$orderBaseManagerStub->expects( $this->once() )->method( 'store' );
		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( $orderBaseItem ) );

		\Aimeos\MShop::inject( 'order/base', $orderBaseManagerStub );


		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'addOrderStatus', 'createCoupons', 'sendEmails' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'createCoupons' )->will( $this->returnValue( $orderBaseItem ) );
		$object->expects( $this->once() )->method( 'addOrderStatus' );
		$object->expects( $this->once() )->method( 'sendEmails' );


		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Voucher\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create()->setBaseId( '-1' );


		$this->access( 'process' )->invokeArgs( $object, [$clientStub, map( [$orderItem] ), 1] );
	}


	public function testProcessException()
	{
		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'load' ) )
			->getMock();

		\Aimeos\MShop::inject( 'order/base', $orderBaseManagerStub );

		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->throwException( new \Aimeos\MShop\Order\Exception() ) );

		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Voucher\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create()->setBaseId( '-1' );

		$this->access( 'process' )->invokeArgs( $this->object, [$clientStub, map( [$orderItem] ), 1] );
	}


	public function testCreateCoupons()
	{
		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( ['addCouponCodes'] )
			->getMock();

			$object->expects( $this->once() )->method( 'addCouponCodes' );

		$orderBaseItem = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$orderProductItem = \Aimeos\MShop::create( $this->context, 'order/base/product' )->create();
		$prodid = \Aimeos\MShop::create( $this->context, 'product' )->find( 'MNOP' )->getId();

		$orderProductItem->setType( 'voucher' )->setProductId( $prodid )->setProductCode( 'MNOP' )->setStockType( 'unitstock' );
		$orderBaseItem->addProduct( $orderProductItem );

		$orderBaseItem = $this->access( 'createCoupons' )->invokeArgs( $object, [$orderBaseItem] );

		$this->assertEquals( 1, count( $orderBaseItem->getProduct( 0 )->getAttribute( 'coupon-code', 'coupon' ) ) );
	}


	public function testSendEmails()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )->method( 'createMessage' )->will( $this->returnValue( $mailMsgStub ) );
		$mailStub->expects( $this->once() )->method( 'send' );

		$this->context->setMail( $mailStub );


		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Voucher\Standard::class )
			->setMethods( ['body', 'header'] )
			->disableOriginalConstructor()
			->getMock();

		$clientStub->expects( $this->once() )->method( 'body' );
		$clientStub->expects( $this->once() )->method( 'header' );


		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->getMock();


		$orderBaseItem = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$orderAddressItem = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$orderProductAttrItem = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create();
		$orderProductItem = \Aimeos\MShop::create( $this->context, 'order/base/product' )->create();

		$prodid = \Aimeos\MShop::create( $this->context, 'product' )->find( 'MNOP' )->getId();
		$orderProductItem->setType( 'voucher' )->setProductId( $prodid )->setProductCode( 'MNOP' )->setStockType( 'unitstock' );

		$orderProductAttrItem->setCode( 'coupon-code' )->setType( 'coupon' )->setValue( 'abcd' );
		$orderProductItem->setAttributeItem( $orderProductAttrItem );

		$orderBaseItem->addAddress( $orderAddressItem, 'payment' );
		$orderBaseItem->addProduct( $orderProductItem );

		$this->access( 'sendEmails' )->invokeArgs( $object, [$orderBaseItem, $clientStub] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
