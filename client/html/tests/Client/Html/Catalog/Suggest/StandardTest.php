<?php

namespace Aimeos\Client\Html\Catalog\Suggest;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Suggest\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
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


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Suggest\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'Unterpro' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$suggestItems = $this->object->getView()->suggestItems;

		$this->assertRegExp( '#\[\{"label":"Unterpro.*","html":".*Unterpro.*"\}\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $item );
		}
	}


	public function testGetBodyUseCodes()
	{
		$this->context->getConfig()->set( 'client/html/catalog/suggest/usecode', true );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'U:TEST' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$suggestItems = $this->object->getView()->suggestItems;

		$this->assertRegExp( '#\[\{"label":"Unit.*","html":".*Unit.*"\}.*\]#smU', $output );
		$this->assertNotEquals( [], $suggestItems );

		foreach( $suggestItems as $item ) {
			$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $item );
		}
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Catalog\Suggest\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->getBody() );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}
}
