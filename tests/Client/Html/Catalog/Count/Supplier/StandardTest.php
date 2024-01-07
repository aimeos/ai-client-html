<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
 */


namespace Aimeos\Client\Html\Catalog\Count\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Count\Supplier\Standard( \TestHelper::context() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( \TestHelper::view() ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '{"', $output );
	}
}
