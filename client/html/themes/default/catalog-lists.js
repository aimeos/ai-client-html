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

		var url = $('.catalog-list-items').data('infinite-url');

		if( typeof url === "string" && url != '' ) {

			$(window).on('scroll', function() {

				var list = $('.catalog-list-items').first();
				var infiniteUrl = list.data('infinite-url');

				if(infiniteUrl && list[0].getBoundingClientRect().bottom < $(window).height() * 3) {

					list.data('infinite-url', '');

					fetch(infiniteUrl).then(response => {
						return response.text();
					}).then(data => {
						var nextPage = $(data);
						var nextUrl = nextPage.find('.catalog-list-items').data( 'infinite-url' );

						list.append(nextPage.find('.catalog-list-items .product'));
						list.data('infinite-url', nextUrl);

						$(nextPage).filter( function (i,a){ return $(a).is('script.catalog-list-stock-script');}).each( function() {
							var script = document.createElement('script');
							script.src = $(this).attr("src");
							document.head.appendChild(script);
						});
						Aimeos.loadImages();

						$(window).trigger('scroll');
					}).catch( function() {
						list.data('infinite-url', infiniteUrl);
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