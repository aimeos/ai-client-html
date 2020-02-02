<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Email\Account;


class StandardTest extends \PHPUnit\Framework\TestCase
{
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
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->extAccountCode = self::$customerItem->getCode();
		$view->extAccountPassword = 'testpwd';
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Account\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/email/from-email', 'me@localhost' );
		$config->set( 'client/html/email/from-name', 'My company' );

		$this->emailMock->expects( $this->once() )->method( 'addHeader' )
			->with( $this->equalTo( 'X-MailGenerator' ), $this->equalTo( 'Aimeos' ) );

		$this->emailMock->expects( $this->once() )->method( 'addTo' )
			->with( $this->equalTo( 'test@example.com' ), $this->equalTo( 'Our Unittest' ) );

		$this->emailMock->expects( $this->once() )->method( 'addFrom' )
			->with( $this->equalTo( 'me@localhost' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'addReplyTo' )
			->with( $this->equalTo( 'me@localhost' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'Your new account' ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

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
