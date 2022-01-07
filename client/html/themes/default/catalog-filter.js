/**
 * Catalog filter actions
 */
AimeosCatalogFilter = {

	MIN_INPUT_LEN: 3,


	/**
	 * Autocompleter for quick search
	 */
	setupSearchAutocompletion: function() {

		$(".catalog-filter-search .value").each(function() {
			var url = $(this).data("url");

			autocomplete({
				input: this,
				debounceWaitMs: 200,
				minLength: AimeosCatalogFilter.MIN_INPUT_LEN,
				fetch: function(text, update) {
					fetch(url.replace('_term_', encodeURIComponent(text))).then(response => {
						return response.json();
					}).then(data => {
						update(data);
					});
				},
				render: function(item, text) {
					return $(item.html.trim()).get(0);
				}
			});
		});
	},


	/**
	 * Sets up the form checks
	 */
	setupFormChecks: function() {

		$(".catalog-filter form").on("submit", function(ev) {

			var result = true;
			var form = $(this);

			$("input.value", this).each(function() {

				var input = $(this);

				if(input.val() !== '' && input.val().length < AimeosCatalogFilter.MIN_INPUT_LEN) {

					if($(this).has(".search-hint").length === 0) {

						var node = $('<div class="search-hint">' + input.data("hint") + '</div>');
						$(".catalog-filter-search", form).after(node);

						var pos = node.position();
						node.css("left", pos.left).css("top", pos.top);
						node.delay(3000).fadeOut(1000, function() {
							node.remove();
						});
					}

					result = false;
				}
			});

			return result;
		});
	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeToggle: function() {

		$('.catalog-filter-attribute h2').on("click", function() {
			$(".attribute-lists", $(this).parents(".catalog-filter-attribute")).each(function() {
				slideToggle(this, 300);
			});
		});
	},

	/**
	 * Toggles the Last Seen filters if hover isn't available
	 */

	setupLastSeenToggle: function() {

		$('.catalog-session-seen .header').on("click", function() {
			$(".seen-items", $(this).parents(".catalog-session-seen")).each(function() {
				slideToggle(this, 300);
			});
		});
	},

	/**
	 * Toggles pinned items
	 */

	setupPinnedToggle: function() {

		$('.catalog-session-pinned .header').on("click", function() {
			$(".pinned-items", $(this).parents(".catalog-session-pinned")).each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeListsToggle: function() {

		$(".catalog-filter-attribute .attribute-lists legend").on("click", function() {
			$(".attr-list", $(this).parents("fieldset.attr-sets")).each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Hides the attribute filter if no products are available for
	 */
	setupAttributeListsEmtpy: function() {

		$(".catalog-filter-attribute .attribute-lists .attr-count").each(function() {
			$(this).parents("fieldset.attr-sets").show();
		});
	},


	/**
	 * Submits the form when clicking on filter attribute names or counts
	 */
	setupAttributeItemSubmit: function() {

		$(".catalog-filter-attribute li.attr-item").on("click", function(ev) {
			var input = $("input", ev.currentTarget);
			input.prop("checked", !input.prop("checked"));

			$(this).parents(".catalog-filter form").get(0).submit();
			return false;
		});
	},


	/**
	 * Syncs the price input field and slider
	 */
	setupPriceSync: function() {

		$(".catalog-filter-price").on("input", ".price-high", function() {
			$(".price-slider", $(this).parents(".catalog-filter-price")).val($(this).val());
		});

		$(".catalog-filter-price").on("input", ".price-slider", function() {
			$(".price-high", $(this).parents(".catalog-filter-price")).val($(this).val());
		});
	},


	/**
	 * Toggles the price filters if hover isn't available
	 */
	setupPriceToggle: function() {

		$('.catalog-filter-price h2').on("click", function() {
			$(".price-lists", $(this).parents(".catalog-filter-price")).each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Toggles the supplier filters if hover isn't available
	 */
	setupSupplierToggle: function() {

		$('.catalog-filter-supplier').on("click", 'h2', function(ev) {
			$(".supplier-lists", $(this).parents(".catalog-filter-supplier")).each(function() {
				slideToggle(this, 300);
			});
		});
	},


	/**
	 * Submits the form when clicking on filter supplier names or counts
	 */
	setupSupplierItemSubmit: function() {

		$(".catalog-filter-supplier li.attr-item").on("click", function(ev) {
			var input = $("input", ev.currentTarget);
			input.prop("checked", !input.prop("checked"));

			$(this).parents(".catalog-filter form").get(0).submit();
			return false;
		});
	},


	/**
	 * Registers events for the catalog filter search input reset
	 */
	setupSearchTextReset: function() {

		$(".catalog-filter-search .value").on("keyup", function() {
			var val = $(this).val() !== "" ? "visible" : "hidden";
			$(".reset .symbol", $(this).parents(".catalog-filter-search")).css("visibility", val);
		});

		$(".catalog-filter-search .reset").on("click", function() {
			var input = $(this).parents(".catalog-filter-search").find(".value");
			$(".symbol", this).css("visibility", "hidden");
			input.get(0).focus();
			input.val("");
			return false;
		});
	},


	/**
	 * Initialize the catalog filter actions
	 */
	init: function() {

		this.setupPriceSync();
		this.setupPriceToggle();
		this.setupSupplierToggle();
		this.setupAttributeToggle();
		this.setupLastSeenToggle();
		this.setupPinnedToggle();
		this.setupAttributeListsEmtpy();
		this.setupAttributeListsToggle();

		this.setupAttributeItemSubmit();
		this.setupSupplierItemSubmit();

		this.setupFormChecks();
		this.setupSearchTextReset();
		this.setupSearchAutocompletion();
	}
};


$(function() {
	AimeosCatalogFilter.init();
});