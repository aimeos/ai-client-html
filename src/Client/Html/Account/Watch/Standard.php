<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	/** client/html/account/watch/name
	 * Class name of the used account watch client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Account\Watch\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Account\Watch\Mywatch
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/account/watch/name = Mywatch
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyWatch"!
	 *
	 * @param string Last part of the class name
	 * @since 2015.10
	 */


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
		$domains = $context->config()->get( 'client/html/account/watch/domains', ['catalog', 'text', 'price', 'media'] );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$customer = $cntl->uses( ['product' => ['watch']] + $domains )->get();

		$products = $customer->getRefItems( 'product', null, 'watch' );
		$products = \Aimeos\MShop::create( $context, 'rule' )->apply( $products, 'catalog' );

		$listItems = $customer->getListItems( 'product', 'watch' );
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

	/** client/html/account/watch/template-body
	 * Relative path to the HTML body template of the account watch client.
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
	 * @since 2015.10
	 * @see client/html/account/watch/template-header
	 */

	/** client/html/account/watch/template-header
	 * Relative path to the HTML header template of the account watch client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the HTML code that is inserted into the HTML page header
	 * of the rendered page in the frontend. The configuration string is the
	 * path to the template file relative to the templates directory (usually
	 * in templates/client/html).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but suffixed by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, it
	 * should be suffixed by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page head
	 * @since 2015.10
	 * @see client/html/account/watch/template-body
	 */

	/** client/html/account/watch/decorators/excludes
	 * Excludes decorators added by the "common" option from the account watch html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "client/html/common/decorators/default" before they are wrapped
	 * around the html client.
	 *
	 *  client/html/account/watch/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/watch/decorators/global
	 * @see client/html/account/watch/decorators/local
	 */

	/** client/html/account/watch/decorators/global
	 * Adds a list of globally available decorators only to the account watch html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/account/watch/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/watch/decorators/excludes
	 * @see client/html/account/watch/decorators/local
	 */

	/** client/html/account/watch/decorators/local
	 * Adds a list of local decorators only to the account watch html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Account\Decorator\*") around the html client.
	 *
	 *  client/html/account/watch/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Account\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/watch/decorators/excludes
	 * @see client/html/account/watch/decorators/global
	 */
}
