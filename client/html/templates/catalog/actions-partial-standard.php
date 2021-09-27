<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

/* Available data:
 * - productItem : Product item incl. referenced items
 */

$enc = $this->encoder();


$pinTarget = $this->config( 'client/html/catalog/session/pinned/url/target' );
$pinController = $this->config( 'client/html/catalog/session/pinned/url/controller', 'catalog' );
$pinAction = $this->config( 'client/html/catalog/session/pinned/url/action', 'detail' );
$pinConfig = $this->config( 'client/html/catalog/session/pinned/url/config', [] );

$watchTarget = $this->config( 'client/html/account/watch/url/target' );
$watchController = $this->config( 'client/html/account/watch/url/controller', 'account' );
$watchAction = $this->config( 'client/html/account/watch/url/action', 'watch' );
$watchConfig = $this->config( 'client/html/account/watch/url/config', [] );

$favTarget = $this->config( 'client/html/account/favorite/url/target' );
$favController = $this->config( 'client/html/account/favorite/url/controller', 'account' );
$favAction = $this->config( 'client/html/account/favorite/url/action', 'favorite' );
$favConfig = $this->config( 'client/html/account/favorite/url/config', [] );


/** client/html/catalog/actions/list
 * List of user action names that should be displayed in the catalog detail view
 *
 * Users can add products to several personal lists that are either only
 * available during the session or permanently if the user is logged in. The list
 * of pinned products is session based while the watch list and the favorite
 * products are durable. For the later two lists, the user has to be logged in
 * so the products can be associated to the user account.
 *
 * The order of the action names in the configuration determines the order of
 * the actions on the catalog detail page.
 *
 * @param array List of user action names
 * @since 2017.04
 * @category User
 * @category Developer
 */
$list = $this->config( 'client/html/catalog/actions/list', ['pin', 'watch', 'favorite'] );


?>
<div class="catalog-actions">

	<?php if( in_array( 'pin', $list ) ) : ?>
		<form class="actions-pin" method="POST" action="<?= $enc->attr( $this->url( $pinTarget, $pinController, $pinAction, [], $pinConfig ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'pin_action' ) ?>" value="add" />
			<input type="hidden" name="<?= $this->formparam( 'pin_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>" />
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>" />
			<button class="actions-button actions-button-pin" title="<?= $enc->attr( $this->translate( 'client/code', 'pin' ) ) ?>"></button>
		</form>
	<?php endif ?>

	<?php if( in_array( 'watch', $list ) ) : ?>
		<form class="actions-watch" method="POST" action="<?= $enc->attr( $this->url( $watchTarget, $watchController, $watchAction, [], $watchConfig ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'wat_action' ) ?>" value="add" />
			<input type="hidden" name="<?= $this->formparam( 'wat_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>" />
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>" />
			<button class="actions-button actions-button-watch" title="<?= $enc->attr( $this->translate( 'client/code', 'watch' ) ) ?>"></button>
		</form>
	<?php endif ?>

	<?php if( in_array( 'favorite', $list ) ) : ?>
		<form class="actions-favorite" method="POST" action="<?= $enc->attr( $this->url( $favTarget, $favController, $favAction, [], $favConfig ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'fav_action' ) ?>" value="add" />
			<input type="hidden" name="<?= $this->formparam( 'fav_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>" />
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>" />
			<button class="actions-button actions-button-favorite" title="<?= $enc->attr( $this->translate( 'client/code', 'favorite' ) ) ?>"></button>
		</form>
	<?php endif ?>

</div>
