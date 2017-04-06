<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Checkout\Standard\Summary;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Summary\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $controller->get();
		$this->object->setView( $view );

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
		$this->object->setView( $view );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="checkout-standard-summary common-summary">', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option container">', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option-account">', $output );
		$this->assertContains( '<div class="checkout-standard-summary-option-terms">', $output );

		$this->assertContains( 'Example company', $output );
		$this->assertContains( 'unitpaymentlabel', $output );
		$this->assertContains( 'unitlabel', $output );
	}


	public function testGetBodyDetail()
	{
		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertContains( '<div class="common-summary-detail container">', $output );
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

		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->object->process();
	}


	public function testProcessComment()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

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
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );


		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$customer = $customerManager->findItem( 'UTC001' );

		$controller->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $customer->getPaymentAddress() );
		$controller->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $customer->getPaymentAddress() );


		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$product = $productManager->findItem( 'CNE' );

		$controller->addProduct( $product->getId(), 2, [], [], [], [], [], 'default' );


		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );

		$service = $serviceManager->findItem( 'unitpaymentcode', [], 'service', 'payment' );
		$controller->setService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT, $service->getId() );

		$service = $serviceManager->findItem( 'unitcode', [], 'service', 'delivery' );
		$controller->setService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY, $service->getId() );


		return $controller->get();
	}
}
