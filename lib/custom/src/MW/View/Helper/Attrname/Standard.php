<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Attrname;


/**
 * View helper class for creating an HTML image tag
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Attrname\Iface
{
	/**
	 * Returns the attribute name with price if available
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item
	 * @return string Attribute name with price (optional)
	 */
	public function transform( \Aimeos\MShop\Attribute\Item\Iface $item ) : string
	{
		if( $priceItem = $item->getRefItems( 'price', 'default', 'default' )->first() )
		{
			/// Configurable product attribute name (%1$s) with sign (%4$s, +/-), price value (%2$s) and currency (%3$s)
			$str = $this->translate( 'client', '%1$s (%4$s%2$s%3$s)' );
			$value = $priceItem->getValue() + $priceItem->getCosts();
			$view = $this->getView();

			return sprintf( $str, $item->getName(),
				$view->number( abs( $value ), $priceItem->getPrecision() ),
				$view->translate( 'currency', $priceItem->getCurrencyId() ),
				( $value < 0 ? '−' : '+' )
			);
		}

		return $item->getName();
	}
}
