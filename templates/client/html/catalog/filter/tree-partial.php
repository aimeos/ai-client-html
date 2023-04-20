<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

$enc = $this->encoder();


/** client/html/catalog/tree/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2019.01
 * @see client/html/catalog/tree/url/controller
 * @see client/html/catalog/tree/url/action
 * @see client/html/catalog/tree/url/config
 * @see client/html/catalog/tree/url/filter
 */
$target = $this->config( 'client/html/catalog/tree/url/target' );

/** client/html/catalog/tree/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2019.01
 * @see client/html/catalog/tree/url/target
 * @see client/html/catalog/tree/url/action
 * @see client/html/catalog/tree/url/config
 * @see client/html/catalog/tree/url/filter
 */
$controller = $this->config( 'client/html/catalog/tree/url/controller', 'catalog' );

/** client/html/catalog/tree/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2019.01
 * @see client/html/catalog/tree/url/target
 * @see client/html/catalog/tree/url/controller
 * @see client/html/catalog/tree/url/config
 * @see client/html/catalog/tree/url/filter
 */
$action = $this->config( 'client/html/catalog/tree/url/action', 'list' );

/** client/html/catalog/tree/url/config
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
 * @since 2019.01
 * @see client/html/catalog/tree/url/target
 * @see client/html/catalog/tree/url/controller
 * @see client/html/catalog/tree/url/action
 * @see client/html/catalog/tree/url/filter
 */
$config = $this->config( 'client/html/catalog/tree/url/config', [] );

/** client/html/catalog/tree/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/catalog/tree/url/target
 * @see client/html/catalog/tree/url/controller
 * @see client/html/catalog/tree/url/action
 * @see client/html/catalog/tree/url/config
 */


/** client/html/common/partials/media
 * Relative path to the media partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The media
 * partial creates an HTML block of for images, video, audio or other documents.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "common/partials/media.php".
 *
 * @param string Relative path to the template file
 * @since 2015.08
 */


?>
<div class="list-container level-<?= $enc->attr( $this->get( 'level', 0 ) ) ?>">

	<?php foreach( $this->get( 'nodes', [] ) as $item ) : ?>
		<?php if( $item->getStatus() > 0 ) : ?>
			<?php $params = array_merge( $this->get( 'params', [] ), ['f_name' => $item->getName( 'url' ), 'f_catid' => $item->getId()] ) ?>

			<div class="cat-item catid-<?= $enc->attr( $item->getId() .
				( !$item->getChildren()->isEmpty() ? ' withchild' : ' nochild' ) .
				( $this->get( 'path', map() )->has( $item->getId() ) ? ' active' : '' ) .
				' catcode-' . $item->getCode() . ' ' . $item->getConfigValue( 'css-class' ) ) ?>"
				data-id="<?= $item->getId() ?>">

				<div class="item-links row">
					<a class="col-10 cat-link name <?= ( $this->get( 'path', map() )->has( $item->getId() ) ? ' active' : '' ) ?>"
						href="<?= $enc->attr( $this->url( $item->getTarget() ?: $target, $controller, $action, $params, [], $config ) ) ?>">
						<div class="media-list">
							<?php foreach( $item->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
								<?= $this->partial(
									$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
									array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
								) ?>
							<?php endforeach ?>
						</div>
						<span class="cat-name"><?= $enc->html( $item->getName(), $enc::TRUST ) ?></span>
					</a>
					<?php if( !$item->getChildren()->isEmpty() ) : ?>
						<div class="col-2 next" data-submenu="<?= $enc->attr( $item->getId() ) ?>"
							title="<?= $enc->attr( $this->translate( 'client', 'Open submenu' ) ) ?>">
						</div>
					<?php else : ?>
						<div class="col-2"></div>
					<?php endif ?>
				</div>

				<?php if( count( $item->getChildren() ) > 0 ) : ?>

					<div id="<?= $enc->attr( $item->getId() ) ?>" class="submenu <?= $enc->attr(
						( $this->get( 'path', map() )->has( $item->getId() ) ? ' active opened' : '' ) ) .
						( !$item->getChildren()->isEmpty() ? ' withchild' : ' nochild' ) ?>"
						<?= $this->get( 'path', map() )->getId()->last() == $item->getId() ? 'aria-current="page"' : '' ?>
						data-id="<?= $item->getId() ?>">

						<div class="row header">
							<div class="col-2 back" data-submenu-close="<?= $enc->attr( $item->getId() ) ?>"></div>
							<div class="col-8 name"><?= $enc->html( $item->getName(), $enc::TRUST ) ?></div>
							<div class="col-2 close"></div>
						</div>

						<?= $this->partial( $this->config( 'client/html/catalog/filter/tree/partial', 'catalog/filter/tree-partial' ), [
							'nodes' => $item->getChildren(),
							'path' => $this->get( 'path', map() ),
							'level' => $this->get( 'level', 0 ) + 1,
							'params' => $this->get( 'params', [] )
						] ) ?>

						<?php if( $item->getLevel() == 1 ) : ?>

							<a class="cat-img <?= $enc->attr( ( $this->get( 'path', map() )->getId()->last() == $item->getId() ? ' active' : '' ) ) ?>"
								title="<?= $enc->attr( $item->getRefItems( 'media', 'menu', 'default' )->getProperties( 'title' )->first() ?: $item->getName() ) ?>"
								href="<?= $enc->attr( $this->url( $item->getTarget() ?: $target, $controller, $action, array_merge( $this->get( 'params', [] ), ['f_name' => $item->getName( 'url' ), 'f_catid' => $item->getId()] ), [], $config ) ) ?>">

								<?php foreach( $item->getRefItems( 'media', 'menu', 'default' ) as $mediaItem ) : ?>
									<img class="img-menu" loading="lazy"
										src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
										srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews( true ), $mediaItem->getFileSystem() ) ) ?>"
										sizes="<?= $enc->attr( $this->config( 'client/html/common/imageset-sizes', '(min-width: 260px) 240px, 100vw' ) ) ?>"
										alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ) ?>"
									>
								<?php endforeach ?>

							</a>

						<?php endif ?>

					</div>

				<?php endif ?>

			</div>
		<?php endif ?>
	<?php endforeach ?>
</div>
