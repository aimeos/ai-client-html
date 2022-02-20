<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
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
	use \Aimeos\Controller\Jobs\Mail;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Product notification e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Sends e-mails for watched products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'customer' );

		$search = $manager->filter( true );
		$func = $search->make( 'customer:has', ['product', 'watch'] );
		$search->add( $search->is( $func, '!=', null ) )->order( 'customer.id' );

		$start = 0;

		do
		{
			$customers = $manager->search( $search->slice( $start ), ['product' => ['watch']] );
			$customers = $this->notify( $customers );
			$customers = $manager->save( $customers );

			$count = count( $customers );
			$start += $count;
		}
		while( $count >= $search->getLimit() );
	}


	/**
	 * Sends product notifications for the given customers in their language
	 *
	 * @param \Aimeos\Map $customers List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
	 * @return \Aimeos\Map List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
	 */
	protected function notify( \Aimeos\Map $customers ) : \Aimeos\Map
	{
		$context = $this->context();
		$date = date( 'Y-m-d H:i:s' );

		foreach( $customers as $customer )
		{
			$listItems = $customer->getListItems( 'product', null, null, false );
			$products = $this->products( $listItems );

			try
			{
				if( !empty( $products ) ) {
					$this->send( $customer, $products );
				}

				$str = sprintf( 'Sent product notification e-mail to "%1$s"', $customer->getPaymentAddress()->getEmail() );
				$context->logger()->debug( $str, 'email/customer/watch' );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
				$msg = sprintf( $str, $customer->getId(), $e->getMessage() ) . PHP_EOL . $e->getTraceAsString();
				$context->logger()->error( $msg, 'email/customer/watch' );
			}

			$remove = $listItems->diffKeys( $products )->filter( function( $listItem ) use ( $date ) {
				return $listItem->getDateEnd() < $date;
			} );

			$customer->deleteListItems( $remove );
		}

		return $customers;
	}


	/**
	 * Returns a filtered list of products for which a notification should be sent
	 *
	 * @param \Aimeos\Map $listItems List of customer list items
	 * @return array Associative list of list IDs as key and product items values
	 */
	protected function products( \Aimeos\Map $listItems ) : array
	{
		$priceManager = \Aimeos\MShop::create( $this->context(), 'price' );
		$result = [];

		foreach( $listItems as $id => $listItem )
		{
			try
			{
				if( $product = $listItem->getRefItem() )
				{
					$config = $listItem->getConfig();
					$prices = $product->getRefItems( 'price', 'default', 'default' );
					$price = $priceManager->getLowestPrice( $prices, 1, $config['currency'] ?? null );

					if( $config['stock'] ?? null || $config['price'] ?? null
						&& $product->inStock() && ( $config['pricevalue'] ?? 0 ) > $price->getValue()
					) {
						$result[$id] = $product->set( 'price', $price );
					}
				}
			}
			catch( \Exception $e ) { ; } // no price available
		}

		return $result;
	}


	/**
	 * Sends the notification e-mail for the given customer address and products
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param array $products List of products a notification should be sent for
	 */
	protected function send( \Aimeos\MShop\Customer\Item\Iface $item, array $products )
	{
		$context = $this->context();
		$config = $context->config();
		$address = $item->getPaymentAddress();

		$view = $this->call( 'mailView', $address->getLanguageId() );
		$view->intro = $this->call( 'mailIntro', $address );
		$view->addressItem = $address;
		$view->products = $products;
		$view->urlparams = [
			'site' => $context->locale()->getSiteItem()->getCode(),
			'locale' => $address->getLanguageId(),
		];

		$this->call( 'mailTo', $address )
			->subject( $context->translate( 'client', 'Your watched products' ) )
			->html( $view->render( $config->get( 'controller/jobs/customer/email/watch/template-html', 'customer/email/watch/html' ) ) )
			->text( $view->render( $config->get( 'controller/jobs/customer/email/watch/template-text', 'customer/email/watch/text' ) ) )
			->send();
	}
}
