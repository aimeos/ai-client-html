<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Update;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $view;
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->view = \TestHelperHtml::getView();

		$this->object = new \Aimeos\Client\Html\Checkout\Update\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object, $this->view );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Update\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$this->assertEquals( null, $object->getHeader() );
	}


	public function testGetBody()
	{
		$this->assertEquals( '', $this->object->getBody() );
	}


	public function testGetBodyHtmlException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Update\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$object->getBody();
	}


	public function testGetBodyFrontendException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Update\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$object->getBody();
	}


	public function testGetBodyMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Update\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$object->setView( $this->view );

		$object->getBody();
	}


	public function testGetBodyException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Checkout\Update\Standard' )
			->setConstructorArgs( array( $this->context, [] ) )
			->setMethods( array( 'addData' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'addData' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->view );

		$object->getBody();
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
		$params = array(
			'code' => 'paypalexpress',
			'orderid' => $this->getOrder( '2011-09-17 16:14:32' )->getId(),
		);

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();
		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $view, $request, '127.0.0.1', 'test' );
		$view->addHelper( 'request', $helper );

		$this->object->process();
	}


	public function testProcessException()
	{
		$mock = $this->getMockBuilder( '\\Aimeos\\Controller\\Frontend\\Service\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['updatePush'] )
			->getMock();

		$mock->expects( $this->once() )->method( 'updatePush' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', $mock );
		$this->object->process();
		\Aimeos\Controller\Frontend\Service\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Service\\Standard', null );

		$this->assertEquals( 500, $this->view->response()->getStatusCode() );
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}
