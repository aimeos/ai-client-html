<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 * @package Controller
 * @subpackage Order
 */


namespace Aimeos\Controller\Jobs\Order\Email\Voucher;


/**
 * Order voucher e-mail job controller.
 *
 * @package Controller
 * @subpackage Order
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	use \Aimeos\Controller\Jobs\Mail;


	private $couponId;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Voucher related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->context()->translate( 'controller/jobs', 'Sends the e-mail with the voucher to the customer' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->context();
		$config = $context->config();

		/** controller/jobs/order/email/voucher/limit-days
		 * Only send voucher e-mails of orders that were created in the past within the configured number of days
		 *
		 * The voucher e-mails are normally send immediately after the voucher
		 * has been ordered. This option prevents e-mails for old orders from
		 * being send in case anything went wrong or an update failed to avoid
		 * confusion of customers.
		 *
		 * @param integer Number of days
		 * @since 2018.07
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/voucher/status
		 */
		$limit = $config->get( 'controller/jobs/order/email/voucher/limit-days', 30 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		/** controller/jobs/order/email/voucher/status
		 * Only send e-mails containing voucher for these payment status values
		 *
		 * E-mail containing vouchers can be sent for these payment status values:
		 *
		 * * 0: deleted
		 * * 1: canceled
		 * * 2: refused
		 * * 3: refund
		 * * 4: pending
		 * * 5: authorized
		 * * 6: received
		 *
		 * @param integer Payment status constant
		 * @since 2018.07
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/voucher/limit-days
		 */
		$status = $config->get( 'controller/jobs/order/email/voucher/status', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );


		$manager = \Aimeos\MShop::create( $context, 'order' );

		$filter = $manager->filter();
		$func = $filter->make( 'order:status', [\Aimeos\MShop\Order\Item\Status\Base::EMAIL_VOUCHER, '1'] );
		$filter->add( [
			$filter->compare( '>=', 'order.mtime', $limitDate ),
			$filter->compare( '==', 'order.statuspayment', $status ),
			$filter->compare( '==', 'order.base.product.type', 'voucher' ),
			$filter->compare( '==', $func, 0 ),
		] );

		$start = 0;

		do
		{
			$items = $manager->search( $filter->slice( $start ), ['order/base', 'order/base/addres', 'order/base/product'] );

			$this->notify( $items );

			$count = count( $items );
			$start += $count;
		}
		while( $count >= $filter->getLimit() );
	}


	/**
	 * Returns the delivery address item of the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Order including address items
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Delivery or voucher address item
	 * @throws \Aimeos\Controller\Jobs\Exception If no address item is available
	 */
	protected function address( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem ) : \Aimeos\MShop\Order\Item\Base\Address\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY;
		if( ( $addr = current( $orderBaseItem->getAddress( $type ) ) ) !== false && $addr->getEmail() !== '' ) {
			return $addr;
		}

		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		if( ( $addr = current( $orderBaseItem->getAddress( $type ) ) ) !== false && $addr->getEmail() !== '' ) {
			return $addr;
		}

		$msg = sprintf( 'No e-mail address found in order base with ID "%1$s"', $orderBaseItem->getId() );
		throw new \Aimeos\Controller\Jobs\Exception( $msg );
	}


	/**
	 * Creates coupon codes for the bought vouchers
	 *
	 * @param \Aimeos\Map $orderProdItems Complete order including addresses, products, services
	 */
	protected function createCoupons( \Aimeos\Map $orderProdItems )
	{
		$map = [];
		$manager = \Aimeos\MShop::create( $this->context(), 'order/base/product/attribute' );

		foreach( $orderProdItems as $orderProductItem )
		{
			if( $orderProductItem->getAttribute( 'coupon-code', 'coupon' ) ) {
				continue;
			}

			$codes = [];

			for( $i = 0; $i < $orderProductItem->getQuantity(); $i++ )
			{
				$str = $i . getmypid() . microtime( true ) . $orderProductItem->getId();
				$code = substr( strtoupper( sha1( $str ) ), -8 );
				$map[$code] = $orderProductItem->getId();
				$codes[] = $code;
			}

			$item = $manager->create()->setCode( 'coupon-code' )->setType( 'coupon' )->setValue( $codes );
			$orderProductItem->setAttributeItem( $item );
		}

		$this->saveCoupons( $map );
		return $orderProdItems;
	}


	/**
	 * Returns the coupon ID for the voucher coupon
	 *
	 * @return string Unique ID of the coupon item
	 */
	protected function couponId() : string
	{
		if( !isset( $this->couponId ) )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'coupon' );
			$filter = $manager->filter()->add( 'coupon.provider', '=~', 'Voucher' )->slice( 0, 1 );

			if( ( $item = $manager->search( $filter )->first() ) === null ) {
				throw new \Aimeos\Controller\Jobs\Exception( 'No coupon provider "Voucher" available' );
			}

			$this->couponId = $item->getId();
		}

		return $this->couponId;
	}


	/**
	 * Sends the voucher e-mail for the given orders
	 *
	 * @param \Aimeos\Map $items List of order items implementing \Aimeos\MShop\Order\Item\Iface with their IDs as keys
	 */
	protected function notify( \Aimeos\Map $items )
	{
		$context = $this->context();
		$sites = $this->sites( $items->getBaseItem()->getSiteId()->unique() );

		$couponManager = \Aimeos\MShop::create( $context, 'coupon' );
		$orderProdManager = \Aimeos\MShop::create( $context, 'order/base/product' );

		foreach( $items as $id => $item )
		{
			$couponManager->begin();
			$orderProdManager->begin();

			try
			{
				$base = $item->getBaseItem();
				$orderProdManager->save( $this->createCoupons( $this->products( $base ) ) );

				$list = $sites->get( $base->getSiteId(), map() );
				$view = $this->view( $base, $list->getTheme()->filter()->last() );

				$this->send( $view, $this->products( $base ), $this->address( $base ), $list->getLogo()->filter()->last() );
				$this->status( $id );

				$orderProdManager->commit();
				$couponManager->commit();

				$str = sprintf( 'Sent voucher e-mails for order ID "%1$s"', $item->getId() );
				$context->logger()->info( $str, 'email/order/voucher' );
			}
			catch( \Exception $e )
			{
				$orderProdManager->rollback();
				$couponManager->rollback();

				$str = 'Error while trying to send voucher e-mails for order ID "%1$s": %2$s';
				$msg = sprintf( $str, $item->getId(), $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
				$context->logger()->info( $msg, 'email/order/voucher' );
			}
		}
	}


	/**
	 * Returns the ordered voucher products from the basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Basket object
	 * @return \Aimeos\Map List of order product items for the voucher products
	 */
	protected function products( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem ) : \Aimeos\Map
	{
		$list = [];

		foreach( $orderBaseItem->getProducts() as $orderProductItem )
		{
			if( $orderProductItem->getType() === 'voucher' ) {
				$list[] = $orderProductItem;
			}

			foreach( $orderProductItem->getProducts() as $subProductItem )
			{
				if( $subProductItem->getType() === 'voucher' ) {
					$list[] = $subProductItem;
				}
			}
		}

		return map( $list );
	}


	/**
	 * Saves the given coupon codes
	 *
	 * @param array $map Associative list of coupon codes as keys and reference Ids as values
	 */
	protected function saveCoupons( array $map )
	{
		$couponId = $this->couponId();
		$manager = \Aimeos\MShop::create( $this->context(), 'coupon/code' );

		foreach( $map as $code => $ref )
		{
			$item = $manager->create()->setParentId( $couponId )
				->setCode( $code )->setRef( $ref )->setCount( null ); // unlimited

			$manager->save( $item );
		}
	}


	/**
	 * Sends the voucher related e-mail for a single order
	 *
	 * @param \Aimeos\MW\View\Iface $view Populated view object
	 * @param \Aimeos\Map $orderProducts List of ordered voucher products
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Address item
	 * @param string|null $logoPath Relative path to the logo in the fs-media file system
	 */
	protected function send( \Aimeos\MW\View\Iface $view, \Aimeos\Map $orderProducts,
		\Aimeos\MShop\Common\Item\Address\Iface $address, string $logoPath = null )
	{
		$context = $this->context();
		$config = $context->config();
		$logo = $this->call( 'mailLogo', $logoPath );

		foreach( $orderProducts as $orderProductItem )
		{
			if( !empty( $codes = $orderProductItem->getAttribute( 'coupon-code', 'coupon' ) ) )
			{
				foreach( (array) $codes as $code )
				{
					$view->orderProductItem = $orderProductItem;
					$view->voucher = $code;

					$msg = $this->call( 'mailTo', $address );
					$view->logo = $msg->embed( $logo, basename( (string) $logoPath ) );

					$msg->subject( $context->translate( 'client', 'Your voucher' ) )
						->html( $view->render( $config->get( 'controller/jobs/order/email/voucher/template-html', 'order/email/voucher/html' ) ) )
						->text( $view->render( $config->get( 'controller/jobs/order/email/voucher/template-text', 'order/email/voucher/text' ) ) )
						->send();
				}
			}
		}
	}


	/**
	 * Returns the site items for the given site codes
	 *
	 * @param iterable $siteIds List of site IDs
	 * @return \Aimeos\Map Site items with codes as keys
	 */
	protected function sites( iterable $siteIds ) : \Aimeos\Map
	{
		$map = [];
		$manager = \Aimeos\MShop::create( $this->context(), 'locale/site' );

		foreach( $siteIds as $siteId )
		{
			$list = explode( '.', trim( $siteId, '.' ) );
			$map[$siteId] = $manager->getPath( end( $list ) );
		}

		return map( $map );
	}


	/**
	 * Adds the status of the delivered e-mail for the given order ID
	 *
	 * @param string $orderId Unique order ID
	 */
	protected function status( string $orderId )
	{
		$orderStatusManager = \Aimeos\MShop::create( $this->context(), 'order/status' );

		$statusItem = $orderStatusManager->create()->setParentId( $orderId )->setValue( 1 )
			->setType( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_VOUCHER );

		$orderStatusManager->save( $statusItem );
	}


	/**
	 * Returns the view populated with common data
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basket including addresses
	 * @param string|null $theme Theme name
	 * @return \Aimeos\MW\View\Iface View object
	 */
	protected function view( \Aimeos\MShop\Order\Item\Base\Iface $base, string $theme = null ) : \Aimeos\MW\View\Iface
	{
		$address = $this->address( $base );
		$langId = $address->getLanguageId() ?: $base->locale()->getLanguageId();

		$view = $this->call( 'mailView', $langId );
		$view->intro = $this->call( 'mailIntro', $address );
		$view->css = $this->call( 'mailCss', $theme );
		$view->address = $address;
		$view->urlparams = [
			'currency' => $base->getPrice()->getCurrencyId(),
			'site' => $base->getSiteCode(),
			'locale' => $langId,
		];

		return $view;
	}
}
