<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

?>
<?php $this->block()->start( 'catalog/count/tree' ) ?>
// <!--
var catalogCounts = <?= $this->get( 'treeCountList', map() )->toJson( JSON_FORCE_OBJECT ) ?>;

$(".catalog-filter-count .cat-item").each(function(index, item) {
	var id = $(item).data("id");

	$("> a.cat-item", item).append(function() {
		if( catalogCounts[id] ) {
			return '<span class="cat-count">' + catalogCounts[id] + '</span>';
		} else if($(item).hasClass("nochild")) {
			$(item).addClass("disabled");
		}
	});
});
// -->
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/count/tree' ) ?>
