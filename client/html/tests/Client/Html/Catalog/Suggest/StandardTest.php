<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Catalog\Suggest;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Catalog\Suggest\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();
		$this->assertNotNull( $output );
	}


	public function testHeaderException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Suggest\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertEquals( null, $object->header() );
	}


	public function testBody()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'Unterpro' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$suggestItems = $this->view->suggestItems;

		$this->assertRegExp( '#\[\{"label":"Unterpro.*","html":".*Unterpro.*"\}\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $item );
		}
	}


	public function testBodyUseCodes()
	{
		$this->context->getConfig()->set( 'client/html/catalog/suggest/usecode', true );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, array( 'f_search' => 'CNC' ) );
		$this->view->addHelper( 'param', $helper );

		$output = $this->object->body();
		$suggestItems = $this->view->suggestItems;

		$this->assertRegExp( '#\[.*\{"label":"Cafe.*","html":".*Cafe.*"\}.*\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $item );
		}
	}


	public function testBodyException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Catalog\Suggest\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::view() );

		$this->assertEquals( null, $object->body() );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}
}
