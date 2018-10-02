<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Common\Decorator;


class ClientTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
	}


	public function testDecorateFactoryClientCommon()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateFactoryClientGlobal()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/catalog/filter/decorators/global', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateSubClientCommon()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/common/decorators/default', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context )->getSubClient( 'tree' );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}


	public function testDecorateSubClientGlobal()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/catalog/filter/tree/decorators/global', array( 'Example' ) );

		$object = \Aimeos\Client\Html\Catalog\Filter\Factory::createClient( $this->context )->getSubClient( 'tree' );

		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $object );
	}
}