<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Filter\Price;


/**
 * Default implementation of catalog price filter section in HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'index' );

		$filter = $manager->filter( true )->slice( 0, 0x7fffffff );
		$name = $filter->make( 'index.price:value', [$context->locale()->getCurrencyId()] );
		$filter->add( $filter->is( $name, '!=', null ) );

		$params = $this->getClientParams( $view->param() );
		unset( $params['f_price'] );

		// We need a key but there's no one for the currency alone available, only price/currency combinations
		$view->priceHigh = (int) $manager->aggregate( $filter, 'product.status', 'agg:' . $name, 'max' )->max();
		$view->priceResetParams = $params;

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		return null;
	}


	/** client/html/catalog/filter/price/template-body
	 * Relative path to the HTML body template of the catalog filter price client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the result shown in the body of the frontend. The
	 * configuration string is the path to the template file relative
	 * to the templates directory (usually in client/html/templates).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but suffixed by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, it
	 * should be suffixed by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page body
	 * @since 2014.03
	 * @see client/html/catalog/filter/price/template-header
	 */
}
