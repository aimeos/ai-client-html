/*
 * backgroundSize: A jQuery cssHook adding support for "cover" and "contain" to IE6,7,8
 *
 * Requirements:
 * - jQuery 1.7.0+
 *
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery.backgroundSize.js
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 *
 * This saved you an hour of work?
 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
 */
(function(e,t,n,r,i){var s=e("<div>")[0],o=/url\(["']?(.*?)["']?\)/,u=[],a={top:0,left:0,bottom:1,right:1,center:.5};if("backgroundSize"in s.style&&!e.debugBGS){return}e.cssHooks.backgroundSize={set:function(t,n){var r=!e.data(t,"bgsImg"),i,s,o;e.data(t,"bgsValue",n);if(r){u.push(t);e.refreshBackgroundDimensions(t,true);s=e("<div>").css({position:"absolute",zIndex:-1,top:0,right:0,left:0,bottom:0,overflow:"hidden"});o=e("<img>").css({position:"absolute"}).appendTo(s),s.prependTo(t);e.data(t,"bgsImg",o[0]);i=(e.css(t,"backgroundPosition")||e.css(t,"backgroundPositionX")+" "+e.css(t,"backgroundPositionY")).split(" ");e.data(t,"bgsPos",[a[i[0]]||parseFloat(i[0])/100,a[i[1]]||parseFloat(i[1])/100]);e.css(t,"zIndex")=="auto"&&(t.style.zIndex=0);e.css(t,"position")=="static"&&(t.style.position="relative");e.refreshBackgroundImage(t)}else{e.refreshBackground(t)}},get:function(t){return e.data(t,"bgsValue")||""}};e.cssHooks.backgroundImage={set:function(t,n){return e.data(t,"bgsImg")?e.refreshBackgroundImage(t,n):n}};e.refreshBackgroundDimensions=function(t,n){var r=e(t),i={width:r.innerWidth(),height:r.innerHeight()},s=e.data(t,"bgsDim"),o=!s||i.width!=s.width||i.height!=s.height;e.data(t,"bgsDim",i);if(o&&!n){e.refreshBackground(t)}};e.refreshBackgroundImage=function(t,n){var r=e.data(t,"bgsImg"),i=(o.exec(n||e.css(t,"backgroundImage"))||[])[1],s=r&&r.src,u=i!=s,a,f;if(u){r.style.height=r.style.width="auto";r.onload=function(){var n={width:r.width,height:r.height};if(n.width==1&&n.height==1){return}e.data(t,"bgsImgDim",n);e.data(t,"bgsConstrain",false);e.refreshBackground(t);r.style.visibility="visible";r.onload=null};r.style.visibility="hidden";r.src=i;if(r.readyState||r.complete){r.src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";r.src=i}t.style.backgroundImage="none"}};e.refreshBackground=function(t){var n=e.data(t,"bgsValue"),i=e.data(t,"bgsDim"),s=e.data(t,"bgsImgDim"),o=e(e.data(t,"bgsImg")),u=e.data(t,"bgsPos"),a=e.data(t,"bgsConstrain"),f,l=i.width/i.height,c=s.width/s.height,h;if(n=="contain"){if(c>l){e.data(t,"bgsConstrain",f="width");h=r.floor((i.height-i.width/c)*u[1]);o.css({top:h});if(f!=a){o.css({width:"100%",height:"auto",left:0})}}else{e.data(t,"bgsConstrain",f="height");h=r.floor((i.width-i.height*c)*u[0]);o.css({left:h});if(f!=a){o.css({height:"100%",width:"auto",top:0})}}}else if(n=="cover"){if(c>l){e.data(t,"bgsConstrain",f="height");h=r.floor((i.height*c-i.width)*u[0]);o.css({left:-h});if(f!=a){o.css({height:"100%",width:"auto",top:0})}}else{e.data(t,"bgsConstrain",f="width");h=r.floor((i.width/c-i.height)*u[1]);o.css({top:-h});if(f!=a){o.css({width:"100%",height:"auto",left:0})}}}};var f=e.event,l,c={_:0},h=0,p,d;l=f.special.throttledresize={setup:function(){e(this).on("resize",l.handler)},teardown:function(){e(this).off("resize",l.handler)},handler:function(t,n){var r=this,i=arguments;p=true;if(!d){e(c).animate(c,{duration:Infinity,step:function(){h++;if(h>l.threshold&&p||n){t.type="throttledresize";f.dispatch.apply(r,i);p=false;h=0}if(h>9){e(c).stop();d=false;h=0}}});d=true}},threshold:1};e(t).on("throttledresize",function(){e(u).each(function(){e.refreshBackgroundDimensions(this)})})})(jQuery,window,document,Math);



/**
 * Aimeos related Javascript code
 * 
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Aimeos common actions
 */
Aimeos = {

	/**
	 * Creates a floating container over the page displaying the given content node
	 */
	createContainer: function(content) {

		var container = $(document.createElement("div"));
		var btnclose = $(document.createElement("a"));

		btnclose.text("X");
		btnclose.addClass("minibutton");
		btnclose.addClass("btn-close");

		container.addClass("aimeos-container");
		container.addClass("aimeos");
		container.prepend(btnclose);
		container.fadeTo(400, 1.0);
		container.append(content);

		$("body").append(container);

		var resize = function() {
			var jqwin = $(window);
			var left = (jqwin.width() - container.outerWidth()) / 2;
			var top = jqwin.scrollTop() + (jqwin.height() - container.outerHeight()) / 2;

			container.css("left", (left > 0 ? left : 0));
			container.css("top", (top > 0 ? top : 0));
		};

		$(window).on("resize", resize);
		resize();
	},


	/**
	 *  Adds an overlay on top of the current page
	 */
	createOverlay: function() {

		var overlay = $(document.createElement("div"));
		overlay.addClass("aimeos-overlay");
		overlay.fadeTo(1000, 0.5);
		$("body").append(overlay);
	},


	/**
	 * Removes an existing overlay from the current page
	 */
	removeOverlay: function() {

		var container = $(".aimeos-container");
		var overlay = $(".aimeos-overlay");

		// remove only if in overlay mode
		if(container.size() + overlay.size() > 0) {

			container.remove();
			overlay.remove();
			return false;
		}

		return true;
	},


	/**
	 * Lazy load product image in list views
	 */
	loadImages: function() {

		var elements = $(".catalog-list-items .lazy-image, .catalog-list-promo .lazy-image");

		for( var i = 0; i < elements.length; i++) {
			var element = $(elements[i]);

			if($(window).scrollTop() + $(window).height() + 2 * element.height() >= element.offset().top) {
				element.css("background-image", "url('" + element.data("src") + "')");
				element.removeClass("lazy-image");
			}
		}
	},


	/**
	 * Sets up the ways to close the container by the user
	 */
	setupContainerClose: function() {

		/* Go back to underlying page when back or close button is clicked */
		$("body").on("click", ".aimeos-container .btn-close", function(ev) {
			return Aimeos.removeOverlay();
		});

		/* Go back to underlying page when ESC is pressed */
		$("body").on("keydown", function(ev) {
			if(ev.which == 27) {
				return Aimeos.removeOverlay();
			}
		});
	},


	/**
	 * Initializes the setup methods
	 */
	init: function(this) {
		if (this && !(this instanceof Window))
			this_ = this;
		this_.setupContainerClose();
	}
};





/**
 * Account favorite actions
 */
AimeosAccountFavorite = {

	/**
	 * Deletes a favorite item without page reload
	 */
	setupProductRemoval: function() {

		$("body").on("click", ".account-favorite a.modify", function(ev) {

			var item = $(this).parents("favorite-item");
			item.addClass("loading");

			$.get($(this).attr("href"), function(data) {

				var doc = document.createElement("html");
				doc.innerHTML = data;

				$(".account-favorite").html($(".account-favorite", doc).html());
			});

			return false;
		});
	},


	/**
	 * Initializes the account favorite actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
		this_.setupProductRemoval();
	}
};





/**
 * Account history actions
 */
AimeosAccountHistory = {

	/**
	 * Shows order details without page reload
	 */
	setupOrderShow: function() {

		$(".account-history .history-item").on("click", "> a", function(ev) {

			var details = $(".account-history-order", ev.delegateTarget);

			if(details.length === 0) {

				$.get($(this).attr("href"), function(data) {

					var doc = document.createElement("html");
					doc.innerHTML = data;

					var node = $(".account-history-order", doc);
					node.css("display", "none");
					$(ev.delegateTarget).append(node);
					node.slideDown();
				});

			} else {
				details.slideToggle();
			}

			return false;
		});
	},


	/**
	 * Closes the order details without page reload
	 */
	setupOrderClose: function() {

		$(".account-history .history-item").on("click", ".btn-close", function(ev) {
			$(".account-history-order", ev.delegateTarget).slideUp();
			return false;
		});
	},


	/**
	 * Initializes the account history actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupOrderShow();
		this_.setupOrderClose();
	}
};




/**
 * Account watch actions
 */
AimeosAccountWatch = {

	/**
	 * Deletes a watched item without page reload
	 */
	setupProductRemoval: function() {

		$("body").on("click", ".account-watch a.modify", function(ev) {

			var item = $(this).parents("watch-item");
			item.addClass("loading");

			$.get($(this).attr("href"), function(data) {

				var doc = document.createElement("html");
				doc.innerHTML = data;

				$(".account-watch").html($(".account-watch", doc).html());
			});

			return false;
		});
	},


	/**
	 * Saves a modifed watched item without page reload
	 */
	setupProductSave: function() {

		$("body").on("click", ".account-watch .standardbutton", function(ev) {

			var form = $(this).parents("form.watch-details");
			form.addClass("loading");

			$.post(form.attr("action"), form.serialize(), function(data) {

				var doc = document.createElement("html");
				doc.innerHTML = data;

				$(".account-watch").html($(".account-watch", doc).html());
			});

			return false;
		});
	},


	/**
	 * Initializes the account watch actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupProductRemoval();
		this_.setupProductSave();
	}
};





/**
 * Basket mini client actions
 */
AimeosBasketMini = {

	/**
	 * Saves a modifed watched item without page reload
	 */
	setupBasketToggle: function() {

		$(".basket-mini-product").on("click", ".minibutton", function(ev) {
			$(".basket", ev.delegateTarget).toggle();
		});
	},

	/**
	 * Initializes the basket mini actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupBasketToggle();
	}
};





/**
 * Basket related client actions
 */
AimeosBasketRelated = {

	/**
	 * Initializes the basket related actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
	}
};





/**
 * Basket standard client actions
 */
AimeosBasketStandard = {

	/**
	 * Updates the basket without page reload
	 */
	updateBasket: function(data) {

		var doc = document.createElement("html");
		doc.innerHTML = data;

		var basket = $(".basket-standard", doc);

		$(".btn-update", basket).hide();
		$(".basket-mini-main .value").text($(".basket .total .price", basket).text());
		$(".basket-mini-main .quantity").text($(".basket .quantity .value", basket).text());

		return basket;
	},


	/**
	 * Goes back to underlying page when back or close button of the basket is clicked
	 */
	setupBasketBack: function() {

		$("body").on("click", ".basket-standard .btn-back", function(ev) {
			return Aimeos.removeOverlay();
		});
	},


	/**
	 * Hides the update button and show only on quantity change
	 */
	setupUpdateHide: function() {

		$(".basket-standard .btn-update").hide();

		$("body").on("focusin", ".basket-standard .basket .product .quantity .value", {}, function(ev) {
			$(".btn-update", ev.delegateTarget).show();
		});
	},


	/**
	 * Updates basket without page reload
	 */
	setupUpdateSubmit: function() {

		$("body").on("submit", ".basket-standard form", function(ev) {
			var form = $(this);

			$.post(form.attr("action"), form.serialize(), function(data) {
				$(".basket-standard").html(AimeosBasketStandard.updateBasket(data).html());
			});

			return false;
		});
	},


	/**
	 * Updates quantity and deletes products without page reload
	 */
	setupUpdateChange: function() {

		$("body").on("click", ".basket-standard a.change", function(ev) {

			$.get($(this).attr("href"), function(data) {
				$(".basket-standard").html(AimeosBasketStandard.updateBasket(data).html());
			});

			return false;
		});
	},


	/**
	 * Initializes the basket standard actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupBasketBack();
		this_.setupUpdateHide();
		this_.setupUpdateSubmit();
		this_.setupUpdateChange();
	}
};




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

		$(".catalog-detail-basket-selection, .catalog-list-items .items-selection").on("change", ".select-list", function(event) {

			var elem = $(this);
			var index = elem.data("index");
			var value = elem.find(".select-option:checked").val();

			var attrDeps = $(event.delegateTarget).data("attrdeps") || {}; // {"<attrid>":["prodid",...],...}
			var prodDeps = $(event.delegateTarget).data("proddeps") || {}; // {"<prodid>":["attrid",...],...}
			var attrMap = {}, attrList = [];


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


			$(".select-list", event.delegateTarget).each(function(i, select) {

				if( event.currentTarget == select ) {
					return;
				}

				$(this).find(".select-option").each(function(i, option) {

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

					if( disabled > 0 ) {
						opt.attr("disabled", "disabled");
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

		$(".catalog-detail-basket-selection, .catalog-list-items .items-selection").on("change", ".select-list", function(event) {

			var map = {}, len = 0;
			var attrDeps = $(event.delegateTarget).data("attrdeps") || {}; // {"<attrid>":["prodid",...],...}


			$(".select-option:checked", event.delegateTarget).each(function(idx, option) {

				var value = $(option).val();

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
					var newPrice = $(".price-prodid-" + prodId, parent);
					var newStock = $(".stock-prodid-" + prodId, parent);

					if( newPrice.length === 0 ) {
						newPrice = $(".price-main", parent);
					}

					if( newStock.length === 0 ) {
						newStock = $(".stockitem:first-child", parent);
					}

					$(".price", parent).removeClass("price-actual");
					newPrice.addClass("price-actual");

					$(".stockitem", parent).removeClass("stock-actual");
					newStock.addClass("stock-actual");

					if( parent.data("reqstock") && newStock.hasClass("stock-out") ) {
						$(".addbasket .btn-action", parent).addClass("btn-disabled").attr("disabled", "disabled");
					} else {
						$(".addbasket .btn-action", parent).removeClass("btn-disabled").removeAttr("disabled");
					}
				}
			}

			$(".catalog-detail-additional .attributes .subproduct-actual").removeClass("subproduct-actual");
			$(".catalog-detail-additional .attributes .subproduct-" + prodId).addClass("subproduct-actual");
		});
	},


	/**
	 * Checks if all required variant attributes are selected
	 */
	setupVariantCheck: function() {

		$(".catalog-detail-basket, .catalog-list-items").on("click", ".addbasket .btn-action", {}, function(event) {

			var result = true;

			$(".selection .select-item", event.delegateTarget).each( function() {

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


	/**
	 * Adds products to the basket without page reload
	 */
	setupBasketAdd: function(data) {

		$(".catalog-detail-basket form, .catalog-list-items form").on("submit", function(ev) {

		    Aimeos.createOverlay();
		    $.post($(this).attr("action"), $(this).serialize(), function(data) {
		        Aimeos.createContainer(AimeosBasketStandard.updateBasket(data));
		    });

		    return false;
		});
	},


	/**
	 * Initializes the common catalog actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupSelectionDependencies();
		this_.setupSelectionContent();
		this_.setupVariantCheck();
		this_.setupBasketAdd();
	}
};


/**
 * Catalog filter actions
 */
AimeosCatalogFilter = {

	/**
	 * Autocompleter for quick search
	 */
	setupSearchAutocompletion: function() {

		var aimeosInputComplete = $(".catalog-filter-search .value");

		aimeosInputComplete.autocomplete({
			minLength : 3,
			delay : 200,
			source : function(req, resp) {
				var nameTerm = {};
				nameTerm[aimeosInputComplete.attr("name")] = req.term;

				$.getJSON(aimeosInputComplete.data("url"), nameTerm, function(data) {
					resp(data);
				});
			},
			select : function(ev, ui) {
				aimeosInputComplete.val(ui.item.label);
				return false;
			}
		}).autocomplete("instance")._renderItem = function(ul, item) {
			return $("<li>").append(item.value).appendTo(ul);
		};
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

				if(input.val() !== '' && input.val().length < 3) {

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
	 * Sets up the fade out of the catalog list
	 */
	setupListFadeout: function() {

		$(".catalog-filter-tree li.cat-item").on("click", function() {
			$(".catalog-list").fadeTo(1000, 0.5);
		});
	},


	/**
	 * Toggles the categories if hover isn't available
	 */
	setupCategoryToggle: function() {

		$(".catalog-filter-tree").on("click", "h2", function(ev) {
			$("> ul", ev.delegateTarget).slideToggle();
		});
	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeToggle: function() {

		$(".catalog-filter-attribute").on("click", "h2", function(ev) {
			$(".attribute-lists", ev.delegateTarget).slideToggle();
		});
	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeListsToggle: function() {

		$(".catalog-filter-attribute .attribute-lists .attr-list").hide();

		$(".catalog-filter-attribute fieldset").on("click", "legend", function(ev) {
			$(".attr-list", ev.delegateTarget).slideToggle();
		});
	},


	/**
	 * Submits the form when clicking on filter attribute names or counts
	 */
	setupAttributeItemSubmit: function() {

		$(".catalog-filter-attribute li.attr-item").on("click", ".attr-name, .attr-count", function(ev) {
			var input = $("input", ev.delegateTarget);
			input.prop("checked") ? input.prop("checked", false) : input.prop("checked", true);

			$(this).parents(".catalog-filter form").submit();
			$(".catalog-list").fadeTo(1000, 0.5);
		});
	},


	/**
	 * Initialize the catalog filter actions
	 */
	init: function(this_) {
		//alert('this: ' + this + ' <=>? this_: ' + this_);
		if (this && ! (this instanceof Window))
			this_ = this;
		//alert(typeof this_.setupCategoryToggle); // <- must output function!
		this_.setupCategoryToggle();
		this_.setupAttributeToggle();
		this_.setupAttributeListsToggle();
		this_.setupListFadeout();

		this_.setupAttributeItemSubmit();

		this_.setupFormChecks();
		this_.setupSearchAutocompletion();
	}
};





/**
 * Catalog list actions
 */
AimeosCatalogList = {

	/**
	 * Switches product images on hover
	 */
	setupImageSwitch: function() {

		$(".catalog-list-items .product .media-list").on("mouseenter", function() {
			var list = $(".media-item", this);

			if( list.length > 1 ) {
				var second = list.eq(1);
				var size = $(this).height();

				$(this).css("background-image", "none"); // Don't let default image shine through
				second.css("background-image", "url('" + second.data("src") + "')");
				second.fadeTo(0, 0.33);

				list.first().fadeTo(400, 0.33, function() {
					list.css('top', -size);
					second.fadeTo(400, 1);
				});
			}
		});

		$(".catalog-list-items .product .media-list").on("mouseleave", function() {
			var list = $(".media-item", this);

			if( list.length > 1 ) {
				list.first().css('opacity', 0.33);

				list.eq(1).fadeTo(400, 0.33, function() {
					list.css('top', 0);
					list.first().fadeTo(400, 1);
				});
			}
		});

		$(".catalog-list-items .product .media-list a").on("click", function(ev) {
			window.location.href = $(this).attr('href');
		});
	},


	/**
	 * Initializes the catalog list actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
		this_.setupImageSwitch();
	}
};





/**
 * Catalog session actions
 */
AimeosCatalogSession = {

	/**
	 * Initializes the catalog session actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
	}
};





/**
 * Catalog stage actions
 */
AimeosCatalogStage = {

	/**
	 * Initializes the catalog stage actions
	 */
	init: function() {
	}
};





/**
 * Checkout standard client actions
 */
AimeosCheckoutStandard = {

	/**
	 * Shows only selected address forms
	 */
		setupAddressForms: function() {

		$(".checkout-standard-address .item-address").has(".header input:not(:checked)").find(".form-list").hide();

		/* Initial state: Hide VAT ID if salution is not "company" */
		$(".checkout-standard-address .form-list .salutation select").each(function(idx, elem) {
			if($(elem).val() !== "company") {
				$(this).parents(".form-list").find(".company,.vatid").hide();
			}
		});

		/* Address form slide up/down when selected */
		$(".checkout-standard-address-billing,.checkout-standard-address-delivery").on("click", ".header input",
			function(ev) {
				$(".form-list", ev.delegateTarget).slideUp(400);
				$(".item-address", ev.delegateTarget).has(this).find(".form-list").slideDown(400);
			});
	},


	/**
	 * Shows company and VAT ID fields if salutation is "company", otherwise
	 * hide the fields
	 */
	setupSalutationCompany: function() {

		$(".checkout-standard-address .form-list").on("change", ".salutation select", function(ev) {
			var fields = $(".company,.vatid", ev.delegateTarget);
			$(this).val() === "company" ? fields.show() : fields.hide();
		});
	},


	/**
	 * Shows states only from selected countries
	 */
	setupCountryState: function() {

		$(".checkout-standard-address .form-list .countryid select").each(function(idx, elem) {
			if($(this).val() !== "") {
				$(this).parents(".form-list").find(".state optgroup:not(." + $(this).val() + ")").hide();
			}
		});

		$(".checkout-standard-address .form-list").on("change", ".countryid select", function(ev) {
			$(".state select", ev.delegateTarget).val("");
			$(".state optgroup", ev.delegateTarget).hide();
			$(".state ." + $(this).val(), ev.delegateTarget).show();
		});
	},


	/**
	 * Shows only form fields of selected service option
	 */
	setupServiceForms: function() {

		/* Hide form fields if delivery/payment option is not selected */
		$(".checkout-standard-delivery,.checkout-standard-payment").each(function(idx, elem) {
			$(elem).find(".form-list").hide();
			$(elem).find(".item-service").has("input:checked").find(".form-list").show();
		});

		/* Delivery/payment form slide up/down when selected */
		$(".checkout-standard-delivery, .checkout-standard-payment").on("click", ".option", function(ev) {
			$(".form-list", ev.delegateTarget).slideUp(400);
			$(".item-service", ev.delegateTarget).has(this).find(".form-list").slideDown(400);
		});
	},


	/**
	 * Checks for mandatory fields in all forms
	 */
	setupMandatoryCheck: function() {

		$(".checkout-standard .form-item").on("blur", "input,select", function(ev) {
			var value = $(this).val();
			var node = $(ev.delegateTarget);
			var regex = new RegExp(node.data('regex'));

			if((value !== '' && value.match(regex)) || (value === '' && !node.hasClass("mandatory"))) {
				node.removeClass("error").addClass("success");
			} else {
				node.removeClass("success").addClass("error");
			}
		});

		$(".checkout-standard form").on("submit", function(ev) {
			var retval = true;
			var nodes = [];

			var testfn = function(idx, element) {

				var elem = $(element);
				var value = elem.find("input,select").val();

				if(value === null || value.trim() === "") {
					elem.addClass("error");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("error");
				}
			};

			var item = $(".checkout-standard .item-new, .item-service");
			 // combining in one has() doesn't work
			item.has(".header,label").has("input:checked").find(".form-list .mandatory").each(testfn);

			$(".checkout-standard-process .form-list .mandatory").each(testfn);

			if( nodes.length !== 0 ) {
				$('html, body').animate({
					scrollTop: $(nodes).first().offset().top + 'px'
				});
			}

			return retval;
		});
	},


	/**
	 * Redirect to payment provider / confirm page when order has been created successfully
	 */
	setupPaymentRedirect: function() {

		var form = $("form").first();
		var node = $(".checkout-standard-process", form);

		if(node.length > 0 && node.has(".mandatory").length === 0 && node.has(".optional").length === 0 && form.attr("action") !== '' ) {
			form.submit();
		}
	},


	/**
	 * Initializes the checkout standard section
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;

		this_.setupAddressForms();
		this_.setupServiceForms();

		this_.setupSalutationCompany();
		this_.setupCountryState();

		this_.setupMandatoryCheck();
		this_.setupPaymentRedirect();
	}
};





/**
 * Checkout confirm client actions
 */
AimeosCheckoutConfirm = {

	/**
	 * Initializes the checkout confirm section
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
	}
};





/**
 * Locale selector actions
 */
AimeosLocaleSelect = {

	/**
	 * Keeps menu open on click resp. closes on second click
	 */
	setupMenuToggle: function() {

		$(".select-menu .select-dropdown").click(function() {
			$("ul", this).toggleClass("active");
			$(this).toggleClass("active");
		});
	},


	/**
	 * Initializes the locale selector actions
	 */
	init: function(this_) {
		if (this && !(this instanceof Window))
			this_ = this;
		this_.setupMenuToggle();
	}
};





/*
 * CSS3 support for IE8
 */
document.createElement("nav");
document.createElement("section");
document.createElement("article");


Aimeos.loadImages();


jQuery(document).ready(function($) {

	/* CSS3 "background-size: contain" support for IE8 */
	$(".catalog-list-items .media-item").css("background-size", "contain");
	$(".catalog-detail-image .item").css("background-size", "contain");


	/* Lazy product image loading in list view */
	Aimeos.loadImages();
	$(window).bind("resize", Aimeos.loadImages);
	$(window).bind("scroll", Aimeos.loadImages);


	Aimeos.init();

	AimeosLocaleSelect.init();

	AimeosCatalog.init();
	AimeosCatalogFilter.init();
	AimeosCatalogList.init();
	AimeosCatalogSession.init();
	AimeosCatalogStage.init();

	AimeosBasketMini.init();
	AimeosBasketRelated.init();
	AimeosBasketStandard.init();

	AimeosCheckoutStandard.init();
	AimeosCheckoutConfirm.init();

	AimeosAccountHistory.init();
	AimeosAccountFavorite.init();
	AimeosAccountWatch.init();
});
