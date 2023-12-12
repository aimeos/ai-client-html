<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */


namespace Aimeos\Client\Html\Basket\Bulk;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Basket\Bulk\Standard( $this->context );
		$this->object->setView( \TestHelper::view() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context );
	}


	public function testHeader()
	{
		$output = $this->object->header();

		$this->assertStringContainsString( '<link rel="stylesheet"', $output );
		$this->assertStringContainsString( '<script defer', $output );
	}


	public function testBody()
	{
		$this->assertStringContainsString( '<div class="section aimeos basket-bulk"', $this->object->body() );
	}
}
