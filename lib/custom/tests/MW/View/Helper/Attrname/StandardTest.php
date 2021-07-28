<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\View\Helper\Attrname;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$translate = new \Aimeos\MW\Translation\None( 'en_GB' );

		$view = new \Aimeos\MW\View\Standard();
		$view->addHelper( 'number', new \Aimeos\MW\View\Helper\Number\Standard( $view ) );
		$view->addHelper( 'translate', new \Aimeos\MW\View\Helper\Translate\Standard( $view, $translate ) );

		$this->object = new \Aimeos\MW\View\Helper\Attrname\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::getContext(), 'attribute' );
		$search = $manager->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '!=', $search->make( 'attribute:has', ['price'] ), null ) );
		$item = $manager->search( $search, ['price'] )->first( new \Exception( 'No item found' ) );

		$result = $this->object->transform( $item );

		$this->assertRegexp( '/[^ ]+ \(\+[0-9]+\.[0-9]+EUR\)/', $result );
	}
}
