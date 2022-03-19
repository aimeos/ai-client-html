<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();



/** client/html/catalog/filter/button
 * Displays the "Search" button in the catalog filter if Javascript is disabled
 *
 * Usually the "Search" button is shown in the catalog filter if the browser
 * doesn't support Javascript or the user has disabled Javascript for the site.
 * If you are using parts of the catalog filter to e.g. render a menu, the
 * button shouldn't be displayed at all. This can be explicitely set via this
 * configuration option.
 *
 * @param boolean A value of "1" to enable the button, "0" to disable it
 * @since 2014.03
 */

$linkKey = $this->param( 'f_catid' ) ? 'client/html/catalog/tree/url' : 'client/html/catalog/lists/url';

$attrIds = array_filter( $this->param( 'f_attrid', [] ) );
$optIds = array_filter( $this->param( 'f_optid', [] ) );
$oneIds = array_filter( $this->param( 'f_oneid', [] ) );
$attrMap = $this->get( 'attributeMap', [] );
$params = $this->param();


?>
<?php $this->block()->start( 'catalog/filter/attribute' ) ?>
<?php if( !empty( $attrMap ) ) : ?>
	<section class="catalog-filter-attribute">
		<h2 class="attr-header"><?= $enc->html( $this->translate( 'client', 'Filter' ), $enc::TRUST ) ?></h2>

		<div class="attribute-lists">

			<?php if( array_merge( $attrIds, $optIds, $oneIds ) !== [] ) : ?>

				<div class="attribute-selected">
					<a class="btn reset" href="<?= $enc->attr( $this->link( $linkKey, $this->get( 'attributeResetParams', [] ) ) ) ?>">
						<?= $enc->html( $this->translate( 'client', 'Reset' ), $enc::TRUST ) ?>
					</a>

					<div class="selected">
						<div class="selected-intro"><?= $enc->html( $this->translate( 'client', 'Your choice' ), $enc::TRUST ); ?></div>

						<?php foreach( $attrMap as $attrType => $attributes ) : ?>
							<?php foreach( $attributes as $id => $attribute ) : ?>
								<?php if( ( $key = array_search( $id, $attrIds ) ) !== false ) : ?>
									<?php $current = $params; if( is_array( $current['f_attrid'] ) ) { unset( $current['f_attrid'][$key] ); } ?>
								<?php elseif( ( $key = array_search( $id, $optIds ) ) !== false ) : ?>
									<?php $current = $params; if( is_array( $current['f_optid'] ) ) { unset( $current['f_optid'][$key] ); } ?>
								<?php elseif( isset( $oneIds[$attrType] ) && ( $key = array_search( $id, (array) $oneIds[$attrType] ) ) !== false ) : ?>
									<?php $current = $params; if( is_array( $current['f_oneid'][$attrType] ) ) { unset( $current['f_oneid'][$attrType][$key] ); } ?>
								<?php else : continue; ?>
								<?php endif; ?>

								<a class="minibutton close" href="<?= $enc->attr( $this->link( $linkKey, $attribute->get( 'params', [] ) ) ); ?>">
									<?= $enc->html( $attribute->getName(), $enc::TRUST ); ?>
								</a>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				</div>

			<?php endif; ?>


			<div class="fieldsets">

				<?php foreach( $attrMap as $attrType => $attributes ) : ?>
					<?php if( !empty( $attributes ) ) : ?>

						<fieldset class="attr-sets attr-<?= $enc->attr( $attrType, $enc::TAINT, '-' ) ?>">
							<legend class="attr-type"><?= $enc->html( $this->translate( 'client/code', $attrType ), $enc::TRUST ) ?></legend>
							<ul class="attr-list"><!--

								<?php foreach( $attributes as $id => $attribute ) : ?>
									--><li class="attr-item" data-id="<?= $enc->attr( $id ) ?>">

										<input class="attr-item" type="checkbox"
											id="attr-<?= $enc->attr( $id ) ?>"
											value="<?= $enc->attr( $id ) ?>"
											name="<?= $enc->attr( $this->formparam( $attribute->get( 'formparam', [] ) ) ) ?>"
											<?= $attribute->get( 'checked', false ) ? 'checked="checked"' : '' ?>
										>

										<label class="attr-name" for="attr-<?= $enc->attr( $id ) ?>"><!--
											--><div class="media-list"><!--

												<?php foreach( $attribute->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
													<?= '-->' . $this->partial(
														$this->config( 'client/html/common/partials/media', 'common/partials/media' ),
														array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
													) . '<!--' ?>
												<?php endforeach ?>

											--></div>
											<span><?= $enc->html( $attribute->getName(), $enc::TRUST ) ?></span><!--
										--></label>
									</li><!--

								<?php endforeach ?>
							--></ul>
						</fieldset>

					<?php endif ?>
				<?php endforeach ?>

			</div>
		</div>

		<?php if( $this->config( 'client/html/catalog/filter/button', true ) ) : ?>
			<noscript>
				<button class="filter btn btn-primary" type="submit">
					<?= $enc->html( $this->translate( 'client', 'Show' ), $enc::TRUST ) ?>
				</button>
			</noscript>
		<?php endif ?>

	</section>
<?php endif ?>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/attribute' ) ?>
