<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Filter\Tree;


/**
 * Default implementation of catalog tree filter section in HTML client.
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
		/** client/html/catalog/filter/tree/domains
		 * List of domain names whose items should be fetched with the filter categories
		 *
		 * The templates rendering the categories in the catalog filter usually
		 * add the images and texts associated to each item. If you want to
		 * display additional content, you can configure your own list of
		 * domains (attribute, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is
		 * required for fetching the content!
		 *
		 * @param array List of domain item names
		 * @since 2014.03
		 * @see controller/frontend/catalog/levels-always
		 * @see controller/frontend/catalog/levels-only
		 * @see client/html/catalog/filter/tree/startid
		 */
		$domains = $view->config( 'client/html/catalog/filter/tree/domains', ['text', 'media', 'media/property'] );

		/** client/html/catalog/filter/tree/startid
		 * The ID of the category node that should be the root of the displayed category tree
		 *
		 * If you want to display only a part of your category tree, you can
		 * configure the ID of the category node from which rendering the
		 * remaining sub-tree should start.
		 *
		 * In most cases you can set this value via the administration interface
		 * of the shop application. In that case you often can configure the
		 * start ID individually for each catalog filter.
		 *
		 * @param string Category ID
		 * @since 2014.03
		 * @see controller/frontend/catalog/levels-always
		 * @see controller/frontend/catalog/levels-only
		 * @see client/html/catalog/filter/tree/domains
		 */
		$startid = $view->config( 'client/html/catalog/filter/tree/startid' );

		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'catalog' )
			->uses( $domains )->root( $startid );

		if( ( $currentid = $view->param( 'f_catid' ) ) === null ) {
			$catItems = $cntl->getTree( \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE )->toList();
		} else {
			$catItems = $cntl->getPath( $currentid );
		}

		$tree = $cntl->visible( $catItems->keys()->all() )->getTree( $cntl::TREE );

		$this->addMetaItemCatalog( $tree, $expire, $tags, ['catalog'] );

		$view->treeCatalogPath = $catItems;
		$view->treeCatalogTree = $tree;

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


	/** client/html/catalog/filter/tree/template-body
	 * Relative path to the HTML body template of the catalog filter tree client.
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
	 * @see client/html/catalog/filter/tree/template-header
	 */
}
