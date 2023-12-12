<?php

namespace Aimeos\Client\Html\Catalog\Session\Seen;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\MShop::cache( true );

		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Client\Html\Catalog\Session\Seen\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( false );
		\Aimeos\MShop::cache( false );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$seen = [1 => 'html product one', 2 => 'html product two'];
		$this->context->session()->set( 'aimeos/catalog/session/seen/list', $seen );

		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();

		$this->assertMatchesRegularExpression( '#.*html product two.*html product one.*#smU', $output ); // list is reversed
		$this->assertStringStartsWith( '<div class="section catalog-session-seen">', $output );
	}
}
