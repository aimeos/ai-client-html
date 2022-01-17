/**
 * Catalog list actions
 */
AimeosCatalogLists = {

	/**
	 * Marks products as pinned
	 */
	setPinned: function() {

		$('.catalog-list-items').each(function() {
			$('.product .btn-pin', this).removeClass('active');

			for(id in $(this).data('pinned')) {
				$('.product[data-prodid="' + id + '"] .btn-pin', this).addClass('active');
			}
		});
	},


	/**
	 * Add to basket
	 */
	onAddBasket: function() {

		$(".catalog-list-items:not(.list) .product").on("click", ".btn-primary", function() {
			var target = $(this).parents(".product").clone();

			if($(".basket .items-selection .selection li, .basket .items-attribute .selection li", target).length) {
				$("form.basket", target).on("click", ".btn-primary", function(ev) {
					var form = $(ev.currentTarget).closest("form.basket");
					fetch(form.attr("action"), {
						body: new FormData(form[0]),
						method: 'POST'
					}).then(function(response) {
						return response.text();
					}).then(function(data) {
						Aimeos.createContainer(AimeosBasket.updateBasket(data));
					});

					return false;
				});

				Aimeos.createOverlay();
				Aimeos.createContainer($('<div class="catalog-list catalog-list-items list">').append(target));
				return false;
			}
		});
	},


	/**
	 * Enables infinite scroll if available
	 */
	onScroll: function() {

		var url = $('.catalog-list-items').data('infiniteurl');

		if(url) {

			$(window).on('scroll', function() {

				var list = $('.catalog-list-items');
				var infiniteUrl = list.data('infiniteurl');

				if(infiniteUrl && list.length && list[0].getBoundingClientRect().bottom < window.innerHeight * 3) {
					list.data('infiniteurl', '');

					fetch(infiniteUrl).then(response => {
						return response.text();
					}).then(data => {
						var nextPage = $('<html/>').html(data);
						var newList = $('.catalog-list-items', nextPage);
						var ids = newList.data('pinned') || {};

						$('.product', newList).each((idx, node) => {
							ids[node.dataset.prodid] ? $('.btn-pin', node).addClass('active') : null;
							list.append(node);
						});

						$('head .items-stock', nextPage).each(() => {
							$(document.head).append($('<script/>').attr('src', $(this).attr('src')));
						});

						list.data('infiniteurl', newList.data('infiniteurl'));
						$(window).trigger('scroll');
						Aimeos.loadImages();
					});
				}
			});
		}
	},


	onPin: function() {

		$("body").on("click", ".catalog-list-items .product .btn-pin", function() {

			var el = $(this);
			var url = el.hasClass('active') ? el.data('rmurl') : el.attr('href');

			if(url) {
				var form = new FormData();
				var csrf = el.closest('form').find('.csrf-token');

				form.append(csrf.attr('name'), csrf.attr('value'));
				el.toggleClass('active');

				fetch(url, {
					method: 'POST',
					body: form
				}).then(response => {
					return response.text();
				}).then(data => {
					var doc = $('<html/>').html(data);
					var pinned = $(".catalog-session-pinned", doc);

					if(pinned.length) {
						$('.catalog-session-pinned').replaceWith(pinned);
					}
				});

				return false;
			}
		});
	},


	/**
	 * Initializes the catalog list actions
	 */
	init: function() {
		this.setPinned();
		this.onAddBasket();
		this.onScroll();
		this.onPin();
	}
};


$(function() {
	AimeosCatalogLists.init();
});
