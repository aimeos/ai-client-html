<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Account;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $aimeos;


	protected function setUp()
	{
		$this->context = \TestHelperJobs::getContext();
		$this->aimeos = \TestHelperJobs::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Customer\Email\Account\Standard( $this->context, $this->aimeos );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Customer account e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends e-mails for new customer accounts';
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


		$queueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Queue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$queueStub->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->onConsecutiveCalls( new \Aimeos\MW\MQueue\Message\Standard( array( 'message' => '{}') ), null ) );

		$queueStub->expects( $this->once() )->method( 'del' );


		$mqueueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$mqueueStub->expects( $this->once() )->method( 'getQueue' )
			->will( $this->returnValue( $queueStub ) );


		$managerStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Manager\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$managerStub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $mqueueStub ) );

		$this->context->setMessageQueueManager( $managerStub );


		$this->object->run();
	}


	public function testRunException()
	{
		$queueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Queue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$queueStub->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->onConsecutiveCalls( new \Aimeos\MW\MQueue\Message\Standard( array( 'message' => 'error') ), null ) );

		$queueStub->expects( $this->once() )->method( 'del' );


		$mqueueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$mqueueStub->expects( $this->once() )->method( 'getQueue' )
			->will( $this->returnValue( $queueStub ) );


		$managerStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Manager\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$managerStub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $mqueueStub ) );

		$this->context->setMessageQueueManager( $managerStub );


		$this->object->run();
	}
}
