<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Client\Html\Basket\Mini;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Basket\Mini\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Basket\Mini\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$output = $this->object->body();
		$miniBasket = $this->object->getView()->miniBasket;

		$this->assertTrue( $miniBasket instanceof \Aimeos\MShop\Order\Item\Base\Iface );
		$this->assertStringContainsString( '<section class="aimeos basket-mini"', $output );
		$this->assertStringContainsString( '<div class="basket-mini-main">', $output );
		$this->assertStringContainsString( '<div class="basket-mini-product">', $output );
	}


	public function testGetBodyAddedOneProduct()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::create( $this->context );

		$productItem = $this->getProductItem( 'CNE' );

		$view = $this->object->getView();

		$controller->addProduct( $productItem, 9 );
		$view->miniBasket = $controller->get();

		$output = $this->object->body();

		$controller->clear();

		$this->assertStringContainsString( '<div class="basket-mini-product">', $output );
		$this->assertRegExp( '#9#smU', $output );
		$this->assertRegExp( '#171.00#smU', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Basket\Mini\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Basket\Mini\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->body() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Basket\Mini\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'data' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertStringContainsString( 'test exception', $object->body() );
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


	public function testProcess()
	{
		$this->object->init();

		$this->assertEmpty( $this->object->getView()->get( 'miniErrorList' ) );
	}


	/**
	 * @param string $code
	 */
	protected function getProductItem( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search, ['price'] )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
