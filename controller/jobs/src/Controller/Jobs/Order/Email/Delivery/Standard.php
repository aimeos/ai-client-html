<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Order
 */


namespace Aimeos\Controller\Jobs\Order\Email\Delivery;


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
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Order delivery related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Sends order delivery status update e-mails' );
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

		$templatePaths = $this->getAimeos()->getCustomPaths( 'client/html/templates' );
		$client = \Aimeos\Client\Html\Email\Delivery\Factory::createClient( $context, $templatePaths );

		$orderManager = \Aimeos\MShop\Factory::createManager( $context, 'order' );

		/** controller/jobs/order/email/delivery/standard/limit-days
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
		 * @see controller/jobs/order/email/delivery/standard/status
		 * @see controller/jobs/order/email/payment/standard/limit-days
		 * @see controller/jobs/service/delivery/process/limit-days
		 */
		$limit = $config->get( 'controller/jobs/order/email/delivery/standard/limit-days', 90 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		$default = array(
			\Aimeos\MShop\Order\Item\Base::STAT_PROGRESS,
			\Aimeos\MShop\Order\Item\Base::STAT_DISPATCHED,
			\Aimeos\MShop\Order\Item\Base::STAT_REFUSED,
			\Aimeos\MShop\Order\Item\Base::STAT_RETURNED,
		);

		/** controller/jobs/order/email/delivery/standard/status
		 * Only send order delivery notification e-mails for these delivery status values
		 *
		 * Notification e-mail about delivery status changes can be sent for these
		 * status values:
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
		 * @see controller/jobs/order/email/payment/standard/status
		 * @see controller/jobs/order/email/delivery/standard/limit-days
		 */
		foreach( (array) $config->get( 'controller/jobs/order/email/delivery/standard/status', $default ) as $status )
		{
			$start = 0;
			$orderSearch = $orderManager->createSearch();

			$param = array( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_DELIVERY, $status );
			$orderFunc = $orderSearch->createFunction( 'order.containsStatus', $param );

			$expr = array(
				$orderSearch->compare( '>=', 'order.mtime', $limitDate ),
				$orderSearch->compare( '==', 'order.statusdelivery', $status ),
				$orderSearch->compare( '==', $orderFunc, 0 ),
			);
			$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

			do
			{
				$orderSearch->setSlice( $start );
				$items = $orderManager->searchItems( $orderSearch );

				$this->process( $client, $items, $status );

				$count = count( $items );
				$start += $count;
			}
			while( $count >= $orderSearch->getSliceSize() );
		}
	}


	/**
	 * Adds the status of the delivered e-mail for the given order ID
	 *
	 * @param string $orderId Unique order ID
	 * @param integer $value Status value
	 */
	protected function addOrderStatus( $orderId, $value )
	{
		$orderStatusManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/status' );

		$statusItem = $orderStatusManager->createItem();
		$statusItem->setParentId( $orderId );
		$statusItem->setType( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_DELIVERY );
		$statusItem->setValue( $value );

		$orderStatusManager->saveItem( $statusItem );
	}


	/**
	 * Returns the delivery address item of the order
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem Order including address items
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Delivery or payment address item
	 * @throws \Aimeos\MShop\Order\Exception If no address item is available
	 */
	protected function getAddressItem( \Aimeos\MShop\Order\Item\Base\Iface $orderBaseItem )
	{
		try
		{
			$addr = $orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

			if( $addr->getEmail() == '' )
			{
				$payAddr = $orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
				$addr->setEmail( $payAddr->getEmail() );
			}
		}
		catch( \Exception $e )
		{
			$addr = $orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		}

		return $addr;
	}


	/**
	 * Returns an initialized view object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item
	 * @param string $langId ISO language code, maybe country specific
	 * @return \Aimeos\MW\View\Iface Initialized view object
	 */
	protected function getView( $context, $langId )
	{
		$view = $context->getView();

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
	 * @param \Aimeos\MShop\Order\Item\Iface[] $items Associative list of order items with their IDs as keys
	 * @param integer $status Delivery status value
	 */
	protected function process( \Aimeos\Client\Html\Iface $client, array $items, $status )
	{
		$context = $this->getContext();
		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );

		foreach( $items as $id => $item )
		{
			try
			{
				$orderBaseItem = $orderBaseManager->load( $item->getBaseId() );
				$addr = $this->getAddressItem( $orderBaseItem );

				$this->processItem( $client, $item, $orderBaseItem, $addr );
				$this->addOrderStatus( $id, $status );

				$str = sprintf( 'Sent order delivery e-mail for status "%1$s" to "%2$s"', $status, $addr->getEmail() );
				$context->getLogger()->log( $str, \Aimeos\MW\Logger\Base::INFO );
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send delivery e-mail for order ID "%1$s" and status "%2$s": %3$s';
				$msg = sprintf( $str, $item->getId(), $item->getDeliveryStatus(), $e->getMessage() );
				$context->getLogger()->log( $msg );
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

		$view = $this->getView( $context, $addrItem->getLanguageId() );
		$view->extAddressItem = $addrItem;
		$view->extOrderBaseItem = $orderBaseItem;
		$view->extOrderItem = $orderItem;

		$client->setView( $view );
		$client->getHeader();
		$client->getBody();

		$context->getMail()->send( $view->mail() );
	}
}
