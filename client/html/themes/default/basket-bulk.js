/**
 * Basket bulk order client actions
 */
AimeosBasketBulk = {

	MIN_INPUT_LEN: 3,
	meta: {},

	/**
	 * Sets up autocompletion for the given node
	 *
	 * @param {object} node
	 */
	autocomplete: function(nodes) {

		nodes.each(function() {
			var node = this;
			autocomplete({
				input: node,
				debounceWaitMs: 200,
				minLength: AimeosBasketBulk.MIN_INPUT_LEN,
				fetch: function(text, update) {

					if(AimeosBasketBulk.meta.resources && AimeosBasketBulk.meta.resources['product']) {
						var params = {};
						var relFilter = {};
						var langid = AimeosBasketBulk.meta.locale && AimeosBasketBulk.meta.locale['locale.languageid'] || '';
						relFilter['index.text:relevance("' + langid + '","' + text + '")'] = 0;

						var filter = {
							filter: {'||': [{'=~': {'product.code': text}}, {'>': relFilter}]},
							include: 'attribute,text,price,product'
						};

						if(AimeosBasketBulk.meta.prefix) {
							params[AimeosBasketBulk.meta.prefix] = filter;
						} else {
							params = filter;
						}

						var url = new URL(AimeosBasketBulk.meta.resources['product']);
						url.search = url.search ? url.search + '&' + window.param(params) : '?' + window.param(params);

						fetch(url).then(response => {
							return response.json();
						}).then(response => {
							var data = [];
							for(var key in (response.data || {})) {
								data = data.concat(AimeosBasketBulk.get(response.data[key], response.included));
							}
							update(data);
						});
					}
				},
				onSelect: function(item) {
					if($(".aimeos.basket-bulk tbody .details .search").last().val() != '') {
							AimeosBasketBulk.add();
					}

					var product = $(node).parent();
					product.find(".productid").val(item.id);
					product.find(".search").val(item.label);

					var row = product.parent();
					row.data('prices', item['prices'] || []);
					row.data('vattributes', item['vattributes'] || []);
					AimeosBasketBulk.update(product.parent());
				}
			});
		});
	},


	/**
	 * Adds a new line to the bulk order form
	 */
	add: function() {

		var line = $("tfoot .prototype").clone();
		var len = $(".aimeos.basket-bulk tbody .details").length;

		AimeosBasketBulk.autocomplete($(".search", line));
		$('[disabled="disabled"]', line).removeAttr("disabled");
		$(".aimeos.basket-bulk tbody").append(line.removeClass("prototype"));

		$('[name]', line).each(function() {
			$(this).attr("name", $(this).attr("name").replace('_idx_', len));
		});
	},


	/**
	 * Deletes lines if clicked on the delete icon
	 */
	delete: function() {

		$(".aimeos.basket-bulk").on("click", ".buttons .delete", function(ev) {
			$(ev.currentTarget).parents(".details").remove();
		});
	},


	/**
	 * Returns the data for the current item
	 *
	 * @param {object} attr JSON:API attribute data of one entry
	 * @param {array} included JSON:API included data array
	 * @param {object} Item with "id", "label" and "prices" property
	 */
	get: function(attr, included) {

		var map = {};
		var rel = attr.relationships || {};

		for(var idx in (included || [])) {
			map[included[idx]['type']] = map[included[idx]['type']] || {};
			map[included[idx]['type']][included[idx]['id']] = included[idx];
		}

		var name = attr['attributes']['product.label'];
		var texts = this.getRef(map, rel, 'text', 'default', 'name');
		var prices = this.getRef(map, rel, 'price', 'default', 'default').sort(function(a, b) {
			return a['attributes']['price.quantity'] - b['attributes']['price.quantity'];
		});

		for(var idx in texts) {
			name = texts[idx]['attributes']['text.content'];
		}

		if(attr['attributes']['product.type'] !== 'select') {
			return [{
				'category': '',
				'id': attr.id,
				'label': attr['attributes']['product.code'] + ': ' + name,
				'prices': prices
			}];
		}


		var result = [];
		var variants = this.getRef(map, rel, 'product', 'default');

		for(var idx in variants) {

			var vrel = variants[idx]['relationships'] || {};
			var vattr = this.getRef(map, vrel, 'attribute', 'variant');
			var vprices = this.getRef(map, vrel, 'price', 'default', 'default');
			var vtexts = this.getRef(map, vrel, 'text', 'default', 'name');
			var vname = variants[idx]['attributes']['product.label'];

			for(var idx in vtexts) {
				vname = vtexts[idx]['attributes']['text.content'];
			}

			result.push({
				'category': name,
				'id': attr.id,
				'label': variants[idx]['attributes']['product.code'] + ': ' + vname,
				'prices': !vprices.length ? prices : vprices.sort(function(a, b) {
					return a['attributes']['price.quantity'] - b['attributes']['price.quantity'];
				}),
				'vattributes': vattr
			})
		}

		return result;
	},


	/**
	 * Returns the attributes for the passed domain, type and listtype
	 *
	 * @param {Object} map
	 * @param {Object} rel
	 * @param {String} domain
	 * @param {String} listtype
	 * @param {String} type
	 */
	getRef: function(map, rel, domain, listtype, type) {

		if(!rel[domain]) {
			return [];
		}

		var list = [];

		for(var idx in (rel[domain]['data'] || [])) {

			var entry = rel[domain]['data'][idx];

			if(map[domain][entry['id']] && map[domain][entry['id']]['attributes']
				&& entry['attributes']['product.lists.type'] === listtype
				&& (!type || map[domain][entry['id']]['attributes'][domain + '.type'] === type)) {

				list.push(map[domain][entry['id']]);
			}
		}

		return list;
	},


	/**
	 * Sets up autocompletion for bulk order form
	 */
	setup: function() {
		var jsonurl = $(".aimeos.basket-bulk[data-jsonurl]").data("jsonurl");

		if(typeof jsonurl === 'undefined' || jsonurl == '') {
			return;
		}

		fetch(jsonurl, {
			method: "OPTIONS",
			header: ["Content-type: application/json"]
		}).then(response => {
			return response.json();
		}).then(options => {
			AimeosBasketBulk.meta = options.meta || {};
		});

		$(".aimeos.basket-bulk").on("click", ".buttons .add", this.add);
		this.autocomplete($(".aimeos.basket-bulk .details .search"));

		$(".aimeos.basket-bulk").on("change", ".details .quantity input", function(ev) {
			AimeosBasketBulk.update($(ev.currentTarget).parents(".details").first());
		});
	},


	/**
	 * Updates the price of the given row element
	 *
	 * @param {DomElement} row HTML DOM node of the table row to update the price for
	 */
	update: function(row) {
		var qty = $(".quantity input", row).val();
		var prices = $(row).data('prices') || [];
		var vattr = $(row).data('vattributes') || [];

		for(var idx in prices) {
			if(prices[idx]['attributes']['price.quantity'] <= qty) {
				var style = {style: 'currency', currency: prices[idx]['attributes']['price.currencyid']};
				var value = Number(prices[idx]['attributes']['price.value']) * qty;
				$(row).find(".price").html(value.toLocaleString(undefined, style));
			}
		}

		var input = $(".product > .attrvarid", row);
		$(".product .vattributes", row).empty();

		for(var idx in vattr) {
			var elem = input.clone();
			elem.attr("name", input.attr("name").replace('_type_', vattr[idx]['attributes']['attribute.type']));
			elem.val(vattr[idx]['attributes']['attribute.id']);
			$(".product .vattributes", row).append(elem);
		}
	},


	/**
	 * Initializes the basket bulk actions
	 */
	init: function() {

		this.setup();
		this.delete();
	}
}


$(function() {
	AimeosBasketBulk.init();
});