<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Session\Pinned;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Session\Pinned\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', null );
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$pinned = array( $this->getProductItem( 'CNC' )->getId() );
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', $pinned );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smU', $output );
		$this->assertStringStartsWith( '<section class="catalog-session-pinned">', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testInitAdd()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();

		$param = array(
			'pin_action' => 'add',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$pinned = $this->context->getSession()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( array( $prodId => $prodId ), $pinned );
	}


	public function testInitDelete()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', array( $prodId => $prodId ) );

		$param = array(
			'pin_action' => 'delete',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$pinned = $this->context->getSession()->get( 'aimeos/catalog/session/pinned/list' );
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
		$manager = \Aimeos\MShop::create( $this->context, 'product' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
