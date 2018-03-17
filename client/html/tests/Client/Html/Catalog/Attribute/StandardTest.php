<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Catalog\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Attribute\Standard( \TestHelperHtml::getContext() );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertContains( '<section class="aimeos catalog-filter"', $output );
		$this->assertContains( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'attribute', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
	}
}
