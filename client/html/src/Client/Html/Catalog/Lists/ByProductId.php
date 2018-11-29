<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Lists;


/**
 * Implementation of catalog list section HTML clients with a configurable list of products by their id.
 *
 * @package Client
 * @subpackage Html
 */
class Byproductid
	extends Standard
{
	/**
	 * Adds a filter condition to restrict to a set of product ids.
	 * Ids are taken from config with key "client/html/catalog/lists/by-product-id/restrict-product-ids
	 *
	 * @param string $text Text to search for
	 * @param string $catid Category ID to search for
	 * @param string $sort Sortation string (relevance, name, price)
	 * @param string $sortdir Sortation direction (+ or -)
	 * @param integer $page Page number starting from 1
	 * @param integer $size Page size
	 * @param boolean $catfilter True to include catalog criteria in product filter, false if not
	 * @param boolean $textfilter True to include text criteria in product filter, false if not
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	protected function createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter )
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		/** client/html/catalog/lists/by-product-id/restrict-product-ids
		 * List of product ids to limit the current list by.
		 * Should be set dynamically through some integration plugin,
		 * to allow a list of products with configurable products.
		 *
		 * @param string List of product ids to limit the current list by
		 * @since 2018.11
		 * @category Developer
		 */
		$productIds = $config->get( 'client/html/catalog/lists/by-product-id/restrict-product-ids', [] );


		$filter = parent::createProductListFilter( $text, $catid, $sort, $sortdir, $page, $size, $catfilter, $textfilter );

		if ( !is_array( $productIds ) || empty( $productIds ) ) {
			return $filter;
		}

		$expr = array(
			$filter->compare( '==', 'product.id', $productIds ),
			$filter->getConditions(),
		);
		$filter->setConditions( $filter->combine( '&&', $expr ) );
		// overwrite default relevance sorting (sorted by position of product in selected category)
		// with sort by position in list of ids
		if ( $sort === '' || $sort === 'relevance' ) {
			$sortfunc = $filter->createFunction( 'sort:product.id', array(implode( ',', $productIds )) );
			$sortation = $filter->sort( '+', $sortfunc );
			$filter->setSortations( [$sortation] );
		}
		return $filter;
	}
}

