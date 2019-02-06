<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Client\Summary;


/**
 * Base class for the summary HTML clients
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
{
	/**
	 * Returns a list of tax rates and values for the given basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing the products, services, etc.
	 * @return array Associative list of tax rates as key and corresponding amounts as value
	 */
	protected function getTaxRates( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$taxrates = [];

		foreach( $basket->getProducts() as $product )
		{
			$price = clone $product->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price, $product->getQuantity() );
			} else {
				$taxrates[$taxrate] = $price->addItem( $price, $product->getQuantity() - 1 );
			}
		}

		foreach( $basket->getService( 'delivery' ) as $service )
		{
			$price = clone $service->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price );
			} else {
				$taxrates[$taxrate] = $price;
			}
		}

		foreach( $basket->getService( 'payment' ) as $service )
		{
			$price = clone $service->getPrice();
			$taxrate = $price->getTaxrate();

			if( isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate]->addItem( $price );
			} else {
				$taxrates[$taxrate] = $price;
			}
		}

		return $taxrates;
	}


	/**
	 * Returns the payment status at which download files are shown
	 *
	 * @return integer Payment status from \Aimeos\MShop\Order\Item\Base
	 */
	protected function getDownloadPaymentStatus()
	{
		$config = $this->getContext()->getConfig();
		$default = \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED;

		/** client/html/common/summary/detail/download/payment-status
		 * Minium payment status value for product download files
		 *
		 * This setting specifies the payment status value of an order for which
		 * links to bought product download files are shown on the "thank you"
		 * page, in the "MyAccount" and in the e-mails sent to the customers.
		 *
		 * The value is one of the payment constant values from
		 * {@link https://github.com/aimeos/aimeos-core/blob/master/lib/mshoplib/src/MShop/Order/Item/Base.php#L105}.
		 * Most of the time, only two values are of interest:
		 * * 5: payment authorized
		 * * 6: payment received
		 *
		 * @param integer Order payment constant value
		 * @since 2016.3
		 * @category User
		 * @category Developer
		 */
		return $config->get( 'client/html/common/summary/detail/download/payment-status', $default );
	}
}