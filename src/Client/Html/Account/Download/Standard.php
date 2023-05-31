<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Download;


/**
 * Default implementation of account download HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/account/download/name
	 * Class name of the used account download client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Account\Download\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Account\Download\Mydownload
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/account/download/name = Mydownload
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyDownload"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return '';
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
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$context = $this->context();

		try
		{
			$view = $this->view();
			$id = $view->param( 'dl_id' );

			/** client/html/account/download/error/url/target
			 * Destination of the URL to redirect the customer if the file download isn't allowed
			 *
			 * The destination can be a page ID like in a content management system or the
			 * module of a software development framework. This "target" must contain or know
			 * the controller that should be called by the generated URL.
			 *
			 * @param string Destination of the URL
			 * @since 2019.04
			 */
			$target = $context->config()->get( 'client/html/account/download/error/url/target' );

			if( $this->checkAccess( $id ) === false ) {
				return $view->response()->withStatus( 401 )->withHeader( 'Location', $view->url( $target ) );
			}

			$manager = \Aimeos\MShop::create( $context, 'order/product/attribute' );
			$item = $manager->get( $id );

			if( $this->checkDownload( $id ) === false ) {
				return $view->response()->withStatus( 403 )->withHeader( 'Location', $view->url( $target ) );
			} else {
				$this->addDownload( $item );
			}

			parent::init();
		}
		catch( \Exception $e )
		{
			$this->logException( $e );
		}
	}


	/**
	 * Adds the necessary headers and the download content to the reponse object
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item Order product attribute item with file reference
	 */
	protected function addDownload( \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item )
	{
		$fs = $this->context()->fs( 'fs-secure' );
		$response = $this->view()->response();
		$value = (string) $item->getValue();

		if( $fs->has( $value ) )
		{
			$name = $item->getName();

			if( pathinfo( $name, PATHINFO_EXTENSION ) == null
					&& ( $ext = pathinfo( $value, PATHINFO_EXTENSION ) ) != null
			) {
				$name .= '.' . $ext;
			}

			$response->withHeader( 'Content-Description', 'File Transfer' );
			$response->withHeader( 'Content-Type', 'application/octet-stream' );
			$response->withHeader( 'Content-Disposition', 'attachment; filename="' . $name . '"' );
			$response->withHeader( 'Content-Length', (string) $fs->size( $value ) );
			$response->withHeader( 'Cache-Control', 'must-revalidate' );
			$response->withHeader( 'Pragma', 'private' );
			$response->withHeader( 'Expires', '0' );

			$response->withBody( $response->createStream( $fs->reads( $value ) ) );
		}
		elseif( filter_var( $value, FILTER_VALIDATE_URL ) !== false )
		{
			$response->withHeader( 'Location', $value );
			$response->withStatus( 303 );
		}
		else
		{
			$response->withStatus( 404 );
		}
	}


	/**
	 * Checks if the customer is allowed to download the file
	 *
	 * @param string|null $id Unique order base product attribute ID referencing the download file
	 * @return bool True if download is allowed, false if not
	 */
	protected function checkAccess( string $id = null ) : bool
	{
		$context = $this->context();

		if( ( $customerId = $context->user() ) !== null && $id !== null )
		{
			$manager = \Aimeos\MShop::create( $context, 'order' );

			$search = $manager->filter();
			$expr = array(
				$search->compare( '==', 'order.customerid', $customerId ),
				$search->compare( '==', 'order.product.attribute.id', $id ),
			);
			$search->setConditions( $search->and( $expr ) );
			$search->slice( 0, 1 );

			if( !$manager->search( $search )->isEmpty() ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Updates the download counter for the downloaded file
	 *
	 * @param string|null $id Unique order base product attribute ID referencing the download file
	 * @return bool True if download is allowed, false if not
	 */
	protected function checkDownload( string $id = null ) : bool
	{
		$context = $this->context();

		/** client/html/account/download/maxcount
		 * Maximum number of file downloads allowed for an ordered product
		 *
		 * This configuration setting enables you to limit the number of downloads
		 * of a product download file. The count is the maximum number for each
		 * bought product and customer, i.e. setting the count to "3" allows
		 * a customer to download the bought product file up to three times.
		 *
		 * The default value of null enforces no limit.
		 *
		 * @param integer Maximum number of downloads
		 * @since 2016.02
		 */
		$maxcnt = $context->config()->get( 'client/html/account/download/maxcount' );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$item = $cntl->uses( ['order' => ['download']] )->get();

		if( ( $listItem = $item->getListItem( 'order', 'download', $id ) ) === null ) {
			$listItem = $cntl->createListItem()->setType( 'download' )->setRefId( $id );
		}

		$config = $listItem->getConfig();
		$count = (int) $listItem->getConfigValue( 'count', 0 );

		if( $maxcnt === null || $count < $maxcnt )
		{
			$config['count'] = $count++;
			$cntl->addListItem( 'order', $listItem->setConfig( $config ) )->store();

			return true;
		}

		return false;
	}


	/** client/html/account/download/decorators/excludes
	 * Excludes decorators added by the "common" option from the account download html client
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
	 *  client/html/account/download/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/download/decorators/global
	 * @see client/html/account/download/decorators/local
	 */

	/** client/html/account/download/decorators/global
	 * Adds a list of globally available decorators only to the account download html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/account/download/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/download/decorators/excludes
	 * @see client/html/account/download/decorators/local
	 */

	/** client/html/account/download/decorators/local
	 * Adds a list of local decorators only to the account download html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Account\Decorator\*") around the html client.
	 *
	 *  client/html/account/download/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Account\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/account/download/decorators/excludes
	 * @see client/html/account/download/decorators/global
	 */
}
