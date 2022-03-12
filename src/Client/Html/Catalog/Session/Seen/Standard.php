<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Session\Seen;


/**
 * Default implementation of catalog session seen section for HTML clients.
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

		/** client/html/catalog/session/seen
		 * All parameters defined for the catalog session seen subpart
		 *
		 * This returns all settings related to the catalog session seen subpart.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/catalog#session
		 */
		$config = $context->config()->get( 'client/html/catalog/session/seen', [] );
		$key = $this->getParamHash( [], $uid . ':catalog:session-seen-body', $config );

		if( ( $html = $session->get( $key ) ) === null )
		{
			/** client/html/catalog/session/seen/template-body
			 * Relative path to the HTML body template of the catalog session seen client.
			 *
			 * The template file contains the HTML code and processing instructions
			 * to generate the result shown in the body of the frontend. The
			 * configuration string is the path to the template file relative
			 * to the templates directory (usually in client/html/templates).
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
			 * @see client/html/catalog/session/seen/template-header
			 */
			$tplconf = 'client/html/catalog/session/seen/template-body';
			$default = 'catalog/session/seen-body';

			$html = $view->render( $view->config( $tplconf, $default ) );

			$cached = $session->get( 'aimeos/catalog/session/seen/cache', [] ) + array( $key => true );
			$session->set( 'aimeos/catalog/session/seen/cache', $cached );
			$session->set( $key, $html );
		}

		$view->block()->set( 'catalog/session/seen', $html );

		return $html;
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
		$session = $this->context()->session();
		$lastSeen = $session->get( 'aimeos/catalog/session/seen/list', [] );

		$view->seenItems = array_reverse( $lastSeen );

		return parent::data( $view, $tags, $expire );
	}
}
