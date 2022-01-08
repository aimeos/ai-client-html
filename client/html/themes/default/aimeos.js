/* Nested parameter encoding 1.1.8 https://github.com/knowledgecode/jquery-param (MIT License) */
(function(c,d){"object"===typeof exports&&"undefined"!==typeof module?module.exports=d():"function"===typeof define&&define.amd?define(d):(c="undefined"!==typeof globalThis?globalThis:c||self,c.param=d())})(this,function(){return function(c){var d=[],g=function(e,a){a="function"===typeof a?a():a;a=null===a?"":void 0===a?"":a;d[d.length]=encodeURIComponent(e)+"="+encodeURIComponent(a)},f=function(e,a){var c;if(e)if(Array.isArray(a)){var b=0;for(c=a.length;b<c;b++)f(e+"["+("object"===typeof a[b]&&a[b]?b:"")+"]",a[b])}else if("[object Object]"===Object.prototype.toString.call(a))for(b in a)f(e+"["+b+"]",a[b]);else g(e,a);else if(Array.isArray(a))for(b=0,c=a.length;b<c;b++)g(a[b].name,a[b].value);else for(b in a)f(b,a[b]);return d};return f("",c).join("&")}});

/* Autocomplete 1590fe0 https://github.com/kraaden/autocomplete (MIT License) */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e="undefined"!=typeof globalThis?globalThis:e||self).autocomplete=t()}(this,(function(){"use strict";return function(e){var t,n,o=document,i=e.container||o.createElement("div"),r=i.style,f=navigator.userAgent,l=~f.indexOf("Firefox")&&~f.indexOf("Mobile"),s=e.debounceWaitMs||0,a=e.preventSubmit||!1,u=e.disableAutoSelect||!1,d=l?"input":"keyup",c=[],p="",v=2,g=e.showOnFocus,m=0;if(void 0!==e.minLength&&(v=e.minLength),!e.input)throw new Error("input undefined");var h=e.input;function E(){n&&window.clearTimeout(n)}function w(){return!!i.parentNode}function y(){var e;m++,c=[],p="",t=void 0,(e=i.parentNode)&&e.removeChild(i)}function L(){for(;i.firstChild;)i.removeChild(i.firstChild);var n=function(e,t){var n=o.createElement("div");return n.textContent=e.label||"",n};e.render&&(n=e.render);var f=function(e,t){var n=o.createElement("div");return n.textContent=e,n};e.renderGroup&&(f=e.renderGroup);var l=o.createDocumentFragment(),s="#9?$";if(c.forEach((function(o){if(o.group&&o.group!==s){s=o.group;var i=f(o.group,p);i&&(i.className+=" group",l.appendChild(i))}var r=n(o,p);r&&(r.addEventListener("click",(function(t){e.onSelect(o,h),y(),t.preventDefault(),t.stopPropagation()})),o===t&&(r.className+=" selected"),l.appendChild(r))})),i.appendChild(l),c.length<1){if(!e.emptyMsg)return void y();var a=o.createElement("div");a.className="empty",a.textContent=e.emptyMsg,i.appendChild(a)}i.parentNode||o.body.appendChild(i),function(){if(w()){r.height="auto",r.width=h.offsetWidth+"px";var t,n=0;f(),f(),e.customize&&t&&e.customize(h,t,i,n)}function f(){var e=o.documentElement,i=e.clientTop||o.body.clientTop||0,f=e.clientLeft||o.body.clientLeft||0,l=window.pageYOffset||e.scrollTop,s=window.pageXOffset||e.scrollLeft,a=(t=h.getBoundingClientRect()).top+h.offsetHeight+l-i,u=t.left+s-f;r.top=a+"px",r.left=u+"px",(n=window.innerHeight-(t.top+h.offsetHeight))<0&&(n=0),r.top=a+"px",r.bottom="",r.left=u+"px",r.maxHeight=n+"px"}}(),function(){var e=i.getElementsByClassName("selected");if(e.length>0){var t=e[0],n=t.previousElementSibling;if(n&&-1!==n.className.indexOf("group")&&!n.previousElementSibling&&(t=n),t.offsetTop<i.scrollTop)i.scrollTop=t.offsetTop;else{var o=t.offsetTop+t.offsetHeight,r=i.scrollTop+i.offsetHeight;o>r&&(i.scrollTop+=o-r)}}}()}function b(){w()&&L()}function T(){b()}function x(e){e.target!==i?b():e.preventDefault()}function C(t){for(var n=t.which||t.keyCode||0,o=0,i=e.keysToIgnore||[38,13,27,39,37,16,17,18,20,91,9];o<i.length;o++){if(n===i[o])return}n>=112&&n<=123&&!e.keysToIgnore||40===n&&w()||S(0)}function k(n){var o=n.which||n.keyCode||0;if(38===o||40===o||27===o){var i=w();if(27===o)y();else{if(!i||c.length<1)return;38===o?function(){if(c.length<1)t=void 0;else if(t===c[0])t=c[c.length-1];else for(var e=c.length-1;e>0;e--)if(t===c[e]||1===e){t=c[e-1];break}}():function(){if(c.length<1&&(t=void 0),t&&t!==c[c.length-1]){for(var e=0;e<c.length-1;e++)if(t===c[e]){t=c[e+1];break}}else t=c[0]}(),L()}return n.preventDefault(),void(i&&n.stopPropagation())}13===o&&(t&&(e.onSelect(t,h),y()),a&&n.preventDefault())}function N(){g&&S(1)}function S(o){var i=++m,r=h.value,f=h.selectionStart||0;r.length>=v||1===o?(E(),n=window.setTimeout((function(){e.fetch(r,(function(e){m===i&&e&&(p=r,t=(c=e).length<1||u?void 0:c[0],L())}),o,f)}),0===o?s:0)):y()}function D(){setTimeout((function(){o.activeElement!==h&&y()}),200)}return i.className="autocomplete "+(e.className||""),r.position="absolute",i.addEventListener("mousedown",(function(e){e.stopPropagation(),e.preventDefault()})),i.addEventListener("focus",(function(){return h.focus()})),h.addEventListener("keydown",k),h.addEventListener(d,C),h.addEventListener("blur",D),h.addEventListener("focus",N),window.addEventListener("resize",T),o.addEventListener("scroll",x,!0),{destroy:function(){h.removeEventListener("focus",N),h.removeEventListener("keydown",k),h.removeEventListener(d,C),h.removeEventListener("blur",D),window.removeEventListener("resize",T),o.removeEventListener("scroll",x,!0),E(),y()}}}}));

