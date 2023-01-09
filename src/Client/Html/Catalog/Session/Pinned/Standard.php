<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Session\Pinned;


/**
 * Default implementation of catalog session pinned section for HTML clients.
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
		$view = $this->view();
		$context = $this->context();
		$session = $context->session();

		/** client/html/catalog/session/pinned
		 * All parameters defined for the catalog session pinned subpart
		 *
		 * This returns all settings related to the catalog session pinned subpart.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/catalog/session#pinned
		 */
		$config = $context->config()->get( 'client/html/catalog/session/pinned', [] );
		$key = $this->getParamHash( [], $uid . ':catalog:session-pinned-body', $config );

		if( ( $html = $session->get( $key ) ) === null )
		{
			/** client/html/catalog/session/pinned/template-body
			 * Relative path to the HTML body template of the catalog session pinned client.
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
			 * @see client/html/catalog/session/pinned/template-header
			 */
			$tplconf = 'client/html/catalog/session/pinned/template-body';
			$default = 'catalog/session/pinned-body';

			$html = $view->render( $view->config( $tplconf, $default ) );

			$cached = $session->get( 'aimeos/catalog/session/pinned/cache', [] ) + array( $key => true );
			$session->set( 'aimeos/catalog/session/pinned/cache', $cached );
			$session->set( $key, $html );
		}

		$view->block()->set( 'catalog/session/pinned', $html );

		return $html;
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$refresh = false;
		$view = $this->view();
		$context = $this->context();
		$session = $context->session();
		$pinned = $session->get( 'aimeos/catalog/session/pinned/list', [] );

		if( $view->request()->getMethod() === 'POST' )
		{
			switch( $view->param( 'pin_action' ) )
			{
				case 'add':

					foreach( (array) $view->param( 'pin_id', [] ) as $id ) {
						$pinned[$id] = $id;
					}

					/** client/html/catalog/session/pinned/maxitems
					 * Maximum number of products displayed in the "pinned" section
					 *
					 * This option limits the number of products that are shown in the
					 * "pinned" section after the users added the product to their list
					 * of pinned products. It must be a positive integer value greater
					 * than 0.
					 *
					 * Note: The higher the value is the more data has to be transfered
					 * to the client each time the user loads a page with the list of
					 * pinned products.
					 *
					 * @param integer Number of products
					 * @since 2014.09
					 */
					$max = $context->config()->get( 'client/html/catalog/session/pinned/maxitems', 50 );

					$pinned = array_slice( $pinned, -$max, $max, true );
					$refresh = true;
					break;

				case 'delete':

					foreach( (array) $view->param( 'pin_id', [] ) as $id ) {
						unset( $pinned[$id] );
					}

					$refresh = true;
					break;
			}
		}


		if( $refresh )
		{
			$session->set( 'aimeos/catalog/session/pinned/list', $pinned );

			foreach( $session->get( 'aimeos/catalog/session/pinned/cache', [] ) as $key => $value ) {
				$session->set( $key, null );
			}
		}
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
		$items = [];
		$context = $this->context();
		$config = $context->config();
		$session = $context->session();

		$domains = $config->get( 'client/html/catalog/domains', ['catalog', 'media', 'price', 'text'] );

		/** client/html/catalog/session/pinned/domains
		 * A list of domain names whose items should be available in the pinned view template for the product
		 *
		 * The templates rendering product details usually add the images,
		 * prices and texts, etc. associated to the product
		 * item. If you want to display additional or less content, you can
		 * configure your own list of domains (attribute, media, price, product,
		 * text, etc. are domains) whose items are fetched from the storage.
		 * Please keep in mind that the more domains you add to the configuration,
		 * the more time is required for fetching the content!
		 *
		 * @param array List of domain names
		 * @since 2015.04
		 * @see client/html/catalog/domains
		 * @see client/html/catalog/lists/domains
		 * @see client/html/catalog/detail/domains
		 */
		$domains = $config->get( 'client/html/catalog/session/pinned/domains', $domains );

		if( ( $pinned = $session->get( 'aimeos/catalog/session/pinned/list', [] ) ) !== [] )
		{
			$result = \Aimeos\Controller\Frontend::create( $context, 'product' )
				->uses( $domains )->product( $pinned )->slice( 0, count( $pinned ) )->search();

			foreach( array_reverse( $pinned ) as $id )
			{
				if( isset( $result[$id] ) ) {
					$items[$id] = $result[$id];
				}
			}
		}

		$view->pinnedProductItems = $items;
		$view->pinnedParams = $this->getClientParams( $view->param() );

		return parent::data( $view, $tags, $expire );
	}
}
