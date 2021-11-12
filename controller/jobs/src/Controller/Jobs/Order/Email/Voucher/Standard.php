<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package Controller
 * @subpackage Order
 */


namespace Aimeos\Controller\Jobs\Order\Email\Voucher;

use \Aimeos\MW\Logger\Base as Log;


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
	private $couponId;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Voucher related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Sends the e-mail with the voucher to the customer' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$config = $context->getConfig();

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
		$status = (array) $config->get( 'controller/jobs/order/email/voucher/status', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );


		$client = \Aimeos\Client\Html\Email\Voucher\Factory::create( $context );
		$orderManager = \Aimeos\MShop::create( $context, 'order' );

		$orderSearch = $orderManager->filter();

		$param = array( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_VOUCHER, '1' );
		$orderFunc = $orderSearch->make( 'order:status', $param );

		$expr = array(
			$orderSearch->compare( '>=', 'order.mtime', $limitDate ),
			$orderSearch->compare( '==', 'order.statuspayment', $status ),
			$orderSearch->compare( '==', 'order.base.product.type', 'voucher' ),
			$orderSearch->compare( '==', $orderFunc, 0 ),
		);
		$orderSearch->setConditions( $orderSearch->and( $expr ) );

		$start = 0;

		do
		{
			$items = $orderManager->search( $orderSearch );

			$this->process( $client, $items, 1 );

			$count = count( $items );
			$start += $count;
			$orderSearch->slice( $start );
		}
		while( $count >= $orderSearch->getLimit() );
	}


	/**
	 * Saves the given coupon codes
	 *
	 * @param array $map Associative list of coupon codes as keys and reference Ids as values
	 */
	protected function addCouponCodes( array $map )
	{
		$couponId = $this->getCouponId();
		$manager = \Aimeos\MShop::create( $this->getContext(), 'coupon/code' );

		foreach( $map as $code => $ref )
		{
			$item = $manager->create()->setParentId( $couponId )
				->setCode( $code )->setRef( $ref )->setCount( null ); // unlimited

			$manager->save( $item );
		}
	}


	/**
	 * Adds the status of the delivered e-mail for the given order ID
	 *
	 * @param string $orderId Unique order ID
	 * @param int $value Status value
	 */
	protected function addOrderStatus( string $orderId, int $value )
	{
		$orderStatusManager = \Aimeos\MShop::create( $this->getContext(), 'order/status' );

		$statusItem = $orderStatusManager->create()->setParentId( $orderId )->setValue( $value )
			->setType( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_VOUCHER );

		$orderStatusManager->save( $statusItem );
	}


	/**
	 * Returns the delivery address item of the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Order including address items
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Delivery or voucher address item
	 * @throws \Aimeos\Controller\Jobs\Exception If no address item is available
	 */
	protected function getAddressItem( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem ) : \Aimeos\MShop\Order\Item\Base\Address\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY;
		if( ( $addr = current( $orderBaseItem->getAddress( $type ) ) ) !== false && $addr->getEmail() !== '' ) {
			return $addr;
		}

		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		if( ( $addr = current( $orderBaseItem->getAddress( $type ) ) ) !== false ) {
			return $addr;
		}

		$msg = sprintf( 'No address found in order base with ID "%1$s"', $orderBaseItem->getId() );
		throw new \Aimeos\Controller\Jobs\Exception( $msg );
	}


	/**
	 * Returns the coupon ID for the voucher coupon
	 *
	 * @return string Unique ID of the coupon item
	 */
	protected function getCouponId() : string
	{
		if( !isset( $this->couponId ) )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), 'coupon' );

			$search = $manager->filter()->slice( 0, 1 );
			$search->setConditions( $search->compare( '=~', 'coupon.provider', 'Voucher' ) );

			if( ( $item = $manager->search( $search )->first() ) === null ) {
				throw new \Aimeos\Controller\Jobs\Exception( 'No coupon provider "Voucher" available' );
			}

			$this->couponId = $item->getId();
		}

		return $this->couponId;
	}


	/**
	 * Returns the ordered voucher products from the basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Basket object
	 * @return array List of order product items for the voucher products
	 */
	protected function getOrderProducts( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem )
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

		return $list;
	}


	/**
	 * Returns an initialized view object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item
	 * @param string $site Site code
	 * @param string|null $currencyId Three letter ISO currency code
	 * @param string|null $langId ISO language code, maybe country specific
	 * @return \Aimeos\MW\View\Iface Initialized view object
	 */
	protected function view( \Aimeos\MShop\Context\Item\Iface $context, string $site, string $currencyId = null, string $langId = null ) : \Aimeos\MW\View\Iface
	{
		$view = $context->view();
		$params = ['locale' => $langId, 'site' => $site, 'currency' => $currencyId];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Locale( $view, $langId );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $context->getConfig() );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $langId ) );
		$view->addHelper( 'translate', $helper );

		return $view;
	}


	/**
	 * Sends the voucher e-mail for the given orders
	 *
	 * @param \Aimeos\Client\Html\Iface $client HTML client object for rendering the voucher e-mails
	 * @param \Aimeos\Map $items List of order items implementing \Aimeos\MShop\Order\Item\Iface with their IDs as keys
	 * @param int $status Delivery status value
	 */
	protected function process( \Aimeos\Client\Html\Iface $client, \Aimeos\Map $items, int $status )
	{
		$context = $this->getContext();
		$couponManager = \Aimeos\MShop::create( $context, 'coupon' );
		$orderBaseManager = \Aimeos\MShop::create( $context, 'order/base' );

		foreach( $items as $id => $item )
		{
			$couponManager->begin();
			$orderBaseManager->begin();

			try
			{
				$orderBaseItem = $orderBaseManager->load( $item->getBaseId() )->off();

				$orderBaseItem = $this->createCoupons( $orderBaseItem );
				$orderBaseManager->store( $orderBaseItem );

				$this->addOrderStatus( $id, $status );
				$this->sendEmails( $orderBaseItem, $client );

				$orderBaseManager->commit();
				$couponManager->commit();

				$str = sprintf( 'Sent voucher e-mails for order ID "%1$s"', $item->getId() );
				$context->getLogger()->log( $str, Log::INFO, 'email/order/voucher' );
			}
			catch( \Exception $e )
			{
				$orderBaseManager->rollback();
				$couponManager->rollback();

				$str = 'Error while trying to send voucher e-mails for order ID "%1$s": %2$s';
				$msg = sprintf( $str, $item->getId(), $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
				$context->getLogger()->log( $msg, Log::INFO, 'email/order/voucher' );
			}
		}
	}


	/**
	 * Creates coupon codes for the bought vouchers
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Complete order including addresses, products, services
	 */
	protected function createCoupons( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem )
	{
		$map = [];
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/base/product/attribute' );

		foreach( $this->getOrderProducts( $orderBaseItem ) as $orderProductItem )
		{
			if( $orderProductItem->getAttribute( 'coupon-code', 'coupon' ) === null )
			{
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
		}

		$this->addCouponCodes( $map );
		return $orderBaseItem;
	}


	/**
	 * Sends the voucher related e-mail for a single order
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Complete order including addresses, products, services
	 * @param \Aimeos\Client\Html\Iface $client HTML client object for rendering the voucher e-mails
	 */
	protected function sendEmails( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem, \Aimeos\Client\Html\Iface $client )
	{
		$context = $this->getContext();
		$addrItem = $this->getAddressItem( $orderBaseItem );
		$currencyId = $orderBaseItem->getPrice()->getCurrencyId();
		$langId = ( $addrItem->getLanguageId() ?: $orderBaseItem->getLocale()->getLanguageId() );

		$view = $this->view( $context, $orderBaseItem->getSiteCode(), $currencyId, $langId );

		foreach( $this->getOrderProducts( $orderBaseItem ) as $orderProductItem )
		{
			if( ( $codes = $orderProductItem->getAttribute( 'coupon-code', 'coupon' ) ) !== null )
			{
				foreach( (array) $codes as $code )
				{
					$message = $context->getMail()->createMessage();
					$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message ) );

					$view->extOrderProductItem = $orderProductItem;
					$view->extAddressItem = $addrItem;
					$view->extVoucherCode = $code;

					$client->setView( $view );
					$client->header();
					$client->body();

					$context->getMail()->send( $view->mail() );
				}
			}
		}
	}
}
