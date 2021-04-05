<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

?>
<?php $this->block()->start( 'catalog/count/attribute' ) ?>
// <!--
var attributeCounts = <?= $this->get( 'attributeCountList', map() )->toJson( JSON_FORCE_OBJECT ) ?>;

$( ".catalog-filter-attribute .attribute-lists li.attr-item" ).each( function( index, item ) {
	$(item).append( function() {
		var itemId = $(this).data( "id" );

		if( attributeCounts[itemId] ) {
			var node = document.createElement( 'span' );
			node.appendChild( document.createTextNode( attributeCounts[itemId] ) );
			$(node).addClass( 'attr-count' );
			return node;
		}

		$(this).addClass( 'disabled' );
	});
});
// -->
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/count/attribute' ) ?>