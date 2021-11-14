<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Common\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $client;
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->client = $this->getMockBuilder( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard' )
			->setMethods( array( 'header', 'body', 'testMethod' ) )
			->setConstructorArgs( array( $this->context, [] ) )
			->getMock();

		$this->object = new \Aimeos\Client\Html\Common\Decorator\Example( $this->client, $this->context, [] );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
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


	public function testHeader()
	{
		$this->client->expects( $this->once() )->method( 'header' )->will( $this->returnValue( 'header' ) );
		$this->assertEquals( 'header', $this->object->header() );
	}


	public function testBody()
	{
		$this->client->expects( $this->once() )->method( 'body' )->will( $this->returnValue( 'body' ) );
		$this->assertEquals( 'body', $this->object->body() );
	}


	public function testGetView()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Iface', $this->view );
	}


	public function testSetView()
	{
		$this->view = new \Aimeos\MW\View\Standard();
		$this->object->setView( $this->view );

		$this->assertSame( $this->view, $this->view );
	}


	public function testModifyBody()
	{
		$this->assertEquals( 'test', $this->object->modifyBody( 'test', 1 ) );
	}


	public function testModifyHeader()
	{
		$this->assertEquals( 'test', $this->object->modifyHeader( 'test', 1 ) );
	}


	public function testInit()
	{
		$this->object->init();
	}


	public function testResponse()
	{
		$this->assertInstanceOf( '\Psr\Http\Message\ResponseInterface', $this->object->response() );
	}


	public function testSetObject()
	{
		$this->assertInstanceOf( \Aimeos\Client\Html\Iface::class, $this->object->setObject( $this->object ) );
	}

}
