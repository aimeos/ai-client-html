<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */

namespace Aimeos\Client\Html\Catalog\Lists;


class TemplateTest extends \PHPUnit\Framework\TestCase
{
	public function testProductUrlsAreAttributeEncoded()
	{
		$path = dirname( __DIR__, 5 ) . '/templates/client/html/catalog/lists/items-list.php';
		$template = file_get_contents( $path );

		$this->assertSame( 2, substr_count( $template, 'href="<?= $enc->attr( $url ) ?>"' ) );
		$this->assertStringNotContainsString( 'href="<?= $url ?>"', $template );
	}
}
