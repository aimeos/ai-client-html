<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Subscription\Process\Processor\Email;


/**
 * Customer group processor for subscriptions
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Subscription\Process\Processor\Base
	implements \Aimeos\Controller\Common\Subscription\Process\Processor\Iface
{
	/** controller/common/subscription/export/csv/processor/email/name
	 * Name of the customer group processor implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Controller\Common\Subscription\Process\Processor\Email\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the processor class name
	 * @since 2018.04
	 * @category Developer
	 */


	/**
	 * Executed after the subscription renewal
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice item
	 */
	public function renewAfter( \Aimeos\MShop\Subscription\Item\Iface $subscription, \Aimeos\MShop\Order\Item\Iface $order )
	{
		if( $subscription->getReason() === \Aimeos\MShop\Subscription\Item\Iface::REASON_PAYMENT ) {
			$this->process( $subscription );
		}
	}


	/**
	 * Processes the end of the subscription
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 */
	public function end( \Aimeos\MShop\Subscription\Item\Iface $subscription )
	{
		$this->process( $subscription );
	}


	/**
	 * Returns the product notification e-mail client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return \Aimeos\Client\Html\Iface Product notification e-mail client
	 */
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( !isset( $this->client ) ) {
			$this->client = \Aimeos\Client\Html\Email\Subscription\Factory::create( $context );
		}

		return $this->client;
	}


	/**
	 * Sends e-mails for the given subscription
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item object
	 */
	protected function process( \Aimeos\MShop\Subscription\Item\Iface $subscription )
	{
		$context = $this->getContext();

		$manager = \Aimeos\MShop::create( $context, 'order/base' );
		$baseItem = $manager->get( $subscription->getOrderBaseId(), ['order/base/address', 'order/base/product'] );

		$addrItem = $baseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, 0 );

		foreach( $baseItem->getProducts() as $orderProduct )
		{
			if( $orderProduct->getId() == $subscription->getOrderProductId() ) {
				$this->sendMail( $context, $subscription, $addrItem, $orderProduct );
			}
		}
	}


	/**
	 * Sends the subscription e-mail for the given customer address and products
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address of the customer
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $product Subscription product the notification should be sent for
	 */
	protected function sendMail( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Subscription\Item\Iface $subscription,
		\Aimeos\MShop\Common\Item\Address\Iface $address, \Aimeos\MShop\Order\Item\Base\Product\Iface $product )
	{
		$view = $context->view();
		$view->extAddressItem = $address;
		$view->extOrderProductItem = $product;
		$view->extSubscriptionItem = $subscription;

		$params = [
			'locale' => $context->getLocale()->getLanguageId(),
			'site' => $context->getLocale()->getSiteItem()->getCode(),
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

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
