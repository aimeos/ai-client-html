<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Account\History\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\History\Lists\Standard( $this->context );
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

		$this->assertStringContainsString( '<div class="account-history-list">', $output );
		$this->assertRegExp( '#<div class="history-item#', $output );
		$this->assertRegExp( '#<h2 class="order-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );
		$this->assertRegExp( '#<div class="order-channel.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="order-payment.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertRegExp( '#<div class="order-delivery.*<span class="value.*</span>.*</div>#smU', $output );
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
		$manager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
