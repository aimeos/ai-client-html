<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Controller\Jobs\Order\Email\Voucher;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$aimeos = \TestHelperJobs::getAimeos();
		$this->context = \TestHelperJobs::getContext();

		$codeManager = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Code\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$codeManager->expects( $this->any() )->method( 'saveItem' );
		\Aimeos\MShop\Factory::injectManager( $this->context, 'coupon/code', $codeManager );

		$this->object = new \Aimeos\Controller\Jobs\Order\Email\Voucher\Standard( $this->context, $aimeos );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

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
		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order', $orderManagerStub );

		$orderItem = $orderManagerStub->createItem();

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderItem ) ) );

		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Voucher\Standard' )
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
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$managerStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'coupon/code', $managerStub );

		$this->access( 'addCouponCodes' )->invokeArgs( $this->object, [['test' => 1]] );
	}


	public function testAddOrderStatus()
	{
		$statusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$statusManagerStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/status', $statusManagerStub );

		$this->access( 'addOrderStatus' )->invokeArgs( $this->object, array( -1, +1 ) );
	}


	public function testGetAddressItem()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );
		$addrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' );

		$item = $manager->createItem();
		$item->setAddress( $addrManager->createItem(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$item->setAddress( $addrManager->createItem(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

		$result = $this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $item ) );

		$this->assertInstanceof( '\Aimeos\MShop\Order\Item\Base\Address\Iface', $result );
	}


	public function testGetAddressItemNone()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );

		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $manager->createItem() ) );
	}


	public function testGetCouponId()
	{
		$this->assertGreaterThan( 0, $this->access( 'getCouponId' )->invokeArgs( $this->object, [] ) );
	}


	public function testGetView()
	{
		$result = $this->access( 'getView' )->invokeArgs( $this->object, array( $this->context, 'unittest', 'EUR', 'de' ) );
		$this->assertInstanceof( '\Aimeos\MW\View\Iface', $result );
	}


	public function testProcess()
	{
		$orderAddrItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();

		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( ['load', 'store'] )
			->getMock();

		$orderBaseItem = $orderBaseManagerStub->createItem();

		$orderBaseManagerStub->expects( $this->once() )->method( 'store' );
		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( $orderBaseItem ) );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/base', $orderBaseManagerStub );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Voucher\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'addOrderStatus', 'createCoupons', 'sendEmails' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'createCoupons' )->will( $this->returnValue( $orderBaseItem ) );
		$object->expects( $this->once() )->method( 'addOrderStatus' );
		$object->expects( $this->once() )->method( 'sendEmails' );


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Voucher\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();


		$this->access( 'process' )->invokeArgs( $object, [$clientStub, [$orderItem], 1] );
	}


	public function testProcessException()
	{
		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'load' ) )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/base', $orderBaseManagerStub );

		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->throwException( new \Aimeos\MShop\Order\Exception() ) );


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Voucher\Standard' )
			->setMethods( array( 'getBody', 'getHeader' ) )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();

		$this->access( 'process' )->invokeArgs( $this->object, [$clientStub, [$orderItem], 1] );
	}


	public function testCreateCoupons()
	{
		$orderBaseItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();
		$orderProductItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' )->createItem();

		$orderBaseItem->addProduct( $orderProductItem->setType( 'voucher' )->setProductCode( 'test' ) );

		$orderBaseItem = $this->access( 'createCoupons' )->invokeArgs( $this->object, [$orderBaseItem] );

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


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Voucher\Standard' )
			->setMethods( ['getBody', 'getHeader'] )
			->disableOriginalConstructor()
			->getMock();

		$clientStub->expects( $this->once() )->method( 'getBody' );
		$clientStub->expects( $this->once() )->method( 'getHeader' );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Voucher\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->getMock();


		$orderBaseItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();
		$orderAddressItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$orderProductAttrItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product/attribute' )->createItem();
		$orderProductItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' )->createItem();

		$orderProductAttrItem->setCode( 'coupon-code' )->setType( 'coupon' )->setValue( 'abcd' );
		$orderProductItem->setAttributeItem( $orderProductAttrItem );

		$orderBaseItem->setAddress( $orderAddressItem, 'payment' );
		$orderBaseItem->addProduct( $orderProductItem->setType( 'voucher' )->setProductCode( 'test' ) );

		$this->access( 'sendEmails' )->invokeArgs( $object, [$orderBaseItem, $clientStub] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\Controller\Jobs\Order\Email\Voucher\Standard' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
