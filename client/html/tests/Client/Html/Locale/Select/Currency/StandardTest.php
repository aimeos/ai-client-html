<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Locale\Select\Currency;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Locale\Select\Currency\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view->selectCurrencyId = 'EUR';
		$this->view->selectLanguageId = 'de';
		$this->view->selectMap = map( [
			'de' => array(
				'EUR' => array( 'locale' => 'de', 'currency' => 'EUR' ),
				'CHF' => array( 'locale' => 'de', 'currency' => 'CHF' ),
			),
			'en' => array( 'USD' => array( 'locale' => 'en', 'currency' => 'USD' ) ),
		] );

		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view->addHelper( 'request', $helper );

		$tags = [];
		$expire = null;
		$output = $this->object->body( 1, $tags, $expire );

		$this->assertStringStartsWith( '<div class="locale-select-currency">', $output );
		$this->assertStringContainsString( '<li class="select-dropdown select-current"><a href="#">EUR', $output );
		$this->assertStringContainsString( '<li class="select-item active">', $output );

		$this->assertEquals( 0, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testInit()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['currency' => 'EUR'] );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 'EUR', $this->context->getSession()->get( 'aimeos/locale/currencyid' ) );
	}
}
