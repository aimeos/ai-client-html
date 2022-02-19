<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $aimeos;


	protected function setUp() : void
	{
		$this->context = \TestHelperJobs::context();
		$this->aimeos = \TestHelperJobs::getAimeos();
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->aimeos );
	}


	public function testGetName()
	{
		$object = new \Aimeos\Controller\Jobs\Customer\Email\Watch\Standard( $this->context, $this->aimeos );
		$this->assertEquals( 'Product notification e-mails', $object->getName() );
	}


	public function testGetDescription()
	{
		$object = new \Aimeos\Controller\Jobs\Customer\Email\Watch\Standard( $this->context, $this->aimeos );
		$this->assertEquals( 'Sends e-mails for watched products', $object->getDescription() );
	}


	public function testRun()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\Base\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\Base\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->setMethods( ['send'] )
			->getMock();

		$mailStub->expects( $this->once() )->method( 'create' )->will( $this->returnValue( $mailMsgStub ) );
		$mailMsgStub->expects( $this->once() )->method( 'send' );

		$this->context->setMail( $mailStub );


		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC', ['media', 'price', 'text'] );
		$price = $product->getRefItems( 'price', 'default', 'default' )->first();

		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( array( $this->context, $this->aimeos ) )
			->setMethods( ['products'] )
			->getMock();

		$object->expects( $this->once() )->method( 'products' )
			->will( $this->returnValue( [$product->set( 'price', $price )] ) );


		$object->run();
	}


	public function testRunException()
	{
		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( [$this->context, $this->aimeos] )
			->setMethods( ['products', 'send'] )
			->getMock();

		$object->expects( $this->once() )->method( 'products' )->will( $this->returnValue( [new \stdClass()] ) );
		$object->expects( $this->once() )->method( 'send' )->will( $this->throwException( new \RuntimeException() ) );

		$object->run();
	}
}
