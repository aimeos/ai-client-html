<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Checkout\Standard\Summary;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Summary\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::create( $this->context, 'basket' )->clear();
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view = \TestHelper::view();
		$this->view->standardStepActive = 'summary';
		$this->view->standardBasket = $this->getBasket();
		$this->view->standardSteps = array( 'before', 'summary' );
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section checkout-standard-summary common-summary">', $output );
		$this->assertStringContainsString( '<div class="checkout-standard-summary-option', $output );
		$this->assertStringContainsString( '<div class="checkout-standard-summary-option-account', $output );
		$this->assertStringContainsString( '<div class="checkout-standard-summary-option-terms', $output );

		$this->assertStringContainsString( 'Example company', $output );
		$this->assertStringContainsString( 'unitpaymentlabel', $output );
		$this->assertStringContainsString( 'Unittest service name', $output );
	}


	public function testBodyDetail()
	{
		$this->view = \TestHelper::view();
		$this->view->standardStepActive = 'summary';
		$this->view->standardBasket = $this->getBasket();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringContainsString( '<div class="common-summary-detail', $output );
		$this->assertMatchesRegularExpression( '#<div class="tax.*<div class="price.*10.84 EUR</div>#smU', $output );
	}


	public function testBodyOtherStep()
	{
		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'cs_order' => 1 ) );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->init();
	}


	public function testInitComment()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );

		$this->view = \TestHelper::view();
		$this->view->standardBasket = $controller->get();

		$param = array( 'cs_comment' => 'test comment' );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$this->object->init();

		$this->assertEmpty( $this->view->get( 'standardStepActive' ) );
	}


	public function testInitOptionOK()
	{
		$this->view->standardBasket = $this->getBasket();
		$this->object->setView( $this->view );

		$param = array(
			'cs_order' => '1',
			'cs_option_terms' => '1',
			'cs_option_terms_value' => '1',
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$this->assertEquals( null, $this->view->get( 'standardStepActive' ) );
	}


	public function testInitOptionInvalid()
	{
		$this->view->standardBasket = $this->getBasket();
		$this->object->setView( $this->view );

		$param = array(
			'cs_order' => '1',
			'cs_option_terms' => '1',
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$this->assertEquals( 'summary', $this->view->get( 'standardStepActive' ) );
		$this->assertArrayHasKey( 'option', $this->view->get( 'summaryErrorCodes', [] ) );
	}


	public function testInitSkip()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'standardStepActive' ) );
	}


	protected function getBasket()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );

		$customerManager = \Aimeos\MShop::create( $this->context, 'customer' );
		$address = $customerManager->find( 'test@example.com' )->getPaymentAddress()->toArray();

		$controller->addAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT, $address );
		$controller->addAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY, $address );

		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$controller->addProduct( $productManager->find( 'CNE', ['price'] ), 2 );

		$domains = ['media', 'price', 'text'];
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$controller->addService( $serviceManager->find( 'unitpaymentcode', $domains, 'service', 'payment' ) );
		$controller->addService( $serviceManager->find( 'unitdeliverycode', $domains, 'service', 'delivery' ) );

		return $controller->get();
	}
}
