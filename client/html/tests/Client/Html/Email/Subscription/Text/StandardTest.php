<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Email\Subscription\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $subscriptionItem;
	private static $productItem;
	private static $addressItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$context = \TestHelperHtml::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'subscription' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );
		$result = $manager->searchItems( $search );

		if( ( self::$subscriptionItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No subscription item found' );
		}


		$manager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );
		$result = $manager->searchItems( $search, ['order/base/address', 'order/base/product'] );

		if( ( $baseItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		foreach( $baseItem->getProducts() as $product )
		{
			if( $product->getProductCode() === 'CNC' ) {
				self::$productItem = $product;
			}
		}

		self::$addressItem = $baseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
	}


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );
		$view->extSubscriptionItem = self::$subscriptionItem;
		$view->extOrderProductItem = self::$productItem;
		$view->extAddressItem = self::$addressItem;
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Subscription\Text\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->emailMock->expects( $this->once() )->method( 'setBody' )
			->with( $this->stringContains( 'Noire' ) );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( 'The subscription', $output );
		$this->assertContains( 'Cafe Noire Cappuccino', $output );
		$this->assertContains( 'If you have any questions', $output );
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
