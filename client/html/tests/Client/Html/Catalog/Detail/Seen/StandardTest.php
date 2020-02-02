<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Client\Html\Catalog\Detail\Seen;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Seen\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$prodid = $this->getProductItem()->getId();

		$session = $this->context->getSession();
		$session->set( 'aimeos/catalog/session/seen/list', array( $prodid => 'test' ) );
		$session->set( 'aimeos/catalog/session/seen/cache', array( $prodid => 'test' ) );

		$view = $this->object->getView();
		$param = array( 'd_prodid' => $prodid );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$str = $session->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $str );
	}


	public function testProcessNoCache()
	{
		$name = $this->getProductItem()->getName( 'url' );
		$session = $this->context->getSession();

		$view = $this->object->getView();
		$param = array( 'd_name' => $name );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$str = $session->get( 'aimeos/catalog/session/seen/list' );
		$this->assertIsArray( $str );
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( \TestHelperHtml::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );

		if( ( $item = $manager->searchItems( $search, ['text'] )->first() ) === null ) {
			throw new \RuntimeException( 'No product item with code "CNE" found' );
		}

		return $item;
	}
}
