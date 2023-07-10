<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Filter\Supplier;


/**
 * Default implementation of catalog supplier filter section in HTML client.
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
		/** client/html/catalog/filter/supplier/domains
		 * List of domain names whose items should be fetched with the filter suppliers
		 *
		 * The templates rendering the suppliers in the catalog filter usually
		 * add the images and texts associated to each item. If you want to
		 * display additional content, you can configure your own list of
		 * domains (supplier, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is
		 * required for fetching the content!
		 *
		 * @param array List of domain item names
		 * @since 2018.07
		 * @see client/html/catalog/filter/supplier/types
		 */
		$domains = $view->config( 'client/html/catalog/filter/supplier/domains', ['text', 'media', 'media/property'] );

		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'supplier' )
			->uses( $domains )->sort( 'supplier.position,supplier.label' );

		$items = $cntl->slice( 0, 20 )->search();
		$items = $cntl->compare( '==', 'supplier.id', $view->param( 'f_supid', [] ) )
			->slice( 0, 100 )->search()->replace( $items );

		$this->addMetaItems( $items, $expire, $tags, ['supplier'] );

		$view->supplierList = $items;
		$view->supplierResetParams = map( $view->param() )->except( 'f_supid' )->toArray();

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


	/** client/html/catalog/filter/supplier/template-body
	 * Relative path to the HTML body template of the catalog filter supplier client.
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
	 * @since 2018.07
	 * @see client/html/catalog/filter/supplier/template-header
	 */
}
