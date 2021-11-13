<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Filter\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Attribute\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<fieldset class="attr-color">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-length">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-width">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-size">', $output );

		$this->assertGreaterThanOrEqual( 3, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testBodyAttributeOrder()
	{

		$conf = new \Aimeos\MW\Config\PHPArray();
		$conf->set( 'client/html/catalog/filter/attribute/types', array( 'color', 'width', 'length' ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $this->view, $conf );
		$this->view->addHelper( 'config', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-width">.*<fieldset class="attr-length">/smu';
		$this->assertStringNotContainsString( '<fieldset class="attr-size">', $output );
		$this->assertRegexp( $regex, $output );
	}


	public function testBodyCategory()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_catid' => -1 ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute', $output );
	}


	public function testBodySearchText()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'test' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute', $output );
	}


	public function testBodySearchAttribute()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_attrid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="catalog-filter-attribute', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
