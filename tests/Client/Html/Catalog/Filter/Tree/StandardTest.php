<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Catalog\Filter\Tree;


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

		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Tree\Standard( $this->context );
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
		$catalogManager = \Aimeos\MShop::create( \TestHelper::context(), 'catalog' );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $node->getChild( 1 )->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( 'Groups', $output );
		$this->assertStringContainsString( 'Neu', $output );
		$this->assertStringContainsString( 'level-2', $output );

		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 9, count( $tags ) );
	}


	public function testBodyLevelsAlways()
	{
		$catalogManager = \Aimeos\MShop::create( \TestHelper::context(), 'catalog' );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$this->context->config()->set( 'controller/frontend/catalog/levels-always', 2 );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $node->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( 'level-2', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 9, count( $tags ) );
	}


	public function testBodyLevelsOnly()
	{
		$catalogManager = \Aimeos\MShop::create( \TestHelper::context(), 'catalog' );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->context->config()->set( 'controller/frontend/catalog/levels-only', 1 );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, array( 'f_catid' => $node->getChild( 0 )->getId() ) );
		$this->view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringNotContainsString( 'level-2', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testBodyLevelsDeep()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringContainsString( 'level-2', $output );
		$this->assertEquals( '2098-01-01 00:00:00', $expire );
		$this->assertEquals( 9, count( $tags ) );
	}
}
