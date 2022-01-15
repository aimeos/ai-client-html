/**
 * Aimeos common catalog actions
 */
 AimeosCatalog = {

	/**
	 * Evaluates the product variant dependencies.
	 *
	 * It does not only work with <select> and <option> tags but also if a
	 *
	 * <div class="select-list" data-index="<index value: 0-31>"> and
	 *
	 * <input class="select-option" type="radio"> or
	 * <input class="select-option" type="checkbox">
	 *
	 * are used. The data-index attribute of the .select-list container is
	 * required to calculate the disabled attributes for each option according
	 * to its dependencies. It must start with "0" and an unique, ascending value
	 * must be assigned to each container. The maximum number of possible indexes
	 * (and therefore dependent containers within an .selection node) is 31
	 * because it's an integer bitmap.
	 */
	setupSelectionDependencies: function() {

		$(".catalog-detail-basket-selection .selection, .catalog-list-items .items-selection .selection").on("change", ".select-list", function() {

			var node = this;
			var el = $(this);
			var index = el.data("index");
			var target = el.parents(".selection");
			var value = el.find(".select-option:checked").val();

			var attrDeps = target.data("attrdeps") || {}; // {"<attrid>":["prodid",...],...}
			var prodDeps = target.data("proddeps") || {}; // {"<prodid>":["attrid",...],...}
			var attrMap = {};

			if( typeof index === "undefined" ) {
				throw new Error( "HTML select node has no attribute data-index" );
			}


			// Retrieves the list of available attribute ID => product ID
			// combinations for the selected value
			if( attrDeps.hasOwnProperty(value) ) {

				for( var i=0; i<attrDeps[value].length; i++ ) {

					var prodId = attrDeps[value][i];

					if( prodDeps.hasOwnProperty(prodId) ) {

						for( var j=0; j<prodDeps[prodId].length; j++ ) {
							attrMap[prodDeps[prodId][j]] = prodId;
						}
					}
				}
			}


			$(".select-list", target).each(function(idx, select) {

				if( node == select ) {
					return;
				}

				if( index === 0 ) {

					var options = $(".select-option", this);

					options.removeAttr("disabled");
					options.data("disabled", 0);
					options.data("by", {});
				}


				$(".select-option", this).each(function(i, option) {

					var opt = $(option);
					var val = opt.val();
					var by = opt.data("by") || {};
					var disabled = opt.data("disabled") || 0;


					// Sets or removes the disabled bits in the bitmap of the
					// .select-option and by which .select-list it was disabled.
					// Each option can be disabled by multiple dependencies and
					// we can remove each of the bits separately again.
					if( value !== "" && val !== "" && !attrMap.hasOwnProperty(val) ) {
						disabled |= 2<<index;
						by[index] = 1;
					} else if( by.hasOwnProperty(index) ) {
						disabled &= ~(2<<index);
						delete by[index];
					}

					if( idx !== 0 && disabled > 0 ) {
						opt.attr("disabled", "disabled");
						opt.prop("selected", false);
						opt.prop("checked", false);
					} else {
						opt.removeAttr("disabled");
					}

					opt.data("disabled", disabled);
					opt.data("by", by);
				});
			});
		});
	},


	/**
	 * Displays the associated stock level, price items and attributes for the selected product variant
	 */
	setupSelectionContent: function() {

		$(".catalog-detail-basket-selection .selection, .catalog-list-items .items-selection .selection").on("change", ".select-list", function() {

			var stock = false;
			var map = {}, len = 0;
			var target = $(this).parents(".selection");
			var attrDeps = target.data("attrdeps") || {}; // {"<attrid>":["prodid",...],...}


			$(".select-option:checked", target).each(function() {

				var value = $(this).val();

				if( value !== "" && attrDeps.hasOwnProperty(value) ) {

					for( var i=0; i<attrDeps[value].length; i++ ) {

						if( map.hasOwnProperty(attrDeps[value][i]) ) {
							map[attrDeps[value][i]]++;
						} else {
							map[attrDeps[value][i]] = 1;
						}
					}
				}

				len++;
			});


			for( var prodId in map ) {

				if( map.hasOwnProperty(prodId) && map[prodId] === len ) {

					var parent = $(this).parents(".catalog-detail-basket, .catalog-list .product");
					var newStock = $('.stock-list [data-prodid="' + prodId + '"]', parent);
					var newPrice = $('.price-list [data-prodid="' + prodId + '"]', parent);

					if( newStock.length === 0 ) {
						newStock = $(".stock-list .articleitem:first-child", parent);
					}

					if( newPrice.length === 0 ) {
						newPrice = $(".price-list .articleitem:first-child", parent);
					}

					$(".articleitem", parent).removeClass("stock-actual");
					newStock.addClass("stock-actual");

					$(".articleitem", parent).removeClass("price-actual");
					newPrice.addClass("price-actual");

					if( !(parent.data("reqstock") && $(".stockitem", newStock).hasClass("stock-out")) ) {
						stock = true;
					}

					$(".catalog-detail-additional .subproduct-actual").removeClass("subproduct-actual");
					$(".catalog-detail-additional .subproduct-" + prodId).addClass("subproduct-actual");
				}
			}

			var parent = $(this).parents(".catalog-detail-basket, .catalog-list .product");

			if(!AimeosCatalog.validateVariant()) {
				$(".addbasket .btn-action", parent).addClass("btn-disabled").attr("disabled", "disabled");
			} else if(stock) {
				$(".addbasket .btn-action", parent).removeClass("btn-disabled").removeAttr("disabled");
			}
		});
	},


	/**
	 * Checks if all required variant attributes are selected
	 */
	setupVariantCheck: function() {

		$(".catalog-detail-basket-selection, .catalog-list-items .items-selection").on("click", ".addbasket .btn-action", function() {

			var result = true;

			$(".selection .select-item", $(this).parents(".items-selection")).each( function() {

				if( $(".select-list", this).val() !== '' || $(".select-option:checked", this).length > 0 ) {
					$(this).removeClass("error");
				} else {
					$(this).addClass("error");
					result = false;
				}
			});

			return result;
		});
	},

	validateVariant: function () {

		var result = true;

		$(".selection .select-item").each( function() {
			if( $(".select-list", this).val() === '' && $(".select-option:checked", this).length <= 0 ) {
				result = false;
			}
		});

		return result;
	},


	/**
	 * Shows the images associated to the variant attributes
	 */
	setupVariantImages: function() {

		$(".catalog-detail-basket-selection .selection, .catalog-list-items .items-selection .selection").on("change", ".select-list", function() {

			var elem = $(this);
			var type = elem.data("type");
			var value = elem.find(".select-option:checked").val();

			elem.closest(".product").find(".image-single .item").each( function() {
				if($(this).data("variant-" + type) == value) {
					swiffyslider.slideTo(this, $(this).parent().data('slick-index'))
				}
			});
		});
	},


	/**
	 * Adds products to the basket without page reload
	 */
	setupBasketAdd: function() {

		$(".catalog-detail-basket form, .catalog-list-items form").on("submit", function() {

			Aimeos.createOverlay();
			fetch($(this).attr("action"), {
				body: new FormData(this),
				method: 'POST'
			}).then(function(response) {
				return response.text();
			}).then(function(data) {
				Aimeos.createContainer(AimeosBasket.updateBasket(data));
			});

			return false;
		});
	},


	/**
	 * Adds a product to the favorite list without page reload
	 */
	setupFavoriteAction: function() {

		$(".catalog-actions .actions-favorite").on("submit", function(ev) {

			ev.preventDefault();
			Aimeos.createOverlay();

			fetch($(this).attr("action"), {
				body: new FormData(this),
				method: 'POST'
			}).then(response => {
				return response.text();
			}).then(data => {
				var doc = document.createElement("html");
				doc.innerHTML = data;
				var content = $(".account-favorite", doc);

				if( content.length > 0 ) {
					Aimeos.createContainer(content);
				} else {
					document.querySelector("html").replaceWith(doc);
				}
			});

			return false;
		});

	},


	/**
	 * Adds a product to the watch list without page reload
	 */
	setupWatchAction: function() {

		$(".catalog-actions .actions-watch").on("click", function(ev) {

			ev.preventDefault();
			Aimeos.createOverlay();

			fetch($(this).attr("action"), {
				body: new FormData(this),
				method: 'POST'
			}).then(response => {
				return response.text();
			}).then(data => {
				var doc = document.createElement("html");
				doc.innerHTML = data;
				var content = $(".account-watch", doc);

				if( content.length > 0 ) {
					Aimeos.createContainer(content);
				} else {
					document.querySelector("html").replaceWith(doc);
				}
			});

			return false;
		});
	},


	/**
	 * Initializes the common catalog actions
	 */
	init: function() {

		this.setupSelectionDependencies();
		this.setupSelectionContent();
		this.setupVariantImages();
		this.setupVariantCheck();
		this.setupBasketAdd();
		this.setupWatchAction();
		this.setupFavoriteAction();
	}
};


$(function() {
	AimeosCatalog.init();
});