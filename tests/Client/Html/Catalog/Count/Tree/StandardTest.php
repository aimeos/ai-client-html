<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Count\Tree;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->object = new \Aimeos\Client\Html\Catalog\Count\Tree\Standard( \TestHelper::context() );
		$this->object->setView( \TestHelper::view() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringContainsString( 'var catalogCounts', $output );
	}
}
