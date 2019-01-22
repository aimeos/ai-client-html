<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Account\Subscription\Detail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Subscription\Detail\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$view = \TestHelperHtml::getView();
		$param = array(
			'sub_action' => 'detail',
			'sub_id' => $this->getSubscriptionItem( $customer->getId() )->getId()
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="account-subscription-detail common-summary', $output );

		$this->assertContains( 'Our Unittest', $output );
		$this->assertContains( 'Example company', $output );

		$this->assertContains( 'Cafe Noire Expresso', $output );
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


	protected function getSubscriptionItem( $customerid )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'subscription' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.customerid', $customerid ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No subscription item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
