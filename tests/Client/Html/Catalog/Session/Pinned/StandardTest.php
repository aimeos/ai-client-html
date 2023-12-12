<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Session\Pinned;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Session\Pinned\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		$this->context->session()->set( 'aimeos/catalog/session/pinned/list', null );

		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$pinned = array( $this->getProductItem( 'CNC' )->getId() );
		$this->context->session()->set( 'aimeos/catalog/session/pinned/list', $pinned );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#.*Cafe Noire Cappuccino.*#smU', $output );
		$this->assertStringStartsWith( '<div class="section catalog-session-pinned">', $output );
	}


	public function testInitAdd()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();

		$param = array(
			'pin_action' => 'add',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$pinned = $this->context->session()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( array( $prodId => $prodId ), $pinned );
	}


	public function testInitDelete()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();
		$this->context->session()->set( 'aimeos/catalog/session/pinned/list', array( $prodId => $prodId ) );

		$param = array(
			'pin_action' => 'delete',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$pinned = $this->context->session()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( [], $pinned );
	}


	/**
	 * Returns the product for the given code.
	 *
	 * @param string $code Unique product code
	 * @throws \Exception If no product is found
	 * @return \Aimeos\MShop\Product\Item\Iface
	 */
	protected function getProductItem( $code )
	{
		return \Aimeos\MShop::create( $this->context, 'product' )->find( $code );
	}
}
