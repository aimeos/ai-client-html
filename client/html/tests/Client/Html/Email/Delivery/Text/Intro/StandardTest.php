<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Email\Delivery\Text\Intro;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $orderItem;
	private static $orderBaseItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperHtml::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $orderManager->searchItems( $search );

		if( ( self::$orderItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order found' );
		}

		self::$orderBaseItem = $orderBaseManager->load( self::$orderItem->getBaseId() );
	}


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView();
		$view->extOrderItem = self::$orderItem;
		$view->extOrderBaseItem = self::$orderBaseItem;
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Delivery\Text\Intro\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( 'The delivery status of your order', $output );
	}


	public function testGetBodyDeliveryDispatched()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_DISPATCHED );
		$view->extOrderItem = $orderItem;

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( 'has been dispatched', $output );
	}


	public function testGetBodyDeliveryRefused()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_REFUSED );
		$view->extOrderItem = $orderItem;

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( 'could not be delivered', $output );
	}


	public function testGetBodyDeliveryReturned()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_RETURNED );
		$view->extOrderItem = $orderItem;

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( 'We received the returned parcel', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
