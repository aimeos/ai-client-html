<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Count;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Catalog\Count\Standard( $this->context );
		$this->object->setView( \TestHelper::view() );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringStartsWith( '{"', $output );
	}


	public function testHeader()
	{
		$this->object->setView( $this->object->data( \TestHelper::view() ) );

		$output = $this->object->header();

		$this->assertNotNull( $output );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'tree', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}

}
