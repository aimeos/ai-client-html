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
	}


	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$this->object->setView( $this->object->addData( \TestHelperHtml::getView() ) );

		$output = $this->object->getBody();

		$this->assertContains( '<div class="account-subscription-list">', $output );
		$this->assertRegExp( '#<li class="subscription-item">#', $output );
		$this->assertRegExp( '#<li class="attr-item subscription-basic">.*<span class="value">[^<]+</span>.*</li>#smU', $output );
		$this->assertRegExp( '#<li class="attr-item subscription-interval">.*<span class="value">[^<]+</span>.*</li>#smU', $output );
		$this->assertRegExp( '#<li class="attr-item subscription-datenext">.*<span class="value">[^<]+</span>.*</li>#smU', $output );
		$this->assertRegExp( '#<li class="attr-item subscription-dateend">.*<span class="value">.*</span>.*</li>#smU', $output );
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
}
