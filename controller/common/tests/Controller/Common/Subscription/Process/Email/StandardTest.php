<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

namespace Aimeos\Controller\Common\Subscription\Process\Processor\Email;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
	}


	public function testRenewAfter()
	{
		$context = \TestHelperCntl::getContext();

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

		$context->setMail( $mailStub );
		$subscription = $this->getSubscription( $context )->setReason( \Aimeos\MShop\Subscription\Item\Iface::REASON_PAYMENT );
		$order = \Aimeos\MShop::create( $context, 'order' )->create();

		$object = new \Aimeos\Controller\Common\Subscription\Process\Processor\Email\Standard( $context );
		$object->renewAfter( $subscription, $order );
	}


	public function testEnd()
	{
		$context = \TestHelperCntl::getContext();

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

		$context->setMail( $mailStub );

		$object = new \Aimeos\Controller\Common\Subscription\Process\Processor\Email\Standard( $context );
		$object->end( $this->getSubscription( $context ) );
	}


	protected function getSubscription( $context )
	{
		$manager = \Aimeos\MShop::create( $context, 'subscription' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \Exception( 'No subscription item found' );
		}

		return $item;
	}
}
