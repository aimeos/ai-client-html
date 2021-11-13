<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Client\Html\Catalog\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Attribute\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="aimeos catalog-filter"', $output );
		$this->assertStringContainsString( '<section class="catalog-filter-attribute', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'attribute', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
	}
}
