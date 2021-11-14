<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html;


/**
 * Common interface for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
interface Iface
{
	/**
	 * Adds the data to the view object required by the templates
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface The view object with the data required by the templates
	 * @since 2018.01
	 */
	public function data( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface;

	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string;

	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string;

	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init();

	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface;

	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( string $content, string $uid ) : string;

	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified header content
	 */
	public function modifyHeader( string $content, string $uid ) : string;

	/**
	 * Returns the PSR-7 response object for the request
	 *
	 * @return \Psr\Http\Message\ResponseInterface Response object
	 */
	public function response() : \Psr\Http\Message\ResponseInterface;

	/**
	 * Injects the reference of the outmost client object or decorator
	 *
	 * @param \Aimeos\Client\Html\Iface $object Reference to the outmost client or decorator
	 * @return \Aimeos\Client\Html\Iface Client object for chaining method calls
	 */
	public function setObject( \Aimeos\Client\Html\Iface $object ) : \Aimeos\Client\Html\Iface;

	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @return \Aimeos\Client\Html\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view ) : \Aimeos\Client\Html\Iface;
}
