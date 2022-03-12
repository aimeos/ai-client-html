<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Watch;


/**
 * Default implementation of account watch HTML client.
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
		$ids = (array) $view->param( 'wat_id', [] );

		if( $context->user() !== null && !empty( $ids ) && $view->request()->getMethod() === 'POST' )
		{
			switch( $view->param( 'wat_action' ) )
			{
				case 'add':
					$this->addItems( $view, $ids ); break;
				case 'edit':
					$this->editItems( $view, $ids ); break;
				case 'delete':
					$this->deleteItems( $view, $ids ); break;
			}
		}
	}


	/**
	 * Adds one or more list items to the given customer item
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param array $ids List of referenced IDs
	 */
	protected function addItems( \Aimeos\Base\View\Iface $view, array $ids )
	{
		$context = $this->context();

		/** client/html/account/watch/maxitems
		 * Maximum number of products that can be watched in parallel
		 *
		 * This option limits the number of products that can be watched
		 * after the users added the products to their watch list.
		 * It must be a positive integer value greater than 0.
		 *
		 * Note: It's recommended to set this value not too high as this
		 * leads to a high memory consumption when the e-mails are generated
		 * to notify the customers. The memory used will up to 100*maxitems
		 * of the footprint of one product item including the associated
		 * texts, prices and media.
		 *
		 * @param integer Number of products
		 * @since 2014.09
		 */
		$max = $context->config()->get( 'client/html/account/watch/maxitems', 100 );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$item = $cntl->uses( ['product' => ['watch']] )->get();

		if( count( $item->getRefItems( 'product', null, 'watch' ) ) + count( $ids ) > $max )
		{
			$msg = sprintf( $context->translate( 'client', 'You can only watch up to %1$s products' ), $max );
			throw new \Aimeos\Client\Html\Exception( $msg );
		}

		foreach( $ids as $id )
		{
			if( ( $listItem = $item->getListItem( 'product', 'watch', $id ) ) === null ) {
				$listItem = $cntl->createListItem();
			}
			$cntl->addListItem( 'product', $listItem->setType( 'watch' )->setRefId( $id ) );
		}

		$cntl->store();
	}


	/**
	 * Removes the referencing list items from the given item
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param array $ids List of referenced IDs
	 */
	protected function deleteItems( \Aimeos\Base\View\Iface $view, array $ids )
	{
		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'customer' );
		$item = $cntl->uses( ['product' => ['watch']] )->get();

		foreach( $ids as $id )
		{
			if( ( $listItem = $item->getListItem( 'product', 'watch', $id ) ) !== null ) {
				$cntl->deleteListItem( 'product', $listItem );
			}
		}

		$cntl->store();
	}


	/**
	 * Updates the item using the given reference IDs
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param array $ids List of referenced IDs
	 */
	protected function editItems( \Aimeos\Base\View\Iface $view, array $ids )
	{
		$context = $this->context();
		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$item = $cntl->uses( ['product' => ['watch']] )->get();

		$config = [
			'timeframe' => $view->param( 'wat_timeframe', 7 ),
			'pricevalue' => $view->param( 'wat_pricevalue', '0.00' ),
			'price' => $view->param( 'wat_price', 0 ),
			'stock' => $view->param( 'wat_stock', 0 ),
			'currency' => $context->locale()->getCurrencyId(),
		];

		foreach( $ids as $id )
		{
			if( ( $listItem = $item->getListItem( 'product', 'watch', $id ) ) !== null )
			{
				$time = time() + ( $config['timeframe'] + 1 ) * 86400;
				$listItem = $listItem->setDateEnd( date( 'Y-m-d 00:00:00', $time ) )->setConfig( $config );

				$cntl->addListItem( 'product', $listItem );
			}
		}

		$cntl->store();
	}


	/**
	 * Returns the sanitized page from the parameters for the product list.
	 *
	 * @param \Aimeos\Base\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return integer Page number starting from 1
	 */
	protected function getProductListPage( \Aimeos\Base\View\Iface $view ) : int
	{
		$page = (int) $view->param( 'wat_page', 1 );
		return ( $page < 1 ? 1 : $page );
	}


	/**
	 * Returns the sanitized page size from the parameters for the product list.
	 *
	 * @param \Aimeos\Base\View\Iface $view View instance with helper for retrieving the required parameters
	 * @return integer Page size
	 */
	protected function getProductListSize( \Aimeos\Base\View\Iface $view ) : int
	{
		/** client/html/account/watch/size
		 * The number of products shown in a list page for watch products
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
		$defaultSize = $this->context()->config()->get( 'client/html/account/watch/size', 48 );

		$size = (int) $view->param( 'watch-size', $defaultSize );
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

		/** client/html/account/watch/domains
		 * A list of domain names whose items should be available in the account watch view template
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
		$domains = $context->config()->get( 'client/html/account/watch/domains', ['text', 'price', 'media'] );
		$domains['product'] = ['watch'];

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$listItems = $cntl->uses( $domains )->get()->getListItems( 'product', 'watch' );
		$total = count( $listItems );

		$size = $this->getProductListSize( $view );
		$current = $this->getProductListPage( $view );
		$last = ( $total != 0 ? ceil( $total / $size ) : 1 );

		$view->watchItems = $listItems;
		$view->watchPageFirst = 1;
		$view->watchPagePrev = ( $current > 1 ? $current - 1 : 1 );
		$view->watchPageNext = ( $current < $last ? $current + 1 : $last );
		$view->watchPageLast = $last;
		$view->watchPageCurr = $current;

		return parent::data( $view, $tags, $expire );
	}
}
