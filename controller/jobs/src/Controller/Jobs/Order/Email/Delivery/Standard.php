<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Order
 */


namespace Aimeos\Controller\Jobs\Order\Email\Delivery;

use \Aimeos\MW\Logger\Base as Log;


/**
 * Order delivery e-mail job controller.
 *
 * @package Controller
 * @subpackage Order
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Order delivery related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Sends order delivery status update e-mails' );
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

		$client = \Aimeos\Client\Html\Email\Delivery\Factory::create( $context );

		$orderManager = \Aimeos\MShop::create( $context, 'order' );

		/** controller/jobs/order/email/delivery/limit-days
		 * Only send delivery e-mails of orders that were created in the past within the configured number of days
		 *
		 * The delivery e-mails are normally send immediately after the delivery
		 * status has changed. This option prevents e-mails for old order from
		 * being send in case anything went wrong or an update failed to avoid
		 * confusion of customers.
		 *
		 * @param integer Number of days
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/delivery/status
		 * @see controller/jobs/order/email/payment/limit-days
		 * @see controller/jobs/service/delivery/process/limit-days
		 */
		$limit = $config->get( 'controller/jobs/order/email/delivery/limit-days', 90 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		$default = array(
			\Aimeos\MShop\Order\Item\Base::STAT_PROGRESS,
			\Aimeos\MShop\Order\Item\Base::STAT_DISPATCHED,
			\Aimeos\MShop\Order\Item\Base::STAT_REFUSED,
			\Aimeos\MShop\Order\Item\Base::STAT_RETURNED,
		);

		/** controller/jobs/order/email/delivery/status
		 * Only send order delivery notification e-mails for these delivery status values
		 *
		 * Notification e-mail about delivery status changes can be sent for these
		 * status values:
		 *
		 * * 0: deleted
		 * * 1: pending
		 * * 2: progress
		 * * 3: dispatched
		 * * 4: delivered
		 * * 5: lost
		 * * 6: refused
		 * * 7: returned
		 *
		 * User-defined status values are possible but should be in the private
		 * block of values between 30000 and 32767.
		 *
		 * @param integer Delivery status constant
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/payment/status
		 * @see controller/jobs/order/email/delivery/limit-days
		 */
		foreach( (array) $config->get( 'controller/jobs/order/email/delivery/status', $default ) as $status )
		{
			$start = 0;
			$orderSearch = $orderManager->filter();

			$param = array( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_DELIVERY, (string) $status );
			$orderFunc = $orderSearch->make( 'order:status', $param );

			$expr = array(
				$orderSearch->compare( '>=', 'order.mtime', $limitDate ),
				$orderSearch->compare( '==', 'order.statusdelivery', $status ),
				$orderSearch->compare( '==', $orderFunc, 0 ),
			);
			$orderSearch->setConditions( $orderSearch->and( $expr ) );

			do
			{
				$orderSearch->slice( $start );
				$items = $orderManager->search( $orderSearch );

				$this->process( $client, $items, $status );

				$count = count( $items );
				$start += $count;
			}
			while( $count >= $orderSearch->getLimit() );
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

		$statusItem = $orderStatusManager->create();
		$statusItem->setParentId( $orderId );
		$statusItem->setType( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_DELIVERY );
		$statusItem->setValue( $value );

		$orderStatusManager->save( $statusItem );
	}


	/**
	 * Returns the delivery address item of the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Order including address items
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Delivery or payment address item
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
	 * Returns an initialized view object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Complete order including addresses, products, services
	 * @param string|null $langId ISO language code, maybe country specific
	 * @return \Aimeos\MW\View\Iface Initialized view object
	 */
	protected function view( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem, string $langId = null ) : \Aimeos\MW\View\Iface
	{
		$view = $context->view();

		$params = [
			'locale' => $langId,
			'site' => $orderBaseItem->getSiteCode(),
			'currency' => $orderBaseItem->getLocale()->getCurrencyId()
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Locale( $view, $langId );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $context->getConfig() );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $context->getMail()->createMessage() );
		$view->addHelper( 'mail', $helper );

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $langId ) );
		$view->addHelper( 'translate', $helper );

		return $view;
	}


	/**
	 * Sends the delivery e-mail for the given orders
	 *
	 * @param \Aimeos\Client\Html\Iface $client HTML client object for rendering the delivery e-mails
	 * @param \Aimeos\Map $items List of order items implementing \Aimeos\MShop\Order\Item\Iface with their IDs as keys
	 * @param int $status Delivery status value
	 */
	protected function process( \Aimeos\Client\Html\Iface $client, \Aimeos\Map $items, int $status )
	{
		$context = $this->getContext();
		$orderBaseManager = \Aimeos\MShop::create( $context, 'order/base' );

		foreach( $items as $id => $item )
		{
			try
			{
				$orderBaseItem = $orderBaseManager->load( $item->getBaseId() );
				$addr = $this->getAddressItem( $orderBaseItem );

				if( $addr->getEmail() )
				{
					$this->processItem( $client, $item, $orderBaseItem, $addr );

					$str = sprintf( 'Sent order delivery e-mail for status "%1$s" to "%2$s"', $status, $addr->getEmail() );
					$context->getLogger()->log( $str, Log::INFO, 'email/order/delivery' );
				}

				$this->addOrderStatus( $id, $status );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send delivery e-mail for order ID "%1$s" and status "%2$s": %3$s';
				$msg = sprintf( $str, $item->getId(), $item->getStatusDelivery(), $e->getMessage() );
				$context->getLogger()->log( $msg . PHP_EOL . $e->getTraceAsString(), Log::ERR, 'email/order/payment' );
			}
		}
	}


	/**
	 * Sends the delivery related e-mail for a single order
	 *
	 * @param \Aimeos\Client\Html\Iface $client HTML client object for rendering the delivery e-mails
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item the delivery related e-mail should be sent for
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Complete order including addresses, products, services
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $addrItem Address item to send the e-mail to
	 */
	protected function processItem( \Aimeos\Client\Html\Iface $client, \Aimeos\MShop\Order\Item\Iface $orderItem,
		\Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem, \Aimeos\MShop\Order\Item\Base\Address\Iface $addrItem )
	{
		$context = $this->getContext();
		$langId = ( $addrItem->getLanguageId() ?: $orderBaseItem->getLocale()->getLanguageId() );

		$view = $this->view( $context, $orderBaseItem, $langId );
		$view->extAddressItem = $addrItem;
		$view->extOrderBaseItem = $orderBaseItem;
		$view->extOrderItem = $orderItem;

		$client->setView( $view );
		$client->header();
		$client->body();

		$context->getMail()->send( $view->mail() );
	}
}
