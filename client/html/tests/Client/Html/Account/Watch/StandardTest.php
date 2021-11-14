<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Account\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Account\Watch\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertEquals( null, $object->header() );
	}


	public function testBody()
	{
		$output = $this->object->body();
		$this->assertStringStartsWith( '<section class="aimeos account-watch"', $output );
	}


	public function testBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'watchErrorList' ) );
	}


	public function testInitAddItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );
		$id = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();
		$this->context->setUserId( $item->getId() );

		$param = ['wat_action' => 'add', 'wat_id' => $id];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setMethods( array( 'addListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'addListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $stub );
		$this->object->init();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}


	public function testInitDeleteItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['product' => ['watch']] );
		$id = $item->getListItems( 'product', 'watch' )->first()->getRefId();
		$this->context->setUserId( $item->getId() );

		$param = ['wat_action' => 'delete', 'wat_id' => $id];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setMethods( array( 'deleteListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'deleteListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $stub );
		$this->object->init();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}


	public function testInitEditItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['product' => ['watch']] );
		$id = $item->getListItems( 'product', 'watch' )->first()->getRefId();
		$this->context->setUserId( $item->getId() );

		$param = ['wat_action' => 'edit', 'wat_id' => $id];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->setMethods( array( 'addListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'addListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $stub );
		$this->object->init();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}
}
