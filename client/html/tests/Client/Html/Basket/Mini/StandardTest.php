<?php

namespace Aimeos\Client\Html\Basket\Mini;


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
		$this->object = new \Aimeos\Client\Html\Basket\Mini\Standard( $this->context, $paths );
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
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Mini\Standard' )
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
		$output = $this->object->getBody();
		$miniBasket = $this->object->getView()->miniBasket;

		$this->assertTrue( $miniBasket instanceof \Aimeos\MShop\Order\Item\Base\Iface );
		$this->assertContains( '<section class="aimeos basket-mini">', $output );
		$this->assertContains( '<div class="basket-mini-main">', $output );
		$this->assertContains( '<div class="basket-mini-product">', $output );
	}


	public function testGetBodyAddedOneProduct()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$productItem = $this->getProductItem( 'CNE' );

		$view = $this->object->getView();

		$controller->addProduct( $productItem->getId(), 9, [], [], [], [], [], 'default' );
		$view->miniBasket = $controller->get();

		$output = $this->object->getBody();

		$controller->clear();

		$this->assertContains( '<div class="basket-mini-product">', $output );
		$this->assertRegExp( '#9#smU', $output );
		$this->assertRegExp( '#171.00#smU', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Mini\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Mini\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Mini\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'setViewParams' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'setViewParams' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertContains( 'test exception', $object->getBody() );
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


	public function testProcess()
	{
		$this->object->process();
	}


	/**
	* @param string $code
	*/
	protected function getProductItem( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}