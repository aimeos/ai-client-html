<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();

		unset( $this->object, $this->context );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'address';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderSkip()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderOtherStep()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'xyz';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$item = $this->getCustomerItem();
		$this->context->setUserId( $item->getId() );

		$view = $this->object->getView();
		$view->standardStepActive = 'address';
		$view->standardSteps = array( 'address', 'after' );
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-address">', $output );

		$this->assertGreaterThanOrEqual( 0, count( $view->addressLanguages ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->addressCountries ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'xyz';
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
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
	 * Returns the customer item for the given code
	 *
	 * @param string $code Unique customer code
	 * @throws \Exception If no customer item is found
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item object
	 */
	protected function getCustomerItem( $code = 'UTC001' )
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'Customer item not found' );
		}

		return $customer;
	}
}
