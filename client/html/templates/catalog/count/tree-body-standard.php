<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

?>
<?php $this->block()->start( 'catalog/count/tree' ); ?>
// <!--
var categoryCounts = <?= json_encode( $this->get( 'treeCountList', [] ), JSON_FORCE_OBJECT ); ?>;

$( ".catalog-filter-count li.cat-item" ).each( function( index, item ) {
	var itemId = $(item).data( "id" );

	$("> a.cat-item", item).append( function() {
		if( categoryCounts[itemId] ) {
			return ' <span class="cat-count">' + categoryCounts[itemId] + '</span>';
		}

		$(item).addClass( 'disabled' );
	});
});
// -->
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/count/tree' ); ?>
