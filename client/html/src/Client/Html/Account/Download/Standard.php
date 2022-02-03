<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
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

			$manager = \Aimeos\MShop::create( $context, 'order/base/product/attribute' );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Order product attribute item with file reference
	 */
	protected function addDownload( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item )
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
			$manager = \Aimeos\MShop::create( $context, 'order/base' );

			$search = $manager->filter();
			$expr = array(
				$search->compare( '==', 'order.base.customerid', $customerId ),
				$search->compare( '==', 'order.base.product.attribute.id', $id ),
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
}
