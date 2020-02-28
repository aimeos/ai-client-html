<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
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

$urls = array(
	'pin' => $this->url( $pinTarget, $pinController, $pinAction, ['pin_action' => 'add', 'pin_id' => $this->productItem->getId(), 'd_name' => $this->productItem->getName( 'url' )], $pinConfig ),
	'watch' => $this->url( $watchTarget, $watchController, $watchAction, ['wat_action' => 'add', 'wat_id' => $this->productItem->getId(), 'd_name' => $this->productItem->getName( 'url' )], $watchConfig ),
	'favorite' => $this->url( $favTarget, $favController, $favAction, ['fav_action' => 'add', 'fav_id' => $this->productItem->getId(), 'd_name' => $this->productItem->getName( 'url' )], $favConfig ),
);


?>
<div class="catalog-actions">
	<?php foreach( $this->config( 'client/html/catalog/actions/list', ['pin', 'watch', 'favorite'] ) as $entry ) : ?>
		<?php if( isset( $urls[$entry] ) ) : ?>
			<a class="actions-button actions-button-<?= $enc->attr( $entry ); ?>" href="<?= $enc->attr( $urls[$entry] ); ?>" title="<?= $enc->attr( $this->translate( 'client/code', $entry ) ); ?>"></a>

		<?php endif; ?>
	<?php endforeach; ?>
</div>
