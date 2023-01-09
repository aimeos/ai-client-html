<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Locale\Select\Currency;


/**
 * Default implementation of acount select currency HTML client.
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

		/** client/html/locale/select/currency/template-body
		 * Relative path to the HTML body template of the locale select currency client.
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
		 * @since 2014.09
		 * @see client/html/locale/select/currency/template-header
		 */
		$tplconf = 'client/html/locale/select/currency/template-body';
		$default = 'locale/select/currency-body';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$context = $this->context();
		$name = $context->config()->get( 'client/html/locale/select/currency/param-name', 'currency' );

		if( $currencyId = $this->view()->param( $name ) ) {
			$context->session()->set( 'aimeos/locale/currencyid', $currencyId );
		}
	}
}
