<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */


namespace Aimeos\Client\Html\Account\Subscription;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->view = \TestHelper::view();
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order/base' )->create();

		$this->object = new \Aimeos\Client\Html\Account\Subscription\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();

		$this->assertStringContainsString( '<link rel="stylesheet"', $output );
		$this->assertStringContainsString( '<script defer', $output );
	}


	public function testBody()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUserId( $manager->find( 'test@example.com' )->getId() );

		$this->object->setView( $this->object->data( \TestHelper::view() ) );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="aimeos account-subscription"', $output );
		$this->assertRegExp( '#<div class="subscription-item#', $output );
		$this->assertRegExp( '#<h2 class="subscription-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-interval.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-datenext.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-dateend.*<span class="value.*</span>.*</div>#smU', $output );

		$this->assertStringContainsString( 'Our Unittest', $output );
		$this->assertStringContainsString( 'Example company', $output );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
	}


	public function testInit()
	{
		$this->view = \TestHelper::view();
		$param = array(
			'sub_action' => 'cancel',
			'sub_id' => $this->getSubscription()->getId()
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );

		$cntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Subscription\\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['cancel'] )
			->getMock();

		\Aimeos\Controller\Frontend\Subscription\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Subscription\\Standard', $cntlStub );

		$cntlStub->expects( $this->once() )->method( 'cancel' );

		$this->object->init();
	}


	protected function getSubscription()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'subscription' );
		$filter = $manager->filter()->add( 'subscription.dateend', '==', '2010-01-01' );

		return $manager->search( $filter )->first( new \Exception( 'No subscription item found' ) );
	}
}
