<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Basket;


/**
 * Abstract class for all basket HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Base
{
	/**
	 * Removes all cached basket parts from the cache.
	 */
	protected function clearCached()
	{
		$session = $this->context()->session();

		foreach( $session->get( 'aimeos/basket/cache', [] ) as $key => $value ) {
			$session->set( $key, null );
		}
	}


	/**
	 * Returns the basket cache entry from the cache if available.
	 *
	 * @param string $key Path to the requested cache entry
	 * @param string|null $default Value returned if requested key isn't found
	 * @return string|null Value associated to the requested key. If no value for the
	 *	key is found in the cache, the given default value is returned
	 */
	protected function getBasketCached( string $key, string $default = null ) : ?string
	{
		return $this->context()->session()->get( $key, $default );
	}


	/**
	 * Adds or overwrite a cache entry for the given key and value.
	 *
	 * @param string $key Path the cache entry should be stored in
	 * @param string|null $value Value stored in the cache for the path
	 * @return string|null Value
	 */
	protected function setBasketCached( string $key, string $value = null ) : ?string
	{
		$context = $this->context();

		/** client/html/basket/cache/enable
		 * Enables or disables caching of the basket content
		 *
		 * For performance reasons, the content of the small baskets is cached
		 * in the session of the customer. The cache is updated each time the
		 * basket content changes either by adding, deleting or editing products.
		 *
		 * To ease development, the caching can be disabled but you shouldn't
		 * disable it in your production environment!
		 *
		 * @param boolean True to enable, false to disable basket content caching
		 * @since 2014.11
		 */
		if( $context->config()->get( 'client/html/basket/cache/enable', true ) != false )
		{
			$session = $context->session();

			$cached = $session->get( 'aimeos/basket/cache', [] ) + array( $key => true );
			$session->set( 'aimeos/basket/cache', $cached );
			$session->set( $key, $value );
		}

		return $value;
	}
}
