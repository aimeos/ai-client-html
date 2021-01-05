<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Watch;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $aimeos;


	protected function setUp() : void
	{
		$this->context = \TestHelperJobs::getContext();
		$this->aimeos = \TestHelperJobs::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Customer\Email\Watch\Standard( $this->context, $this->aimeos );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product notification e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends e-mails for watched products';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$mailStub->expects( $this->once() )->method( 'send' );

		$this->context->setMail( $mailStub );


		$product = $this->getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( array( $this->context, $this->aimeos ) )
			->setMethods( array( 'getProductList' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getProductList' )
			->will( $this->returnValue( [-1 => ['item' => $product, 'price' => $prices->first(), 'currency' => 'EUR']] ) );


		$object->run();
	}


	public function testRunException()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->context->setMail( $mailStub );


		$product = $this->getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( array( $this->context, $this->aimeos ) )
			->setMethods( array( 'getProductList' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getProductList' )
			->will( $this->returnValue( [-1 => ['item' => $product, 'price' => $prices->first(), 'currency' => 'EUR']] ) );


		$object->run();
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		if( ( $item = $manager->search( $search, ['media', 'price', 'text'] )->first() ) === null ) {
			throw new \RuntimeException( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
