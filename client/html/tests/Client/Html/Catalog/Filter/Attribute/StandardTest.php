<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Catalog\Filter\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Attribute\Standard( \TestHelperHtml::getContext(), $paths );
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

		$this->assertContains( '<fieldset class="attr-color">', $output );
		$this->assertContains( '<fieldset class="attr-length">', $output );
		$this->assertContains( '<fieldset class="attr-width">', $output );
		$this->assertContains( '<fieldset class="attr-size">', $output );

		$this->assertGreaterThan( 3, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBodyAttributeOrder()
	{
		$view = $this->object->getView();

		$conf = new \Aimeos\MW\Config\PHPArray();
		$conf->set( 'client/html/catalog/filter/attribute/types', array( 'color', 'width', 'length' ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
		$view->addHelper( 'config', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-width">.*<fieldset class="attr-length">/smu';
		$this->assertNotContains( '<fieldset class="attr-size">', $output );
		$this->assertRegexp( $regex, $output );
	}


	public function testGetBodyCategory()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchAttribute()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_attrid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->addData( $view ) );
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
