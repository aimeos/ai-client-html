/**
 * Basket mini client actions
 */
AimeosBasketMini = {

	/**
	 * Updates the basket mini content using the JSON API
	 */
	update: function() {

		var jsonurl = $(".aimeos.basket-mini[data-jsonurl]").data("jsonurl");

		if(typeof jsonurl === 'undefined' || jsonurl == '') {
			return;
		}

		fetch(jsonurl, {
			method: "OPTIONS",
			headers: {'Content-Type': 'application/json'}
		}).then(response => {
			return response.json();
		}).then(options => {
			fetch(options.meta.resources['basket'], {
				headers: {'Content-Type': 'application/json'}
			}).then(response => {
				return response.json();
			}).then(basket => {
				AimeosBasketMini.updateBasket(basket);
			});
		});
	},


	/**
	 * Updates the basket mini content
	 */
	updateBasket: function(basket) {

		if(!(basket.data && basket.data.attributes)) {
			return;
		}

		var attr = basket.data.attributes;
		var price = Number.parseFloat(attr['order.base.price']);
		var delivery = Number.parseFloat(attr['order.base.costs']);

		var formatter = new Intl.NumberFormat([], {
			currency: attr['order.base.currencyid'],
			style: "currency"
		});

		$(".aimeos .basket-mini-main .value").html(formatter.format(price + delivery));
		$(".aimeos .basket-mini-product .total .price").html(formatter.format(price + delivery));
		$(".aimeos .basket-mini-product .delivery .price").html(formatter.format(delivery));

		if(basket.included) {

			var csrf = '';
			var count = 0;
			var body = $(".aimeos .basket-mini-product .basket-body");
			var prototype = $(".aimeos .basket-mini-product .product.prototype");

			if(basket.meta && basket.meta.csrf) {
				csrf = basket.meta.csrf.name + '=' + basket.meta.csrf.value;
			}

			$(".aimeos .basket-mini-product .product").not(".prototype").remove();

			for(var i=0; i<basket.included.length; i++) {
				var entry = basket.included[i];

				if(entry.type === 'basket/product') {
					var product = prototype.clone();

					product.data("urldata", csrf);
					product.data("url", entry.links && entry.links.self && entry.links.self.href || '');

					$(".name", product).html(entry.attributes['order.base.product.name']);
					$(".quantity", product).html(entry.attributes['order.base.product.quantity']);
					$(".price", product).html(formatter.format(entry.attributes['order.base.product.price']));

					body.append(product.removeClass("prototype"));

					count += Number.parseInt(entry.attributes["order.base.product.quantity"]);
				}
			}

			$(".aimeos .basket-mini-main .quantity").html(count);
		}
	},


	/**
	 * Delete a product without page reload
	 */
	setupBasketDelete: function() {

		$(".aimeos .basket-mini-product").on("click", ".delete", function() {

			fetch($(this).closest(".product").data("url"), {
				method: "DELETE",
				headers: {'Content-Type': 'application/json'}
			}).then(response => {
				return response.json();
			}).then(function(basket) {
				AimeosBasketMini.updateBasket(basket);
			});

			return false;
		});
	},


	/**
	 * Initializes the basket mini actions
	 */
	init: function() {

		this.setupBasketDelete();
	}
};
