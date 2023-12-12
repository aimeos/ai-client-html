<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Basket\Standard;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();

		$this->view = \TestHelper::view();
		$this->view->standardBasket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\Client\Html\Basket\Standard\Standard( $this->context );
		$this->object = new \Aimeos\Client\Html\Common\Decorator\Exceptions( $this->object, $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::create( $this->context, 'basket' )->clear();
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$tags = [];
		$expire = null;

		$this->object->setView( $this->object->data( $this->view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Basket | Aimeos</title>', $output );
	}


	public function testBody()
	{
		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos basket-standard', $output );
		$this->assertStringContainsString( '<div class="common-summary-detail', $output );
		$this->assertStringContainsString( '<div class="basket-standard-coupon', $output );
	}


	public function testBodyAddSingle()
	{
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'CNE' )->getId(),
			'b_quantity' => 1,
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="price.+18.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="subtotal.+<div class="price.+18.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="delivery.+<div class="price.+1.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+19.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="tax.+<div class="price.+3.03 .+</div>#smU', $output );
	}


	public function testBodyAddMulti()
	{
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

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="price.+18.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="price.+600.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="subtotal.+<div class="price.+618.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="delivery.+<div class="price.+31.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+649.00 .+</div>#smU', $output );
	}


	public function testBodyAddVariantAttribute()
	{
		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$search = $attrManager->filter();
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->or( array(
				$search->and( array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type', 'length' ),
				) ),
				$search->and( array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type', 'width' ),
				) ),
			) ),
		);
		$search->setConditions( $search->and( $expr ) );
		$attributes = $attrManager->search( $search );

		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'U:TEST' )->getId(),
			'b_quantity' => 2,
			'b_attrvarid' => $attributes->keys()->toArray(),
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<li class="attr-item.*<span class="value">.*30.*</span>.*</li>.*<li class="attr-item.*<span class="value">.*30.*</span>.*</li>#smU', $output );
	}


	public function testBodyAddConfigAttribute()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type', 'color' ),
		);
		$search->setConditions( $search->and( $expr ) );

		if( ( $attribute = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No attribute' );
		}

		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'CNE' )->getId(),
			'b_quantity' => 2,
			'b_attrconfid' => ['id' => [0 => $attribute->getId()], 'qty' => [0 =>1]],
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<li class="attr-item.*<span class="value">.*weiß.*</span>.*</li>#smU', $output );
	}


	public function testBodyAddCustomAttribute()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'custom' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type', 'date' ),
		);
		$search->setConditions( $search->and( $expr ) );

		if( ( $attribute = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No attribute' );
		}

		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->getProductItem( 'U:TESTP' )->getId(),
			'b_quantity' => 2,
			'b_attrcustid' => array( $attribute->getId() => '2000-01-01' ),
			'b_stocktype' => 'default',
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<li class="attr-item.*<span class="value">.*2000-01-01.*</span>.*</li>#smU', $output );
	}


	public function testBodyEditSingle()
	{
		$this->addProduct( 'CNE', 2, 'default' );

		$param = array(
			'b_action' => 'edit',
			'b_position' => 0,
			'b_quantity' => 1,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="price.+18.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="subtotal.+<div class="price.+18.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="delivery.+<div class="price.+1.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+19.00 .+</div>#smU', $output );
	}


	public function testBodyEditMulti()
	{
		$this->addProduct( 'CNE', 1, 'default' );
		$this->addProduct( 'CNC', 2, 'default' );

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

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="price.+36.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="price.+600.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="subtotal.+<div class="price.+636.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="delivery.+<div class="price.+32.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+668.00 .+</div>#smU', $output );
	}


	public function testBodyDeleteSingle()
	{
		$this->addProduct( 'CNE', 2, 'default' );
		$this->addProduct( 'CNC', 1, 'default' );

		$param = array(
			'b_action' => 'delete',
			'b_position' => 1,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="price.+36.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="subtotal.+<div class="price.+36.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="delivery.+<div class="price.+2.00 .+</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+38.00 .+</div>#smU', $output );
	}


	public function testBodyDeleteMulti()
	{
		$this->addProduct( 'CNE', 1, 'default' );
		$this->addProduct( 'CNC', 1, 'default' );

		$param = array(
			'b_action' => 'delete',
			'b_position' => array( 0, 1 ),
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<div class="total.+<div class="price.+0.00 .+</div>#smU', $output );
	}


	public function testBodyDeleteInvalid()
	{
		$param = array(
			'b_action' => 'delete',
			'b_position' => -1,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 1, count( $this->view->get( 'errors', [] ) ) );
	}


	public function testBodyAddCoupon()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$controller->addProduct( $this->getProductItem( 'CNC' ), 1 );


		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$this->view->standardBasket = $controller->get();
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#<li class="attr-item">.*90AB.*</li>#smU', $output );
	}


	public function testBodyDeleteCoupon()
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$controller->addProduct( $this->getProductItem( 'CNC' ), 1 );


		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();


		$param = array( 'b_action' => 'coupon-delete', 'b_coupon' => '90AB' );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$this->view->standardBasket = $controller->get();
		$output = $this->object->body();

		$this->assertDoesNotMatchRegularExpression( '#<ul class="attr-list">#smU', $output );
	}


	public function testBodyOverwriteCoupon()
	{
		$this->context->config()->set( 'client/html/basket/standard/coupon/overwrite', true );

		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$controller->addProduct( $this->getProductItem( 'CNC' ), 1 );


		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['b_coupon' => '90AB'] );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['b_coupon' => 'OPQR'] );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();

		$controller = \Aimeos\Controller\Frontend::create( $this->context, 'basket' );
		$this->view->standardBasket = $controller->get();
		$output = $this->object->body();

		$this->assertStringContainsString( 'OPQR', $output );
		$this->assertStringNotContainsString( '90AB', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	/**
	 * @param string $code
	 * @param integer $quantity
	 * @param string $stockType
	 */
	protected function addProduct( $code, $quantity, $stockType )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$param = array(
			'b_action' => 'add',
			'b_prodid' => $item->getId(),
			'b_quantity' => $quantity,
			'b_stocktype' => $stockType,
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->init();
	}


	/**
	 * @param string $code
	 */
	protected function getProductItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search, ['price'] )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
