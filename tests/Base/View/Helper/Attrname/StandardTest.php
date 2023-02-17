<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


namespace Aimeos\Base\View\Helper\Attrname;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$translate = new \Aimeos\Base\Translation\None( 'en_GB' );

		$view = new \Aimeos\Base\View\Standard();
		$view->addHelper( 'number', new \Aimeos\Base\View\Helper\Number\Standard( $view ) );
		$view->addHelper( 'translate', new \Aimeos\Base\View\Helper\Translate\Standard( $view, $translate ) );

		$this->object = new \Aimeos\Base\View\Helper\Attrname\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' );
		$search = $manager->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '!=', $search->make( 'attribute:has', ['price'] ), null ) );
		$item = $manager->search( $search, ['price'] )->first( new \Exception( 'No item found' ) );

		$result = $this->object->transform( $item );

		$this->assertMatchesRegularExpression( '/[^ ]+ \(\+[0-9]+\.[0-9]+EUR\)/', $result );
	}
}
