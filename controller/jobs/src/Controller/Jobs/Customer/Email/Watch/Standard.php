<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Customer
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Watch;


/**
 * Product notification e-mail job controller.
 *
 * @package Controller
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	private $client;
	private $types;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Product notification e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Sends e-mails for watched products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$langIds = [];
		$context = $this->getContext();

		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );
		$custManager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );

		$localeItems = $localeManager->searchItems( $localeManager->createSearch() );

		foreach( $localeItems as $localeItem )
		{
			$langId = $localeItem->getLanguageId();

			if( isset( $langIds[$langId] ) ) {
				continue;
			}

			$langIds[$langId] = true;
			// fetch language specific text and media items for products
			$context->getLocale()->setLanguageId( $langId );

			$search = $custManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'customer.languageid', $langId ),
				$search->compare( '==', 'customer.lists.domain', 'product' ),
				$search->compare( '==', 'customer.lists.type.code', 'watch' ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', 'customer.id' ) ) );

			$start = 0;

			do
			{
				$search->setSlice( $start );
				$customers = $custManager->searchItems( $search );

				$this->execute( $context, $customers );

				$count = count( $customers );
				$start += $count;
			}
			while( $count >= $search->getSliceSize() );
		}
	}


	/**
	 * Sends product notifications for the given customers in their language
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $customers List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
	 */
	protected function execute( \Aimeos\MShop\Context\Item\Iface $context, array $customers )
	{
		$prodIds = $custIds = [];
		$listItems = $this->getListItems( $context, array_keys( $customers ) );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'customer/lists' );

		foreach( $listItems as $id => $listItem )
		{
			$refId = $listItem->getRefId();
			$custIds[ $listItem->getParentId() ][$id] = $refId;
			$prodIds[$refId] = $refId;
		}

		$date = date( 'Y-m-d H:i:s' );
		$products = $this->getProducts( $context, $prodIds, 'default' );

		foreach( $custIds as $custId => $list )
		{
			$custListItems = $listIds = [];

			foreach( $list as $listId => $prodId )
			{
				$listItem = $listItems[$listId];

				if( $listItem->getDateEnd() < $date ) {
					$listIds[] = $listId;
				}

				$custListItems[$listId] = $listItems[$listId];
			}

			try
			{
				$custProducts = $this->getProductList( $products, $custListItems );

				if( !empty( $custProducts ) )
				{
					$addr = $customers[$custId]->getPaymentAddress();
					$this->sendMail( $context, $addr, $custProducts );

					$str = sprintf( 'Sent product notification e-mail to "%1$s"', $addr->getEmail() );
					$context->getLogger()->log( $str, \Aimeos\MW\Logger\Base::DEBUG );

					$listIds += array_keys( $custProducts );
				}
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
				$context->getLogger()->log( sprintf( $str, $custId, $e->getMessage() ) );
			}

			$listManager->deleteItems( $listIds );
		}
	}


	/**
	 * Returns the product notification e-mail client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return \Aimeos\Client\Html\Iface Product notification e-mail client
	 */
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( !isset( $this->client ) )
		{
			$templatePaths = $this->getAimeos()->getCustomPaths( 'client/html/templates' );
			$this->client = \Aimeos\Client\Html\Email\Watch\Factory::createClient( $context, $templatePaths );
		}

		return $this->client;
	}


	/**
	 * Returns the list items for the given customer IDs and list type ID
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $custIds List of customer IDs
	 * @return array List of customer list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\MShop\Context\Item\Iface $context, array $custIds )
	{
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'customer/lists' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'customer.lists.domain', 'product' ),
			$search->compare( '==', 'customer.lists.parentid', $custIds ),
			$search->compare( '==', 'customer.lists.type.code', 'watch' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $listManager->searchItems( $search );
	}


	/**
	 * Returns a filtered list of products for which a notification should be sent
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $products List of product items
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of customer list items
	 * @return array Multi-dimensional associative list of list IDs as key and product / price item maps as values
	 */
	protected function getProductList( array $products, array $listItems )
	{
		$result = [];
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );

		foreach( $listItems as $id => $listItem )
		{
			try
			{
				$refId = $listItem->getRefId();
				$config = $listItem->getConfig();

				if( isset( $products[$refId] ) )
				{
					$prices = $products[$refId]->getRefItems( 'price', 'default', 'default' );
					$currencyId = ( isset( $config['currency'] ) ? $config['currency'] : null );

					$price = $priceManager->getLowestPrice( $prices, 1, $currencyId );

					if( isset( $config['stock'] ) && $config['stock'] == 1 ||
						isset( $config['price'] ) && $config['price'] == 1 &&
						isset( $config['pricevalue'] ) && $config['pricevalue'] > $price->getValue()
					) {
						$result[$id]['item'] = $products[$refId];
						$result[$id]['price'] = $price;
					}
				}
			}
			catch( \Exception $e ) { ; } // no price available
		}

		return $result;
	}


	/**
	 * Returns the products for the given IDs which are in stock
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $prodIds List of product IDs
	 * @param string $stockType Stock type code
	 */
	protected function getProducts( \Aimeos\MShop\Context\Item\Iface $context, array $prodIds, $stockType )
	{
		$productCodes = $stockMap = [];
		$productItems = $this->getProductItems( $context, $prodIds );

		foreach( $productItems as $productItem ) {
			$productCodes[] = $productItem->getCode();
		}

		foreach( $this->getStockItems( $context, $productCodes, $stockType ) as $stockItem ) {
			$stockMap[ $stockItem->getProductCode() ] = true;
		}

		foreach( $productItems as $productId => $productItem )
		{
			if( !isset( $stockMap[ $productItem->getCode() ] ) ) {
				unset( $productItems[$productId] );
			}
		}

		return $productItems;
	}


	/**
	 * Returns the product items for the given product IDs
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $prodIds List of product IDs
	 */
	protected function getProductItems( \Aimeos\MShop\Context\Item\Iface $context, array $prodIds )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		$search = $productManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.id', $prodIds ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $productManager->searchItems( $search, array( 'text', 'price', 'media' ) );
	}


	/**
	 * Returns the stock items for the given product codes
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $prodCodes List of product codes
	 * @param string $stockType Stock type code
	 */
	protected function getStockItems( \Aimeos\MShop\Context\Item\Iface $context, array $prodCodes, $stockType )
	{
		$stockManager = \Aimeos\MShop\Factory::createManager( $context, 'stock' );

		$search = $stockManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'stock.productcode', $prodCodes ),
			$search->compare( '==', 'stock.type.code', $stockType ),
			$search->combine( '||', array(
				$search->compare( '==', 'stock.stocklevel', null ),
				$search->compare( '>', 'stock.stocklevel', 0 ),
			)),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $stockManager->searchItems( $search );
	}


	/**
	 * Sends the notification e-mail for the given customer address and products
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address of the customer
	 * @param array $products List of products a notification should be sent for
	 */
	protected function sendMail( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Common\Item\Address\Iface $address, array $products )
	{
		$view = $context->getView();
		$view->extProducts = $products;
		$view->extAddressItem = $address;

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $address->getLanguageId() ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->getClient( $context );
		$client->setView( $view );
		$client->getHeader();
		$client->getBody();

		$mailer->send( $message );
	}
}
