<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


/** client/html/catalog/session/seen/count/enable
 * Displays the number of last seen products in the header of the last seen list
 *
 * This configuration option enables or disables displaying the total number
 * of last seen products in the header of section. This increases the usability
 * if more than the shown products are available in the list but this depends on
 * the design of the site.
 *
 * @param integer Zero to disable the counter, one to enable it
 * @since 2014.09
 * @see client/html/catalog/session/pinned/count/enable
 */


?>
<?php $this->block()->start( 'catalog/session/seen' ) ?>
<section class="catalog-session-seen">
	<div class="container-xxl">
		<h2 class="header">
			<?= $this->translate( 'client', 'Last seen' ) ?>
			<?php if( $this->config( 'client/html/catalog/session/seen/count/enable', true ) ) : ?>
				<span class="count"><?= count( $this->get( 'seenItems', [] ) ) ?></span>
			<?php endif ?>
		</h2>

		<ul class="seen-items">
			<?php foreach( $this->get( 'seenItems', [] ) as $seen ) : ?>
				<li class="seen-item product">
					<?= $seen ?>
				</li>
			<?php endforeach ?>
		</ul>
	</div>
</section>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/session/seen' ) ?>
