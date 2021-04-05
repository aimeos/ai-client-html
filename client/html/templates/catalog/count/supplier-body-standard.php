<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

?>
<?php $this->block()->start( 'catalog/count/supplier' ) ?>
// <!--
var supplierCounts = <?= $this->get( 'supplierCountList', map() )->toJson( JSON_FORCE_OBJECT ) ?>;

$( ".catalog-filter-supplier .supplier-lists li.attr-item" ).each( function( index, item ) {
	$(item).append( function() {
		var itemId = $(this).data( "id" );

		if( supplierCounts[itemId] ) {
			var node = document.createElement( 'span' );
			node.appendChild( document.createTextNode( supplierCounts[itemId] ) );
			$(node).addClass( 'attr-count' );
			return node;
		}

		$(this).addClass( 'disabled' );
	});
});
// -->
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/count/supplier' ) ?>