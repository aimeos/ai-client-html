<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Stage;


/**
 * Default implementation of catalog stage section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/stage/name
	 * Class name of the used catalog stage client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Catalog\Stage\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Catalog\Stage\Mystage
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/catalog/stage/name = Mystage
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyStage"!
	 *
	 * @param string Last part of the class name
	 * @since 2014.03
	 */


	private array $tags = [];
	private ?string $expire = null;
	private ?\Aimeos\Base\View\Iface $view = null;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$prefixes = ['f_catid'];
		$config = $this->context()->config();

		/** client/html/catalog/stage/cache
		 * Enables or disables caching only for the catalog stage component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @see client/html/catalog/detail/cache
		 * @see client/html/catalog/filter/cache
		 * @see client/html/catalog/lists/cache
		 */

		/** client/html/catalog/stage
		 * All parameters defined for the catalog stage component and its subparts
		 *
		 * This returns all settings related to the stage component.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/catalog#stage
		 */
		$confkey = 'client/html/catalog/stage';

		if( $html = $this->cached( 'body', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/stage/template-body
		 * Relative path to the HTML body template of the catalog stage client.
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
		 * @see client/html/catalog/stage/template-header
		 */
		$template = $config->get( 'client/html/catalog/stage/template-body', 'catalog/stage/body' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->cache( 'body', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$prefixes = ['f_catid'];
		$config = $this->context()->config();
		$confkey = 'client/html/catalog/stage';

		if( $html = $this->cached( 'header', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/catalog/stage/template-header
		 * Relative path to the HTML header template of the catalog stage client.
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
		 * @since 2014.03
		 * @see client/html/catalog/stage/template-body
		 */
		$template = $config->get( 'client/html/catalog/stage/template-header', 'catalog/stage/header' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $view->render( $template );

		return $this->cache( 'header', $uid, $prefixes, $confkey, $html, $this->tags, $this->expire );
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
		$config = $context->config();
		$params = map( $this->getClientParams( $view->param(), ['f_', 'l_type'] ) );

		if( $catid = $params->get( 'f_catid', $config->get( 'client/html/catalog/lists/catid-default' ) ) )
		{
			$controller = \Aimeos\Controller\Frontend::create( $context, 'catalog' );

			/** client/html/catalog/domains
			 * A list of domain names whose items should be available in the catalog view templates
			 *
			 * @see client/html/catalog/stage/domains
			 */
			$domains = ['attribute', 'media', 'media/property', 'text'];
			$domains = $config->get( 'client/html/catalog/domains', $domains );

			/** client/html/catalog/stage/domains
			 * A list of domain names whose items should be available in the catalog stage view template
			 *
			 * The templates rendering the catalog stage section use the texts and
			 * maybe images and attributes associated to the categories. You can
			 * configure your own list of domains (attribute, media, price, product,
			 * text, etc. are domains) whose items are fetched from the storage.
			 * Please keep in mind that the more domains you add to the configuration,
			 * the more time is required for fetching the content!
			 *
			 * This configuration option overwrites the "client/html/catalog/domains"
			 * option that allows to configure the domain names of the items fetched
			 * for all catalog related data.
			 *
			 * @param array List of domain names
			 * @since 2014.03
			 * @see client/html/catalog/domains
			 * @see client/html/catalog/detail/domains
			 * @see client/html/catalog/lists/domains
			 */
			$domains = $config->get( 'client/html/catalog/stage/domains', $domains );

			$stageCatPath = $controller->uses( $domains )->getPath( $catid );

			$mediaItems = $stageCatPath->getRefItems( 'media', 'stage', 'default' )->find( function( \Aimeos\Map $entry ) {
				return !$entry->isEmpty();
			}, true );

			$this->addMetaItems( $stageCatPath, $expire, $tags );

			$view->stageCurrentCatItem = $stageCatPath->last();
			$view->stageMediaItems = $mediaItems;
			$view->stageCatPath = $stageCatPath;
			$view->stageCatId = $catid;
		}

		$view->stageParams = $params->all();

		return parent::data( $view, $tags, $expire );
	}


	/** client/html/catalog/stage/decorators/excludes
	 * Excludes decorators added by the "common" option from the catalog stage html client
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
	 *  client/html/catalog/stage/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
	 * "client/html/common/decorators/default" to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/global
	 * @see client/html/catalog/stage/decorators/local
	 */

	/** client/html/catalog/stage/decorators/global
	 * Adds a list of globally available decorators only to the catalog stage html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/stage/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/excludes
	 * @see client/html/catalog/stage/decorators/local
	 */

	/** client/html/catalog/stage/decorators/local
	 * Adds a list of local decorators only to the catalog stage html client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
	 *
	 *  client/html/catalog/stage/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
	 *
	 * @param array List of decorator names
	 * @see client/html/common/decorators/default
	 * @see client/html/catalog/stage/decorators/excludes
	 * @see client/html/catalog/stage/decorators/global
	 */
}
