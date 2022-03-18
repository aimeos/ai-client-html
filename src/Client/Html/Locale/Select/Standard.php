<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Locale\Select;


/**
 * Default implementation of locale select HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/locale/select/subparts
	 * List of HTML sub-clients rendered within the locale select section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.09
	 */
	private $subPartPath = 'client/html/locale/select/subparts';

	/** client/html/locale/select/language/name
	 * Name of the language part used by the locale selector client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Html\Locale\Select\Language\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.09
	 */

	/** client/html/locale/select/currency/name
	 * Name of the currency part used by the locale selector client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Html\Locale\Select\Currency\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.09
	 */
	private $subPartNames = array( 'language', 'currency' );

	private $tags = [];
	private $expire;
	private $view;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->body( $uid );
		}

		/** client/html/locale/select/template-body
		 * Relative path to the HTML body template of the locale select client.
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
		 * @since 2014.09
		 * @see client/html/locale/select/template-header
		 */
		$template = $this->context()->config()->get( 'client/html/locale/select/template-body', 'locale/select/body' );

		return $view->set( 'body', $html )->render( $template );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );

		/** client/html/locale/select/template-header
		 * Relative path to the HTML header template of the locale select client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the templates directory (usually
		 * in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but suffixed by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, it
		 * should be suffixed by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.09
		 * @see client/html/locale/select/template-body
		 */
		$template = $this->context()->config()->get( 'client/html/locale/select/template-header', 'locale/select/header' );

		return $view->render( $template );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/locale/select/decorators/excludes
		 * Excludes decorators added by the "common" option from the locale select html client
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
		 *  client/html/locale/select/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @see client/html/common/decorators/default
		 * @see client/html/locale/select/decorators/global
		 * @see client/html/locale/select/decorators/local
		 */

		/** client/html/locale/select/decorators/global
		 * Adds a list of globally available decorators only to the locale select html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/locale/select/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @see client/html/common/decorators/default
		 * @see client/html/locale/select/decorators/excludes
		 * @see client/html/locale/select/decorators/local
		 */

		/** client/html/locale/select/decorators/local
		 * Adds a list of local decorators only to the locale select html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Locale\Decorator\*") around the html client.
		 *
		 *  client/html/locale/select/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Locale\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @see client/html/common/decorators/default
		 * @see client/html/locale/select/decorators/excludes
		 * @see client/html/locale/select/decorators/global
		 */
		return $this->createSubClient( 'locale/select/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return $this->context()->config()->get( $this->subPartPath, $this->subPartNames );
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
		$map = [];
		$context = $this->context();
		$config = $context->config();
		$locale = $context->locale();

		/** client/html/locale/select/language/param-name
		 * Name of the parameter that contains the language ID value
		 *
		 * Frameworks and applications normally use its own predefined parameter
		 * that contains the current language ID if they are multi-language
		 * capable. To adapt the Aimeos parameter name to the already used name,
		 * you are able to configure it by using this setting.
		 *
		 * @param string Parameter name for language ID
		 * @since 2015.06
		 * @see client/html/locale/select/currency/param-name
		 */
		$langname = $config->get( 'client/html/locale/select/language/param-name', 'locale' );

		/** client/html/locale/select/currency/param-name
		 * Name of the parameter that contains the currency ID value
		 *
		 * Frameworks and applications normally use its own predefined parameter
		 * that contains the current currency ID if they already support multiple
		 * currencies. To adapt the Aimeos parameter name to the already used name,
		 * you are able to configure it by using this setting.
		 *
		 * @param string Parameter name for currency ID
		 * @since 2015.06
		 * @see client/html/locale/select/language/param-name
		 */
		$curname = $config->get( 'client/html/locale/select/currency/param-name', 'currency' );


		$items = \Aimeos\Controller\Frontend::create( $context, 'locale' )
			->sort( 'position' )->slice( 0, 10000 )->search();

		foreach( $items as $item )
		{
			$curId = $item->getCurrencyId();
			$langId = $item->getLanguageId();
			$map[$langId][$curId] = [$langname => $langId, $curname => $curId];
		}

		$view->selectMap = map( $map );
		$view->selectParams = $view->param();
		$view->selectLanguageId = $locale->getLanguageId();
		$view->selectCurrencyId = $locale->getCurrencyId();

		return parent::data( $view, $tags, $expire );
	}
}