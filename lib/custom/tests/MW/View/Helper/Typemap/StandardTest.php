<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\View\Helper\Typemap;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Typemap\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::getContext(), 'attribute' );
		$items = $manager->search( $manager->filter()->slice( 0, 1 ) );

		$result = $this->object->transform( $items );

		$this->assertCount( 1, $result );
		$this->assertIsArray( reset( $result ) );
	}
}
