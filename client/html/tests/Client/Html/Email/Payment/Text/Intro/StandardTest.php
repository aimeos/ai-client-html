<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Email\Payment\Text\Intro;


class StandardTest extends \PHPUnit_Framework_TestCase
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

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Email\Payment\Text\Intro\Standard( $this->context, $paths );

		$view = \TestHelperHtml::getView();
		$view->extOrderItem = self::$orderItem;
		$view->extOrderBaseItem = self::$orderBaseItem;
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

		$this->assertContains( 'Thank you for your order', $output );
	}


	public function testGetBodyPaymentRefund()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_REFUND );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertContains( 'The payment for your order', $output );
	}


	public function testGetBodyPaymentPending()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertContains( 'The order is pending until we receive the final payment', $output );
	}


	public function testGetBodyPaymentReceived()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertContains( 'We have received your payment', $output );
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
