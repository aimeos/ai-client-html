<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Client\Html\Email\Payment\Html;


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
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperHtml::context() );
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
		$this->context = \TestHelperHtml::context();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$this->view = \TestHelperHtml::view( 'unittest', $this->context->config() );
		$this->view->message = 'Thank you';
		$this->view->extOrderItem = self::$orderItem;
		$this->view->extOrderBaseItem = self::$orderBaseItem;
		$this->view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $this->view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Payment\Html\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = '..' . $ds . 'themes' . $ds . 'default' . $ds . 'assets' . $ds . 'logo.png';
		$this->context->config()->set( 'client/html/email/logo', $file );

		$this->emailMock->expects( $this->once() )->method( 'embed' )
			->will( $this->returnValue( 'cid:123-unique-id' ) );

		$this->emailMock->expects( $this->once() )->method( 'html' );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<!doctype html>', $output );
		$this->assertStringContainsString( 'cid:123-unique-id', $output );
		$this->assertStringContainsString( 'Thank you', $output );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
		$this->assertStringContainsString( 'If you have any questions', $output );
		$this->assertStringContainsString( 'All orders are subject', $output );
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
