<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Email\Delivery\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $orderItem;
	private static $orderBaseItem;
	private $object;
	private $context;
	private $emailMock;
	private $view;


	public static function setUpBeforeClass() : void
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperHtml::getContext() );
		$orderBaseManager = $manager->getSubManager( 'base' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );

		if( ( self::$orderItem = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No order found' );
		}

		self::$orderBaseItem = $orderBaseManager->load( self::$orderItem->getBaseId() );
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$this->view = \TestHelperHtml::view();
		$this->view->message = 'The delivery status';
		$this->view->extOrderItem = self::$orderItem;
		$this->view->extOrderBaseItem = self::$orderBaseItem;
		$this->view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $this->view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Delivery\Text\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->emailMock->expects( $this->once() )->method( 'setBody' )
			->with( $this->stringContains( 'delivery status' ) );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( 'The delivery status', $output );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
		$this->assertStringContainsString( 'If you have any questions', $output );
		$this->assertStringContainsString( 'All orders are subject to our terms and conditions.', $output );
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
