<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2025
 */


namespace Aimeos\Client\Html\Common\Decorator;


class ExceptionsTest extends \PHPUnit\Framework\TestCase
{
	private $client;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$this->view = \TestHelper::view();

		$this->client = $this->getMockBuilder( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard' )
			->setConstructorArgs( [$context] )
			->onlyMethods( ['body', 'header', 'init'] )
			->getMock();

		$this->object = new \Aimeos\Client\Html\Common\Decorator\Exceptions( $this->client, $context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->view, $this->object, $this->client );
	}


	public function testBody()
	{
		$this->client->expects( $this->once() )->method( 'body' )->willReturn( 'test' );

		$this->assertEquals( 'test', $this->object->body() );
	}


	public function testBodyClientException()
	{
		$this->client->expects( $this->once() )->method( 'body' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testBodyFrontendException()
	{
		$this->client->expects( $this->once() )->method( 'body' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testBodyMShopException()
	{
		$this->client->expects( $this->once() )->method( 'body' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testBodyException()
	{
		$this->client->expects( $this->once() )->method( 'body' )
			->will( $this->throwException( new \Exception() ) );

		$this->assertStringContainsString( 'A non-recoverable error occured', $this->object->body() );
	}


	public function testHeader()
	{
		$this->client->expects( $this->once() )->method( 'header' )->willReturn( 'test' );

		$this->assertEquals( 'test', $this->object->header() );
	}


	public function testHeaderClientException()
	{
		$this->client->expects( $this->once() )->method( 'header' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$this->assertEquals( '', $this->object->header() );
	}


	public function testHeaderFrontendException()
	{
		$this->client->expects( $this->once() )->method( 'header' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$this->assertEquals( '', $this->object->header() );
	}


	public function testHeaderMShopException()
	{
		$this->client->expects( $this->once() )->method( 'header' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$this->assertEquals( '', $this->object->header() );
	}


	public function testHeaderException()
	{
		$this->client->expects( $this->once() )->method( 'header' )
			->will( $this->throwException( new \Exception() ) );

		$this->assertEquals( '', $this->object->header() );
	}


	public function testInit()
	{
		$this->client->expects( $this->once() )->method( 'init' );

		$this->object->init();
	}


	public function testInitClientException()
	{
		$this->client->expects( $this->once() )->method( 'init' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$this->object->init();

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testInitFrontendException()
	{
		$this->client->expects( $this->once() )->method( 'init' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$this->object->init();

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testInitMShopException()
	{
		$this->client->expects( $this->once() )->method( 'init' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$this->object->init();

		$this->assertStringContainsString( 'test exception', $this->object->body() );
	}


	public function testInitException()
	{
		$this->client->expects( $this->once() )->method( 'init' )
			->will( $this->throwException( new \Exception() ) );

		$this->object->init();

		$this->assertStringContainsString( 'A non-recoverable error occured', $this->object->body() );
	}
}
