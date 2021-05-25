<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Lists\Promo;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $catItem;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$search = $catalogManager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		if( ( $this->catItem = $catalogManager->search( $search, ['product'] )->first() ) === null ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$this->view = \TestHelperHtml::getView();
		$this->view->listParams = [];
		$this->view->listCurrentCatItem = $this->catItem;

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Promo\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->getHeader();

		$this->assertStringContainsString( '<script', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringContainsString( '<section class="catalog-list-promo">', $output );
		$this->assertRegExp( '/.*Expresso.*Cappuccino.*/smu', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetBodyDefaultCatid()
	{
		unset( $this->view->listCurrentCatItem );
		$this->object->setView( $this->view );
		$this->context->getConfig()->set( 'client/html/catalog/lists/catid-default', $this->catItem->getId() );

		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();

		$this->assertStringContainsString( '<section class="catalog-list-promo">', $output );
		$this->assertRegExp( '/.*Expresso.*Cappuccino.*/smu', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
