<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Checkout\Standard;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
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


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'address';

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'c_step' => 'payment' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos checkout-standard"', $output );
		$this->assertRegExp( '#<ol class="steps">.*<li class="step.*>.*</li>.*</ol>#smU', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepage()
	{
		$view = $this->object->getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'address', 'delivery', 'payment', 'summary' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepagePartitial()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'delivery';

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepageDifferentStep()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'address';

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->getBody() );
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Checkout\Standard\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'A non-recoverable error occured', $object->getBody() );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'address', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
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


	public function testProcess()
	{
		$this->object->process();

		$this->assertEmpty( $this->object->getView()->get( 'standardErrorList' ) );
	}
}
