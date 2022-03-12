<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Favorite;


/**
 * Default implementation of account favorite HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();
		$context = $this->context();
		$ids = (array) $view->param( 'fav_id', [] );

		if( $context->user() !== null && !empty( $ids ) && $view->request()->getMethod() === 'POST' )
		{
			switch( $view->param( 'fav_action' ) )
			{
				case 'add':
					$this->addFavorites( $ids ); break;
				case 'delete':
					$this->deleteFavorites( $ids ); break;
			}
		}
	}


	/**
	 * Adds new product favorite references to the given customer
	 *
	 * @param array $ids List of product IDs
	 */
	protected function addFavorites( array $ids )
	{
		$context = $this->context();

		/** client/html/account/favorite/maxitems
		 * Maximum number of products that can be favorites
		 *
		 * This option limits the number of products users can add to their
		 * favorite list. It must be a positive integer value greater than 0.
		 *
		 * @param integer Number of products
		 * @since 2019.04
		 */
		$max = $context->config()->get( 'client/html/account/favorite/maxitems', 100 );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$item = $cntl->uses( ['product' => ['favorite']] )->get();

		if( count( $item->getRefItems( 'product', null, 'favorite' ) ) + count( $ids ) > $max )
		{
			$msg = sprintf( $context->translate( 'client', 'You can only save up to %1$s products as favorites' ), $max );
			throw new \Aimeos\Client\Html\Exception( $msg );
		}

		foreach( $ids as $id )
		{
			if( ( $listItem = $item->getListItem( 'product', 'favorite', $id ) ) === null ) {
				$listItem = $cntl->createListItem();
			}
			$cntl->addListItem( 'product', $listItem->setType( 'favorite' )->setRefId( $id ) );
		}

		$cntl->store();
	}


	/**
	 * Removes product favorite references from the customer
	 *
	 * @param array $ids List of product IDs
	 */
	protected function deleteFavorites( array $ids )
	{
		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'customer' );
		$item = $cntl->uses( ['product' => ['favorite']] )->get();

		foreach( $ids as $id )
		{
			if( ( $listItem = $item->getListItem( 'product', 'favorite', $id ) ) !== null ) {
				$cntl->deleteListItem( 'product', $listItem );
			}
		}

		$cntl->store();
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param \Aimeos\Base\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return int Page number starting from 1
	 */
	protected function getProductListPage( \Aimeos\Base\View\Iface $view ) : int
	{
		$page = (int) $view->param( 'fav_page', 1 );
		return ( $page < 1 ? 1 : $page );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param \Aimeos\Base\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return int Page size
	 */
	protected function getProductListSize( \Aimeos\Base\View\Iface $view ) : int
	{
		/** client/html/account/favorite/size
		 * The number of products shown in a list page for favorite products
		 *
		 * Limits the number of products that is shown in the list pages to the
		 * given value. If more products are available, the products are split
		 * into bunches which will be shown on their own list page. The user is
		 * able to move to the next page (or previous one if it's not the first)
		 * to display the next (or previous) products.
		 *
		 * The value must be an integer number from 1 to 100. Negative values as
		 * well as values above 100 are not allowed. The value can be overwritten
		 * per request if the "l_size" parameter is part of the URL.
		 *
		 * @param integer Number of products
		 * @since 2014.09
		 * @see client/html/catalog/lists/size
		 */
		$defaultSize = $this->context()->config()->get( 'client/html/account/favorite/size', 48 );

		$size = (int) $view->param( 'fav-size', $defaultSize );
		return ( $size < 1 || $size > 100 ? $defaultSize : $size );
	}


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

		/** client/html/account/favorite/domains
		 * A list of domain names whose items should be available in the account favorite view template
		 *
		 * The templates rendering product details usually add the images,
		 * prices and texts associated to the product item. If you want to
		 * display additional or less content, you can configure your own
		 * list of domains (attribute, media, price, product, text, etc. are
		 * domains) whose items are fetched from the storage. Please keep
		 * in mind that the more domains you add to the configuration, the
		 * more time is required for fetching the content!
		 *
		 * @param array List of domain names
		 * @since 2014.09
		 * @see client/html/catalog/domains
		 */
		$domains = $context->config()->get( 'client/html/account/favorite/domains', ['text', 'price', 'media'] );
		$domains['product'] = ['favorite'];

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$listItems = $cntl->uses( $domains )->get()->getListItems( 'product', 'favorite' );
		$total = count( $listItems );

		$size = $this->getProductListSize( $view );
		$current = $this->getProductListPage( $view );
		$last = ( $total != 0 ? ceil( $total / $size ) : 1 );

		$view->favoriteItems = $listItems;
		$view->favoritePageFirst = 1;
		$view->favoritePagePrev = ( $current > 1 ? $current - 1 : 1 );
		$view->favoritePageNext = ( $current < $last ? $current + 1 : $last );
		$view->favoritePageLast = $last;
		$view->favoritePageCurr = $current;

		return parent::data( $view, $tags, $expire );
	}
}
