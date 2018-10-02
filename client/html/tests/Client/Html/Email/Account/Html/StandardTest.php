<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Email\Account\Html;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $customerItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$context = \TestHelperHtml::getContext();

		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( self::$customerItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer found' );
		}
	}


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->emailMock = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )->getMock();

		$view = \TestHelperHtml::getView( 'unittest', $this->context->getConfig() );
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->extAccountCode = self::$customerItem->getCode();
		$view->extAccountPassword = 'testpwd';
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object = new \Aimeos\Client\Html\Email\Account\Html\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$ds = DIRECTORY_SEPARATOR;

		$logo = '..' . $ds . 'themes' . $ds . 'elegance' . $ds . 'media' . $ds . 'aimeos.png';
		$this->context->getConfig()->set( 'client/html/email/logo', $logo );

		$theme = '..' . $ds . 'themes' . $ds . 'elegance';
		$this->context->getConfig()->set( 'client/html/common/template/baseurl', $theme );


		$this->emailMock->expects( $this->once() )->method( 'embedAttachment' )
			->will( $this->returnValue( 'cid:123-unique-id' ) );

		$this->emailMock->expects( $this->once() )->method( 'setBodyHtml' )
			->with( $this->matchesRegularExpression( '#<html>.*<title>E-mail notification</title>.*<meta.*Aimeos.*<body>#smu' ) );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( '<html>', $output );
		$this->assertContains( 'cid:123-unique-id', $output );

		$this->assertContains( '<p class="email-common-salutation', $output );

		$this->assertContains( '<p class="email-common-intro', $output );
		$this->assertContains( 'An account', $output );

		$this->assertRegexp( '#<style.*/style>.*<div class="account-detail content-block">#smU', $output );
		$this->assertContains( 'Account', $output );
		$this->assertContains( 'Password', $output );

		$this->assertContains( '<p class="email-common-outro', $output );
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
