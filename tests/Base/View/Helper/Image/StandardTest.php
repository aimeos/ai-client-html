<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
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


	public function testTransformRejectsUnsafeVariantNames()
	{
		$types = ['color', 'size-mm', 'size_2', '', "x><img/src='x'/onerror='window.imageXss=1'>", 'x/onerror=alert(1)'];

		foreach( ['image/jpeg', 'video/mp4'] as $mime )
		{
			$media = ( new \Aimeos\MShop\Media\Item\Standard( 'media.' ) )->setMimetype( $mime )->setUrl( 'image.jpg' );

			foreach( $types as $id => $type )
			{
				$attr = ( new \Aimeos\MShop\Attribute\Item\Standard( 'attribute.' ) )->setId( (string) ( $id + 1 ) )->setType( $type );
				$list = new \Aimeos\MShop\Common\Item\Lists\Standard( 'media.lists.', ['media.lists.type' => 'variant'] );
				$media->addListItem( 'attribute', $list, $attr );
			}

			$result = $this->object->transform( $media );
			$this->assertStringContainsString( 'data-variant-color="1"', $result );
			$this->assertStringContainsString( 'data-variant-size-mm="2"', $result );
			$this->assertStringContainsString( 'data-variant-size_2="3"', $result );
			$this->assertSame( 3, substr_count( $result, ' data-variant-' ) );
			$this->assertStringNotContainsString( 'onerror', $result );
		}
	}


	public function testTransformEscapesAttributeValues()
	{
		foreach( ['image/jpeg', 'video/mp4'] as $mime )
		{
			$media = new \Aimeos\MShop\Media\Item\Standard( 'media.', [
				'media.id' => '1" onerror="alert(1)', 'media.mimetype' => $mime, 'media.url' => 'image.jpg'
			] );
			$result = $this->object->transform( $media, '240px" onload="alert(2)' );

			$this->assertStringContainsString( 'id="image-1&quot; onerror=&quot;alert(1)"', $result );
			$this->assertStringNotContainsString( ' onerror="', $result );
			$this->assertStringNotContainsString( ' onload="', $result );

			if( $mime === 'image/jpeg' ) {
				$this->assertStringContainsString( 'sizes="240px&quot; onload=&quot;alert(2)"', $result );
			}
		}
	}
}
