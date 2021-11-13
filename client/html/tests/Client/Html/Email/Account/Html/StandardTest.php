<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Email\Account\Html;


class StandardTest extends \PHPUnit\Framework\TestCase
{
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
	}


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$this->view = \TestHelperHtml::view( 'unittest', $this->context->getConfig() );
		$this->view->extAddressItem = self::$customerItem->getPaymentAddress();
		$this->view->extAccountCode = self::$customerItem->getCode();
		$this->view->extAccountPassword = 'testpwd';
		$this->view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $this->view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Account\Html\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$ds = DIRECTORY_SEPARATOR;

		$logo = '..' . $ds . 'themes' . $ds . 'default' . $ds . 'media' . $ds . 'aimeos.png';
		$this->context->getConfig()->set( 'client/html/email/logo', $logo );

		$theme = '..' . $ds . 'themes' . $ds . 'default';
		$this->context->getConfig()->set( 'client/html/common/template/baseurl', $theme );


		$this->emailMock->expects( $this->once() )->method( 'embedAttachment' )
			->will( $this->returnValue( 'cid:123-unique-id' ) );

		$this->emailMock->expects( $this->once() )->method( 'setBodyHtml' )
			->with( $this->matchesRegularExpression( '#<title>.*Your new account.*</title>#smu' ) );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<!doctype html>', $output );
		$this->assertStringContainsString( 'cid:123-unique-id', $output );

		$this->assertStringContainsString( 'email-common-salutation', $output );

		$this->assertStringContainsString( 'email-common-intro', $output );
		$this->assertStringContainsString( 'An account', $output );

		$this->assertStringContainsString( 'Account', $output );
		$this->assertStringContainsString( 'Password', $output );

		$this->assertStringContainsString( 'email-common-outro', $output );
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
