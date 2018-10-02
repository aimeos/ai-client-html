<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Controller\Jobs\Order\Email\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$aimeos = \TestHelperJobs::getAimeos();
		$this->context = \TestHelperJobs::getContext();

		$this->object = new \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard( $this->context, $aimeos );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

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

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order', $orderManagerStub );

		$orderItem = $orderManagerStub->createItem();

		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), [], [], [] ) );

		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Delivery\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'process' ) )
			->getMock();

		$object->expects( $this->exactly( 4 ) )->method( 'process' );

		$object->run();
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
		$baseItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();

		$result = $this->access( 'getView' )->invokeArgs( $this->object, array( $this->context, $baseItem, 'de' ) );

		$this->assertInstanceof( '\Aimeos\MW\View\Iface', $result );
	}


	public function testProcess()
	{
		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Delivery\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->setMethods( array( 'addOrderStatus', 'getAddressItem', 'processItem' ) )
			->getMock();

		$addrItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$object->expects( $this->once() )->method( 'getAddressItem' )->will( $this->returnValue( $addrItem ) );
		$object->expects( $this->once() )->method( 'addOrderStatus' );
		$object->expects( $this->once() )->method( 'processItem' );


		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'load' ) )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/base', $orderBaseManagerStub );

		$baseItem = $orderBaseManagerStub->createItem();
		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )->will( $this->returnValue( $baseItem ) );


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Delivery\Standard' )
			->disableOriginalConstructor()
			->getMock();


		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();

		$this->access( 'process' )->invokeArgs( $object, array( $clientStub, array( $orderItem ), 1 ) );
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


		$object = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Order\Email\Delivery\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJobs::getAimeos() ) )
			->getMock();


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Delivery\Standard' )
			->setMethods( array( 'getBody', 'getHeader' ) )
			->disableOriginalConstructor()
			->getMock();

		$clientStub->expects( $this->once() )->method( 'getBody' );
		$clientStub->expects( $this->once() )->method( 'getHeader' );


		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();
		$orderBaseItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();
		$addrItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();

		$this->access( 'processItem' )->invokeArgs( $object, array( $clientStub, $orderItem, $orderBaseItem, $addrItem ) );
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


		$clientStub = $this->getMockBuilder( '\Aimeos\Client\Html\Email\Delivery\Standard' )
			->setMethods( array( 'getBody', 'getHeader' ) )
			->disableOriginalConstructor()
			->getMock();

		$orderItem = \Aimeos\MShop\Factory::createManager( $this->context, 'order' )->createItem();

		$this->access( 'process' )->invokeArgs( $this->object, array( $clientStub, array( $orderItem ), 1 ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\Controller\Jobs\Order\Email\Delivery\Standard' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}

}
