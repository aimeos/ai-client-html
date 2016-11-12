<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Client\Html\Catalog\Stage\Breadcrumb;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Breadcrumb\Standard( $context, $paths );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$view = \TestHelperHtml::getView();

		$view->stageCatPath = $catalogManager->getPath( $catItem->getId() );

		$this->object->setView( $view );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertRegExp( '#Root.*.Categories.*.Kaffee.*#smU', $output );
		$this->assertStringStartsWith( '<div class="catalog-stage-breadcrumb">', $output );
	}


	public function testGetBodyNoCatId()
	{
		$this->object->setView( \TestHelperHtml::getView() );

		$output = $this->object->getBody();
		$this->assertRegExp( '#Your search result#smU', $output );
		$this->assertStringStartsWith( '<div class="catalog-stage-breadcrumb">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
