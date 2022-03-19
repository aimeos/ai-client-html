<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


/** client/html/catalog/suggest/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * Note: Up to 2015-02, the setting was available as
 * client/html/catalog/listsimple/url/target
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @see client/html/catalog/suggest/url/controller
 * @see client/html/catalog/suggest/url/action
 * @see client/html/catalog/suggest/url/config
 * @see client/html/catalog/listsimple/url/target
 */

/** client/html/catalog/suggest/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * Note: Up to 2015-02, the setting was available as
 * client/html/catalog/listsimple/url/controller
 *
 * @param string Name of the controller
 * @since 2014.03
 * @see client/html/catalog/suggest/url/target
 * @see client/html/catalog/suggest/url/action
 * @see client/html/catalog/suggest/url/config
 * @see client/html/catalog/listsimple/url/controller
 */

/** client/html/catalog/suggest/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * Note: Up to 2015-02, the setting was available as
 * client/html/catalog/listsimple/url/action
 *
 * @param string Name of the action
 * @since 2014.03
 * @see client/html/catalog/suggest/url/target
 * @see client/html/catalog/suggest/url/controller
 * @see client/html/catalog/suggest/url/config
 * @see client/html/catalog/listsimple/url/action
 */

/** client/html/catalog/suggest/url/config
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
 * Note: Up to 2015-02, the setting was available as
 * client/html/catalog/listsimple/url/config
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @see client/html/catalog/suggest/url/target
 * @see client/html/catalog/suggest/url/controller
 * @see client/html/catalog/suggest/url/action
 * @see client/html/url/config
 * @see client/html/catalog/listsimple/url/config
 */

/** client/html/catalog/filter/search/force-search
 * Always reuse the current input for full text searches
 *
 * Normally, the full text search string is added to the input field after each
 * search. This is also the standard behavior of other shops.
 *
 * If it's desired, setting this configuration option to "0" will drop the full
 * text search input so it's not used if the user selects a category or attribute
 * filter.
 *
 * @param boolean True to reuse the search string, false to clear after each search
 * @since 2020.04
 */
$enforce = $this->config( 'client/html/catalog/filter/search/force-search', true );


?>
<?php $this->block()->start( 'catalog/filter/search' ) ?>
<section class="catalog-filter-search">

	<h2><?= $enc->html( $this->translate( 'client', 'Search' ), $enc::TRUST ) ?></h2>
	<div class="search-lists">
		<div class="input-group">
			<input class="form-control value" autocomplete="off"
				name="<?= $enc->attr( $this->formparam( 'f_search' ) ) ?>"
				title="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"
				placeholder="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"
				value="<?= $enc->attr( $enforce ? $this->param( 'f_search' ) : '' ) ?>"
				data-url="<?= $enc->attr( $this->link( 'client/html/catalog/suggest/url', ['f_search' => '_term_'] ) ) ?>"
				data-hint="<?= $enc->attr( $this->translate( 'client', 'Please enter at least three characters' ) ) ?>"
			><!--
			--><button class="btn reset" type="reset" title="<?= $enc->attr( $this->translate( 'client', 'Reset' ) ) ?>"><span class="symbol"></span></button><!--
			--><button class="btn btn-search" type="submit" title="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"></button>
		</div>
	</div>
</section>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/search' ) ?>
