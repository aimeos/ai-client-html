<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html;


/**
 * Common abstract class for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	implements \Aimeos\Client\Html\Iface
{
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
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Client\Html\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		throw new \Aimeos\Client\Html\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
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
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], &$expire = null )
	{
		foreach( $this->getSubClients() as $name => $subclient ) {
			$view = $subclient->addData( $view, $tags, $expire );
		}

		return $view;
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '' )
	{
		$html = '';

		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $this->view )->getHeader( $uid );
		}

		return $html;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\Client\Html\Iface Outmost decorator object
	 */
	protected function getObject()
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
	public function getView()
	{
		if( !isset( $this->view ) ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'No view available' ) );
		}

		return $this->view;
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid )
	{
		$view = $this->getView();

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
	public function modifyHeader( $content, $uid )
	{
		$view = $this->getView();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );
			$content = $subclient->modifyHeader( $content, $uid );
		}

		return $content;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 */
	public function process()
	{
		$view = $this->getView();

		foreach( $this->getSubClients() as $subclient )
		{
			$subclient->setView( $view );

			if( $subclient->process() === false ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Injects the reference of the outmost client object or decorator
	 *
	 * @param \Aimeos\Client\Html\Iface $object Reference to the outmost client or decorator
	 * @return \Aimeos\Client\Html\Iface Client object for chaining method calls
	 */
	public function setObject( \Aimeos\Client\Html\Iface $object )
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
	public function setView( \Aimeos\MW\View\Iface $view )
	{
		$this->view = $view;
		return $this;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Client\Html\Catalog\Decorator\"
	 * @return \Aimeos\Client\Html\Iface Client object
	 */
	protected function addDecorators( \Aimeos\Client\Html\Iface $client, array $decorators, $classprefix )
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
	protected function addClientDecorators( \Aimeos\Client\Html\Iface $client, $path )
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );
		$config = $this->context->getConfig();

		$decorators = $config->get( 'client/html/common/decorators/default', [] );
		$excludes = $config->get( 'client/html/' . $path . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Client\\Html\\Common\\Decorator\\';
		$client = $this->addDecorators( $client, $decorators, $classprefix );

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
	protected function addMetaItems( $items, &$expire, array &$tags, array $custom = [] )
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

		if( !is_array( $items ) ) {
			$items = array( $items );
		}

		$expires = $idMap = [];

		foreach( $items as $item )
		{
			if( $item instanceof \Aimeos\MShop\Common\Item\ListRef\Iface )
			{
				$this->addMetaItemRef( $item, $expires, $tags, $tagAll );
				$idMap[ $item->getResourceType() ][] = $item->getId();
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
	 * @param boolean $tagAll True of tags for all items should be added, false if only for the main item
	 */
	private function addMetaItemSingle( \Aimeos\MShop\Common\Item\Iface $item, array &$expires, array &$tags, $tagAll )
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
	}


	/**
	 * Adds expire date and tags for referenced items
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item with associated list items
	 * @param array &$expires Will contain the list of expiration dates
	 * @param array &$tags List of tags the new tags will be added to
	 * @param boolean $tagAll True of tags for all items should be added, false if only for the main item
	 */
	private function addMetaItemRef( \Aimeos\MShop\Common\Item\ListRef\Iface $item, array &$expires, array &$tags, $tagAll )
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
	protected function createSubClient( $path, $name )
	{
		$path = strtolower( $path );

		if( $name === null ) {
			$name = $this->context->getConfig()->get( 'client/html/' . $path . '/name', 'Standard' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid characters in client name "%1$s"', $name ) );
		}

		$subnames = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );
		$classname = '\\Aimeos\\Client\\Html\\' . $subnames . '\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $this->context );

		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\Client\\Html\\Iface', $object );

		return $this->addClientDecorators( $object, $path );
	}


	/**
	 * Returns the minimal expiration date.
	 *
	 * @param string|null $first First expiration date or null
	 * @param string|null $second Second expiration date or null
	 * @return string|null Expiration date
	 */
	protected function expires( $first, $second )
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
	protected function getClientParams( array $params, array $prefixes = array( 'f', 'l', 'd', 'a' ) )
	{
		$list = [];

		foreach( $params as $key => $value )
		{
			if( in_array( $key[0], $prefixes ) && $key[1] === '_' ) {
				$list[$key] = $value;
			}
		}

		return $list;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
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
	protected function getParamHash( array $prefixes = array( 'f', 'l', 'd' ), $key = '', array $config = [] )
	{
		$locale = $this->getContext()->getLocale();
		$params = $this->getClientParams( $this->getView()->param(), $prefixes );
		ksort( $params );

		if( ( $pstr = json_encode( $params ) ) === false || ( $cstr = json_encode( $config ) ) === false ) {
			throw new \Aimeos\Client\Html\Exception( 'Unable to encode parameters or configuration options' );
		}

		return md5( $key . $pstr . $cstr . $locale->getLanguageId() . $locale->getCurrencyId() );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	abstract protected function getSubClientNames();


	/**
	 * Returns the configured sub-clients or the ones named in the default parameter if none are configured.
	 *
	 * @return array List of sub-clients implementing \Aimeos\Client\Html\Iface	ordered in the same way as the names
	 */
	protected function getSubClients()
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
	protected function getTemplatePath( $confkey, $default )
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
	 * @return string Cached entry or empty string if not available
	 */
	protected function getCached( $type, $uid, array $prefixes, $confkey )
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

		$cfg = $config->get( 'client/html', [] );

		$keys = array(
			'body' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':body', $cfg ),
			'header' => $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':header', $cfg ),
		);

		if( !isset( $this->cache[ $keys[$type] ] ) ) {
			$this->cache = $context->getCache()->getMultiple( $keys );
		}

		return ( isset( $this->cache[ $keys[$type] ] ) ? $this->cache[ $keys[$type] ] : null );
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
	protected function setCached( $type, $uid, array $prefixes, $confkey, $value, array $tags, $expire )
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
			$cfg = $config->get( 'client/html', [] );
			$key = $this->getParamHash( $prefixes, $uid . ':' . $confkey . ':' . $type, $cfg );

			$context->getCache()->set( $key, $value, $expire, array_unique( $tags ) );
		}
		catch( \Exception $e )
		{
			$msg = sprintf( 'Unable to set cache entry: %1$s', $e->getMessage() );
			$context->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::NOTICE );
		}
	}


	/**
	 * Writes the exception details to the log
	 *
	 * @param \Exception $e Exception object
	 */
	protected function logException( \Exception $e )
	{
		$logger = $this->context->getLogger();

		$logger->log( $e->getMessage(), \Aimeos\MW\Logger\Base::WARN, 'client/html' );
		$logger->log( $e->getTraceAsString(), \Aimeos\MW\Logger\Base::WARN, 'client/html' );
	}


	/**
	 * Replaces the section in the content that is enclosed by the marker.
	 *
	 * @param string $content Cached content
	 * @param string $section New section content
	 * @param string $marker Name of the section marker without "<!-- " and " -->" parts
	 */
	protected function replaceSection( $content, $section, $marker )
	{
		$start = 0;
		$len = strlen( $section );
		$marker = '<!-- ' . $marker . ' -->';

		while( ( $start = @strpos( $content, $marker, $start ) ) !== false )
		{
			if( ( $end = strpos( $content, $marker, $start + 1 ) ) !== false ) {
				$content = substr_replace( $content, $section, $start, $end - $start + strlen( $marker ) );
			}

			$start += 2 * strlen( $marker ) + $len;
		}

		return $content;
	}


	/**
	 * Translates the plugin error codes to human readable error strings.
	 *
	 * @param array $codes Associative list of scope and object as key and error code as value
	 * @return array List of translated error messages
	 */
	protected function translatePluginErrorCodes( array $codes )
	{
		$errors = [];
		$i18n = $this->getContext()->getI18n();

		foreach( $codes as $scope => $list )
		{
			foreach( $list as $object => $errcode )
			{
				$key = $scope . ( $scope !== 'product' ? '.' . $object : '' ) . '.' . $errcode;
				$errors[] = $i18n->dt( 'mshop/code', $key );
			}
		}

		return $errors;
	}
}
