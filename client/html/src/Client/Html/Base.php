<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html;

use \Aimeos\MW\Logger\Base as Log;


/**
 * Common abstract class for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	implements \Aimeos\Client\Html\Iface, \Aimeos\MW\Macro\Iface
{
	use \Aimeos\MW\Macro\Traits;


	private $view;
	private $cache;
	private $object;
	private $context;
	private $subclients;


	/**
	 * Initializes the class instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Adds the data to the view object required by the templates
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface The view object with the data required by the templates
	 * @since 2018.01
	 */
	public function data( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		foreach( $this->getSubClients() as $name => $subclient ) {
			$view = $subclient->data( $view, $tags, $expire );
		}

		return $view;
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$html = '';

		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $this->view )->header( $uid );
		}

		return $html;
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function init()
	{
		$view = $this->view();

		foreach( $this->getSubClients() as $subclient ) {
			$subclient->setView( $view )->init();
		}
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( string $content, string $uid ) : string
	{
		$view = $this->view();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );
			$content = $subclient->modifyBody( $content, $uid );
		}

		return $content;
	}


	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified header content
	 */
	public function modifyHeader( string $content, string $uid ) : string
	{
		$view = $this->view();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );
			$content = $subclient->modifyHeader( $content, $uid );
		}

		return $content;
	}


	/**
	 * Returns the PSR-7 response object for the request
	 *
	 * @return \Psr\Http\Message\ResponseInterface Response object
	 */
	public function response() : \Psr\Http\Message\ResponseInterface
	{
		return $this->view()->response();
	}


	/**
	 * Injects the reference of the outmost client object or decorator
	 *
	 * @param \Aimeos\Client\Html\Iface $object Reference to the outmost client or decorator
	 * @return \Aimeos\Client\Html\Iface Client object for chaining method calls
	 */
	public function setObject( \Aimeos\Client\Html\Iface $object ) : \Aimeos\Client\Html\Iface
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @return \Aimeos\Client\Html\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view ) : \Aimeos\Client\Html\Iface
	{
		$this->view = $view;
		return $this;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\Client\Html\Iface Outmost decorator object
	 */
	protected function getObject() : \Aimeos\Client\Html\Iface
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 */
	protected function view() : \Aimeos\MW\View\Iface
	{
		if( !isset( $this->view ) ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'No view available' ) );
		}

		return $this->view;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Client\Html\Catalog\Decorator\"
	 * @return \Aimeos\Client\Html\Iface Client object
	 */
	protected function addDecorators( \Aimeos\Client\Html\Iface $client, array $decorators, string $classprefix ) : \Aimeos\Client\Html\Iface
	{
		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client = new $classname( $client, $this->context );

			\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\Client\\Html\\Common\\Decorator\\Iface', $client );
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param string $path Client string in lower case, e.g. "catalog/detail/basic"
	 * @return \Aimeos\Client\Html\Iface Client object
	 */
	protected function addClientDecorators( \Aimeos\Client\Html\Iface $client, string $path ) : \Aimeos\Client\Html\Iface
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( '/', '\\', ucwords( $path, '/' ) );
		$config = $this->context->getConfig();

		$classprefix = '\\Aimeos\\Client\\Html\\Common\\Decorator\\';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/global', [] );
		$client = $this->addDecorators( $client, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Client\\Html\\' . $localClass . '\\Decorator\\';
		$decorators = $config->get( 'client/html/' . $path . '/decorators/local', [] );
		$client = $this->addDecorators( $client, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Adds the cache tags to the given list and sets a new expiration date if necessary based on the given item.
	 *
	 * @param array|\Aimeos\MShop\Common\Item\Iface $items Item or list of items, maybe with associated list items
	 * @param string|null &$expire Expiration date that will be overwritten if an earlier date is found
	 * @param array &$tags List of tags the new tags will be added to
	 * @param array $custom List of custom tags which are added too
	 */
	protected function addMetaItems( $items, string &$expire = null, array &$tags, array $custom = [] )
	{
		/** client/html/common/cache/tag-all
		 * Adds tags for all items used in a cache entry
		 *
		 * Each cache entry storing rendered parts for the HTML header or body
		 * can be tagged with information which items like texts, media, etc.
		 * are used in the HTML. This allows removing only those cache entries
		 * whose content has really changed and only that entries have to be
		 * rebuild the next time.
		 *
		 * The standard behavior stores only tags for each used domain, e.g. if
		 * a text is used, only the tag "text" is added. If you change a text
		 * in the administration interface, all cache entries with the tag
		 * "text" will be removed from the cache. This effectively wipes out
		 * almost all cached entries, which have to be rebuild with the next
		 * request.
		 *
		 * Important: As a list or detail view can use several hundred items,
		 * this configuration option will also add this number of tags to the
		 * cache entry. When using a cache adapter that can't insert all tags
		 * at once, this slows down the initial cache insert (and therefore the
		 * page speed) drastically! It's only recommended to enable this option
		 * if you use the DB, Mysql or Redis adapter that can insert all tags
		 * at once.
		 *
		 * @param boolean True to add tags for all items, false to use only a domain tag
		 * @since 2014.07
		 * @category Developer
		 * @category User
		 * @see client/html/common/cache/force
		 * @see madmin/cache/manager/name
		 * @see madmin/cache/name
		 */
		$tagAll = $this->context->getConfig()->get( 'client/html/common/cache/tag-all', false );

		if( !is_array( $items ) && !is_map( $items ) ) {
			$items = map( [$items] );
		}

		$expires = $idMap = [];

		foreach( $items as $item )
		{
			if( $item instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface )
			{
				$this->addMetaItemRef( $item, $expires, $tags, $tagAll );
				$idMap[$item->getResourceType()][] = $item->getId();
			}

			$this->addMetaItemSingle( $item, $expires, $tags, $tagAll );
		}

		if( $expire !== null ) {
			$expires[] = $expire;
		}

		if( !empty( $expires ) ) {
			$expire = min( $expires );
		}

		$tags = array_unique( array_merge( $tags, $custom ) );
	}


	/**
	 * Adds expire date and tags for a single item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item, maybe with associated list items
	 * @param array &$expires Will contain the list of expiration dates
	 * @param array &$tags List of tags the new tags will be added to
	 * @param bool $tagAll True of tags for all items should be added, false if only for the main item
	 */
	private function addMetaItemSingle( \Aimeos\MShop\Common\Item\Iface $item, array &$expires, array &$tags, bool $tagAll )
	{
		$domain = str_replace( '/', '_', $item->getResourceType() ); // maximum compatiblity

		if( $tagAll === true ) {
			$tags[] = $domain . '-' . $item->getId();
		} else {
			$tags[] = $domain;
		}

		if( $item instanceof \Aimeos\MShop\Common\Item\Time\Iface && ( $date = $item->getDateEnd() ) !== null ) {
			$expires[] = $date;
		}

		if( $item instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface ) {
			$this->addMetaItemRef( $item, $expires, $tags, $tagAll );
		}
	}


	/**
	 * Adds expire date and tags for referenced items
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with associated list items
	 * @param array &$expires Will contain the list of expiration dates
	 * @param array &$tags List of tags the new tags will be added to
	 * @param bool $tagAll True of tags for all items should be added, false if only for the main item
	 */
	private function addMetaItemRef( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array &$expires, array &$tags, bool $tagAll )
	{
		foreach( $item->getListItems() as $listitem )
		{
			if( ( $refItem = $listitem->getRefItem() ) === null ) {
				continue;
			}

			if( $tagAll === true ) {
				$tags[] = str_replace( '/', '_', $listitem->getDomain() ) . '-' . $listitem->getRefId();
			}

			if( ( $date = $listitem->getDateEnd() ) !== null ) {
				$expires[] = $date;
			}

			$this->addMetaItemSingle( $refItem, $expires, $tags, $tagAll );
		}
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $path Name of the sub-part in lower case (can contain a path like catalog/filter/tree)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\Client\Html\Iface Sub-part object
	 */
	protected function createSubClient( string $path, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		$path = strtolower( $path );

		if( $name === null ) {
			$name = $this->context->getConfig()->get( 'client/html/' . $path . '/name', 'Standard' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid characters in client name "%1$s"', $name ) );
		}

		$subnames = str_replace( '/', '\\', ucwords( $path, '/' ) );
		$classname = '\\Aimeos\\Client\\Html\\' . $subnames . '\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $this->context );
		$object = \Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\Client\\Html\\Iface', $object );
		$object = $this->addClientDecorators( $object, $path );

		return $object->setObject( $object );
	}


	/**
	 * Returns the minimal expiration date.
	 *
	 * @param string|null $first First expiration date or null
	 * @param string|null $second Second expiration date or null
	 * @return string|null Expiration date
	 */
	protected function expires( string $first = null, string $second = null ) : ?string
	{
		return ( $first !== null ? ( $second !== null ? min( $first, $second ) : $first ) : $second );
	}


	/**
	 * Returns the parameters used by the html client.
	 *
	 * @param array $params Associative list of all parameters
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @return array Associative list of parameters used by the html client
	 */
	protected function getClientParams( array $params, array $prefixes = ['f_', 'l_', 'd_'] ) : array
	{
		return map( $params )->filter( function( $val, $key ) use ( $prefixes ) {
			return \Aimeos\MW\Str::starts( $key, $prefixes );
		} )->toArray();
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Generates an unique hash from based on the input suitable to be used as part of the cache key
	 *
	 * @param array $prefixes List of prefixes the parameters must start with
	 * @param string $key Unique identifier if the content is placed more than once on the same page
	 * @param array $config Multi-dimensional array of configuration options used by the client and sub-clients
	 * @return string Unique hash
	 */
	protected function getParamHash( array $prefixes = ['f_', 'l_', 'd_'], string $key = '', array $config = [] ) : string
	{
		$locale = $this->getContext()->getLocale();
		$pstr = map( $this->getClientParams( $this->view()->param(), $prefixes ) )->ksort()->toJson();

		if( ( $cstr = json_encode( $config ) ) === false ) {
			throw new \Aimeos\Client\Html\Exception( 'Unable to encode parameters or configuration options' );
		}

		return md5( $key . $pstr . $cstr . $locale->getLanguageId() . $locale->getCurrencyId() . $locale->getSiteId() );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	abstract protected function getSubClientNames() : array;


	/**
	 * Returns the configured sub-clients or the ones named in the default parameter if none are configured.
	 *
	 * @return array List of sub-clients implementing \Aimeos\Client\Html\Iface	ordered in the same way as the names
	 */
	protected function getSubClients() : array
	{
		if( !isset( $this->subclients ) )
		{
			$this->subclients = [];

			foreach( $this->getSubClientNames() as $name ) {
				$this->subclients[$name] = $this->getSubClient( $name );
			}
		}

		return $this->subclients;
	}


	/**
	 * Returns the template for the given configuration key
	 *
	 * If the "l_type" parameter is present, a specific template for this given
	 * type is used if available.
	 *
	 * @param string $confkey Key to the configuration setting for the template
	 * @param string $default Default template if none is configured or not found
	 * @return string Relative template path
	 */
	protected function getTemplatePath( string $confkey, string $default ) : string
	{
		if( ( $type = $this->view->param( 'l_type' ) ) !== null && ctype_alnum( $type ) !== false ) {
			return $this->view->config( $confkey . '-' . $type, $this->view->config( $confkey, $default ) );
		}

		return $this->view->config( $confkey, $default );
	}


	/**
	 * Returns the cache entry for the given unique ID and type.
	 *
	 * @param string $type Type of the cache entry, i.e. "body" or "header"
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param string[] $prefixes List of prefixes of all parameters that are relevant for generating the output
	 * @param string $confkey Configuration key prefix that matches all relevant settings for the component
	 * @return string|null Cached entry or null if not available
	 */
	protected function getCached( string $type, string $uid, array $prefixes, string $confkey ) : ?string
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		/** client/html/common/cache/force
		 * Enforces content caching regardless of user logins
		 *
		 * Caching the component output is normally disabled as soon as the
		 * user has logged in. This enables displaying user or user group
		 * specific content without mixing standard and user specific output.
		 *
		 * If you don't have any user or user group specific content
		 * (products, categories, attributes, media, prices, texts, etc.),
		 * you can enforce content caching nevertheless to keep response
		 * times as low as possible.
		 *
		 * @param boolean True to cache output regardless of login, false for no caching
		 * @since 2015.08
		 * @category Developer
		 * @category User
		 * @see client/html/common/cache/tag-all
		 */
		$force = $config->get( 'client/html/common/cache/force', false );
		$enable = $config->get( $confkey . '/cache', true );

		if( $enable == false || $force == false && $context->getUserId() !== null ) {
			return null;
		}

		$cfg = array_merge( $config->get( 'client/html', [] ), $this->getSubClientNames() );

		$keys = array(
			'body' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':body', $cfg ),
			'header' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':header', $cfg ),
		);

		if( !isset( $this->cache[$keys[$type]] ) ) {
			$this->cache = $context->getCache()->getMultiple( $keys );
		}

		return ( isset( $this->cache[$keys[$type]] ) ? $this->cache[$keys[$type]] : null );
	}


	/**
	 * Returns the cache entry for the given type and unique ID.
	 *
	 * @param string $type Type of the cache entry, i.e. "body" or "header"
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param string[] $prefixes List of prefixes of all parameters that are relevant for generating the output
	 * @param string $confkey Configuration key prefix that matches all relevant settings for the component
	 * @param string $value Value string that should be stored for the given key
	 * @param array $tags List of tag strings that should be assoicated to the given value in the cache
	 * @param string|null $expire Date/time string in "YYYY-MM-DD HH:mm:ss"	format when the cache entry expires
	 */
	protected function setCached( string $type, string $uid, array $prefixes, string $confkey, string $value, array $tags, string $expire = null )
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		$force = $config->get( 'client/html/common/cache/force', false );
		$enable = $config->get( $confkey . '/cache', true );

		if( $enable == false || $force == false && $context->getUserId() !== null ) {
			return;
		}

		try
		{
			$cfg = array_merge( $config->get( 'client/html', [] ), $this->getSubClientNames() );
			$key = $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':' . $type, $cfg );

			$context->getCache()->set( $key, $value, $expire, array_unique( $tags ) );
		}
		catch( \Exception $e )
		{
			$msg = sprintf( 'Unable to set cache entry: %1$s', $e->getMessage() );
			$context->getLogger()->log( $msg, Log::NOTICE, 'client/html' );
		}
	}


	/**
	 * Writes the exception details to the log
	 *
	 * @param \Exception $e Exception object
	 */
	protected function logException( \Exception $e )
	{
		$msg = $e->getMessage() . PHP_EOL . $e->getTraceAsString();
		$this->context->getLogger()->log( $msg, Log::WARN, 'client/html' );
	}


	/**
	 * Replaces the section in the content that is enclosed by the marker.
	 *
	 * @param string $content Cached content
	 * @param string $section New section content
	 * @param string $marker Name of the section marker without "<!-- " and " -->" parts
	 */
	protected function replaceSection( string $content, string $section, string $marker ) : string
	{
		$start = 0;
		$len = strlen( $section );
		$clen = strlen( $content );
		$marker = '<!-- ' . $marker . ' -->';

		while( $start < $clen && ( $start = @strpos( $content, $marker, $start ) ) !== false )
		{
			if( ( $end = strpos( $content, $marker, $start + 1 ) ) !== false ) {
				$content = substr_replace( $content, $section, $start, $end - $start + strlen( $marker ) );
			}

			$start += 2 * strlen( $marker ) + $len;
		}

		return $content;
	}
}
