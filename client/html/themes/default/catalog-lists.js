/**
 * Catalog list actions
 */
AimeosCatalogList = {

	/**
	 * Add to basket
	 */
	setupAddBasket: function() {

		$(".catalog-list-items:not(.list) .product").on("click", ".btn-primary", function() {

			var empty = true;
			var target = $(this).parents(".product");

			$(".basket .items-selection .selection li, .basket .items-attribute .selection li", target).each(function() {
				if($(this).length) {
					empty = false; return false;
				}
			});

			if(!empty) {
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
				Aimeos.createContainer($('<div class="catalog-list catalog-list-items">')
					.append($('<div class="list-items list">').append(target)) );
				return false;
			}
		});
	},


	/**
	 * Enables infinite scroll if available
	 */
	setupInfiniteScroll: function() {

		var url = document.querySelector('.catalog-list-items').dataset['infiniteurl'];

		if( typeof url === "string" && url != '' ) {

			$(window).on('scroll', function() {

				var list = document.querySelector('.catalog-list-items');
				var infiniteUrl = list.dataset['infiniteurl'];

				if(infiniteUrl && list.getBoundingClientRect().bottom < window.innerHeight * 3) {
					list.dataset['infiniteurl'] = '';

					fetch(infiniteUrl).then(response => {
						return response.text();
					}).then(data => {
						var nextPage = document.createElement("html");
						nextPage.innerHTML = data;

						list.dataset['infiniteurl'] = nextPage.querySelector('.catalog-list-items').dataset['infiniteurl'];

						nextPage.querySelectorAll('.catalog-list-items .product').forEach(el => {
							list.append(el);
						});

						nextPage.querySelectorAll('head .items-stock').forEach(el => {
							var script = document.createElement('script');
							script.src = el.getAttribute("src");
							document.head.appendChild(script);
						});

						Aimeos.loadImages();
						$(window).trigger('scroll');
					}).catch( function() {
						list.data('infiniteurl', infiniteUrl);
					});
				}
			});
		}
	},


	setupPinned: function() {

		$(".catalog-list-items .product").on("click", ".btn-pin", function(ev) {

			var url;
			var el = $(this);

			if(el.hasClass('active')) {
				el.removeClass('active');
				url = el.data('rmurl');
			} else {
				el.addClass('active');
				url = el.attr('href');
			}

			if(!url) {
				return true;
			}

			fetch(url).then(response => {
				return response.text();
			}).then(data => {
				var doc = document.createElement("html");
				doc.innerHTML = data;

				var pinned = $(".catalog-session-pinned", doc);
				if(pinned) {
					$('.catalog-session-pinned').replaceWith(pinned);
				}
			});

			return false;
		});
	},


	/**
	 * Initializes the catalog list actions
	 */
	init: function() {
		this.setupAddBasket();
		this.setupInfiniteScroll();
		this.setupPinned();
	}
};


$(function() {
	AimeosCatalogList.init();
});