<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Email\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $productItems;
	private static $customerItem;
	private $object;
	private $context;
	private $emailMock;
	private $view;


	public static function setUpBeforeClass() : void
	{
		$context = \TestHelperHtml::getContext();

		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $context );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', 'test@example.com' ) );

		if( ( self::$customerItem = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No customer found' );
		}

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );

		foreach( $manager->search( $search, array( 'text', 'price', 'media' ) ) as $id => $product )
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

		$this->view = \TestHelperHtml::view( 'unittest', $this->context->getConfig() );
		$this->view->extProducts = self::$productItems;
		$this->view->extAddressItem = self::$customerItem->getPaymentAddress();
		$this->view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $this->view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Watch\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/email/from-email', 'me@example.com' );
		$config->set( 'client/html/email/from-name', 'My company' );

		$this->emailMock->expects( $this->once() )->method( 'addHeader' )
			->with( $this->equalTo( 'X-MailGenerator' ), $this->equalTo( 'Aimeos' ) );

		$this->emailMock->expects( $this->once() )->method( 'addTo' )
			->with( $this->equalTo( 'test@example.com' ), $this->equalTo( 'Our Unittest' ) );

		$this->emailMock->expects( $this->once() )->method( 'addFrom' )
			->with( $this->equalTo( 'me@example.com' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'addReplyTo' )
			->with( $this->equalTo( 'me@example.com' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'Your watched products' ) );

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
