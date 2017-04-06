<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/** client/html/common/content/baseurl
 * Path to the content directory or content base URL to a server
 *
 * This option must point to the base path or URL of the used content (images,
 * downloadable files, etc.).
 *
 * If you use an absolute URL prefer the https protocol to avoid issues with mixed
 * content. Browsers avoid to load files via http if the page was retrieved via
 * https.
 *
 * @param string Relative path or absolute URL
 * @since 2014.03
 * @see client/html/common/content/baseurl
 */
$contentUrl = $this->config( 'client/html/common/content/baseurl' );

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
 * @category User
 * @category Developer
 * @see client/html/catalog/filter/attribute/types
 * @see client/html/catalog/filter/attribute/types-oneof
 */
$options = $this->config( 'client/html/catalog/filter/attribute/types-option', [] );

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
 * @category User
 * @category Developer
 * @see client/html/catalog/filter/attribute/types
 * @see client/html/catalog/filter/attribute/types-option
 */
$oneof = $this->config( 'client/html/catalog/filter/attribute/types-oneof', [] );

/** client/html/catalog/filter/standard/button
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
$button = $this->config( 'client/html/catalog/filter/standard/button', true );

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );

$attrMap = $this->get( 'attributeMap', [] );
$attrIds = $this->param( 'f_attrid', [] );
$oneIds = $this->param( 'f_oneid', [] );
$optIds = $this->param( 'f_optid', [] );
$params = $this->param();


?>
<?php $this->block()->start( 'catalog/filter/attribute' ); ?>
<section class="catalog-filter-attribute">

	<?php if( !empty( $attrMap ) ) : ?>

		<h2><?php echo $enc->html( $this->translate( 'client', 'Attributes' ), $enc::TRUST ); ?></h2>


		<?php if( !empty( $attrIds ) || !empty( $optIds ) ) : ?>

			<div class="attribute-selected">
				<span class="selected-intro"><?php echo $enc->html( $this->translate( 'client', 'Your choice' ), $enc::TRUST ); ?></span>

				<ul class="attr-list">
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
							<li class="attr-item">
								<a class="attr-name" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $current, [], $listConfig ) ); ?>">
									<?php echo $enc->html( $attribute->getName(), $enc::TRUST ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>

				<?php if( count( $attrIds ) > 1 || count( $optIds ) > 1 || count( $oneIds ) > 1 ) : ?>
					<?php $current = $params; unset( $current['f_attrid'],  $current['f_optid'],  $current['f_oneid'] ); ?>
					<a class="selected-all" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $current, [], $listConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'client', 'clear all' ), $enc::TRUST ); ?>
					</a>
				<?php endif; ?>
			</div>

		<?php endif; ?>


		<div class="attribute-lists"><!--

			<?php foreach( $attrMap as $attrType => $attributes ) : ?>
				<?php if( !empty( $attributes ) ) : ?>

					--><fieldset class="attr-<?php echo $enc->attr( $attrType, $enc::TAINT, '-' ); ?>">
						<legend><?php echo $enc->html( $this->translate( 'client/code', $attrType ), $enc::TRUST ); ?></legend>
						<ul class="attr-list"><!--

							<?php $fparam = ( in_array( $attrType, $oneof ) ? array( 'f_oneid', $attrType, '' ) : ( in_array( $attrType, $options ) ? array( 'f_optid', '' ) : array( 'f_attrid', '' ) ) ); ?>
							<?php foreach( $attributes as $id => $attribute ) : ?>

								--><li class="attr-item" data-id="<?php echo $enc->attr( $id ); ?>">

									<input class="attr-item" type="checkbox"
										id="attr-<?php echo $enc->attr( $id ); ?>"
										name="<?php echo $enc->attr( $this->formparam( $fparam ) ); ?>"
										value="<?php echo $enc->attr( $id ); ?>"
										<?php echo ( in_array( $id, $attrIds ) || in_array( $id, $optIds ) || isset( $oneIds[$attrType] ) && in_array( $id, (array) $oneIds[$attrType] ) ? 'checked="checked"' : '' ); ?>
									/>

									<label class="attr-name" for="attr-<?php echo $enc->attr( $id ); ?>"><!--
										--><div class="media-list"><!--

											<?php foreach( $attribute->getListItems( 'media', 'icon' ) as $listItem ) : ?>
												<?php if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
													<?php echo '-->' . $this->partial(
														$this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ),
														array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) )
													) . '<!--'; ?>
												<?php endif; ?>
											<?php endforeach; ?>

										--></div>
										<span><?php echo $enc->html( $attribute->getName(), $enc::TRUST ); ?></span><!--
									--></label>
								</li><!--

							<?php endforeach; ?>
						--></ul>
					</fieldset><!--

				<?php endif; ?>
			<?php endforeach; ?>

		--></div>

	<?php endif; ?>


	<?php if( $button ) : ?>
		<noscript>
			<button class="filter standardbutton btn-action" type="submit">
				<?php echo $enc->html( $this->translate( 'client', 'Show' ), $enc::TRUST ); ?>
			</button>
		</noscript>
	<?php endif; ?>

</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/filter/attribute' ); ?>
