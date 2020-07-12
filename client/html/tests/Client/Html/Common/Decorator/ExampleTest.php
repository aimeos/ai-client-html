<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Common\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $client;
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperHtml::getContext();

		$this->client = $this->getMockBuilder( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard' )
			->setMethods( array( 'getHeader', 'getBody', 'testMethod' ) )
			->setConstructorArgs( array( $context, [] ) )
			->getMock();

		$this->object = new \Aimeos\Client\Html\Common\Decorator\Example( $this->client, $context, [] );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	public function testCall()
	{
		$this->client->expects( $this->once() )->method( 'testMethod' ) ->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->testMethod() );
	}


	public function testGetSubClient()
	{
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $this->object->getSubClient( 'tree' ) );
	}


	public function testGetHeader()
	{
		$this->client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );
		$this->assertEquals( 'header', $this->object->getHeader() );
	}


	public function testGetBody()
	{
		$this->client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$this->assertEquals( 'body', $this->object->getBody() );
	}


	public function testGetView()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Iface', $this->object->getView() );
	}


	public function testSetView()
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object->setView( $view );

		$this->assertSame( $view, $this->object->getView() );
	}


	public function testModifyBody()
	{
		$this->assertEquals( 'test', $this->object->modifyBody( 'test', 1 ) );
	}


	public function testModifyHeader()
	{
		$this->assertEquals( 'test', $this->object->modifyHeader( 'test', 1 ) );
	}


	public function testProcess()
	{
		$this->object->process();
	}


	public function testSetObject()
	{
		$this->assertInstanceOf( \Aimeos\Client\Html\Iface::class, $this->object->setObject( $this->object ) );
	}

}