/* slideToggle 44ede23 https://github.com/ericbutler555/plain-js-slidetoggle (MIT License) */
function slideToggle(t,e,o){0===t.clientHeight?j(t,e,o,!0):j(t,e,o)}function slideUp(t,e,o){j(t,e,o)}function slideDown(t,e,o){j(t,e,o,!0)}function j(t,e,o,i){void 0===e&&(e=400),void 0===i&&(i=!1),t.style.overflow="hidden",i&&(t.style.display="block");var p,l=window.getComputedStyle(t),n=parseFloat(l.getPropertyValue("height")),a=parseFloat(l.getPropertyValue("padding-top")),s=parseFloat(l.getPropertyValue("padding-bottom")),r=parseFloat(l.getPropertyValue("margin-top")),d=parseFloat(l.getPropertyValue("margin-bottom")),g=n/e,y=a/e,m=s/e,u=r/e,h=d/e;window.requestAnimationFrame(function l(x){void 0===p&&(p=x);var f=x-p;i?(t.style.height=g*f+"px",t.style.paddingTop=y*f+"px",t.style.paddingBottom=m*f+"px",t.style.marginTop=u*f+"px",t.style.marginBottom=h*f+"px"):(t.style.height=n-g*f+"px",t.style.paddingTop=a-y*f+"px",t.style.paddingBottom=s-m*f+"px",t.style.marginTop=r-u*f+"px",t.style.marginBottom=d-h*f+"px"),f>=e?(t.style.height="",t.style.paddingTop="",t.style.paddingBottom="",t.style.marginTop="",t.style.marginBottom="",t.style.overflow="",i||(t.style.display="none"),"function"==typeof o&&o()):window.requestAnimationFrame(l)})}

