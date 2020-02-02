<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Email\Watch\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $productItems;
	private static $customerItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass() : void
	{
		$context = \TestHelperHtml::getContext();

		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );

		if( ( self::$customerItem = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No customer found' );
		}

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );

		foreach( $manager->searchItems( $search, array( 'text', 'price', 'media' ) ) as $id => $product )
		{
			$prices = $product->getRefItems( 'price', 'default', 'default' );

			self::$productItems[$id]['price'] = $prices->first();
			self::$productItems[$id]['currency'] = 'EUR';
			self::$productItems[$id]['item'] = $product;
		}
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );
		$view->extProducts = self::$productItems;
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Watch\Text\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->emailMock->expects( $this->once() )->method( 'setBody' )
			->with( $this->stringContains( 'Noire' ) );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( 'One or more products', $output );
		$this->assertStringContainsString( 'Cafe Noire Cappuccino', $output );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
		$this->assertStringContainsString( 'If you have any questions', $output );
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
