<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\Client\Html\Account\Review\Todo;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$customer = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context )->find( 'test@example.com' );
		$this->context->setUserId( $customer->getId() );

		$this->object = new \Aimeos\Client\Html\Account\Review\Todo\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetBody()
	{
		$view = \TestHelperHtml::getView();
		$this->object->setView( $this->object->addData( $view ) );

		$output = $this->object->getBody();

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
}
