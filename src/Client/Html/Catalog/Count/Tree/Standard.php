<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Count\Tree;


/**
 * Default implementation of catalog count tree section HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
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
		$config = $context->config();

		/** client/html/catalog/count/tree/aggregate
		 * Enables or disables generating product counts for the category catalog filter
		 *
		 * This configuration option allows shop owners to enable or disable product counts
		 * for the tree section of the catalog filter HTML client.
		 *
		 * @param boolean Disabled if "0", enabled if "1"
		 * @since 2014.03
		 */
		if( $config->get( 'client/html/catalog/count/tree/aggregate', true ) == true )
		{
			$startid = $view->config( 'client/html/catalog/filter/tree/startid' );
			$level = $view->config( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

			$cntl = \Aimeos\Controller\Frontend::create( $context, 'catalog' )->root( current( (array) $startid ) );
			$root = $cntl->getTree( \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

			if( ( $catId = $view->param( 'f_catid', $root->getId() ) ) != null && $catId != $root->getId() ) {
				$cntl->visible( $cntl->getPath( $catId )->keys()->toArray() );
			} elseif( $level != \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE ) {
				$cntl->visible( [$root->getId()] );
			}

			$tree = $cntl->getTree();
			$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )
				->radius( $view->param( 'f_point', [] ), $view->param( 'f_dist' ) )
				->category( $tree->toList()->keys()->toArray() )
				->supplier( $view->param( 'f_supid', [] ) )
				->allof( $view->param( 'f_attrid', [] ) )
				->oneOf( $view->param( 'f_optid', [] ) )
				->oneOf( $view->param( 'f_oneid', [] ) )
				->text( $view->param( 'f_search' ) )
				->slice( 0, 0x7fffffff ) // restricted by mshop/common/manager/aggregate/limit
				->sort();

			$view->treeCountList = $cntl->aggregate( 'index.catalog.id' );

			if( $level === \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE ) {
				$view->treeCountList = map( $this->counts( $this->traverse( $tree, $view->treeCountList->toArray() ) ) );
			}
		}

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the product counts per node
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $node Tree node, maybe with children
	 * @return array Associative list of catalog IDs as keys and product counts as values
	 */
	protected function counts( \Aimeos\MShop\Catalog\Item\Iface $node ) : array
	{
		$list = [$node->getId() => $node->count];

		foreach( $node->getChildren() as $child ) {
			$list += $this->counts( $child );
		}

		return $list;
	}


	/**
	 * Traverses the tree and adds the aggregated product counts to each node
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $node Tree node, maybe with children
	 * @param array $counts Associative list of catalog IDs as keys and product counts as values
	 * @return \Aimeos\MShop\Catalog\Item\Iface Updated tree node
	 */
	protected function traverse( \Aimeos\MShop\Catalog\Item\Iface $node, array $counts ) : \Aimeos\MShop\Catalog\Item\Iface
	{
		$count = ( isset( $counts[$node->getId()] ) ? $counts[$node->getId()] : 0 );

		foreach( $node->getChildren() as $child ) {
			$count += $this->traverse( $child, $counts )->count;
		}

		$node->count = $count;
		return $node;
	}


	/** client/html/catalog/count/tree/template-body
	 * Relative path to the HTML body template of the catalog count tree client.
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
	 * @see client/html/catalog/count/tree/template-header
	 */
}
