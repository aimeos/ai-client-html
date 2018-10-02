<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Account;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Account\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::clear();
		\Aimeos\MShop\Factory::setCache( false );

		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();

		unset( $this->object, $this->object );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertNotNull( $output );
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
		$customerItem = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context )->findItem( 'UTC001' );

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setEmail( 'unittest@aimeos.org' );

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$basketCntl->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $addrItem );

		$view = \TestHelperHtml::getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'cs_option_account' => 1 ) );
		$view->addHelper( 'param', $helper );
		$this->object->setView( $view );

		$customerStub = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Customer\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'addItem' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'addItem' )
			->will( $this->returnValue( $customerStub->createItem() ) );

		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $customerStub );
		$this->object->process();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}
}
