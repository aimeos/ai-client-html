<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Controller\Jobs\Order\Email\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$aimeos = \TestHelperJobs::getAimeos();
		$this->context = \TestHelperJobs::getContext();

		$this->object = new \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard( $this->context, $aimeos );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testGetName()
	{
		$this->assertEquals( 'Order delivery related e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends order delivery status update e-mails';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		\Aimeos\MShop::inject( 'order', $orderManagerStub );

		$orderItem = $orderManagerStub->createItem();

		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( map( [$orderItem] ), map(), map(), map() ) );

		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'process' ) )
			->getMock();

		$object->expects( $this->exactly( 4 ) )->method( 'process' );

		$object->run();
	}


	public function testGetAddressItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base' );
		$addrManager = \Aimeos\MShop::create( $this->context, 'order/base/address' );

		$item = $manager->createItem();
		$item->addAddress( $addrManager->createItem(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$item->addAddress( $addrManager->createItem(), \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

		$result = $this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $item ) );

		$this->assertInstanceof( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $result );
	}


	public function testGetAddressItemNone()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base' );

		$this->expectException( \Aimeos\Controller\Jobs\Exception::class );
		$this->access( 'getAddressItem' )->invokeArgs( $this->object, array( $manager->createItem() ) );
	}


	public function testGetView()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$this->context->setMail( $mailStub );
		$baseItem = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();

		$result = $this->access( 'getView' )->invokeArgs( $this->object, array( $this->context, $baseItem, 'de' ) );

		$this->assertInstanceof( \Aimeos\MW\View\Iface::class, $result );
	}


	public function testProcess()
	{
		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'addOrderStatus', 'getAddressItem', 'processItem' ) )
			->getMock();

		$addrItem = \Aimeos\MShop::create( $this->context, 'order/base/address' )->createItem();
		$object->expects( $this->once() )->method( 'getAddressItem' )->will( $this->returnValue( $addrItem ) );
		$object->expects( $this->once() )->method( 'addOrderStatus' );
		$object->expects( $this->once() )->method( 'processItem' );


		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'load' ) )
			->getMock();

		\Aimeos\MShop::inject( 'order/base', $orderBaseManagerStub );

		$baseItem = $orderBaseManagerStub->createItem();
		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )->will( $this->returnValue( $baseItem ) );


		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Delivery\Standard::class )
			->disableOriginalConstructor()
			->getMock();


		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->createItem()->setBaseId( '-1' );

		$this->access( 'process' )->invokeArgs( $object, [$clientStub, map( [$orderItem] ), 1] );
	}


	public function testProcessItem()
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


		$object = $this->getMockBuilder( \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->getMock();


		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Delivery\Standard::class )
			->setMethods( array( 'getBody', 'getHeader' ) )
			->disableOriginalConstructor()
			->getMock();

		$clientStub->expects( $this->once() )->method( 'getBody' );
		$clientStub->expects( $this->once() )->method( 'getHeader' );


		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->createItem();
		$orderBaseItem = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();
		$addrItem = \Aimeos\MShop::create( $this->context, 'order/base/address' )->createItem();

		$this->access( 'processItem' )->invokeArgs( $object, array( $clientStub, $orderItem, $orderBaseItem, $addrItem ) );
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


		$clientStub = $this->getMockBuilder( \Aimeos\Client\Html\Email\Delivery\Standard::class )
			->setMethods( array( 'getBody', 'getHeader' ) )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->createItem()->setBaseId( '-1' );

		$this->access( 'process' )->invokeArgs( $this->object, [$clientStub, map( [$orderItem] ), 1] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}

}
