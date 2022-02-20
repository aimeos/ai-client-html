<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
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
	use \Aimeos\Controller\Jobs\Mail;


	/**
	 * Executed after the subscription renewal
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice item
	 */
	public function renewAfter( \Aimeos\MShop\Subscription\Item\Iface $subscription, \Aimeos\MShop\Order\Item\Iface $order )
	{
		if( $subscription->getReason() === \Aimeos\MShop\Subscription\Item\Iface::REASON_PAYMENT ) {
			$this->notify( $subscription );
		}
	}


	/**
	 * Processes the end of the subscription
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
	 */
	public function end( \Aimeos\MShop\Subscription\Item\Iface $subscription )
	{
		$this->notify( $subscription );
	}


	/**
	 * Sends e-mails for the given subscription
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item object
	 */
	protected function notify( \Aimeos\MShop\Subscription\Item\Iface $subscription )
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'order/base' );
		$base = $manager->get( $subscription->getOrderBaseId(), ['order/base/address', 'order/base/product'] );

		$address = current( $base->getAddress( 'payment' ) );
		$siteIds = explode( '.', trim( $base->getSiteId(), '.' ) );
		$sites = \Aimeos\MShop::create( $context, 'locale/site' )->getPath( end( $siteIds ) );

		$view = $this->view( $base, $sites->getTheme()->filter()->last() );
		$view->subscriptionItem = $subscription;
		$view->addressItem = $address;

		foreach( $base->getProducts() as $orderProduct )
		{
			if( $orderProduct->getId() == $subscription->getOrderProductId() ) {
				$this->send( $view->set( 'orderProductItem', $orderProduct ), $address, $sites->getLogo()->filter()->last() );
			}
		}
	}


	/**
	 * Sends the subscription e-mail to the customer
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $address Address item
	 * @param string|null $logoPath Path to the logo
	 */
	protected function send( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Order\Item\Base\Address\Iface $address, string $logoPath = null )
	{
		$context = $this->context();
		$config = $context->config();

		$msg = $this->call( 'mailTo', $address );
		$view->logo = $msg->embed( $this->call( 'mailLogo', $logoPath ), basename( (string) $logoPath ) );

		$msg->subject( $context->translate( 'client', 'Your subscription' ) )
			->html( $view->render( $config->get( 'controller/jobs/order/email/subscription/template-html', 'order/email/subscription/html' ) ) )
			->text( $view->render( $config->get( 'controller/jobs/order/email/subscription/template-text', 'order/email/subscription/text' ) ) )
			->send();
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
		$address = current( $base->getAddress( 'payment' ) );
		$langId = $address->getLanguageId() ?: $base->locale()->getLanguageId();

		$view = $this->call( 'mailView', $langId );
		$view->intro = $this->call( 'mailIntro', $address );
		$view->css = $this->call( 'mailCss', $theme );
		$view->urlparams = [
			'currency' => $base->getPrice()->getCurrencyId(),
			'site' => $base->getSiteCode(),
			'locale' => $langId,
		];

		return $view;
	}
}
