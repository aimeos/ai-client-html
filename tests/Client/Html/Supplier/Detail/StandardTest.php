<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2022
 */


namespace Aimeos\Client\Html\Supplier\Detail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Test supplier | Aimeos</title>', $output );
		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBody()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_supid' => $this->getSupplierItem()->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<section class="aimeos supplier-detail"', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-basic', $output );
		$this->assertStringContainsString( '<div class="supplier-detail-image', $output );

		$this->assertEquals( '2100-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testBodyDefaultId()
	{
		$context = clone $this->context;
		$context->config()->set( 'client/html/supplier/detail/supid-default', $this->getSupplierItem()->getId() );

		$this->object = new \Aimeos\Client\Html\Supplier\Detail\Standard( $context );
		$this->object->setView( \TestHelper::view() );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, [] );
		$this->view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringContainsString( '<h1 class="name" itemprop="name">Test supplier</h1>', $output );
	}


	protected function getSupplierItem( $code = 'unitSupplier001', $domains = [] )
	{
		return \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context )->find( $code, $domains );
	}
}
