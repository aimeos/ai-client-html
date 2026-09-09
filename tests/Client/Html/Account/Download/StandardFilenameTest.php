<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Client\Html\Account\Download;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;


#[AllowMockObjectsWithoutExpectations]
class StandardFilenameTest extends \PHPUnit\Framework\TestCase
{
	public function testAddDownloadUsesStoredClientFilename()
	{
		$path = 'a/b/ab' . str_repeat( '0', 30 ) . '/client-file.pdf';
		$this->assertSame( 'attachment; filename="client-file.pdf"', $this->downloadHeader( $path ) );
	}


	public function testAddDownloadUsesLegacyLabelAndExtension()
	{
		$header = $this->downloadHeader( 'tmp/download/test.txt', 'test download' );
		$this->assertSame( 'attachment; filename="test download.txt"', $header );
	}


	private function downloadHeader( string $path, ?string $name = null ) : string
	{
		$stream = fopen( 'php://temp', 'w+' );
		fwrite( $stream, 'test' );
		rewind( $stream );

		$fs = $this->getMockBuilder( \Aimeos\Base\Filesystem\Standard::class )
			->setConstructorArgs( [['basedir' => sys_get_temp_dir()]] )
			->onlyMethods( ['has', 'size', 'reads'] )
			->getMock();
		$fs->expects( $this->once() )->method( 'has' )->with( $path )->willReturn( true );
		$fs->expects( $this->once() )->method( 'size' )->with( $path )->willReturn( 4 );
		$fs->expects( $this->once() )->method( 'reads' )->with( $path )->willReturn( $stream );

		$manager = $this->createMock( \Aimeos\Base\Filesystem\Manager\Iface::class );
		$manager->expects( $this->once() )->method( 'get' )->with( 'fs-secure' )->willReturn( $fs );

		$context = new \Aimeos\MShop\Context();
		$context->setFilesystemManager( $manager );

		$view = new \Aimeos\Base\View\Standard();
		$response = ( new \Nyholm\Psr7\Factory\Psr17Factory() )->createResponse();
		$view->addHelper( 'response', new \Aimeos\Base\View\Helper\Response\Standard( $view, $response ) );

		$item = $this->createMock( \Aimeos\MShop\Order\Item\Product\Attribute\Iface::class );
		$item->expects( $this->once() )->method( 'getValue' )->willReturn( $path );

		if( $name === null ) {
			$item->expects( $this->never() )->method( 'getName' );
		} else {
			$item->expects( $this->once() )->method( 'getName' )->willReturn( $name );
		}

		$object = new class( $context ) extends Standard {
			public function addDownload( \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item ) : void
			{
				parent::addDownload( $item );
			}
		};
		$object->setView( $view );
		$object->addDownload( $item );

		return $view->response()->getHeaderLine( 'Content-Disposition' );
	}
}
