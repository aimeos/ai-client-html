<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

?>
<?php $this->block()->start( 'catalog/count/tree' ); ?>
// <!--
var level = <?= $this->config( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE ) ?>;
var catalogCounts = <?= json_encode( $this->get( 'treeCountList', [] ), JSON_FORCE_OBJECT ); ?>;

$(".catalog-filter-count ul.level-0 > li.cat-item").each(function(index, item) {

	var traverse = function(item) {
		var id = $(item).data("id");
		var count = parseInt(catalogCounts[id]) || 0;

		$("> ul > li.cat-item", item).each(function(idx, node){
			count += traverse(node);
		});

		if(count > 0) {
			$("> a.cat-item", item).append(function() {
				return '<span class="cat-count">' + count + '</span>';
			});
		} else if($(item).hasClass("nochild")) {
			$(item).addClass("disabled");
		}

		return level == 3 ? count : 0;
	};

	traverse(item);
});
// -->
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/count/tree' ); ?>
