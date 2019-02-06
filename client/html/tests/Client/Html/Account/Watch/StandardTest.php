<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Account\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Account\Watch\Standard( $this->context );
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
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos account-watch"', $output );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( \TestHelperHtml::getView() );

		$this->assertContains( 'test exception', $object->getBody() );
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Client\Html\Account\Watch\Standard::class )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
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


	public function testProcessAddItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'add',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'saveItem', 'moveItem' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'mshop/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'moveItem' );
		$listManagerStub->expects( $this->once() )->method( 'saveItem' )
			->will( $this->returnValue( $listManagerStub->createItem() ) );


		$this->object->process();
	}


	public function testProcessEditItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'edit',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );


		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'mshop/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$item = $listManagerStub->createItem();
		$item->setRefId( 321 );

		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $item ) ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );


		$this->object->process();
	}


	public function testProcessDeleteItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'delete',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'mshop/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'deleteItems' );


		$this->object->process();
	}
}