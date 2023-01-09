<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
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
		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )
			->text( $view->param( 'f_search' ) )
			->category( $this->categories( $view ), 'default', $this->level() )
			->radius( $view->param( 'f_point', [] ), $view->param( 'f_dist' ) )
			->supplier( $this->suppliers( $view ) )
			->allOf( $this->attributes() )
			->allOf( $view->param( 'f_attrid', [] ) )
			->oneOf( $view->param( 'f_optid', [] ) )
			->oneOf( $view->param( 'f_oneid', [] ) );

		$name = $cntl->function( 'index.price:value', [$context->locale()->getCurrencyId()] );
		$cntl->compare( '!=', $name, null );

		$this->call( 'conditions', $cntl, $view );

		// We need a key but there's no one for the currency alone available, only price/currency combinations
		$view->priceHigh = (int) $cntl->aggregate( 'product.status', 'agg:' . $name, 'max' )->max() + 1;
		$view->priceResetParams = map( $this->getClientParams( $view->param() ) )->remove( 'f_price' )->all();

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


	/**
	 * Returns the attribute IDs used for filtering products
	 *
	 * @return array List of attribute IDs
	 */
	protected function attributes() : array
	{
		$attrids = $this->context()->config()->get( 'client/html/catalog/lists/attrid-default' );
		$attrids = $attrids != null && is_scalar( $attrids ) ? explode( ',', $attrids ) : $attrids; // workaround for TYPO3

		return (array) $attrids;
	}


	/**
	 * Returns the category IDs used for filtering products
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return array List of category IDs
	 */
	protected function categories( \Aimeos\Base\View\Iface $view ) : array
	{
		$catids = $view->param( 'f_catid', $this->context()->config()->get( 'client/html/catalog/lists/catid-default' ) );
		$catids = $catids != null && is_scalar( $catids ) ? explode( ',', $catids ) : $catids; // workaround for TYPO3

		return array_filter( (array) $catids );
	}


	/**
	 * Adds additional conditions for filtering
	 *
	 * @param \Aimeos\Controller\Frontend\Product\Iface $cntl Product controller
	 * @param \Aimeos\Base\View\Iface $view View object
	 */
	protected function conditions( \Aimeos\Controller\Frontend\Product\Iface $cntl, \Aimeos\Base\View\Iface $view )
	{
		if( $view->config( 'client/html/catalog/instock', false ) ) {
			$cntl->compare( '>', 'product.instock', 0 );
		}
	}


	/**
	 * Returns the category depth level
	 *
	 * @return int Category depth level
	 */
	protected function level() : int
	{
		return $this->context()->config()->get( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
	}


	/**
	 * Returns the supplier IDs used for filtering products
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return array List of supplier IDs
	 */
	protected function suppliers( \Aimeos\Base\View\Iface $view ) : array
	{
		$supids = $view->param( 'f_supid', $this->context()->config()->get( 'client/html/catalog/lists/supid-default' ) );
		$supids = $supids != null && is_scalar( $supids ) ? explode( ',', $supids ) : $supids; // workaround for TYPO3

		return (array) $supids;
	}


	/** client/html/catalog/filter/price/template-body
	 * Relative path to the HTML body template of the catalog filter price client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the result shown in the body of the frontend. The
	 * configuration string is the path to the template file relative
	 * to the templates directory (usually in templates/client/html).
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
