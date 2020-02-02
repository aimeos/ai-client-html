<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2020
 */


namespace Aimeos\Client\Html\Email\Voucher\Html;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $orderItem;
	private static $orderBaseItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass() : void
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperHtml::getContext() );
		$orderBaseManager = $manager->getSubManager( 'base' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );

		if( ( self::$orderItem = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No order found' );
		}

		self::$orderBaseItem = $orderBaseManager->load( self::$orderItem->getBaseId() );
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );

		$view->extAddressItem = self::$orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, 0 );
		$view->extOrderProductItem = self::$orderBaseItem->getProducts()->first();
		$view->extVoucherCode = 'test';

		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Voucher\Html\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown() : void
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
			->with( $this->matchesRegularExpression( '#<title>.*Your voucher.*</title>#smu' ) );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<!doctype html>', $output );
		$this->assertStringContainsString( 'cid:123-unique-id', $output );

		$this->assertStringContainsString( 'email-common-intro', $output );
		$this->assertStringContainsString( 'Your voucher:', $output );

		$this->assertStringContainsString( 'email-common-body', $output );
		$this->assertStringContainsString( 'The value of your voucher', $output );

		$this->assertStringContainsString( 'email-common-outro', $output );
		$this->assertStringContainsString( 'your voucher', $output );
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
}
