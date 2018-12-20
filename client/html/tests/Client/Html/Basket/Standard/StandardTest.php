<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Basket\Standard;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Basket\Standard\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Standard\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getHeader();
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos basket-standard"', $output );
		$this->assertContains( '<div class="common-summary-detail', $output );
		$this->assertContains( '<div class="basket-standard-coupon', $output );
	}


	public function testGetBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Standard\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Standard\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyMShopException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Standard\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->getBody();
	}


	public function testGetBodyException()
	{
		$mock = $this->getMockBuilder( '\Aimeos\Client\Html\Basket\Standard\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$mock->setView( \TestHelperHtml::getView() );

		$mock->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->getBody();
	}


	public function testGetBodyAddSingle()
	{
		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'CNE' )->getId(),
			'b_quantity' => 1,
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">18.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">1.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">19.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="tax">.*<td class="price">3.03 .+</td>.*.*</tfoot>#smU', $output );
	}


	public function testGetBodyAddMulti()
	{
		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prod' => array(
				array(
					'prodid' => $this->getProductItem( 'CNC' )->getId(),
					'quantity' => 1,
					'stocktype' => 'default',
				),
				array(
					'prodid' => $this->getProductItem( 'CNE' )->getId(),
					'quantity' => 1,
					'stocktype' => 'default',
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="price">600.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">618.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">31.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">649.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyAddVariantAttribute()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->combine( '||', array(
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type.code', 'length' ),
				) ),
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type.code', 'width' ),
				) ),
			) ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$attributes = $attrManager->searchItems( $search, [] );

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'U:TEST' )->getId(),
			'b_quantity' => 2,
			'b_attrvarid' => array_keys( $attributes ),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item.*<span class="value">.*30.*</span>.*</li>.*<li class="attr-item.*<span class="value">.*30.*</span>.*</li>#smU', $output );
	}


	public function testGetBodyAddConfigAttribute()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrManager->searchItems( $search, [] );

		if( ( $attribute = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No attribute' );
		}

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'CNE' )->getId(),
			'b_quantity' => 2,
			'b_attrconfid' => ['id' => [0 => $attribute->getId()], 'qty' => [0 =>1]],
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item.*<a class="change" href=[^>]*>.*<span class="value">weiß</span>.*</a>.*</li>#smU', $output );
	}


	public function testGetBodyAddHiddenAttribute()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'm' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'size' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrManager->searchItems( $search, [] );

		if( ( $attribute = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No attribute' );
		}

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'CNE' )->getId(),
			'b_quantity' => 2,
			'b_attrhideid' => $attribute->getId(),
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertNotRegExp( '#<li class="attr-item.*<span class="value">m</span>.*</li>#smU', $output );
	}


	public function testGetBodyAddCustomAttribute()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$search = $attrManager->createSearch();
		$expr = array(
				$search->compare( '==', 'attribute.code', 'custom' ),
				$search->compare( '==', 'attribute.domain', 'product' ),
				$search->compare( '==', 'attribute.type.code', 'date' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrManager->searchItems( $search, [] );

		if( ( $attribute = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No attribute' );
		}

		$view = $this->object->getView();
		$param = array(
				'b_action' => 'add',
				'b_prodid' => $this->getProductItem( 'U:TESTP' )->getId(),
				'b_quantity' => 2,
				'b_attrcustid' => array( $attribute->getId() => '2000-01-01' ),
				'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item.*<span class="value">2000-01-01</span>.*</li>#smU', $output );
	}


	public function testGetBodyEditSingle()
	{
		$this->addProduct( 'CNE', 2, 'default' );

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'edit',
			'b_position' => 0,
			'b_quantity' => 1,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">18.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">1.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">19.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyEditMulti()
	{
		$this->addProduct( 'CNE', 1, 'default' );
		$this->addProduct( 'CNC', 2, 'default' );

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'edit',
			'b_prod' => array(
				array(
					'position' => 0,
					'quantity' => 2,
				),
				array(
					'position' => 1,
					'quantity' => 1,
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">36.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="price">600.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">636.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">32.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">668.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteSingle()
	{
		$this->addProduct( 'CNE', 2, 'default' );
		$this->addProduct( 'CNC', 1, 'default' );

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => 1,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">36.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">36.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">2.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">38.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteMulti()
	{
		$this->addProduct( 'CNE', 1, 'default' );
		$this->addProduct( 'CNC', 1, 'default' );

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => array( 0, 1 ),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">0.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteInvalid()
	{
		$view = $this->object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => -1,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$this->assertEquals( 1, count( $view->get( 'standardErrorList', [] ) ) );
	}


	public function testGetBodyAddCoupon()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1 );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*90ab.*</li>#smU', $output );
	}


	public function testGetBodyDeleteCoupon()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1 );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();


		$param = array( 'b_action' => 'coupon-delete', 'b_coupon' => '90AB' );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertNotRegExp( '#<ul class="attr-list">#smU', $output );
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


	/**
	 * @param string $code
	 * @param integer $quantity
	 * @param string $stockType
	 */
	protected function addProduct( $code, $quantity, $stockType )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$view = $this->object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $item->getId(),
			'b_quantity' => $quantity,
			'b_stocktype' => $stockType,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

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
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
