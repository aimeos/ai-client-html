<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2020
 */


namespace Aimeos\Client\Html\Account\Subscription\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Subscription\Lists\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$this->object->setView( $this->object->addData( \TestHelperHtml::getView() ) );

		$output = $this->object->getBody();

		$this->assertStringContainsString( '<div class="account-subscription-list">', $output );
		$this->assertRegExp( '#<div class="subscription-item#', $output );
		$this->assertRegExp( '#<h2 class="subscription-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-interval.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-datenext.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="subscription-dateend.*<span class="value.*</span>.*</div>#smU', $output );
	}


	public function testProcess()
	{
		$view = \TestHelperHtml::getView();
		$param = array(
			'sub_action' => 'cancel',
			'sub_id' => $this->getSubscription()->getId()
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );

		$cntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Subscription\\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['cancel'] )
			->getMock();

		\Aimeos\Controller\Frontend\Subscription\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Subscription\\Standard', $cntlStub );

		$cntlStub->expects( $this->once() )->method( 'cancel' );

		$this->object->process();
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
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );

		if( ( $item = $manager->searchItems( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getSubscription()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'subscription' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );

		if( ( $item = $manager->searchItems( $search )->first() ) === null ) {
			throw new \Exception( 'No subscription item found' );
		}

		return $item;
	}
}
