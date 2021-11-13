<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Checkout\Standard\Payment;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Payment\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->clear();

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$this->view = \TestHelperHtml::view();
		$this->view->standardStepActive = 'payment';
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderSkip()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$this->view = \TestHelperHtml::view();
		$this->view->standardStepActive = 'payment';
		$this->view->standardSteps = array( 'before', 'payment', 'after' );
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->object->setView( $this->object->data( $this->view ) );

		$output = $this->object->body();
		$this->assertStringStartsWith( '<section class="checkout-standard-payment">', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.accountowner mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.accountno mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.bankcode mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="row form-item form-group directdebit.bankname mandatory">#smU', $output );

		$this->assertGreaterThan( 0, count( $this->view->paymentServices ) );
	}


	public function testBodyOtherStep()
	{
		$this->view = \TestHelperHtml::view();
		$this->object->setView( $this->view );

		$output = $this->object->body();
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


	public function testInit()
	{
		$this->object->init();

		$this->assertEquals( 'payment', $this->view->get( 'standardStepActive' ) );
	}


	public function testInitExistingId()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$this->view = \TestHelperHtml::view();

		$param = array(
			'c_paymentoption' => $service->getId(),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->object->init();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context )->get();
		$this->assertEquals( 'unitpaymentcode', $basket->getService( 'payment', 0 )->getCode() );
	}


	public function testInitInvalidId()
	{
		$this->view = \TestHelperHtml::view();

		$param = array( 'c_paymentoption' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->expectException( '\\Aimeos\\MShop\\Exception' );
		$this->object->init();
	}


	public function testInitNotExistingAttributes()
	{
		$manager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );

		if( ( $service = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'Service item not found' );
		}

		$this->view = \TestHelperHtml::view();

		$param = array(
			'c_paymentoption' => $service->getId(),
			'c_payment' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->view );

		$this->expectException( '\\Aimeos\\Controller\\Frontend\\Basket\\Exception' );
		$this->object->init();
	}
}
