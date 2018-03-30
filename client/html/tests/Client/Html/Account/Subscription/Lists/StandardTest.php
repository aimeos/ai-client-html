<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Account\Subscription\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Subscription\Lists\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );

		\Aimeos\MShop\Factory::setCache( true );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$this->object->setView( $this->object->addData( \TestHelperHtml::getView() ) );

		$output = $this->object->getBody();

		$this->assertContains( '<div class="account-subscription-list">', $output );
		$this->assertRegExp( '#<li class="subscription-item#', $output );
		$this->assertRegExp( '#<div class="attr-item subscription-basic.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="attr-item subscription-interval.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="attr-item subscription-datenext.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="attr-item subscription-dateend.*<span class="value.*</span>.*</div>#smU', $output );
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
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	/**
	 * @param string $code
	 */
	protected function getCustomerItem( $code )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getSubscription()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'subscription' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) !== false ) {
			return $item;
		}

		throw new \Exception( 'No subscription item found' );
	}
}
