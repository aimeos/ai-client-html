<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Catalog\Session\Pinned;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Session\Pinned\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', null );
		unset( $this->object );
	}


	public function testGetBody()
	{
		$pinned = array( $this->getProductItem( 'CNC' )->getId() );
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', $pinned );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smU', $output );
		$this->assertStringStartsWith( '<section class="catalog-session-pinned">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcessAdd()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();

		$view = $this->object->getView();
		$param = array(
			'pin_action' => 'add',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$pinned = $this->context->getSession()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( array( $prodId => $prodId ), $pinned );
	}


	public function testProcessDelete()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', array( $prodId => $prodId ) );

		$view = $this->object->getView();
		$param = array(
			'pin_action' => 'delete',
			'pin_id' => $prodId,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

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

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
