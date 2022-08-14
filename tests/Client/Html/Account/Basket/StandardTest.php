<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */


namespace Aimeos\Client\Html\Account\Basket;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->view = \TestHelper::view();

		$this->object = new \Aimeos\Client\Html\Account\Basket\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

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
		$this->context->setUserId( -1 );
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );

		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="aimeos account-basket', $output );
		$this->assertRegExp( '#<div class="basket-item#', $output );
		$this->assertRegExp( '#<h2 class="basket-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );

		$this->assertStringContainsString( '<div class="account-basket-detail common-summary', $output );
	}
}
