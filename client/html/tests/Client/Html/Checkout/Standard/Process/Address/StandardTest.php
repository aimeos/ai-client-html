<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Process\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Process\Address\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();

		unset( $this->object, $this->context );
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
		$addrItem->setId( null );

		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$basketCntl->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $addrItem );

		$this->context->setUserId( $customerItem->getId() );
		$basketCntl->get()->setCustomerId( $customerItem->getId() );

		$customerStub = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Customer\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveAddressItem' ) )
			->getMock();

		$customerStub->expects( $this->once() )->method( 'saveAddressItem' );

		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', $customerStub );
		$this->object->process();
		\Aimeos\Controller\Frontend\Customer\Factory::injectController( '\Aimeos\Controller\Frontend\Customer\Standard', null );
	}
}
