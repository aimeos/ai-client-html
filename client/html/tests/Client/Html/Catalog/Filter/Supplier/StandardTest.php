<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\Client\Html\Catalog\Filter\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Supplier\Standard( \TestHelperHtml::getContext() );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->body();

		$regex = '#<div class="supplier-lists">.*<ul class="attr-list">.*<li.*<li.*</ul>.*</fieldset>#smu';
		$this->assertRegexp( $regex, $output );

		$this->assertGreaterThan( 2, count( $tags ) );
		$this->assertEquals( '2100-01-01 00:00:00', $expire );
	}


	public function testBodyCategory()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier', $output );
	}


	public function testBodySearchText()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier', $output );
	}


	public function testBodySearchSupplier()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_supid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
