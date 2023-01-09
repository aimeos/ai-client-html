<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Filter\Attribute;


/**
 * Default implementation of catalog attribute filter section in HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
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
		$attrMap = [];

		/** client/html/catalog/filter/attribute/types-option
		 * List of attribute types whose IDs should be used in a global "OR" condition
		 *
		 * The attribute section in the catalog filter component can display all
		 * attributes a visitor can use to filter the listed products to those that
		 * contains one or more attributes.
		 *
		 * This configuration setting lists the attribute types where at least one of
		 * all attributes must be referenced by the found products. Only one attribute
		 * of all listed attributes types (whatever matches) in enough. This setting is
		 * different from "client/html/catalog/filter/attribute/types-oneof" because
		 * it's not limited within the same attribute type
		 *
		 * @param array List of attribute type codes
		 * @since 2016.10
		 * @see client/html/catalog/filter/attribute/types
		 * @see client/html/catalog/filter/attribute/types-oneof
		 */
		$options = $view->config( 'client/html/catalog/filter/attribute/types-option', [] );

		/** client/html/catalog/filter/attribute/types-oneof
		 * List of attribute types whose values should be used in a type specific "OR" condition
		 *
		 * The attribute section in the catalog filter component can display all
		 * attributes a visitor can use to filter the listed products to those that
		 * contains one or more attributes.
		 *
		 * This configuration setting lists the attribute types where at least one of
		 * the attributes within the same attribute type must be referenced by the found
		 * products.
		 *
		 * @param array List of attribute type codes
		 * @since 2016.10
		 * @see client/html/catalog/filter/attribute/types
		 * @see client/html/catalog/filter/attribute/types-option
		 */
		$oneof = $view->config( 'client/html/catalog/filter/attribute/types-oneof', [] );

		/** client/html/catalog/filter/attribute/types
		 * List of attribute types that should be displayed in this order in the catalog filter
		 *
		 * The attribute section in the catalog filter component can display
		 * all attributes a visitor can use to reduce the listed products
		 * to those that contains one or more attributes. By default, all
		 * available attributes will be displayed and ordered by their
		 * attribute type.
		 *
		 * With this setting, you can limit the attribute types to only thoses
		 * whose names are part of the setting value. Furthermore, a particular
		 * order for the attribute types can be enforced that is different
		 * from the standard order.
		 *
		 * @param array List of attribute type codes
		 * @since 2015.05
		 * @see client/html/catalog/filter/attribute/domains
		 * @see client/html/catalog/filter/attribute/types-oneof
		 * @see client/html/catalog/filter/attribute/types-option
		 */
		$attrTypes = $view->config( 'client/html/catalog/filter/attribute/types', [] );
		$attrTypes = ( !is_array( $attrTypes ) ? explode( ',', $attrTypes ) : $attrTypes );

		/** client/html/catalog/filter/attribute/domains
		 * List of domain names whose items should be fetched with the filter attributes
		 *
		 * The templates rendering the attributes in the catalog filter usually
		 * add the images and texts associated to each item. If you want to
		 * display additional content, you can configure your own list of
		 * domains (attribute, media, price, product, text, etc. are domains)
		 * whose items are fetched from the storage. Please keep in mind that
		 * the more domains you add to the configuration, the more time is
		 * required for fetching the content!
		 *
		 * @param array List of domain item names
		 * @since 2015.05
		 * @see client/html/catalog/filter/attribute/types
		 */
		$domains = $view->config( 'client/html/catalog/filter/attribute/domains', ['text', 'media', 'media/property'] );

		$attributes = \Aimeos\Controller\Frontend::create( $this->context(), 'attribute' )
			->uses( $domains )->type( $attrTypes )->compare( '!=', 'attribute.type', ['date', 'price', 'text'] )
			->sort( 'position' )->slice( 0, 10000 )->search();

		$this->addMetaItems( $attributes, $expire, $tags );


		$active = [];
		$params = $this->getClientParams( $view->param() );

		$attrIds = array_filter( $view->param( 'f_attrid', [] ) );
		$oneIds = array_filter( $view->param( 'f_oneid', [] ) );
		$optIds = array_filter( $view->param( 'f_optid', [] ) );

		foreach( $attributes as $id => $item )
		{
			$attrparams = $params;
			$type = $item->getType();

			if( ( $key = array_search( $id, $attrIds ) ) !== false )
			{
				$item = $item->set( 'checked', true );
				unset( $attrparams['f_attrid'][$key] );
			}
			elseif( ( $key = array_search( $id, $optIds ) ) !== false )
			{
				$item = $item->set( 'checked', true );
				unset( $attrparams['f_optid'][$key] );
			}
			elseif( isset( $oneIds[$type] ) && ( $key = array_search( $id, (array) $oneIds[$type] ) ) !== false )
			{
				$item = $item->set( 'checked', true );
				unset( $attrparams['f_oneid'][$key] );
			}

			$fparams = $this->getFormParams( $type, $oneof, $options );
			$active[$item->getType()] = (int) $item->get( 'checked', $active[$item->getType()] ?? false );
			$attrMap[$item->getType()][$id] = $item->set( 'params', $attrparams )->set( 'formparam', $fparams );
		}

		arsort( $active );
		unset( $params['f_attrid'], $params['f_oneid'], $params['f_optid'] );

		$view->attributeResetParams = $params;
		$view->attributeMap = $this->sort( $attrMap, $attrTypes );
		$view->attributeMapActive = map( $view->attributeMap )->uksort( function( $a, $b ) use ( $active ) {
			return $active[$b] <=> $active[$a];
		} );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		return null;
	}


	/**
	 * Returns the form parameter names for the given attribute type
	 *
	 * @param string $type Attribute type code
	 * @param array $oneof List of attribute type codes for one of several filter
	 * @param array $options List of attribute type codes for optional filter
	 * @return array Ordered list of form parameter names
	 */
	protected function getFormParams( string $type, array $oneof, array $options ) : array
	{
		if( in_array( $type, $oneof ) ) {
			return ['f_oneid', $type, ''];
		}

		if( in_array( $type, $options ) ) {
			return ['f_optid', ''];
		}

		return ['f_attrid', ''];
	}


	/**
	 * Sorts the attribute types according to the configured order
	 *
	 * @param array $attrMap Associative list of attribute types as keys and attribute items as values
	 * @param array $attrTypes List of attribute type names
	 * @return array Sorted associative list of attribute types and attribute items
	 */
	protected function sort( array $attrMap, array $attrTypes ) : array
	{
		if( !empty( $attrTypes ) )
		{
			$map = [];

			foreach( $attrTypes as $type )
			{
				if( isset( $attrMap[$type] ) ) {
					$map[$type] = $attrMap[$type];
				}
			}

			return $map;
		}

		foreach( $attrMap as $type => &$map )
		{
			uasort( $map, function( $a, $b ) {
				return $a->getPosition() <=> $b->getPosition();
			} );
		}

		return $attrMap;
	}


	/** client/html/catalog/filter/attribute/template-body
	 * Relative path to the HTML body template of the catalog filter attribute client.
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
	 * @see client/html/catalog/filter/attribute/template-header
	 */
}
