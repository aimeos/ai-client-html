<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */

namespace Aimeos\Client\Html\Checkout\Standard;


class ServiceTemplateTest extends \PHPUnit\Framework\TestCase
{
	public function testBodyOnlyRendersDescriptions()
	{
		$manager = new \Aimeos\MShop\Text\Manager\Standard( \TestHelper::context() );
		$service = new \Aimeos\MShop\Service\Item\Standard( 'service.' );
		$service->setLabel( 'Delivery & payment<img src=x onerror=alert(1)>' );
		$payload = '<img src=x onerror=alert(2)>';
		$texts = array_fill_keys( ['title', 'label', 'meta-description', 'url', 'media.url', 'img-description'], $payload );
		$texts['content'] = json_encode( ['html' => '<p>CMS only</p>', 'css' => $payload] );
		$texts['short'] = '<strong>Short description</strong>' . $payload;
		$texts['long'] = '<em>Long description</em>' . $payload;

		foreach( $texts as $type => $content )
		{
			$text = $manager->create( ['text.type' => $type, 'text.domain' => 'service', 'text.content' => $content] );
			$list = new \Aimeos\MShop\Common\Item\Lists\Standard( 'service.lists.', ['service.lists.type' => 'default'] );
			$service->addListItem( 'text', $list, $text );
		}

		foreach( ['delivery', 'payment'] as $part )
		{
			$view = \TestHelper::view();
			$view->set( $part . 'Services', [$service] );
			$output = $view->render( 'checkout/standard/' . $part . '-body' );

			$this->assertStringContainsString( '<h2>Delivery &amp; payment</h2>', $output, $part );
			$this->assertStringContainsString( '<strong>Short description</strong>', $output, $part );
			$this->assertStringContainsString( '<em>Long description</em>', $output, $part );
			$this->assertStringNotContainsString( 'onerror', $output, $part );
			$this->assertStringNotContainsString( 'CMS only', $output, $part );

			foreach( ['title', 'label', 'meta-description', 'url', 'media.url', 'img-description', 'content'] as $type ) {
				$this->assertStringNotContainsString( '<p class="' . $type . '">', $output, $part );
			}
		}
	}


	public function testBodyEncodesLocalizedName()
	{
		$manager = new \Aimeos\MShop\Text\Manager\Standard( \TestHelper::context() );
		$text = $manager->create( ['text.type' => 'name', 'text.domain' => 'service',
			'text.content' => 'Name & label<img src=x onerror=alert(1)>' ] );
		$service = new \Aimeos\MShop\Service\Item\Standard( 'service.' );
		$list = new \Aimeos\MShop\Common\Item\Lists\Standard( 'service.lists.', ['service.lists.type' => 'default'] );
		$service->addListItem( 'text', $list, $text );

		foreach( ['delivery', 'payment'] as $part )
		{
			$view = \TestHelper::view();
			$view->set( $part . 'Services', [$service] );
			$output = $view->render( 'checkout/standard/' . $part . '-body' );

			$this->assertStringContainsString( '<h2>Name &amp; label</h2>', $output, $part );
			$this->assertStringNotContainsString( 'onerror', $output, $part );
			$this->assertStringNotContainsString( '<p class="name">', $output, $part );
		}
	}
}
