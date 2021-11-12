<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Customer
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Watch;

use \Aimeos\MW\Logger\Base as Log;


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
	public function getName() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Product notification e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Sends e-mails for watched products' );
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

		$localeManager = \Aimeos\MShop::create( $context, 'locale' );
		$custManager = \Aimeos\MShop::create( $context, 'customer' );

		$localeItems = $localeManager->search( $localeManager->filter() );

		foreach( $localeItems as $localeItem )
		{
			$langId = $localeItem->getLanguageId();

			if( isset( $langIds[$langId] ) ) {
				continue;
			}

			$langIds[$langId] = true;
			// fetch language specific text and media items for products
			$context->getLocale()->setLanguageId( $langId );

			$search = $custManager->filter( true );
			$func = $search->make( 'customer:has', ['product', 'watch'] );
			$expr = array(
				$search->compare( '==', 'customer.languageid', $langId ),
				$search->compare( '!=', $func, null ),
				$search->getConditions(),
			);
			$search->setConditions( $search->and( $expr ) );
			$search->setSortations( array( $search->sort( '+', 'customer.id' ) ) );

			$start = 0;

			do
			{
				$search->slice( $start );
				$customers = $custManager->search( $search );

				$this->execute( $context, $customers );

				$count = count( $customers );
				$start += $count;
			}
			while( $count >= $search->getLimit() );
		}
	}


	/**
	 * Sends product notifications for the given customers in their language
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\Map $customers List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
	 */
	protected function execute( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Map $customers )
	{
		$prodIds = $custIds = [];
		$listItems = $this->getListItems( $context, $customers->keys() );
		$listManager = \Aimeos\MShop::create( $context, 'customer/lists' );

		foreach( $listItems as $id => $listItem )
		{
			$refId = $listItem->getRefId();
			$custIds[$listItem->getParentId()][$id] = $refId;
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

				if( !empty( $custProducts ) && ( $custItem = $customers->get( $custId ) ) !== null )
				{
					$addr = $custItem->getPaymentAddress();
					$this->sendMail( $context, $addr, $custProducts );

					$str = sprintf( 'Sent product notification e-mail to "%1$s"', $addr->getEmail() );
					$context->getLogger()->log( $str, Log::DEBUG, 'email/customer/watch' );

					$listIds += array_keys( $custProducts );
				}
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
				$msg = sprintf( $str, $custId, $e->getMessage() ) . PHP_EOL . $e->getTraceAsString();
				$context->getLogger()->log( $msg, Log::ERR, 'email/customer/watch' );
			}

			$listManager->delete( $listIds );
		}
	}


	/**
	 * Returns the product notification e-mail client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return \Aimeos\Client\Html\Iface Product notification e-mail client
	 */
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context ) : \Aimeos\Client\Html\Iface
	{
		if( !isset( $this->client ) ) {
			$this->client = \Aimeos\Client\Html\Email\Watch\Factory::create( $context );
		}

		return $this->client;
	}


	/**
	 * Returns the list items for the given customer IDs and list type ID
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $custIds List of customer IDs
	 * @return \Aimeos\Map List of customer list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Map $custIds ) : \Aimeos\Map
	{
		$listManager = \Aimeos\MShop::create( $context, 'customer/lists' );

		$search = $listManager->filter();
		$expr = array(
			$search->compare( '==', 'customer.lists.domain', 'product' ),
			$search->compare( '==', 'customer.lists.parentid', $custIds->toArray() ),
			$search->compare( '==', 'customer.lists.type', 'watch' ),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 0x7fffffff );

		return $listManager->search( $search );
	}


	/**
	 * Returns a filtered list of products for which a notification should be sent
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $products List of product items
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of customer list items
	 * @return array Multi-dimensional associative list of list IDs as key and product / price item maps as values
	 */
	protected function getProductList( \Aimeos\Map $products, array $listItems ) : array
	{
		$result = [];
		$priceManager = \Aimeos\MShop::create( $this->getContext(), 'price' );

		foreach( $listItems as $id => $listItem )
		{
			try
			{
				$refId = $listItem->getRefId();
				$config = $listItem->getConfig();

				if( ( $product = $products->get( $refId ) ) !== null )
				{
					$prices = $product->getRefItems( 'price', 'default', 'default' );
					$currencyId = ( isset( $config['currency'] ) ? $config['currency'] : null );

					$price = $priceManager->getLowestPrice( $prices, 1, $currencyId );

					if( isset( $config['stock'] ) && $config['stock'] == 1 ||
						isset( $config['price'] ) && $config['price'] == 1 &&
						isset( $config['pricevalue'] ) && $config['pricevalue'] > $price->getValue()
					) {
						$result[$id]['item'] = $product;
						$result[$id]['currency'] = $currencyId;
						$result[$id]['price'] = $price;
					}
				}
			}
			catch( \Exception $e ) {; } // no price available
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
	protected function getProducts( \Aimeos\MShop\Context\Item\Iface $context, array $prodIds, string $stockType )
	{
		$stockMap = [];

		$manager = \Aimeos\MShop::create( $context, 'product' );
		$filter = $manager->filter( true )->add( ['product.id' => $prodIds] )->slice( 0, count( $prodIds ) );
		$productItems = $manager->search( $filter, ['text', 'price', 'media'] );

		foreach( $this->getStockItems( $context, $productItems->keys()->toArray(), $stockType ) as $stockItem ) {
			$stockMap[$stockItem->getProductId()] = true;
		}

		foreach( $productItems as $productId => $productItem )
		{
			if( !isset( $stockMap[$productId] ) ) {
				unset( $productItems[$productId] );
			}
		}

		return $productItems;
	}


	/**
	 * Returns the stock items for the given product IDs
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $prodIds List of product IDs
	 * @param string $stockType Stock type code
	 * @return \Aimeos\Map Associative list of stock IDs as keys and stock items implementing \Aimeos\MShop\Stock\Item\Iface
	 */
	protected function getStockItems( \Aimeos\MShop\Context\Item\Iface $context, array $prodIds, string $stockType ) : \Aimeos\Map
	{
		$stockManager = \Aimeos\MShop::create( $context, 'stock' );

		$search = $stockManager->filter( true );
		$expr = array(
			$search->compare( '==', 'stock.productid', $prodIds ),
			$search->compare( '==', 'stock.type', $stockType ),
			$search->or( array(
				$search->compare( '==', 'stock.stocklevel', null ),
				$search->compare( '>', 'stock.stocklevel', 0 ),
			) ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 100000 ); // performance speedup

		return $stockManager->search( $search );
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
		$view = $context->view();
		$view->extProducts = $products;
		$view->extAddressItem = $address;

		$params = [
			'locale' => $context->getLocale()->getLanguageId(),
			'site' => $context->getLocale()->getSiteItem()->getCode(),
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Locale( $view, $context->getLocale()->getLanguageId() );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $address->getLanguageId() ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->getClient( $context );
		$client->setView( $view );
		$client->header();
		$client->body();

		$mailer->send( $message );
	}
}
