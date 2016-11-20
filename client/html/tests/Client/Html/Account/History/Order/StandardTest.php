<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Account\History\Order;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = clone \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Account\History\Order\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$view = $this->object->getView();
		$param = array(
			'his_action' => 'order',
			'his_id' => $this->getOrderItem( $customer->getId() )->getId()
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="account-history-order common-summary">', $output );

		$this->assertContains( 'Our Unittest', $output );
		$this->assertContains( 'Example company', $output );

		$this->assertContains( '<h4>solucia</h4>', $output );
		$this->assertContains( '<h4>ogone</h4>', $output );

		$this->assertContains( '>5678<', $output );
		$this->assertContains( 'This is a comment', $output );

		$this->assertContains( 'Cafe Noire Expresso', $output );
		$this->assertContains( 'Cafe Noire Cappuccino', $output );
		$this->assertContains( 'Unittest: Monetary rebate', $output );
		$this->assertContains( '<td class="price">55.00 EUR</td>', $output );
		$this->assertContains( '<td class="value">14 articles</td>', $output );

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


	protected function getOrderItem( $customerid )
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'order.base.customerid', $customerid )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
