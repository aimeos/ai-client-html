<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 */

$enc = $this->encoder();


/** client/html/account/watch/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.09
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/config
 * @see client/html/account/watch/url/filter
 */

/** client/html/account/watch/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.09
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/config
 * @see client/html/account/watch/url/filter
 */

/** client/html/account/watch/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.09
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/config
 * @see client/html/account/watch/url/filter
 */

/** client/html/account/watch/url/config
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
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/filter
 */

/** client/html/account/watch/url/filter
 * Removes parameters for the detail page before generating the URL
 *
 * This setting removes the listed parameters from the URLs. Keep care to
 * remove no required parameters!
 *
 * @param array List of parameter names to remove
 * @since 2022.10
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/config
 */


?>
<?php if( !$this->get( 'watchItems', map() )->isEmpty() ) : ?>

	<div class="section aimeos account-watch" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">
		<div class="container-xxl">

			<h2 class="header"><?= $this->translate( 'client', 'Watched products' ) ?></h2>

			<div class="watch-items">
				<?php foreach( $this->get( 'watchItems', map() )->reverse() as $listItem ) : ?>
					<?php if( ( $productItem = $listItem->getRefItem() ) !== null ) : ?>

						<div class="product watch-item" data-prodid="<?= $enc->attr( $productItem->getId() ) ?>">
							<?php $params = ['wat_action' => 'delete', 'wat_id' => $listItem->getRefId()] + $this->get( 'watchParams', [] ) ?>
							<form class="delete" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/watch/url', $params ) ) ?>">
								<button class="minibutton delete" title="<?= $this->translate( 'client', 'Delete item' ) ?>"></button>
								<?= $this->csrf()->formfield() ?>
							</form>

							<?php $name = $productItem->getName( 'url' ) ?>
							<?php $params = ['path' => $name, 'd_name' => $name, 'd_prodid' => $productItem->getId(), 'd_pos' => ''] ?>
							<a class="watch-basic" href="<?= $enc->attr( $this->link( 'client/html/catalog/detail/url', $params ) ) ?>">
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

							<?php $url = $this->link( 'client/html/account/watch/url', $this->get( 'watchParams', [] ) ) ?>
							<form class="watch-details" method="POST" action="<?= $enc->attr( $url ) ?>">
								<input type="hidden" name="<?= $enc->attr( $this->formparam( array( 'wat_action' ) ) ) ?>" value="edit">
								<input type="hidden" name="<?= $enc->attr( $this->formparam( array( 'wat_id' ) ) ) ?>" value="<?= $enc->attr( $listItem->getRefId() ) ?>">
								<?= $this->csrf()->formfield() ?>

								<ul class="form-list">
									<?php $config = $listItem->getConfig() ?>

									<?php $timeframe = ( isset( $config['timeframe'] ) ? (int) $config['timeframe'] : 7 ) ?>
									<li class="form-item timeframe">
										<label for="watch-timeframe"><?= $enc->html( $this->translate( 'client', 'Time frame' ), $enc::TRUST ) ?></label><!--
										--><select id="watch-timeframe" name="<?= $enc->attr( $this->formparam( array( 'wat_timeframe' ) ) ) ?>">
											<option value="7" <?= ( $timeframe == 7 ? 'selected="selected"' : '' ) ?>>
												<?= $enc->html( $this->translate( 'client', 'One week' ) ) ?>
											</option>
											<option value="14" <?= ( $timeframe == 14 ? 'selected="selected"' : '' ) ?>>
												<?= $enc->html( $this->translate( 'client', 'Two weeks' ) ) ?>
											</option>
											<option value="30" <?= ( $timeframe == 30 ? 'selected="selected"' : '' ) ?>>
												<?= $enc->html( $this->translate( 'client', 'One month' ) ) ?>
											</option>
											<option value="90" <?= ( $timeframe == 90 ? 'selected="selected"' : '' ) ?>>
												<?= $enc->html( $this->translate( 'client', 'Three month' ) ) ?>
											</option>
										</select>
									</li>

									<?php $price = ( isset( $config['price'] ) ? (int) $config['price'] : 0 ) ?>
									<li class="form-item price">
										<label for="watch-price"><?= $enc->html( $this->translate( 'client', 'If price decreases' ), $enc::TRUST ) ?></label><!--
										--><input type="checkbox"
											name="<?= $enc->attr( $this->formparam( array( 'wat_price' ) ) ) ?>"
											id="watch-price"
											value="1"
											<?= ( $price ? 'checked="checked"' : '' ) ?>
										>
										<input type="hidden"
											name="<?= $enc->attr( $this->formparam( array( 'wat_pricevalue' ) ) ) ?>"
											value="<?= $enc->attr( ( $priceItem = $productItem->getRefItems( 'price', null, 'default' )->first() ) !== null ? $priceItem->getValue() : '0.00' ) ?>"
										>
									</li>

									<?php $stock = ( isset( $config['stock'] ) ? (int) $config['stock'] : 0 ) ?>
									<li class="form-item stock">
										<label for="watch-stock"><?= $enc->html( $this->translate( 'client', 'If back in stock' ), $enc::TRUST ) ?></label><!--
											--><input type="checkbox"
												name="<?= $enc->attr( $this->formparam( array( 'wat_stock' ) ) ) ?>"
												id="watch-stock"
												value="1"
												<?= ( $stock ? 'checked="checked"' : '' ) ?>
											>
									</li>
								</ul>

								<button class="btn btn-primary btn-action"><?= $enc->html( $this->translate( 'client', 'Watch' ), $enc::TRUST ) ?></button>
							</form>

						</div>

					<?php endif ?>
				<?php endforeach ?>
			</div>


			<?php if( $this->get( 'watchPageLast', 1 ) > 1 ) : ?>
				<nav class="pagination">
					<div class="sort">
						<span>&nbsp;</span>
					</div>
					<div class="browser">

						<?php $params = array( 'wat_page' => $this->watchPageFirst ) + $this->get( 'watchParams', [] ) ?>
						<a class="first" href="<?= $enc->attr( $this->link( 'client/html/account/watch/url', $params ) ) ?>">
							<?= $enc->html( $this->translate( 'client', '◀◀' ), $enc::TRUST ) ?>
						</a>

						<?php $params = array( 'wat_page' => $this->watchPagePrev ) + $this->get( 'watchParams', [] ) ?>
						<a class="prev" href="<?= $enc->attr( $this->link( 'client/html/account/watch/url', $params ) ) ?>" rel="prev">
							<?= $enc->html( $this->translate( 'client', '◀' ), $enc::TRUST ) ?>
						</a>

						<span>
							<?= $enc->html( sprintf(
								$this->translate( 'client', 'Page %1$d of %2$d' ),
								$this->get( 'watchPageCurr', 1 ),
								$this->get( 'watchPageLast', 1 )
							) ) ?>
						</span>

						<?php $params = array( 'wat_page' => $this->watchPageNext ) + $this->get( 'watchParams', [] ) ?>
						<a class="next" href="<?= $enc->attr( $this->link( 'client/html/account/watch/url', $params ) ) ?>" rel="next">
							<?= $enc->html( $this->translate( 'client', '▶' ), $enc::TRUST ) ?>
						</a>

						<?php $params = array( 'wat_page' => $this->watchPageLast ) + $this->get( 'watchParams', [] ) ?>
						<a class="last" href="<?= $enc->attr( $this->link( 'client/html/account/watch/url', $params ) ) ?>">
							<?= $enc->html( $this->translate( 'client', '▶▶' ), $enc::TRUST ) ?>
						</a>
					</div>
				</nav>
			<?php endif ?>

		</div>
	</div>

<?php endif ?>
