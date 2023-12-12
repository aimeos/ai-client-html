<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


namespace Aimeos\Client\Html\Catalog\Filter\Supplier;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Supplier\Standard( $this->context );
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

		$regex = '#<div class="supplier-lists">.*<ul class="attr-list">.*</ul>.*</fieldset>#smu';

		$this->assertMatchesRegularExpression( $regex, $output );
		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBodyCategory()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => -1 ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-supplier', $output );
	}


	public function testBodySearchText()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'test' ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-supplier', $output );
	}


	public function testBodySearchSupplier()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_supid' => array( -1, -2 ) ) );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<div class="section catalog-filter-supplier', $output );
	}
}
