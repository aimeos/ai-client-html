<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */


namespace Aimeos\Client\Html\Email\Subscription;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $subscriptionItem;
	private static $productItem;
	private static $addressItem;
	private $object;
	private $context;
	private $emailMock;
	private $view;


	public static function setUpBeforeClass() : void
	{
		$context = \TestHelperHtml::context();
		$manager = \Aimeos\MShop::create( $context, 'subscription' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );

		if( ( self::$subscriptionItem = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No subscription item found' );
		}


		$manager = \Aimeos\MShop::create( $context, 'order/base' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );

		if( ( $baseItem = $manager->search( $search, ['order/base/address', 'order/base/product'] )->first() ) === null ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		foreach( $baseItem->getProducts() as $product )
		{
			if( $product->getProductCode() === 'CNC' ) {
				self::$productItem = $product;
			}
		}

		self::$addressItem = $baseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, 0 );
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::context();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\Base\\Mail\\Message\\None' )->getMock();

		$this->view = \TestHelperHtml::view( 'unittest', $this->context->config() );
		$this->view->extSubscriptionItem = self::$subscriptionItem;
		$this->view->extOrderProductItem = self::$productItem;
		$this->view->extAddressItem = self::$addressItem;
		$this->view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $this->view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Subscription\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$config = $this->context->config();
		$config->set( 'client/html/email/from-email', 'me@example.com' );
		$config->set( 'client/html/email/from-name', 'My company' );

		$this->emailMock->expects( $this->once() )->method( 'header' )
			->with( $this->equalTo( 'X-MailGenerator' ), $this->equalTo( 'Aimeos' ) );

		$this->emailMock->expects( $this->once() )->method( 'to' )
			->with( $this->equalTo( 'test@example.com' ), $this->equalTo( 'Our Unittest' ) );

		$this->emailMock->expects( $this->once() )->method( 'from' )
			->with( $this->equalTo( 'me@example.com' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'replyTo' )
			->with( $this->equalTo( 'me@example.com' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'subject' )
			->with( $this->stringContains( 'Your subscription' ) );

		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringContainsString( 'Dear Mr Our Unittest', $output );
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
