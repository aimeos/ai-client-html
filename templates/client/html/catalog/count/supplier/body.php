<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2022
 */

?>
<?php $this->block()->start( 'catalog/count/supplier' ) ?>
// <!--
var supplierCounts = <?= $this->get( 'supplierCountList', map() )->toJson( JSON_FORCE_OBJECT ) ?>;

$( ".catalog-filter-supplier .supplier-lists li.attr-item" ).each( function( index, item ) {
	var itemId = $(item).data( "id" );

	if( supplierCounts[itemId] ) {
		$(".attr-name", item).append('&nbsp;' + '<span class="attr-count">' + supplierCounts[itemId] + '</span>');
	} else {
		$(item).addClass( 'disabled' );
	}
});
// -->
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/count/supplier' ) ?>