<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Detail\Seen;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Seen\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testInit()
	{
		$prodid = $this->getProductItem()->getId();

		$session = $this->context->getSession();
		$session->set( 'aimeos/catalog/session/seen/list', array( $prodid => 'test' ) );
		$session->set( 'aimeos/catalog/session/seen/cache', array( $prodid => 'test' ) );

		$param = array( 'd_prodid' => $prodid );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$str = $session->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $str );
	}


	public function testInitNoCache()
	{
		$name = $this->getProductItem()->getName( 'url' );
		$session = $this->context->getSession();

		$param = array( 'd_name' => $name );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$str = $session->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $str );
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( \TestHelperHtml::getContext() );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );

		if( ( $item = $manager->search( $search, ['text'] )->first() ) === null ) {
			throw new \RuntimeException( 'No product item with code "CNE" found' );
		}

		return $item;
	}
}
