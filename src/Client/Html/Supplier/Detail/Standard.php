<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Supplier\Detail;


/**
 * Default implementation of supplier detail section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
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
		$prefixes = ['f_supid'];

		/** client/html/supplier/detail/cache
		 * Enables or disables caching only for the supplier detail component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @see client/html/supplier/detail/cache
		 * @see client/html/supplier/filter/cache
		 * @see client/html/supplier/lists/cache
		 */

		/** client/html/supplier/detail
		 * All parameters defined for the supplier detail component and its subparts
		 *
		 * This returns all settings related to the detail component.
		 * Please refer to the single settings for details.
		 *
		 * @param array Associative list of name/value settings
		 * @see client/html/supplier#detail
		 */
		$confkey = 'client/html/supplier/detail';

		if( $html = $this->cached( 'body', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/supplier/detail/template-body
		 * Relative path to the HTML body template of the supplier detail client.
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
		 * @since 2020.10
		 * @see client/html/supplier/detail/template-header
		 */
		$template = $this->context()->config()->get( 'client/html/supplier/detail/template-body', 'supplier/detail/body' );

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );
		$html = $this->modify( $view->render( $template ), $uid );

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
		$prefixes = ['f_supid'];
		$confkey = 'client/html/supplier/detail';

		if( $html = $this->cached( 'header', $uid, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		/** client/html/supplier/detail/template-header
		 * Relative path to the HTML header template of the supplier detail client.
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
		 * @since 2020.10
		 * @see client/html/supplier/detail/template-body
		 */
		$template = $this->context()->config()->get( 'client/html/supplier/detail/template-header', 'supplier/detail/header' );

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

		/** client/html/supplier/detail/supid-default
		 * The default supplier ID used if none is given as parameter
		 *
		 * You can configure the default supplier ID if no ID is passed in the
		 * URL using this configuration.
		 *
		 * @param string Supplier ID
		 * @since 2021.01
		 * @see client/html/catalog/lists/catid-default
		 */
		if( $supid = $view->param( 'f_supid', $config->get( 'client/html/supplier/detail/supid-default' ) ) )
		{
			$controller = \Aimeos\Controller\Frontend::create( $context, 'supplier' );

			/** client/html/supplier/detail/domains
			 * A list of domain names whose items should be available in the supplier detail view template
			 *
			 * The templates rendering the supplier detail section use the texts and
			 * maybe images and attributes associated to the categories. You can
			 * configure your own list of domains (attribute, media, price, product,
			 * text, etc. are domains) whose items are fetched from the storage.
			 * Please keep in mind that the more domains you add to the configuration,
			 * the more time is required for fetching the content!
			 *
			 * @param array List of domain names
			 * @since 2020.10
			 */
			$domains = $config->get( 'client/html/supplier/detail/domains', ['supplier/address', 'media', 'text'] );

			$supplier = $controller->uses( $domains )->get( $supid );

			$this->addMetaItems( $supplier, $expire, $tags );

			$view->detailSupplierItem = $supplier;
			$view->detailSupplierAddresses = $this->getAddressStrings( $view, $supplier->getAddressItems() );
		}

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the addresses as list of strings
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param iterable $addresses List of address items implementing \Aimeos\MShop\Common\Item\Address\Iface
	 * @return \Aimeos\Map List of address strings
	 */
	protected function getAddressStrings( \Aimeos\Base\View\Iface $view, iterable $addresses ) : \Aimeos\Map
	{
		$list = [];

		foreach( $addresses as $id => $addr )
		{
			$list[$id] = preg_replace( "/\n+/m", "\n", trim( sprintf(
				/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
				/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
				/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
				/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
				$view->translate( 'client', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'
				),
				$addr->getCompany(),
				$view->translate( 'mshop/code', (string) $addr->getSalutation() ),
				$addr->getTitle(),
				$addr->getFirstName(),
				$addr->getLastName(),
				$addr->getAddress1(),
				$addr->getAddress2(),
				$addr->getAddress3(),
				$addr->getPostal(),
				$addr->getCity(),
				$addr->getState(),
				$view->translate( 'country', (string) $addr->getCountryId() ),
				$view->translate( 'language', (string) $addr->getLanguageId() ),
				$addr->getEmail(),
				$addr->getTelephone(),
				$addr->getTelefax(),
				$addr->getWebsite(),
				$addr->getVatID()
			) ) );
		}

		return map( $list );
	}
}
