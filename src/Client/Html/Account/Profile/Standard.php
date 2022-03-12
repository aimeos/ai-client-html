<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Account\Profile;


/**
 * Default implementation of account profile HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Iface
{
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

		/** client/html/common/address/salutations
		 * List of salutions the customers can select from in the HTML frontend
		 *
		 * The following salutations are available:
		 *
		 * * empty string for "unknown"
		 * * company
		 * * mr
		 * * ms
		 *
		 * You can modify the list of salutation codes and remove the ones
		 * which shouldn't be used or add new ones.
		 *
		 * @param array List of available salutation codes
		 * @since 2021.04
		 * @see client/html/account/profile/address/salutations
		 */
		$salutations = $config->get( 'client/html/common/address/salutations', ['', 'company', 'mr', 'ms'] );

		/** client/html/account/profile/domains
		 * A list of domain names whose items should be available in the account profile view template
		 *
		 * The templates rendering customer details can contain additional
		 * items. If you want to display additional content, you can configure
		 * your own list of domains (attribute, media, price, product, text,
		 * etc. are domains) whose items are fetched from the storage.
		 *
		 * @param array List of domain names
		 * @since 2016.10
		 */
		$domains = $config->get( 'client/html/account/profile/domains', ['customer/address'] );

		$item = \Aimeos\Controller\Frontend::create( $context, 'customer' )->uses( $domains )->get();

		$localeManager = \Aimeos\MShop::create( $context, 'locale' );
		$languages = $localeManager->search( $localeManager->filter( true ) )
			->col( 'locale.languageid', 'locale.languageid' );

		$deliveries = [];
		$addr = $item->getPaymentAddress();

		if( !$addr->getLanguageId() ) {
			$addr->setLanguageId( $context->locale()->getLanguageId() );
		}

		$billing = $addr->toArray();
		$billing['string'] = $this->call( 'getAddressString', $view, $addr );

		foreach( $item->getAddressItems() as $pos => $address )
		{
			$delivery = $address->toArray();
			$delivery['string'] = $this->call( 'getAddressString', $view, $address );
			$deliveries[$pos] = $delivery;
		}

		$view->profileItem = $item;
		$view->addressBilling = $billing;
		$view->addressDelivery = $deliveries;
		$view->addressCountries = $view->config( 'client/html/checkout/standard/address/countries', [] );
		$view->addressStates = $view->config( 'client/html/checkout/standard/address/states', [] );
		$view->addressSalutations = $salutations;
		$view->addressLanguages = $languages;

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$view = $this->view();

		if( !$view->param( 'address/save' ) && !$view->param( 'address/delete' ) ) {
			return;
		}

		$cntl = \Aimeos\Controller\Frontend::create( $this->context(), 'customer' );
		$addrItems = $cntl->uses( ['customer/address'] )->get()->getAddressItems();
		$cntl->add( $view->param( 'address/payment', [] ) );
		$map = [];

		foreach( $view->param( 'address/delivery/customer.address.id', [] ) as $pos => $id )
		{
			foreach( $view->param( 'address/delivery', [] ) as $key => $list )
			{
				if( array_key_exists( $pos, $list ) ) {
					$map[$pos][$key] = $list[$pos];
				}
			}
		}

		if( $pos = $view->param( 'address/delete' ) ) {
			unset( $map[$pos] );
		}

		foreach( $map as $pos => $data )
		{
			$addrItem = $addrItems->get( $pos ) ?: $cntl->createAddressItem();
			$cntl->addAddressItem( $addrItem->fromArray( $data ), $pos );
			$addrItems->remove( $pos );
		}

		foreach( $addrItems as $addrItem ) {
			$cntl->deleteAddressItem( $addrItem );
		}

		$cntl->store();
	}


	/**
	 * Returns the address as string
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Order address item
	 * @return string Address as string
	 */
	protected function getAddressString( \Aimeos\Base\View\Iface $view, \Aimeos\MShop\Common\Item\Address\Iface $addr )
	{
		return preg_replace( "/\n+/m", "\n", trim( sprintf(
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


	/** client/html/account/profile/template-body
	 * Relative path to the HTML body template of the account profile client.
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
	 * @since 2016.10
	 * @see client/html/account/profile/template-header
	 */

	/** client/html/account/profile/template-header
	 * Relative path to the HTML header template of the account profile client.
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
	 * @since 2016.10
	 * @see client/html/account/profile/template-body
	 */
}
