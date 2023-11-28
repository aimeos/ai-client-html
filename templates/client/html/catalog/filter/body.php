<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


$enc = $this->encoder();


/** client/html/catalog/lists/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/catalog/lists/url/controller
 * @see client/html/catalog/lists/url/action
 * @see client/html/catalog/lists/url/config
 * @see client/html/catalog/lists/url/filter
 */

/** client/html/catalog/lists/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/catalog/lists/url/target
 * @see client/html/catalog/lists/url/action
 * @see client/html/catalog/lists/url/config
 * @see client/html/catalog/lists/url/filter
 */

/** client/html/catalog/lists/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/catalog/lists/url/target
 * @see client/html/catalog/lists/url/controller
 * @see client/html/catalog/lists/url/config
 * @see client/html/catalog/lists/url/filter
 */

/** client/html/catalog/lists/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/catalog/lists/url/target
 * @see client/html/catalog/lists/url/controller
 * @see client/html/catalog/lists/url/action
 * @see client/html/catalog/lists/url/filter
 */

/** client/html/catalog/lists/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/catalog/lists/url/target
 * @see client/html/catalog/lists/url/controller
 * @see client/html/catalog/lists/url/action
 * @see client/html/catalog/lists/url/config
 */

$linkKey = $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';
$params = map( $this->param() )->only( ['f_catid', 'f_name'] );

if( $catid = $this->config( 'client/html/catalog/filter/tree/startid' ) ) {
	$params = $params->union( ['f_catid' => $catid] );
}


?>
<div class="section aimeos catalog-filter" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<nav class="container-xxl">
		<form method="GET" action="<?= $enc->attr( $this->link( $linkKey, $params->all() ) ) ?>">

			<?php foreach( map( $this->param() )->only( ['f_sort', 'l_type'] ) as $name => $value ) : ?>
				<input type="hidden" name="<?= $enc->attr( $this->formparam( $name ) ) ?>" value="<?= $enc->attr( $value ) ?>">
			<?php endforeach ?>

			<?= $this->block()->get( 'catalog/filter/tree' ) ?>
			<?= $this->block()->get( 'catalog/filter/search' ) ?>
			<?= $this->block()->get( 'catalog/filter/price' ) ?>
			<?= $this->block()->get( 'catalog/filter/supplier' ) ?>
			<?= $this->block()->get( 'catalog/filter/attribute' ) ?>

		</form>
	</nav>

</div>