/* zeynepjs v2.1.4 https://github.com/hsynlms/zeynepjs (MIT License) */
!function(l,s){var n={htmlClass:!0};function i(e,t){this.element=e,this.eventController=o,this.options=l.extend({},n,t),this.options.initialized=!1,this.init()}i.prototype.init=function(){var s=this.element,e=this.options,i=this.eventController.bind(this);!0!==e.initialized&&(i("loading"),s.find("[data-submenu]").on("click",function(e){e.preventDefault();var t,n=l(this).attr("data-submenu"),o=l("#"+n);o.length&&(i("opening",t={subMenu:!0,menuId:n}),s.find(".submenu.current").removeClass("current"),o.addClass("opened current"),s.hasClass("submenu-opened")||s.addClass("submenu-opened"),s.scrollTop(0),i("opened",t))}),s.find("[data-submenu-close]").on("click",function(e){e.preventDefault();var t,n=l(this).attr("data-submenu-close"),o=l("#"+n);o.length&&(i("closing",t={subMenu:!0,menuId:n}),o.removeClass("opened current"),s.find(".submenu.opened:last").addClass("current"),s.find(".submenu.opened").length||s.removeClass("submenu-opened"),o.scrollTop(0),i("closed",t))}),i("load"),this.options.htmlClass&&!l("html").hasClass("zeynep-initialized")&&l("html").addClass("zeynep-initialized"),e.initialized=!0)},i.prototype.open=function(){this.eventController("opening",{subMenu:!1}),this.element.addClass("opened"),this.options.htmlClass&&l("html").addClass("zeynep-opened"),this.eventController("opened",{subMenu:!1})},i.prototype.close=function(e){e||this.eventController("closing",{subMenu:!1}),this.element.removeClass("opened"),this.options.htmlClass&&l("html").removeClass("zeynep-opened"),e||this.eventController("closed",{subMenu:!1})},i.prototype.destroy=function(){this.eventController("destroying"),this.close(!0),this.element.find(".submenu.opened").removeClass("opened"),this.element.removeData(s),this.eventController("destroyed"),this.options=n,this.options.htmlClass&&l("html").removeClass("zeynep-initialized"),delete this.element,delete this.options,delete this.eventController},i.prototype.on=function(e,t){r.call(this,e,t)};var o=function(e,t){if(this.options[e]){if("function"!=typeof this.options[e])throw Error("event handler must be a function: "+e);this.options[e].call(this,this.element,this.options,t)}},r=function(e,t){if("string"!=typeof e)throw Error("event name is expected to be a string but got: "+typeof e);if("function"!=typeof t)throw Error("event handler is not a function for: "+e);this.options[e]=t};l.fn[s]=function(e){var t,n,o;return t=l(this[0]),n=e,o=null,t.data(s)?o=t.data(s):(o=new i(t,n||{}),t.data(s,o)),o}}(window.$,"zeynep");


