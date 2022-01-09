
/**
 * Aimeos detail related Javascript code
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2014-2020
 */


/**
 * Aimeos catalog detail actions
 */
AimeosCatalogDetail = {

	options: null,


	fetchReviews: function(container) {

		var jsonUrl = $(".catalog-detail").data("jsonurl");
		var prodid = $(container).data("productid");

		if(prodid && jsonUrl) {

			this.options = $.ajax({
				url: jsonUrl,
				method: "OPTIONS",
				dataType: "json"
			});

			this.options.done(function(data) {

				if(data && data.meta && data.meta.resources && data.meta.resources.review) {

					var args = {
						filter: {f_refid: prodid},
						sort: "-ctime"
					};
					var params = {};

					if(data.meta.prefix) {
						params[data.meta.prefix] = args;
					} else {
						params = args;
					}

					$.ajax({
						url: data.meta.resources.review,
						method: "GET",
						dataType: "json",
						data: params
					}).done(function(response) {
						AimeosCatalogDetail.addReviews(response, container);
					});


					args['aggregate'] = 'review.rating';

					if(data.meta.prefix) {
						params[data.meta.prefix] = args;
					} else {
						params = args;
					}

					$.ajax({
						url: data.meta.resources.review,
						method: "GET",
						dataType: "json",
						data: params
					}).done(function(response) {

						if(response && response.data) {

							var ratings = $(".rating-dist", container).data("ratings") || 1;

							$.each(response.data, function(idx, entry) {
								var percent = (entry.attributes || 0) * 100 / ratings;
								$("#rating-" + (entry.id || 0)).val(percent).text(percent);
							});
						}
					});
				}
			});
		}
	},


	addReviews: function(response, container) {

		if(response && response.data) {

			var template = $(".review-item.prototype", container);
			var more = $(".review-list .more", container);
			var list = $(".review-items", container);

			$.each(response.data, function(idx, entry) {
				item = AimeosCatalogDetail.updateReview(entry, template);
				list.append(item);

				var height = item.innerHeight();

				$("> *:not(.review-show)", item).each(function() {
					height -= $(this).outerHeight(true);
				});

				if(height >= 0) {
					$(".review-show", item).hide();
				}
			});

			if(response.links && response.links.next) {
				more.attr("href", response.links.next).addClass("show");
			} else {
				more.removeClass("show");
			}
		}
	},


	updateReview: function(entry, template) {

		var response = (entry.attributes['review.response'] || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		var comment = (entry.attributes['review.comment'] || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		var date = new Date(entry.attributes['review.ctime'] || '');
		var cnt = parseInt(entry.attributes['review.rating'], 10);
		var item = template.clone().removeClass("prototype");
		var symbol = $(".review-rating", item).text();

		if(response) {
			$(".review-response", item).html($(".review-response", item).html() + response.replace(/\n+/g, '<br/>'));
		} else {
			$(".review-response", item).remove();
		}

		$(".review-comment", item).html(comment.replace(/\n+/g, '<br/>'));
		$(".review-name", item).text(entry.attributes['review.name'] || '');
		$(".review-rating", item).text(symbol.repeat(cnt || 1));
		$(".review-ctime", item).text(date.toDateString());

		return item;
	},


	/**
	 * Opens the lightbox with big images
	 */
	setupImageLightbox: function() {

		$(".catalog-detail-image").on("click", ".image-single .item", function(ev) {

			var list = [];
			var vwidth = $(window).width();
			var gallery = $(ev.delegateTarget);
			var pswp = $(".pswp", gallery);
			var options = $(gallery).data("options") || {};

			if( pswp.length === 0 ) {
				console.log( 'No element with class .pswp for PhotoSwipe found' );
				return false;
			}

			$(".image-single .item", gallery).each(function(idx, item) {
				var entries = $(item).data("sources");
				var imgurl;

				for(var width in entries) {
					if(width <= vwidth) {
						imgurl = entries[width];
					}
				}

				list.push({
					msrc: $(item).data("image"),
					src: imgurl,
					pid: idx,
					h: 0,
					w: 0
				});
			});

			gallery._photoswipe = new PhotoSwipe(pswp[0], PhotoSwipeUI_Default, list, options);
			gallery._photoswipe.init();

			gallery._photoswipe.listen("imageLoadComplete", function(idx, item) {

				if( item.w === 0 && item.h === 0 ) {
					var imgpreload = new Image();

					imgpreload.onload = function() {
						item.w = this.width;
						item.h = this.height;
						gallery._photoswipe.updateSize(true);
					};

					imgpreload.src = item.src;
				}
			});
		});
	},


	/**
	 * Single and thumbnail image slider
	 */
	setupImageSlider: function() {

		$(".thumbs img").on("click", function(ev) {
			$(ev.currentTarget).closest('.swiffy-slider').each(function() {
				swiffyslider.slideTo(this, $(ev.currentTarget).index())
			});
		});
	},


	/**
	 * Initializes the slide in/out for block prices
	 */
	setupBlockPriceSlider: function() {

		$(".catalog-detail-basket .price-item:not(.price-item:first-of-type)").hide();

		$('.catalog-detail-basket .price-list').on("click", function(ev) {
			$(".price-item:not(.price-item:first-of-type)").each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Initializes the slide in/out for delivery/payment services
	 */
	setupServiceSlider: function() {

		$(".catalog-detail-service .service-list").hide();

		$('.catalog-detail-service').on("click", function(ev) {
			$(".service-list").each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Initializes the slide in/out for additional content
	 */
	setupAdditionalContentSlider: function() {

		$(".catalog-detail-additional .content").hide();

		$(".catalog-detail-additional .additional-box").on("click", ".header", function(ev) {
			$(".content", ev.delegateTarget).slideToggle();
			$(".header", ev.delegateTarget).toggleClass("toggle-js");
		});
	},


	/**
	 * Initializes loading reviews
	 */
	setupReviews: function() {

		var list = document.querySelectorAll('.catalog-detail .reviews');

		if(list.length > 0) {
			if('IntersectionObserver' in window) {
				let observer = new IntersectionObserver(function(entries, observer) {
					for(let entry of entries) {
						if(entry.isIntersecting) {
							observer.unobserve(entry.target);
							AimeosCatalogDetail.fetchReviews(list[0]);
						}
					}
				},{
					threshold: 0.01
				});


				observer.observe(list[0]);
			}
		} else {
			AimeosCatalogDetail.fetchReviews(list[0]);
		}
	},


	/**
	 * Initializes loading more reviews
	 */
	setupReviewLoadMore: function() {

		$(".catalog-detail-additional .reviews").on("click", ".more", function(ev) {

			ev.preventDefault();

			$.ajax({
				url: $(this).attr("href"),
				method: "GET",
				dataType: "json"
			}).done(function(response) {
				if(response && response.data) {
					AimeosCatalogDetail.addReviews(response, ev.delegateTarget);
				}
			});
		});
	},


	/**
	 * Initializes sorting reviews
	 */
	setupReviewsSort: function() {

		$(".catalog-detail-additional .reviews").on("click", ".sort .sort-option", function(ev) {

			ev.preventDefault();

			$.ajax({
				url: $(this).attr("href"),
				method: "GET",
				dataType: "json"
			}).done(function(response) {

				if(response && response.data) {

					var reviews = $(".review-items", ev.delegateTarget);
					var prototype = $(".prototype", reviews);

					reviews.empty().append(prototype);
					AimeosCatalogDetail.addReviews(response, ev.delegateTarget);

					$(".sort-option", ev.delegateTarget).removeClass("active");
					$(ev.currentTarget).addClass("active");
				}
			});

			return false;
		});
	},


	/**
	 * Initializes sorting reviews
	 */
	setupReviewsShow: function() {

		$(".catalog-detail-additional .reviews").on("click", ".review-show", function(ev) {

			$(ev.currentTarget).parents(".review-item").css('max-height', 'fit-content');
			$(ev.currentTarget).hide();
			ev.preventDefault();
		});
	},


	/**
	 * Initializes the catalog detail actions
	 */
	init: function() {

		this.setupImageSlider();
		this.setupImageLightbox();
		this.setupServiceSlider();
		this.setupBlockPriceSlider();
		this.setupAdditionalContentSlider();

		this.setupReviews();
		this.setupReviewsShow();
		this.setupReviewsSort();
		this.setupReviewLoadMore();
	}
};



/**
 * Setup the JS for the catalog detail section
 */
$(function() {
	AimeosCatalogDetail.init();
});
