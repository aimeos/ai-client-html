<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Count\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\Client\Html\Catalog\Count\Attribute\Standard( \TestHelperHtml::getContext() );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringContainsString( 'var attributeCount', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
