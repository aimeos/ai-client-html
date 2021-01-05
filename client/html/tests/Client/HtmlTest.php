<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client;


class HtmlTest extends \PHPUnit\Framework\TestCase
{
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();
	}


	protected function tearDown() : void
	{
		unset( $this->context );
	}


	public function testCreate()
	{
		$client = \Aimeos\Client\Html::create( $this->context, 'account/favorite' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateName()
	{
		$client = \Aimeos\Client\Html::create( $this->context, 'account/favorite', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateNameEmpty()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html::create( $this->context, '' );
	}


	public function testCreateNameParts()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html::create( $this->context, 'account_favorite' );
	}


	public function testCreateNameInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html::create( $this->context, '%account/favorite' );
	}


	public function testCreateNameNotFound()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Favorite\Factory::create( $this->context, 'account/fav' );
	}

}
