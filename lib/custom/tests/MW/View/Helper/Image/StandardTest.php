<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW\View\Helper\Image;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$view->addHelper( 'content', new \Aimeos\MW\View\Helper\Content\Standard( $view, '/path/to' ) );
		$view->addHelper( 'encoder', new \Aimeos\MW\View\Helper\Encoder\Standard( $view ) );

		$this->object = new \Aimeos\MW\View\Helper\Image\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$context = \TestHelper::getContext();
		$manager = \Aimeos\MShop::create( $context, 'media' );

		$attrItem = \Aimeos\MShop::create( $context, 'attribute' )->create()->setType( 'color' )->setId( 123 );
		$listItem = $manager->createListItem()->setType( 'variant' );

		$mediaItem = $manager->create()->setLabel( 'testimage' )
			->setPreviews( ['100' => 'image-1.jpg', '200' => 'image-2.jpg'] )
			->addListItem( 'attribute', $listItem, $attrItem );

		$result = $this->object->transform( $mediaItem );

		$this->assertStringContainsString( '/path/to/image-1.jpg 100w, /path/to/image-2.jpg 200w', $result );
		$this->assertStringContainsString( 'src="/path/to/image-1.jpg"', $result );
		$this->assertStringContainsString( 'data-variant-color="123"', $result );
	}
}
