<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
	 * Processes the end of the subscription
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 */
	public function end( \Aimeos\MShop\Subscription\Item\Iface $subscription )
	{
		$context = $this->getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$baseItem = $manager->getItem( $subscription->getOrderBaseId(), ['order/base/address', 'order/base/product'] );

		$addrItem = $baseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		foreach( $baseItem->getProducts() as $orderProduct )
		{
			if( $orderProduct->getId() == $subscription->getOrderProductId() ) {
				$this->sendMail( $context, $subscription, $addrItem, $orderProduct );
			}
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
		if( !isset( $this->client ) ) {
			$this->client = \Aimeos\Client\Html\Email\Subscription\Factory::createClient( $context );
		}

		return $this->client;
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
		$view = $context->getView();
		$view->extAddressItem = $address;
		$view->extOrderProductItem = $product;
		$view->extSubscriptionItem = $subscription;

		$params = [
			'locale' => $context->getLocale()->getLanguageId(),
			'site' => $context->getLocale()->getSite()->getCode(),
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
		$client->getHeader();
		$client->getBody();

		$mailer->send( $message );
	}
}
