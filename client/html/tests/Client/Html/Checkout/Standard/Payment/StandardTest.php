<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Checkout\Standard\Payment;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Payment\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();

		unset( $this->object, $this->context );
	}


	public function testGetHeader()
	{
		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'payment';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderSkip()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$view->standardStepActive = 'payment';
		$view->standardSteps = array( 'before', 'payment', 'after' );
		$view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->createItem();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-payment">', $output );
		$this->assertRegExp( '#<li class="form-item form-group directdebit.accountowner mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item form-group directdebit.accountno mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item form-group directdebit.bankcode mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item form-group directdebit.bankname mandatory">#smU', $output );

		$this->assertGreaterThan( 0, count( $view->paymentServices ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = \TestHelperHtml::getView();
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
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

		$this->assertEquals( 'payment', $this->object->getView()->get( 'standardStepActive' ) );
	}


	public function testProcessExistingId()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$view = \TestHelperHtml::getView();

		$param = array(
			'c_paymentoption' => $service->getId(),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'unitpaymentcode', $basket->getService( 'payment', 0 )->getCode() );
	}


	public function testProcessInvalidId()
	{
		$view = \TestHelperHtml::getView();

		$param = array( 'c_paymentoption' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->expectException( '\\Aimeos\\MShop\\Exception' );
		$this->object->process();
	}


	public function testProcessNotExistingAttributes()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$view = \TestHelperHtml::getView();

		$param = array(
			'c_paymentoption' => $service->getId(),
			'c_payment' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->expectException( '\\Aimeos\\Controller\\Frontend\\Basket\\Exception' );
		$this->object->process();
	}
}
