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


	private $sites = [];


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
		$date = date( 'Y-m-d H:i:s' );
		$context = $this->context();


		foreach( $customers as $customer )
		{
			$listItems = $customer->getListItems( 'product', null, null, false );
			$products = $this->products( $listItems );

			try
			{
				if( !empty( $products ) )
				{
					$sites = $this->sites( $customer->getSiteId() );

					$view = $this->view( $customer->getPaymentAddress(), $sites->getTheme()->filter()->last() );
					$view->products = $products;

					$this->send( $view, $customer->getPaymentAddress(), $sites->getLogo()->filter()->last() );
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
		$result = [];
		$priceManager = \Aimeos\MShop::create( $this->context(), 'price' );

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
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Address item
	 * @param string|null $logoPath Path to the logo
	 */
	protected function send( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Common\Item\Address\Iface $address, string $logoPath = null )
	{
		$context = $this->context();
		$config = $context->config();

		$msg = $this->call( 'mailTo', $address );
		$view->logo = $msg->embed( $this->call( 'mailLogo', $logoPath ), basename( (string) $logoPath ) );

		$msg->subject( $context->translate( 'client', 'Your watched products' ) )
			->html( $view->render( $config->get( 'controller/jobs/customer/email/watch/template-html', 'customer/email/watch/html' ) ) )
			->text( $view->render( $config->get( 'controller/jobs/customer/email/watch/template-text', 'customer/email/watch/text' ) ) )
			->send();
	}


	/**
	 * Returns the list of site items from the given site ID up to the root site
	 *
	 * @param string|null $siteId Site ID like "1.2.4."
	 * @return \Aimeos\Map List of site items
	 */
	protected function sites( string $siteId = null ) : \Aimeos\Map
	{
		if( !$siteId && !isset( $this->sites[''] ) ) {
			$this->sites[''] = map( \Aimeos\MShop::create( $this->context(), 'locale/site' )->find( 'default' ) );
		}

		if( !isset( $this->sites[(string) $siteId] ) )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'locale/site' );
			$siteIds = explode( '.', trim( (string) $siteId, '.' ) );

			$this->sites[$siteId] = $manager->getPath( end( $siteIds ) );
		}

		return $this->sites[$siteId];
	}


	/**
	 * Returns the view populated with common data
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Address item
	 * @param string|null $theme Theme name
	 * @return \Aimeos\MW\View\Iface View object
	 */
	protected function view( \Aimeos\MShop\Common\Item\Address\Iface $address, string $theme = null ) : \Aimeos\MW\View\Iface
	{
		$view = $this->call( 'mailView', $address->getLanguageId() );
		$view->intro = $this->call( 'mailIntro', $address );
		$view->css = $this->call( 'mailCss', $theme );
		$view->urlparams = [
			'site' => $this->context()->locale()->getSiteItem()->getCode(),
			'locale' => $address->getLanguageId(),
		];

		return $view;
	}
}
