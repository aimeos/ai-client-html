<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Catalog\Filter\Tree;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Tree\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object );
	}


	public function testGetBody()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperHtml::getContext() );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getChild( 1 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertContains( 'Groups', $output );
		$this->assertContains( 'Neu', $output );
		$this->assertContains( 'level-2', $output );

		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetBodyLevelsAlways()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperHtml::getContext() );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$this->context->getConfig()->set( 'client/html/catalog/filter/tree/levels-always', 2 );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 3, count( $tags ) );
	}


	public function testGetBodyLevelsOnly()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperHtml::getContext() );
		$node = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->context->getConfig()->set( 'client/html/catalog/filter/tree/levels-only', 1 );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $node->getChild( 0 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->addData( $view, $tags, $expire ) );
		$output = $this->object->getBody();

		$this->assertNotContains( 'level-2', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 2, count( $tags ) );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
