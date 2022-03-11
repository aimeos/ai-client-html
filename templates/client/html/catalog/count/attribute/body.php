<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

?>
<?php $this->block()->start( 'catalog/count/attribute' ) ?>
// <!--
var attributeCounts = <?= $this->get( 'attributeCountList', map() )->toJson( JSON_FORCE_OBJECT ) ?>;

$( ".catalog-filter-attribute .attribute-lists li.attr-item" ).each( function( index, item ) {
	var itemId = $(item).data( "id" );

	if( attributeCounts[itemId]) {
		$(".attr-name", item).append('&nbsp;' + '<span class="attr-count">' + attributeCounts[itemId] + '</span>');
	} else {
		$(item).addClass("disabled");
	}
});
// -->
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/count/attribute' ) ?>