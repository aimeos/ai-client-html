<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Test class for \Aimeos\Client\Html\Common\Decorator\Example.
 */
class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $client;
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp() : void
	{
		$context = \TestHelperHtml::getContext();

		$this->client = $this->getMockBuilder( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard' )
			->setMethods( array( 'header', 'body', 'testMethod' ) )
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


	public function testInit()
	{
		$this->object->init();
	}


	public function testSetObject()
	{
		$this->assertInstanceOf( \Aimeos\Client\Html\Iface::class, $this->object->setObject( $this->object ) );
	}

}
