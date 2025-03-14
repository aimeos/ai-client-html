<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Provides common methods for html client decorators.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Base
	implements \Aimeos\Client\Html\Common\Decorator\Iface
{
	private \Aimeos\Client\Html\Iface $client;


	/**
	 * Initializes a new client decorator object.
	 *
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 */
	public function __construct( \Aimeos\Client\Html\Iface $client, \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		$this->client = $client;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Client\Html\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		return @call_user_func_array( array( $this->client, $name ), $param );
	}


	/**
	 * Adds the data to the view object required by the templates
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface The view object with the data required by the templates
	 * @since 2018.01
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		return $this->client->data( $view, $tags, $expire );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, ?string $name = null ) : \Aimeos\Client\Html\Iface
	{
		return $this->client->getSubClient( $type, $name );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		return $this->client->header( $uid );
	}


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return $this->client->body( $uid );
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 */
	public function view() : \Aimeos\Base\View\Iface
	{
		return $this->client->view();
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @return \Aimeos\Client\Html\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\Base\View\Iface $view ) : \Aimeos\Client\Html\Iface
	{
		$this->client->setView( $view );
		return $this;
	}


	/**
	 * Modifies the cached content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified content
	 */
	public function modify( string $content, string $uid ) : string
	{
		return $this->client->modify( $content, $uid );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		return $this->client->init();
	}


	/**
	 * Injects the reference of the outmost client object or decorator
	 *
	 * @param \Aimeos\Client\Html\Iface $object Reference to the outmost client or decorator
	 * @return \Aimeos\Client\Html\Iface Client object for chaining method calls
	 */
	public function setObject( \Aimeos\Client\Html\Iface $object ) : \Aimeos\Client\Html\Iface
	{
		parent::setObject( $object );

		$this->client->setObject( $object );

		return $this;
	}


	/**
	 * Returns the inner client object
	 *
	 * @return \Aimeos\Client\Html\Iface HTML client
	 */
	protected function client()
	{
		return $this->client;
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return [];
	}
}
