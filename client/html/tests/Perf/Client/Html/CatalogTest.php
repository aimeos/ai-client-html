<?php

namespace Aimeos\Perf\Client\Html;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */
class CatalogTest extends \PHPUnit\Framework\TestCase
{
	protected $context;
	protected $paths;
	protected $view;


	protected function setUp()
	{
		$this->paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->context = \TestHelperHtml::getContext( 'unitperf' );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'home' ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $catalogItem = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No catalog item with code "home" found' );
		}

		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'perf-00000' ) );
		$items = $productManager->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No product item with code "perf-00000" found' );
		}

		$this->view = \TestHelperHtml::getView( 'unitperf' );

		$param = array(
			'f_catid' => $catalogItem->getId(),
			'd_prodid' => $productItem->getId()
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
	}


	public function testFilter()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterHeader()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog filter header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterBody()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Filter\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testList()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = \Aimeos\Client\Html\Catalog\Lists\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Lists\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListHeader()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Lists\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog list header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListBody()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Lists\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetail()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = \Aimeos\Client\Html\Catalog\Detail\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Detail\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailHeader()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Detail\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog detail header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailBody()
	{
		$start = microtime( true );

		$client = \Aimeos\Client\Html\Catalog\Detail\Factory::create( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
