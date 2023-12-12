<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Suggest;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Suggest\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testBody()
	{
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'Unterpro' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$suggestItems = $this->view->suggestItems;

		$this->assertMatchesRegularExpression( '#\[\{"label":"Unterpro.*","html":".*Unterpro.*"\}\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $item );
		}
	}


	public function testBodyUseCodes()
	{
		$this->context->config()->set( 'client/html/catalog/suggest/usecode', true );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'CNC' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$suggestItems = $this->view->suggestItems;

		$this->assertMatchesRegularExpression( '#\[.*\{"label":"Cafe.*","html":".*Cafe.*"\}.*\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $item );
		}
	}
}
