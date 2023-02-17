<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Account\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();
		$this->context->setUserId( \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' )->getId() );

		$this->object = new \Aimeos\Client\Html\Account\Watch\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();

		$this->assertStringContainsString( '<link class="account-watch"', $output );
		$this->assertStringContainsString( '<script defer class="account-watch"', $output );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos account-watch"', $output );
		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
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
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->onlyMethods( array( 'addListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'addListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $stub );
		$this->object->init();
	}


	public function testInitDeleteItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['product' => ['watch']] );
		$id = $item->getListItems( 'product', 'watch' )->first()->getRefId();
		$this->context->setUserId( $item->getId() );

		$param = ['wat_action' => 'delete', 'wat_id' => $id];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->onlyMethods( array( 'deleteListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'deleteListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $stub );
		$this->object->init();
	}


	public function testInitEditItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com', ['product' => ['watch']] );
		$id = $item->getListItems( 'product', 'watch' )->first()->getRefId();
		$this->context->setUserId( $item->getId() );

		$param = ['wat_action' => 'edit', 'wat_id' => $id];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );


		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Customer\Standard::class )
			->onlyMethods( array( 'addListItem', 'store' ) )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$stub->expects( $this->once() )->method( 'addListItem' );
		$stub->expects( $this->once() )->method( 'store' );


		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Customer\Standard::class, $stub );
		$this->object->init();
	}
}
