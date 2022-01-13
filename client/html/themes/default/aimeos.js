/* Nested parameter encoding 1.1.8 https://github.com/knowledgecode/jquery-param (MIT License) */
(function(c,d){"object"===typeof exports&&"undefined"!==typeof module?module.exports=d():"function"===typeof define&&define.amd?define(d):(c="undefined"!==typeof globalThis?globalThis:c||self,c.param=d())})(this,function(){return function(c){var d=[],g=function(e,a){a="function"===typeof a?a():a;a=null===a?"":void 0===a?"":a;d[d.length]=encodeURIComponent(e)+"="+encodeURIComponent(a)},f=function(e,a){var c;if(e)if(Array.isArray(a)){var b=0;for(c=a.length;b<c;b++)f(e+"["+("object"===typeof a[b]&&a[b]?b:"")+"]",a[b])}else if("[object Object]"===Object.prototype.toString.call(a))for(b in a)f(e+"["+b+"]",a[b]);else g(e,a);else if(Array.isArray(a))for(b=0,c=a.length;b<c;b++)g(a[b].name,a[b].value);else for(b in a)f(b,a[b]);return d};return f("",c).join("&")}});

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
 * Basket client actions
 */
AimeosBasket = {

	/**
	 * Updates the basket mini content using the JSON API
	 */
	updateMini: function() {

		var jsonurl = $(".aimeos.basket-mini[data-jsonurl]").data("jsonurl");

		if(!jsonurl) {
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
	 * Updates the basket without page reload
	 */
	updateBasket: function(data) {

		var doc = document.createElement("html");
		doc.innerHTML = data;

		var basket = $(".basket-standard", doc);

		$(".btn-update", basket).hide();
		AimeosBasket.updateMini();

		return basket;
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
	 * Image switch
	 */
	setupImageSwitch: function() {

		$('.media-list').each(function() {
			if ($(this).find('div').length < 2) {
				$(this).addClass('no-switch');
			}
		});
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
		this.setupImageSwitch();
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

	Aimeos.loadImages();
});