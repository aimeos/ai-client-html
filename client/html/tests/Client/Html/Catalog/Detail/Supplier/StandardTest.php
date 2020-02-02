<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 */


namespace Aimeos\Client\Html\Catalog\Detail\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();

		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Supplier\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$tags = [];
		$expire = null;
		$view = $this->object->getView();
		$view->detailProductItem = \Aimeos\MShop::create( $this->context, 'product' )->findItem( 'CNC' );

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertEquals( '', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );

		$rendered = $view->block()->get( 'catalog/detail/supplier' );
		$this->assertStringStartsWith( '<div class="catalog-detail-supplier', $rendered );
	}

	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
