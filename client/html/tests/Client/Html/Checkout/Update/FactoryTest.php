<?php

namespace Aimeos\Client\Html\Checkout\Update;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
	}


	protected function tearDown() : void
	{
		unset( $this->context );
	}


	public function testCreateClient()
	{
		$client = \Aimeos\Client\Html\Checkout\Update\Factory::create( $this->context );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientName()
	{
		$client = \Aimeos\Client\Html\Checkout\Update\Factory::create( $this->context, 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientNameInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Checkout\Update\Factory::create( $this->context, '$$$' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Checkout\Update\Factory::create( $this->context, 'notfound' );
	}

}
