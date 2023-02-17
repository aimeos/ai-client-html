<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Client\Html\Account\History;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->view = \TestHelper::view();

		$this->object = new \Aimeos\Client\Html\Account\History\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$output = $this->object->header();

		$this->assertStringContainsString( '<link rel="stylesheet"', $output );
		$this->assertStringContainsString( '<script defer', $output );
	}


	public function testBody()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUserId( $manager->find( 'test@example.com' )->getId() );

		$output = $this->object->body();

		$this->assertStringContainsString( '<div class="section aimeos account-history', $output );
		$this->assertMatchesRegularExpression( '#<div class="history-item#', $output );
		$this->assertMatchesRegularExpression( '#<h2 class="order-basic.*<span class="value[^<]+</span>.*</h2>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="order-invoiceno.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="order-payment.*<span class="value[^<]+</span>.*</div>#smU', $output );
		$this->assertMatchesRegularExpression( '#<div class="order-delivery.*<span class="value.*</span>.*</div>#smU', $output );

		$this->assertStringContainsString( '<div class="account-history-detail common-summary', $output );

		$this->assertStringContainsString( 'Our Unittest', $output );
		$this->assertStringContainsString( 'Example company', $output );

		$this->assertStringContainsString( '<h4>unitdeliverycode</h4>', $output );
		$this->assertStringContainsString( '<h4>unitpaymentcode</h4>', $output );

		$this->assertStringContainsString( '>1234<', $output );
		$this->assertStringContainsString( 'This is a comment', $output );

		$this->assertStringContainsString( 'Cafe Noire Expresso', $output );
		$this->assertStringContainsString( 'Cafe Noire Cappuccino', $output );
		$this->assertStringContainsString( 'Unittest: Monetary rebate', $output );
		$this->assertMatchesRegularExpression( '#<div class="price.+55.00 EUR</div>#', $output );
		$this->assertMatchesRegularExpression( '#<div class="quantity.+14 articles</div>#', $output );
	}
}
