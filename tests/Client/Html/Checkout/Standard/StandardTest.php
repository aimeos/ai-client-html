<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Client\Html\Checkout\Standard;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>summary | Aimeos</title>', $output );
	}


	public function testBody()
	{
		$this->view->standardStepActive = 'address';

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'c_step' => 'payment' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos checkout-standard"', $output );
		$this->assertRegExp( '#<ol class="steps">.*<li class="step.*>.*</li>.*</ol>#smU', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testBodyOnepage()
	{

		$config = $this->context->config();
		$config->set( 'client/html/checkout/standard/onepage', array( 'address', 'delivery', 'payment', 'summary' ) );

		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $this->view, $config );
		$this->view->addHelper( 'config', $helper );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testBodyOnepagePartitial()
	{
		$this->view->standardStepActive = 'delivery';

		$config = $this->context->config();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $this->view, $config );
		$this->view->addHelper( 'config', $helper );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
	}


	public function testBodyOnepageDifferentStep()
	{
		$this->view->standardStepActive = 'address';

		$config = $this->context->config();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $this->view, $config );
		$this->view->addHelper( 'config', $helper );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="checkout-standard-address', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-delivery', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-payment', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-summary', $output );
		$this->assertStringNotContainsString( '<section class="checkout-standard-order', $output );
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


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'errors' ) );
	}
}
