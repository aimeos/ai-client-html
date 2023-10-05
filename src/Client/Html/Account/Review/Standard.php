<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Review;


/**
 * Default implementation of account review HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Iface
{
	/** client/html/account/review/name
	 * Class name of the used account review client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Account\Review\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Account\Review\Myreview
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/account/review/name = Myreview
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyReview"!
	 *
	 * @param string Last part of the class name
	 * @since 2016.10
	 */


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
		$products = [];
		$context = $this->context();
		$config = $context->config();

		/** client/html/account/review/size
		 * Maximum number of products shown for review
		 *
		 * After customers bought products, they can write a review for those items.
		 * The products bought last will be displayed first for review and this
		 * setting limits the number of products shown in the account page.
		 *
		 * @param int Number of products
		 * @since 2020.10
		 * @see client/html/account/review/days-after
		 */
		$size = $config->get( 'client/html/account/review/size', 10 );

		/** client/html/account/review/days-after
		 * Number of days after the product can be reviewed
		 *
		 * After customers bought products, they can write a review for those items.
		 * To avoid fake or revenge reviews, the option for reviewing the products is
		 * shown after the configured number of days to customers.
		 *
		 * @param int Number of days
		 * @since 2020.10
		 * @see client/html/account/review/size
		 */
		$days = $config->get( 'client/html/account/review/days-after', 0 );

		$orders = \Aimeos\Controller\Frontend::create( $context, 'order' )
			->compare( '>', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING )
			->compare( '<=', 'order.ctime', date( 'Y-m-d H:i:s', time() - $days * 86400 ) )
			->uses( ['order', 'order/product', 'product' => [], 'text' => ['name'], 'media' => ['default']] )
			->sort( '-order.ctime' )
			->slice( 0, $size )
			->search();

		$orderProducts = $orders->getProducts()->flat()->col( null, 'order.product.productid' );

		$exclude = \Aimeos\Controller\Frontend::create( $context, 'review' )
			->for( 'product', $orderProducts->keys()->toArray() )
			->slice( 0, $orderProducts->count() )
			->list()->getRefId();

		$view->reviewProductItems = $orderProducts->except( $exclude )->take( $size );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();

		if( ( $reviews = $view->param( 'review', [] ) ) !== [] )
		{
			$ratings = 0;
			$context = $this->context();

			$cntl = \Aimeos\Controller\Frontend::create( $context, 'review' );
			$addr = \Aimeos\Controller\Frontend::create( $context, 'customer' )->get()->getPaymentAddress();

			foreach( $reviews as $values )
			{
				$item = $cntl->create( $values )->setDomain( 'product' )->setName( $addr->getFirstName() );

				if( $item->getRating() )
				{
					$cntl->save( $item ); // only if value is greater than 0
					$ratings++;
				}
			}

			if( $ratings ) {
				$view->infos = array_merge( $view->get( 'infos', [] ), [$view->translate( 'client', 'Thank you for your review!' )] );
			}
		}

		parent::init();
	}


	/** client/html/account/review/template-body
	 * Relative path to the HTML body template of the account review client.
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
	 * @since 2020.10
	 * @see client/html/account/review/template-header
	 */

	/** client/html/account/review/template-header
	 * Relative path to the HTML header template of the account review client.
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
	 * @since 2020.10
	 * @see client/html/account/review/template-body
	 */

	/** client/html/account/review/decorators/excludes
	 * Excludes decorators added by the "common" option from the account review html client
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
	 *  client/html/account/review/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @see client/html/common/decorators/default
	 * @see client/html/account/review/decorators/global
	 * @see client/html/account/review/decorators/local
	 */

	/** client/html/account/review/decorators/global
	 * Adds a list of globally available decorators only to the account review html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/account/review/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @see client/html/common/decorators/default
	 * @see client/html/account/review/decorators/excludes
	 * @see client/html/account/review/decorators/local
	 */

	/** client/html/account/review/decorators/local
	 * Adds a list of local decorators only to the account review html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Account\Decorator\*") around the html client.
	 *
	 *  client/html/account/review/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Account\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @see client/html/common/decorators/default
	 * @see client/html/account/review/decorators/excludes
	 * @see client/html/account/review/decorators/global
	 */
}
