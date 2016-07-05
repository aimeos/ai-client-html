<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Client\Html\Catalog\Lists\Type;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$view = \TestHelperHtml::getView();
		$context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();

		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Type\Standard( $context, $paths );
		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-list-type">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
