<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address\Delivery;


/**
 * Default implementation of checkout billing address HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	private array $mandatory = array(
		'order.address.firstname',
		'order.address.lastname',
		'order.address.address1',
		'order.address.postal',
		'order.address.city',
		'order.address.languageid',
	);

	private array $optional = array(
		'order.address.salutation',
		'order.address.company',
		'order.address.vatid',
		'order.address.address2',
		'order.address.countryid',
		'order.address.state',
	);


	/**
	 * Stores the given or fetched billing address in the basket.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init()
	{
		$context = $this->context();
		$view = $this->view();

		try
		{
			if( ( $id = $view->param( 'ca_delivery_delete', null ) ) !== null )
			{
				$cntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );

				if( ( $item = $cntl->uses( ['customer/address'] )->get()->getAddressItem( $id ) ) !== null )
				{
					$cntl->deleteAddressItem( $item )->store();
					throw new \Aimeos\Client\Html\Exception( sprintf( 'Delivery address deleted successfully' ) );
				}
			}

			// only start if there's something to do
			if( $view->param( 'ca_deliveryoption' ) === null ) {
				return;
			}

			$this->setAddress( $view );

			parent::init();
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$view->addressDeliveryError = $e->getErrorList();
			throw $e;
		}
	}


	/**
	 * Checks the address fields for missing data and sanitizes the given parameter list.
	 *
	 * @param array &$params Associative list of address keys (order.address.* or customer.address.*) and their values
	 * @return array List of missing field names
	 */
	protected function checkFields( array &$params ) : array
	{
		$view = $this->view();

		/** client/html/checkout/standard/address/delivery/mandatory
		 * List of delivery address input fields that are required
		 *
		 * You can configure the list of delivery address fields that are
		 * necessary and must be filled by the customer before he can
		 * continue the checkout process. Available field keys are:
		 *
		 * * order.address.company
		 * * order.address.vatid
		 * * order.address.salutation
		 * * order.address.firstname
		 * * order.address.lastname
		 * * order.address.address1
		 * * order.address.address2
		 * * order.address.address3
		 * * order.address.postal
		 * * order.address.city
		 * * order.address.state
		 * * order.address.languageid
		 * * order.address.countryid
		 * * order.address.telephone
		 * * order.address.telefax
		 * * order.address.email
		 * * order.address.website
		 *
		 * @param array List of field keys
		 * @since 2015.02
		 * @see client/html/checkout/standard/address/delivery/disable-new
		 * @see client/html/checkout/standard/address/delivery/salutations
		 * @see client/html/checkout/standard/address/delivery/optional
		 * @see client/html/checkout/standard/address/delivery/hidden
		 * @see client/html/checkout/standard/address/validate
		 * @see common/countries
		 * @see common/states
		 */
		$mandatory = $view->config( 'client/html/checkout/standard/address/delivery/mandatory', $this->mandatory );

		/** client/html/checkout/standard/address/delivery/optional
		 * List of delivery address input fields that are optional
		 *
		 * You can configure the list of delivery address fields that
		 * customers can fill but don't have to before they can
		 * continue the checkout process. Available field keys are:
		 *
		 * * order.address.company
		 * * order.address.vatid
		 * * order.address.salutation
		 * * order.address.firstname
		 * * order.address.lastname
		 * * order.address.address1
		 * * order.address.address2
		 * * order.address.address3
		 * * order.address.postal
		 * * order.address.city
		 * * order.address.state
		 * * order.address.languageid
		 * * order.address.countryid
		 * * order.address.telephone
		 * * order.address.telefax
		 * * order.address.email
		 * * order.address.website
		 * * nostore
		 *
		 * Using the "nostore" field displays the option to avoid storing the
		 * delivery address permanently in the customer account.
		 *
		 * @param array List of field keys
		 * @since 2015.02
		 * @see client/html/checkout/standard/address/delivery/disable-new
		 * @see client/html/checkout/standard/address/delivery/salutations
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/hidden
		 * @see client/html/checkout/standard/address/validate
		 * @see common/countries
		 * @see common/states
		 */
		$optional = $view->config( 'client/html/checkout/standard/address/delivery/optional', $this->optional );

		/** client/html/checkout/standard/address/delivery/hidden
		 * List of delivery address input fields that are optional
		 *
		 * You can configure the list of delivery address fields that
		 * are hidden when a customer enters his delivery address.
		 * Available field keys are:
		 *
		 * * order.address.company
		 * * order.address.vatid
		 * * order.address.salutation
		 * * order.address.firstname
		 * * order.address.lastname
		 * * order.address.address1
		 * * order.address.address2
		 * * order.address.address3
		 * * order.address.postal
		 * * order.address.city
		 * * order.address.state
		 * * order.address.languageid
		 * * order.address.countryid
		 * * order.address.telephone
		 * * order.address.telefax
		 * * order.address.email
		 * * order.address.website
		 *
		 * Caution: Only hide fields that don't require any input
		 *
		 * @param array List of field keys
		 * @since 2015.02
		 * @see client/html/checkout/standard/address/delivery/disable-new
		 * @see client/html/checkout/standard/address/delivery/salutations
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 * @see common/countries
		 * @see common/states
		 */
		$hidden = $view->config( 'client/html/checkout/standard/address/delivery/hidden', [] );

		/** client/html/checkout/standard/address/validate
		 *
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 */

		$allFields = array_flip( array_merge( $mandatory, $optional, $hidden ) );
		$invalid = $this->validateFields( $params, $allFields );
		$this->checkSalutation( $params, $mandatory );

		foreach( $invalid as $key => $name )
		{
			$msg = $view->translate( 'client', 'Delivery address part "%1$s" is invalid' );
			$invalid[$key] = sprintf( $msg, $name );
		}

		foreach( $mandatory as $key )
		{
			if( !isset( $params[$key] ) || $params[$key] == '' )
			{
				$msg = $view->translate( 'client', 'Delivery address part "%1$s" is missing' );
				$invalid[$key] = sprintf( $msg, substr( $key, 19 ) );
				unset( $params[$key] );
			}
		}

		return $invalid;
	}


	/**
	 * Additional checks for the salutation
	 *
	 * @param array &$params Associative list of address keys (order.address.* or customer.address.*) and their values
	 * @param array &$mandatory List of mandatory field names
	 * @since 2016.05
	 */
	protected function checkSalutation( array &$params, array &$mandatory )
	{
		if( isset( $params['order.address.salutation'] )
				&& $params['order.address.salutation'] === \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY
				&& in_array( 'order.address.company', $mandatory ) === false
		) {
			$mandatory[] = 'order.address.company';
		}
	}


	/**
	 * Returns the address as string
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param \Aimeos\MShop\Order\Item\Address\Iface $addr Order address item
	 * @return string Address as string
	 */
	protected function getAddressString( \Aimeos\Base\View\Iface $view, \Aimeos\MShop\Order\Item\Address\Iface $addr )
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


	/**
	 * Sets the new address
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @throws \Aimeos\Client\Html\Exception If an error occurs
	 * @since 2016.05
	 */
	protected function setAddress( \Aimeos\Base\View\Iface $view )
	{
		$address = null;
		$context = $this->context();
		$ctrl = \Aimeos\Controller\Frontend::create( $context, 'basket' );

		/** client/html/checkout/standard/address/delivery/disable-new
		 * Disables the option to enter a different delivery address for an order
		 *
		 * Besides the billing address, customers can usually enter a different
		 * delivery address as well. To suppress displaying the form fields for
		 * a delivery address, you can set this configuration option to "1".
		 *
		 * @param boolean A value of "1" to disable, "0" enables the delivery address form
		 * @since 2015.02
		 * @see client/html/checkout/standard/address/delivery/salutations
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 * @see client/html/checkout/standard/address/delivery/hidden
		 */
		$disable = $view->config( 'client/html/checkout/standard/address/delivery/disable-new', false );
		$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY;

		if( ( $option = $view->param( 'ca_deliveryoption', 'null' ) ) === 'null' && $disable === false ) // new address
		{
			$params = $view->param( 'ca_delivery', [] );

			if( ( $view->addressDeliveryError = $this->checkFields( $params ) ) !== [] ) {
				throw new \Aimeos\Client\Html\Exception( sprintf( 'At least one delivery address part is missing or invalid' ) );
			}

			$ctrl->addAddress( $type, $params, 0 );
		}
		else if( ( $option = $view->param( 'ca_deliveryoption' ) ) !== 'like' ) // existing address
		{
			$params = $view->param( 'ca_delivery_' . $option, [] );

			if( !empty( $params ) && ( $view->addressDeliveryError = $this->checkFields( $params ) ) !== [] ) {
				throw new \Aimeos\Client\Html\Exception( sprintf( 'At least one delivery address part is missing or invalid' ) );
			}

			$custCntl = \Aimeos\Controller\Frontend::create( $context, 'customer' );

			if( ( $address = $custCntl->uses( ['customer/address'] )->get()->getAddressItem( $option ) ) !== null )
			{
				$params = array_replace( $address->toArray(), $params + ['order.address.addressid' => $option] );
				$addr = $ctrl->addAddress( $type, $params, 0 )->get()->getAddress( $type, 0 ); // sanitize address first
				$custCntl->addAddressItem( $address->copyFrom( $addr ), $option )->store(); // update existing address
			}
			else
			{
				$ctrl->addAddress( $type, $params, 0 );
			}
		}
		else
		{
			$ctrl->deleteAddress( $type );
		}
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
		$manager = \Aimeos\MShop::create( $context, 'order/address' );
		$basketCntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );

		$addrStrings = $addrValues = [];
		$addrMap = map( $basketCntl->get()->getAddress( 'delivery' ) );

		foreach( $view->get( 'addressDeliveryItems', [] ) as $id => $address )
		{
			$params = $view->param( 'ca_delivery_' . $id, [] );
			$basketValues = $addrMap->get( $id, map() )->toArray();
			$addr = $manager->create()->copyFrom( $address )->fromArray( $basketValues )->fromArray( $params );

			$addrStrings[$id] = $this->getAddressString( $view, $addr );
			$addrValues[$id] = $addr->toArray();
		}

		$values = !$addrMap->isEmpty() ? $addrMap->first()->toArray() : [];
		$values = array_merge( $values, $view->param( 'ca_delivery', [] ) );
		$addrNew = $manager->create()->fromArray( $values );

		$addrStringNew = $this->getAddressString( $view, $addrNew );
		$option = $addrNew->getAddressId() ?: ( $addrMap->isEmpty() ? 'like' : 'null' );

		$view->addressDeliveryOption = $view->param( 'ca_deliveryoption', $option );
		$view->addressDeliveryValuesNew = $addrNew->toArray();
		$view->addressDeliveryStringNew = $addrStringNew;
		$view->addressDeliveryStrings = $addrStrings;
		$view->addressDeliveryValues = $addrValues;

		$salutations = $context->config()->get( 'client/html/common/address/salutations', ['', 'mr', 'ms'] );

		/** client/html/checkout/standard/address/delivery/salutations
		 * List of salutions the customer can select from for the delivery address
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
		 * @since 2015.02
		 * @see client/html/checkout/standard/address/delivery/disable-new
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 * @see client/html/checkout/standard/address/delivery/hidden
		 * @see client/html/common/address/salutations
		 * @see common/countries
		 * @see common/states
		 */
		$view->addressDeliverySalutations = $view->config( 'client/html/checkout/standard/address/delivery/salutations', $salutations );

		$mandatory = $view->config( 'client/html/checkout/standard/address/delivery/mandatory', $this->mandatory );
		$optional = $view->config( 'client/html/checkout/standard/address/delivery/optional', $this->optional );
		$hidden = $view->config( 'client/html/checkout/standard/address/delivery/hidden', [] );

		$css = [];

		foreach( $mandatory as $name ) {
			$css[$name][] = 'mandatory';
		}

		foreach( $optional as $name ) {
			$css[$name][] = 'optional';
		}

		foreach( $hidden as $name ) {
			$css[$name][] = 'hidden';
		}

		$view->addressDeliveryMandatory = $mandatory;
		$view->addressDeliveryOptional = $optional;
		$view->addressDeliveryHidden = $hidden;
		$view->addressDeliveryCss = $css;

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Validate the address key/value pairs using regular expressions
	 *
	 * @param array &$params Associative list of address keys (order.address.* or customer.address.*) and their values
	 * @param array $fields List of field names to validate
	 * @return array List of invalid address keys
	 * @since 2016.05
	 */
	protected function validateFields( array &$params, array $fields ) : array
	{
		$config = $this->context()->config();

		/** client/html/checkout/standard/address/validate/company
		 * Regular expression to check the "company" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/vatid
		 * Regular expression to check the "vatid" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/salutation
		 * Regular expression to check the "salutation" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/firstname
		 * Regular expression to check the "firstname" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/lastname
		 * Regular expression to check the "lastname" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/address1
		 * Regular expression to check the "address1" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/address2
		 * Regular expression to check the "address2" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/address3
		 * Regular expression to check the "address3" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/postal
		 * Regular expression to check the "postal" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/city
		 * Regular expression to check the "city" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/state
		 * Regular expression to check the "state" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/languageid
		 * Regular expression to check the "languageid" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/countryid
		 * Regular expression to check the "countryid" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/telephone
		 * Regular expression to check the "telephone" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/telefax
		 * Regular expression to check the "telefax" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/email
		 * Regular expression to check the "email" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		/** client/html/checkout/standard/address/validate/website
		 * Regular expression to check the "website" address value
		 *
		 * @see client/html/checkout/standard/address/validate
		 */

		$invalid = [];

		foreach( $params as $key => $value )
		{
			if( isset( $fields[$key] ) )
			{
				$name = ( $pos = strrpos( $key, '.' ) ) ? substr( $key, $pos + 1 ) : $key;
				$regex = $config->get( 'client/html/checkout/standard/address/validate/' . $name );

				if( $regex && preg_match( '/' . $regex . '/', $value ) !== 1 )
				{
					$invalid[$key] = $name;
					unset( $params[$key] );
				}
			}
			else
			{
				unset( $params[$key] );
			}
		}

		return $invalid;
	}


	/** client/html/checkout/standard/address/delivery/template-body
	 * Relative path to the HTML body template of the checkout standard address delivery client.
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
	 * @see client/html/checkout/standard/address/delivery/template-header
	 */
}
