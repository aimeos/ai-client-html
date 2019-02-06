<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$seenList = $this->get( 'seenItems', [] );

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
 * @category Developer
 * @see client/html/catalog/session/pinned/count/enable
 */
$count = $this->config( 'client/html/catalog/session/seen/count/enable', 1 );


?>
<?php $this->block()->start( 'catalog/session/seen' ); ?>
<section class="catalog-session-seen">

	<h2 class="header">
		<?= $this->translate( 'client', 'Last seen' ); ?>
		<?php if( $count ) : ?>
			<span class="count"><?= count( $seenList ); ?></span>
		<?php endif; ?>
	</h2>

	<ul class="seen-items">
		<?php foreach( $seenList as $seen ) : ?>
			<li class="seen-item">
				<?= $seen; ?>
			</li>
		<?php endforeach; ?>
	</ul>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/session/seen' ); ?>
