<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Client\Html\Account\Review\Todo;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

		$customer = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context )->find( 'test@example.com' );
		$this->context->setUserId( $customer->getId() );

		$this->object = new \Aimeos\Client\Html\Account\Review\Todo\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->view = $this->object->data( \TestHelperHtml::view() );
		$this->view->todoProductItems = map( \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' ) );

		$output = $this->object->setView( $this->view )->body();

		$this->assertStringContainsString( '<div class="account-review-todo">', $output );
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


	public function getInit()
	{
		$param = ['review-todo' => [['review.rating' => 5]]];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->object->setView( $this->view );

		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Review\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['save'] )
			->getMock();

		$stub->expects( $this->once() )->method( 'save' );

		\Aimeos\Controller\Frontend\Review\Factory::injectController( '\Aimeos\Controller\Frontend\Review\Standard', $stub );
		$this->object->init();
		\Aimeos\Controller\Frontend\Review\Factory::injectController( '\Aimeos\Controller\Frontend\Review\Standard', null );

		$this->assertEmpty( $this->view->get( 'reviewErrorList' ) );
		$this->assertCount( 1, $this->view->get( 'reviewInfoList' ) );
	}
}
