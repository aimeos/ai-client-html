/**
 * Catalog list actions
 */
AimeosCatalogLists = {

	/**
	 * Marks products as pinned
	 */
	setPinned() {

		$('.catalog-list-items').each((idx, el) => {
			$('.product .btn-pin', el).removeClass('active');

			for(id in $(el).data('pinned')) {
				$('.product[data-prodid="' + id + '"] .btn-pin', el).addClass('active');
			}
		});
	},


	/**
	 * Add to basket
	 */
	onAddBasket() {

		$(".catalog-list-items:not(.list) .product").on("click", ".btn-primary", ev => {
			const target = $(ev.currentTarget).closest(".product");

			if($(".basket .items-selection .selection li, .basket .items-attribute .selection li", target).length) {

				const node = target.clone();

				$("[id]", node).each((idx, el) => {
					el.setAttribute("id", el.getAttribute("id") + '-2');
				});

				$("[for]", node).each((idx, el) => {
					el.setAttribute("for", el.getAttribute("for") + '-2');
				});

				$("form.basket", node).on("click", ".btn-primary", (ev) => {
					const form = $(ev.currentTarget).closest("form.basket");

					fetch(form.attr("action"), {
						body: new FormData(form[0]),
						method: 'POST'
					}).then(response => {
						return response.text();
					}).then(data => {
						Aimeos.createContainer(AimeosBasket.updateBasket(data));
					});

					return false;
				});

				Aimeos.createOverlay();
				Aimeos.createContainer($('<div class="catalog-list catalog-list-items list">').append(node));
				return false;
			}
		});
	},


	/**
	 * Enables infinite scroll if available
	 */
	onScroll() {

		const url = $('.catalog-list-items').data('infiniteurl');

		if(url) {

			const scroll = function() {

				const list = $('.catalog-list-items');
				const infiniteUrl = list.data('infiniteurl');

				if(infiniteUrl && list.length && list[0].getBoundingClientRect().bottom < window.innerHeight * 3) {
					list.data('infiniteurl', '');

					fetch(infiniteUrl).then(response => {
						return response.text();
					}).then(data => {
						const nextPage = $('<html/>').html(data);
						const newList = $('.catalog-list-items', nextPage);
						const ids = newList.data('pinned') || {};

						$('.product', newList).each((idx, node) => {
							ids[node.dataset.prodid] ? $('.btn-pin', node).addClass('active') : null;
							list.append(node);
						});

						$('head .items-stock', nextPage).each((idx, node) => {
							$(document.head).append($('<script/>').attr('src', $(node).attr('src')));
						});

						list.data('infiniteurl', newList.data('infiniteurl'));
						$(window).trigger('scroll');
						Aimeos.loadImages();
					});
				}
			};

			$(window).on('scroll', scroll);
			scroll();
		}
	},


	/**
	 * Add products to pinned list
	 */
	onPin() {

		$("body").on("click", ".catalog-list-items .product .btn-pin", ev => {

			const el = $(ev.currentTarget);
			const url = el.hasClass('active') ? el.data('rmurl') : el.attr('href');

			if(url) {
				const form = new FormData();
				const csrf = el.closest('form').find('.csrf-token');

				form.append(csrf.attr('name'), csrf.attr('value'));
				el.toggleClass('active');

				fetch(url, {
					method: 'POST',
					body: form
				}).then(response => {
					return response.text();
				}).then(data => {
					const doc = $('<html/>').html(data);
					const pinned = $(".catalog-session-pinned", doc);

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
	init() {
		this.setPinned();
		this.onAddBasket();
		this.onScroll();
		this.onPin();
	}
};


$(function() {
	AimeosCatalogLists.init();
});
