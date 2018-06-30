<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Email\Voucher\Html;


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

		$products = self::$orderBaseItem->getProducts();
		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );

		$view->extAddressItem = self::$orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$view->extOrderProductItem = reset( $products );
		$view->extVoucherCode = 'test';

		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Voucher\Html\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = '..' . $ds . 'themes' . $ds . 'elegance' . $ds . 'media' . $ds . 'aimeos.png';
		$this->context->getConfig()->set( 'client/html/email/logo', $file );

		$this->emailMock->expects( $this->once() )->method( 'embedAttachment' )
			->will( $this->returnValue( 'cid:123-unique-id' ) );

		$this->emailMock->expects( $this->once() )->method( 'setBodyHtml' )
			->with( $this->matchesRegularExpression( '#<html>.*<title>E-mail notification</title>.*<meta.*Aimeos.*<body>#smu' ) );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<html>', $output );
		$this->assertContains( 'cid:123-unique-id', $output );

		$this->assertContains( '<p class="email-common-intro', $output );
		$this->assertContains( 'Your voucher:', $output );

		$this->assertContains( '<p class="email-common-body', $output );
		$this->assertContains( 'The value of your voucher', $output );

		$this->assertContains( '<p class="email-common-outro', $output );
		$this->assertContains( 'your voucher', $output );
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
