<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 */


namespace Aimeos\Base\View\Helper\Image;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$conf = new \Aimeos\Base\Config\PHPArray( ['resource' => ['fs-media' => ['baseurl' => '/path/to']]] );
		$view = new \Aimeos\Base\View\Standard();

		$view->addHelper( 'config', new \Aimeos\Base\View\Helper\Config\Standard( $view, $conf ) );
		$view->addHelper( 'content', new \Aimeos\Base\View\Helper\Content\Standard( $view ) );
		$view->addHelper( 'encoder', new \Aimeos\Base\View\Helper\Encoder\Standard( $view ) );

		$this->object = new \Aimeos\Base\View\Helper\Image\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'media' );

		$attrItem = \Aimeos\MShop::create( $context, 'attribute' )->create()->setType( 'color' )->setId( 123 );
		$listItem = $manager->createListItem()->setType( 'variant' );

		$mediaItem = $manager->create()->setLabel( 'testimage' )->setUrl( 'image.jpg' )
			->setPreviews( ['100' => 'image-1.jpg', '200' => 'image-2.jpg'] )
			->addListItem( 'attribute', $listItem, $attrItem );

		$result = $this->object->transform( $mediaItem, '240px' );

		$this->assertStringContainsString( '/path/to/image-1.jpg 100w, /path/to/image-2.jpg 200w', $result );
		$this->assertStringContainsString( 'src="/path/to/image-1.jpg"', $result );
		$this->assertStringContainsString( 'data-zoom="/path/to/image.jpg"', $result );
		$this->assertStringContainsString( 'data-variant-color="123"', $result );
		$this->assertStringContainsString( 'sizes="240px"', $result );
	}
}
