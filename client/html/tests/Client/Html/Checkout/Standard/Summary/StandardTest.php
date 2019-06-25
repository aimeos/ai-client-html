<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Summary;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$view = \TestHelperHtml::getView();
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Summary\Standard( $this->context );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testGetHeader()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );

		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $controller->get();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderOtherStep()
	{
		$output = $this->object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $this->getBasket();
		$view->standardSteps = array( 'before', 'summary' );
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="checkout-standard-summary common-summary">', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option-account', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option-terms', $output );

		$this->assertContains( 'Example company', $output );
		$this->assertContains( 'unitpaymentlabel', $output );
		$this->assertContains( 'Unittest service name', $output );
	}


	public function testGetBodyDetail()
	{
		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertContains( '<div class="common-summary-detail', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="tax">.*<td class="price">10.52 EUR</td>.*.*</tfoot>#smU', $output );
	}


	public function testGetBodyOtherStep()
	{
		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
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
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'cs_order' => 1 ) );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->process();
	}


	public function testProcessComment()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );

		$view = \TestHelperHtml::getView();
		$view->standardBasket = $controller->get();

		$param = array( 'cs_comment' => 'test comment' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$this->object->process();
	}


	public function testProcessOptionOK()
	{
		$view = $this->object->getView();
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $view );

		$param = array(
			'cs_order' => '1',
			'cs_option_terms' => '1',
			'cs_option_terms_value' => '1',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$this->assertEquals( null, $view->get( 'standardStepActive' ) );
	}


	public function testProcessOptionInvalid()
	{
		$view = $this->object->getView();
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $view );

		$param = array(
			'cs_order' => '1',
			'cs_option_terms' => '1',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$this->assertEquals( 'summary', $view->get( 'standardStepActive' ) );
		$this->assertArrayHasKey( 'option', $view->get( 'summaryErrorCodes', [] ) );
	}


	public function testProcessSkip()
	{
		$this->object->process();
	}


	protected function getBasket()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );

		$customerManager = \Aimeos\MShop::create( $this->context, 'customer' );
		$address = $customerManager->findItem( 'UTC001' )->getPaymentAddress()->toArray();

		$controller->addAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $address );
		$controller->addAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $address );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$controller->addProduct( $productManager->findItem( 'CNE' ), 2 );

		$domains = ['media', 'price', 'text'];
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$controller->addService( $serviceManager->findItem( 'unitpaymentcode', $domains, 'service', 'payment' ) );
		$controller->addService( $serviceManager->findItem( 'unitcode', $domains, 'service', 'delivery' ) );

		return $controller->get();
	}
}
