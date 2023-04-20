<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();

/** client/html/account/favorite/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.09
 * @see client/html/account/favorite/url/controller
 * @see client/html/account/favorite/url/action
 * @see client/html/account/favorite/url/config
 * @see client/html/account/favorite/url/filter
 */

/** client/html/account/favorite/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.09
 * @see client/html/account/favorite/url/target
 * @see client/html/account/favorite/url/action
 * @see client/html/account/favorite/url/config
 * @see client/html/account/favorite/url/filter
 */

/** client/html/account/favorite/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.09
 * @see client/html/account/favorite/url/target
 * @see client/html/account/favorite/url/controller
 * @see client/html/account/favorite/url/config
 * @see client/html/account/favorite/url/filter
 */

/** client/html/account/favorite/url/config
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
 * @see client/html/account/favorite/url/target
 * @see client/html/account/favorite/url/controller
 * @see client/html/account/favorite/url/action
 * @see client/html/account/favorite/url/filter
 */

/** client/html/account/favorite/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/account/favorite/url/target
 * @see client/html/account/favorite/url/controller
 * @see client/html/account/favorite/url/action
 * @see client/html/account/favorite/url/config
 */


?>
<?php if( !$this->get( 'favoriteItems', map() )->isEmpty() ) : ?>

	<div class="section aimeos account-favorite" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<h2 class="header"><?= $this->translate( 'client', 'Favorite products' ) ?></h2>

			<div class="favorite-items product-list">

				<?php foreach( $this->get( 'favoriteItems', map() )->reverse() as $listItem ) : ?>
					<?php if( ( $productItem = $listItem->getRefItem() ) !== null ) : ?>

						<div class="product favorite-item" data-prodid="<?= $enc->attr( $productItem->getId() ) ?>">
							<?php $params = ['fav_action' => 'delete', 'fav_id' => $listItem->getRefId()] + $this->get( 'favoriteParams', [] ) ?>
							<form class="delete" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/favorite/url', $params ) ) ?>">
								<button class="minibutton delete" title="<?= $this->translate( 'client', 'Delete item' ) ?>"></button>
								<?= $this->csrf()->formfield() ?>
							</form>

							<?php $params = ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => ''] ?>
							<a href="<?= $enc->attr( $this->link( 'client/html/catalog/detail/url', $params ) ) ?>">
								<?php $mediaItems = $productItem->getRefItems( 'media', 'default', 'default' ) ?>

								<?php if( $mediaItem = $mediaItems->first() ) : ?>
									<div class="media-item">
										<img loading="lazy"
											sizes="<?= $enc->attr( $this->config( 'client/html/common/imageset-sizes', '(min-width: 260px) 240px, 100vw' ) ) ?>"
											src="<?= $enc->attr( $this->content( $mediaItem->getPreview(), $mediaItem->getFileSystem() ) ) ?>"
											srcset="<?= $enc->attr( $this->imageset( $mediaItem->getPreviews( true ), $mediaItem->getFileSystem() ) ) ?>"
											alt="<?= $enc->attr( $mediaItem->getProperties( 'title' )->first() ?: $mediaItem->getLabel() ) ?>"
										>
									</div>
								<?php else : ?>
									<div class="media-item"></div>
								<?php endif ?>

								<h2 class="name"><?= $enc->html( $productItem->getName(), $enc::TRUST ) ?></h2>
								<div class="price-list">
									<?= $this->partial(
										$this->config( 'client/html/common/partials/price', 'common/partials/price' ),
										array( 'prices' => $productItem->getRefItems( 'price', null, 'default' ) )
									) ?>
								</div>
							</a>
						</div>

					<?php endif ?>
				<?php endforeach ?>

			</div>

			<?php if( $this->get( 'favoritePageLast', 1 ) > 1 ) : ?>

				<nav class="pagination">
					<div class="sort">
						<span>&nbsp;</span>
					</div>
					<div class="browser">

						<?php $params = array( 'fav_page' => $this->favoritePageFirst ) + $this->get( 'favoriteParams', [] ) ?>
						<a class="first" href="<?= $enc->attr( $this->link( 'client/html/account/favorite/url' ) ) ?>">
							<?= $enc->html( $this->translate( 'client', '◀◀' ), $enc::TRUST ) ?>
						</a>

						<?php $params = array( 'fav_page' => $this->favoritePagePrev ) + $this->get( 'favoriteParams', [] ) ?>
						<a class="prev" href="<?= $enc->attr( $this->link( 'client/html/account/favorite/url' ) ) ?>" rel="prev">
							<?= $enc->html( $this->translate( 'client', '◀' ), $enc::TRUST ) ?>
						</a>

						<span>
							<?= $enc->html( sprintf(
								$this->translate( 'client', 'Page %1$d of %2$d' ),
								$this->get( 'favoritePageCurr', 1 ),
								$this->get( 'favoritePageLast', 1 )
							) ) ?>
						</span>

						<?php $params = array( 'fav_page' => $this->favoritePageNext ) + $this->get( 'favoriteParams', [] ) ?>
						<a class="next" href="<?= $enc->attr( $this->link( 'client/html/account/favorite/url' ) ) ?>" rel="next">
							<?= $enc->html( $this->translate( 'client', '▶' ), $enc::TRUST ) ?>
						</a>

						<?php $params = array( 'fav_page' => $this->favoritePageLast ) + $this->get( 'favoriteParams', [] ) ?>
						<a class="last" href="<?= $enc->attr( $this->link( 'client/html/account/favorite/url' ) ) ?>">
							<?= $enc->html( $this->translate( 'client', '▶▶' ), $enc::TRUST ) ?>
						</a>

					</div>
				</nav>

			<?php endif ?>

		</div>
	</div>

<?php endif ?>

