<?php

namespace Aimeos\Client\Html\Catalog\Stock;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */
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

		$this->object = new \Aimeos\Client\Html\Catalog\Stock\Standard( $this->context );
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
		$prodid = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'st_pid' => $prodid ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$this->assertMatchesRegularExpression( '/"' . $prodid . '".*stock-high/', $output );
	}
}
