<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Common\Factory;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
		$config = $this->context->getConfig();

		$config->set( 'client/html/common/decorators/default', [] );
		$config->set( 'client/html/admin/decorators/global', [] );
		$config->set( 'client/html/admin/decorators/local', [] );
	}


	public function testInjectClient()
	{
		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, 'Standard' );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', $client );

		$injectedClient = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, 'Standard' );

		$this->assertSame( $client, $injectedClient );
	}


	public function testInjectClientReset()
	{
		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, 'Standard' );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', $client );
		\Aimeos\Client\Html\Catalog\Filter\Factory::injectClient( '\\Aimeos\\Client\\Html\\Catalog\\Filter\\Standard', null );

		$new = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, 'Standard' );

		$this->assertNotSame( $client, $new );
	}

}
