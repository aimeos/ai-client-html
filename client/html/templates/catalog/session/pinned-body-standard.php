<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


/** client/html/catalog/session/pinned/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/pinned/url/controller
 * @see client/html/catalog/session/pinned/url/action
 * @see client/html/catalog/session/pinned/url/config
 */
$pinTarget = $this->config( 'client/html/catalog/session/pinned/url/target' );

/** client/html/catalog/session/pinned/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/pinned/url/target
 * @see client/html/catalog/session/pinned/url/action
 * @see client/html/catalog/session/pinned/url/config
 */
$pinController = $this->config( 'client/html/catalog/session/pinned/url/controller', 'catalog' );

/** client/html/catalog/session/pinned/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/pinned/url/target
 * @see client/html/catalog/session/pinned/url/controller
 * @see client/html/catalog/session/pinned/url/config
 */
$pinAction = $this->config( 'client/html/catalog/session/pinned/url/action', 'detail' );

/** client/html/catalog/session/pinned/url/config
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
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/pinned/url/target
 * @see client/html/catalog/session/pinned/url/controller
 * @see client/html/catalog/session/pinned/url/action
 * @see client/html/url/config
 */
$pinConfig = $this->config( 'client/html/catalog/session/pinned/url/config', [] );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', [] );
$detailFilter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );

/** client/html/catalog/session/pinned/count/enable
 * Displays the number of pinned products in the header of the pinned list
 *
 * This configuration option enables or disables displaying the total number
 * of pinned products in the header of section. This increases the usability if
 * more than the shown products are available in the list but this depends on
 * the design of the site.
 *
 * @param integer Zero to disable the counter, one to enable it
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/seen/count/enable
 */


?>
<?php $this->block()->start( 'catalog/session/pinned' ) ?>
<section class="catalog-session-pinned">

	<h1 class="header">
		<?= $this->translate( 'client', 'Pinned products' ) ?>
		<?php if( $this->config( 'client/html/catalog/session/pinned/count/enable', true ) ) : ?>
			<span class="count"><?= count( $this->get( 'pinnedProductItems', [] ) ) ?></span>
		<?php endif ?>
	</h1>

	<ul class="pinned-items">
		<?php foreach( $this->get( 'pinnedProductItems', [] ) as $id => $productItem ) : ?>
			<?php $pinParams = ['pin_action' => 'delete', 'pin_id' => $id] + $this->get( 'pinnedParams', [] ) ?>
			<?php $detailParams = array_diff_key( ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $id, 'd_pos' => ''], $detailFilter ) ?>

			<li class="pinned-item">
				<a class="modify" href="<?= $this->url( $pinTarget, $pinController, $pinAction, $pinParams, [], $pinConfig ) ?>"></a>

				<a href="<?= $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $detailParams, [], $detailConfig ) ) ?>">

					<?php $mediaItems = $productItem->getRefItems( 'media', 'default', 'default' ) ?>
					<?php if( ( $mediaItem = $mediaItems->first() ) !== null ) : ?>
						<div class="media-item" style="background-image: url('<?= $enc->attr( $this->content( $mediaItem->getPreview() ) ) ?>')"></div>
					<?php else : ?>
						<div class="media-item"></div>
					<?php endif ?>

					<h3 class="name"><?= $enc->html( $productItem->getName(), $enc::TRUST ) ?></h3>
					<div class="price-list">
						<?= $this->partial(
							$this->config( 'client/html/common/partials/price', 'common/partials/price-standard' ),
							array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) )
						) ?>
					</div>

				</a>
			</li>

		<?php endforeach ?>
	</ul>

</section>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/session/pinned' ) ?>
