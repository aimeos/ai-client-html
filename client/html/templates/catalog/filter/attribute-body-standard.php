<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
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
 * @category User
 * @category Developer
 */

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );

$attrIds = array_filter( $this->param( 'f_attrid', [] ) );
$optIds = array_filter( $this->param( 'f_optid', [] ) );
$oneIds = array_filter( $this->param( 'f_oneid', [] ) );


?>
<?php $this->block()->start( 'catalog/filter/attribute' ) ?>
<?php if( !empty( $this->get( 'attributeMap', [] ) ) ) : ?>
	<section class="catalog-filter-attribute col">
		<h2><?= $enc->html( $this->translate( 'client', 'Attributes' ), $enc::TRUST ) ?></h2>

		<div class="attribute-lists">

			<?php if( array_merge( $attrIds, $optIds, $oneIds ) !== [] ) : ?>
				<a class="btn btn-secondary attribute-selected" href="<?= $enc->attr( $this->url( $listTarget, $listController, $listAction, $this->get( 'attributeResetParams', [] ), [], $listConfig ) ) ?>">
					<?= $enc->html( $this->translate( 'client', 'Reset' ), $enc::TRUST ) ?>
				</a>
			<?php endif ?>

			<div class="fieldsets">

				<?php foreach( $this->get( 'attributeMap', [] ) as $attrType => $attributes ) : ?>
					<?php if( !empty( $attributes ) ) : ?>

						<fieldset class="attr-<?= $enc->attr( $attrType, $enc::TAINT, '-' ) ?>">
							<legend><?= $enc->html( $this->translate( 'client/code', $attrType ), $enc::TRUST ) ?></legend>
							<ul class="attr-list"><!--

								<?php foreach( $attributes as $id => $attribute ) : ?>
									--><li class="attr-item" data-id="<?= $enc->attr( $id ) ?>">

										<input class="attr-item" type="checkbox"
											id="attr-<?= $enc->attr( $id ) ?>"
											value="<?= $enc->attr( $id ) ?>"
											name="<?= $enc->attr( $this->formparam( $attribute->get( 'formparam', [] ) ) ) ?>"
											<?= $attribute->get( 'checked', false ) ? 'checked="checked"' : '' ?>
										/>

										<label class="attr-name" for="attr-<?= $enc->attr( $id ) ?>"><!--
											--><div class="media-list"><!--

												<?php foreach( $attribute->getRefItems( 'media', 'icon', 'default' ) as $mediaItem ) : ?>
													<?= '-->' . $this->partial(
														$this->config( 'client/html/common/partials/media', 'common/partials/media-standard' ),
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