/**
 * Aimeos related Javascript code
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2014-2018
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

		btnclose.addClass("minibutton");
		btnclose.addClass("btn-close");

		container.addClass("aimeos-container");
		container.addClass("aimeos");
		container.prepend(btnclose);
		container.append(content);

		$("body").append(container);

		var resize = function() {
			var win = $(window);
			var left = (win.width() - container.outerWidth()) / 2;
			var top = window.scrollY + (win.height() - container.outerHeight()) / 2;

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
		overlay.addClass("aimeos-overlay").addClass("show");
		$("body").append(overlay);
	},


	/**
	 *  Adds a spinner on top of the current page
	 */
	createSpinner: function() {

		var spinner = $(document.createElement("div"));
		spinner.addClass("aimeos-spinner");
		$("body").append(spinner);
	},


	/**
	 * Removes an existing overlay from the current page
	 */
	removeOverlay: function() {

		var container = $(".aimeos-container");
		var overlay = $(".aimeos-overlay");

		// remove only if in overlay mode
		if(container.length + overlay.length > 0) {

			container.remove();
			overlay.remove();
			return false;
		}

		return true;
	},


	/**
	 * Removes an existing spinner from the current page
	 */
	removeSpinner: function() {
		$(".aimeos-spinner").remove();
	},


	/**
	 * Lazy load product image in list views
	 */
	loadImages: function() {

		var render = function(element) {

			if(element.tagName === 'IMG') {
				element.setAttribute("srcset", element.getAttribute("data-srcset"));
				element.setAttribute("src", element.getAttribute("data-src"));
			} else if(element.classList.contains('background')) {
				var url = '';
				var srcset = element.getAttribute("data-background");

				srcset && srcset.split(',').every(function(str) {
					var parts = str.trim().split(' ');

					if(parseInt((parts[1] || '').replace('w', '')) < window.innerWidth) {
						return true;
					}
					url = parts[0];
					return false;
				});

				element.style.backgroundImage = "url('" + url + "')";
			}

			element.classList.remove("lazy-image");
		};


		if('IntersectionObserver' in window) {

			let callback = function(entries, observer) {
				for(let entry of entries) {
					if(entry.isIntersecting) {
						observer.unobserve(entry.target);
						render(entry.target);
					}
				};
			};

			document.querySelectorAll(".aimeos .lazy-image").forEach(function(el) {
				(new IntersectionObserver(callback, {rootMargin: '240px', threshold: 0})).observe(el);
			});

		} else {

			document.querySelectorAll(".aimeos .lazy-image").forEach(function(el) {
				render(el);
			});
		}
	},


	/**
	 * Sets up the ways to close the container by the user
	 */
	setupContainerClose: function() {

		/* Go back to underlying page when back or close button is clicked */
		$("body").on("click", ".aimeos-overlay, .aimeos-container .btn-close", function() {
			return Aimeos.removeOverlay();
		});

		/* Go back to underlying page when ESC is pressed */
		$("body").on("keydown", function(ev) {
			if(ev.key == "Escape") {
				return Aimeos.removeOverlay();
			}
		});
	},


	/**
	 * Initializes the setup methods
	 */
	init: function() {
		this.setupContainerClose();
	}
};


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
		AimeosBasketMini.update();

		return basket;
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


			$(".select-option:checked", target).each(function(idx, option) {

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
					var newStock = $(".stock-list [data-prodid=" + prodId + "]", parent);
					var newPrice = $(".price-list [data-prodid=" + prodId + "]", parent);

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

			elem.parents(".product").find(".image-single .item").each( function(ev) {

				if( $(this).data("variant-" + type) == value ) {
					var idx = $(this).parent().data('slick-index');
					$('.product .image-single').slick('slickGoTo', idx, false);
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
				Aimeos.createContainer(AimeosBasketStandard.updateBasket(data));
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
					$("html").replaceWith(doc);
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
					$("html").replaceWith(doc);
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


/**
 * Page actions
 */

AimeosPage = {

	/**
	* Link to top
	*/
	setupLinkTop: function() {

	var target = document.querySelector("footer");

	var backToTop = document.querySelector(".back-to-top");
	var rootElement = document.documentElement;

	function callback(entries, observer) {
		  entries.forEach((entry) => {
		if (entry.isIntersecting) {
		  // Show button
			  backToTop.classList.add("showBtn");
		} else {
		  // Hide button
			  backToTop.classList.remove("showBtn");
		}
		  });
	}
	function scrollToTop() {
		rootElement.scrollTo({
		top: 0,
		behavior: "smooth"
		});
	}
	backToTop.addEventListener("click", scrollToTop);
	let observer = new IntersectionObserver(callback);
	observer.observe(target);
	},

	/**
	 * Menu transition
	 */
	setupMenuTransition: function() {

		window.onscroll = function() {scrollFunction()};

		function scrollFunction() {
			if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80){
				$(".navbar").addClass("navbar-scroll");
			} else {
				$(".navbar").removeClass("navbar-scroll");
			}
		}
	},


	/**
	 * Mega menu
	 */
	setupMenuMenu: function() {

		var $dropdowns = $('.top-item'); // Specifying the element is faster for older browsers

		/**
		* Touch events
		*
		* Support click to open if we're dealing with a touchscreen
		* Mouseenter (used with .hover()) does not trigger when user enters from outside document window
		*/
		$dropdowns.on('mouseover', function(){
			var $this = $(this);
			if ($this.prop('hoverTimeout')){
				$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
			}
			$this.prop('hoverIntent', setTimeout(function(){
				$this.addClass('hover');
			},));
		})
		.on('mouseleave', function(){
			var $this = $(this);
			if ($this.prop('hoverIntent')){
				$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
			}
			$this.prop('hoverTimeout', setTimeout(function(){
				$this.removeClass('hover');
			},));
		});


		/**
		* Functions for Touch Devices (such as Laptops or screens with touch)
		*/
		window.matchMedia('(min-width: 991px)').addEventListener('change', event => {

			if (event.matches) {

				/**
				* Mouse events
				*
				* Mimic hoverIntent plugin by waiting for the mouse to 'settle' within the target before triggering
				*/
				$dropdowns.each(function(){

					var $this = $(this);

					this.addEventListener('touchstart', function(e){

						if (e.touches.length === 1){
							// Prevent touch events within dropdown bubbling down to document
							e.stopPropagation();
							// Toggle hover
							if (!$this.hasClass('hover')){
								// Prevent link on first touch
								if (e.target === this || e.target.parentNode === this){
									e.preventDefault();
								}
								// Hide other open dropdowns
								$dropdowns.removeClass('hover');
								$this.addClass('hover');
								// Hide dropdown on touch outside
								document.addEventListener('touchstart', closeDropdown = function(e){

									e.stopPropagation();
									$this.removeClass('hover');
									document.removeEventListener('touchstart', closeDropdown);

								});
							}
						}
					}, false);
				});
			}
		});
	},


	/**
	 * Initializes offscreen menus
	 */
	setupOffscreen: function() {

		// loop all zeynepjs menus for initialization
		$('.zeynep').each(function (idx, el) {
			$(el).zeynep({});
		})

		// handle zeynepjs overlay click
		$('.aimeos-overlay-offscreen').on('click', function () {
			$('.aimeos-overlay-offscreen').removeClass('show');
			$('.zeynep.opened').removeClass('opened');
		});
	},


	/**
	 * Show/hide basket offscreen menu
	 */
	setupOffscreenBasket: function() {

		// open basket side menu
		$('.aimeos.basket-mini > a').on('click', function () {
			$('.basket-mini-offscreen').addClass('opened');
			$('.basket-mini .aimeos-overlay-offscreen').addClass('show');
		});

		$('.mini-basket-close').on('click', function () {
			$('.basket-mini-offscreen').removeClass('opened');
			$('.basket-mini .aimeos-overlay-offscreen').removeClass('show');
		});
	},


	/**
	 * Show/hide category offscreen menu
	 */
	setupOffscreenCategory: function() {

		$(".open-menu").on('click', function () {
			$('.category-lists').addClass('opened');
			$('.catalog-filter .aimeos-overlay-offscreen').addClass('show');
		});

		$(".menu-close").on('click', function () {
			$('.category-lists').removeClass('opened');
			$('.catalog-filter .aimeos-overlay-offscreen').removeClass('show');
		});
	},


	/**
	 * Initializes the menu actions
	 */
	init: function() {
		this.setupMenuTransition();
		this.setupLinkTop();
		this.setupMenuMenu();
		this.setupOffscreen();
		this.setupOffscreenBasket();
		this.setupOffscreenCategory();
	}
};


/*
 * Disable CSS rules only necessary if no Javascript is available
 */
$("html").removeClass("no-js");


$(function() {

	Aimeos.init();
	AimeosPage.init();
	AimeosCatalog.init();

	Aimeos.loadImages();
});