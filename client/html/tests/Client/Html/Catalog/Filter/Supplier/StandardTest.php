<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\Html\Catalog\Filter\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Supplier\Standard( \TestHelperHtml::getContext(), $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $this->object->getView(), $tags, $expire ) );
		$output = $this->object->getBody();

		$regex = '#<fieldset class="supplier-lists">.*<ul class="attr-list">.*<li.*<li.*</ul>.*</fieldset>#smu';
		$this->assertRegexp( $regex, $output );

		$this->assertGreaterThan( 2, count( $tags ) );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
	}


	public function testGetBodyCategory()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier">', $output );
	}


	public function testGetBodySearchSupplier()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_supid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-supplier">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
