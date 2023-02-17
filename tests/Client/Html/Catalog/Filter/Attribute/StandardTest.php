<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Filter\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Attribute\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<fieldset class="attr-sets attr-color">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-sets attr-length">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-sets attr-width">', $output );
		$this->assertStringContainsString( '<fieldset class="attr-sets attr-size">', $output );

		$this->assertEquals( 0, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testBodyAttributeOrder()
	{

		$conf = new \Aimeos\Base\Config\PHPArray();
		$conf->set( 'client/html/catalog/filter/attribute/types', array( 'color', 'width', 'length' ) );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $this->view, $conf );
		$this->view->addHelper( 'config', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$regex = '/<fieldset class="attr-sets attr-color">.*<fieldset class="attr-sets attr-width">.*<fieldset class="attr-sets attr-length">/smu';
		$this->assertStringNotContainsString( '<fieldset class="attr-sets attr-size">', $output );
		$this->assertMatchesRegularExpression( $regex, $output );
	}


	public function testBodyCategory()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => -1 ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-attribute', $output );
	}


	public function testBodySearchText()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'test' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-attribute', $output );
	}


	public function testBodySearchAttribute()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_attrid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-attribute', $output );
	}
}
