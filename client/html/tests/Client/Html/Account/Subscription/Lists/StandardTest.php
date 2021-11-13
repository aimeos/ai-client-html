<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Client\Html\Account\Subscription\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Subscription\Lists\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$customer = $this->getCustomerItem( 'test@example.com' );
		$this->context->setUserId( $customer->getId() );

		$this->object->setView( $this->object->data( \TestHelperHtml::view() ) );

		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="account-subscription-list">', $output );
		$this->assertRegExp( '#<div class="subscription-item#', $output );
		$this->assertRegExp( '#<h2 class="subscription-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-interval.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-datenext.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-dateend.*<span class="value.*</span>.*</div>#smU', $output );
	}


	public function testInit()
	{
		$this->view = \TestHelperHtml::view();
		$param = array(
			'sub_action' => 'cancel',
			'sub_id' => $this->getSubscription()->getId()
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
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


	/**
	 * @param string $code
	 */
	protected function getCustomerItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getSubscription()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'subscription' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \Exception( 'No subscription item found' );
		}

		return $item;
	}
}
