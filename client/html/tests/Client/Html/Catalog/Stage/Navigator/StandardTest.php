<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Stage\Navigator;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Navigator\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_pos' => 1 ) );
		$this->view->addHelper( 'param', $helper );

		$this->view->navigationPrev = '#';
		$this->view->navigationNext = '#';

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<!-- catalog.stage.navigator -->', $output );
		$this->assertStringContainsString( '<a class="prev"', $output );
		$this->assertStringContainsString( '<a class="next"', $output );
	}


	public function testModifyHeader()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_pos' => 1 ) );
		$this->view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->object->modifyHeader( $content, 1 );

		$this->assertStringContainsString( '<!-- catalog.stage.navigator -->', $output );
	}


	public function testModifyBody()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'd_pos' => 1 ) );
		$this->view->addHelper( 'param', $helper );

		$content = '<!-- catalog.stage.navigator -->test<!-- catalog.stage.navigator -->';
		$output = $this->object->modifyBody( $content, 1 );

		$this->assertStringContainsString( '<div class="catalog-stage-navigator">', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
