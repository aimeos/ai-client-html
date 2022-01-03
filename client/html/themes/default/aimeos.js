/*
 * Autocomplete
 * https://github.com/tomik23/autocomplete
*/
var Autocomplete=function(){"use strict";const t=(t,e)=>{for(let s in e)"addClass"===s?t.classList.add(e[s]):"removeClass"===s?t.classList.remove(e[s]):t.setAttribute(s,e[s])},e=(t,e)=>e.value=(t=>t.firstElementChild||t)(t).textContent.trim(),s=(t,e)=>{t.scrollTop=t.offsetTop-e.offsetHeight},i=(t,e)=>{t.setAttribute("aria-activedescendant",e||"")},h=(t,e,s,i)=>{const h=i.previousSibling,r=h?h.offsetHeight:0;if("0"==t.getAttribute("aria-posinset")&&(i.scrollTop=t.offsetTop-((t,e)=>{const s=document.querySelectorAll("#"+t+" > li:not(."+e+")");let i=0;return[].slice.call(s).map(t=>i+=t.offsetHeight),i})(e,s)),t.offsetTop-r<i.scrollTop)i.scrollTop=t.offsetTop-r;else{const e=t.offsetTop+t.offsetHeight-r;e>i.scrollTop+i.offsetHeight&&(i.scrollTop=e-i.offsetHeight)}},r=27,o=13,n=38,l=40,a=9;return class{constructor(c,d){let{delay:u=500,clearButton:m=!0,howManyCharacters:p=1,selectFirst:L=!1,insertToInput:v=!1,showAllValues:x=!1,cache:f=!1,disableCloseOnSelect:g=!1,classGroup:A,classPreventClosing:b,classPrefix:y,ariaLabelClear:C,onSearch:S,onResults:E=(()=>{}),onSubmit:T=(()=>{}),onOpened:B=(()=>{}),onReset:k=(()=>{}),onRender:w=(()=>{}),onClose:I=(()=>{}),noResults:R=(()=>{}),onSelectedItem:F=(()=>{})}=d;var O;this.init=()=>{const{resultList:e,root:s}=this;this.clearbutton(),((e,s,i,h,r)=>{t(s,{id:i,tabIndex:"0",role:"listbox"}),t(h,{addClass:r+"-results-wrapper"}),h.insertAdjacentElement("beforeend",s),e.parentNode.insertBefore(h,e.nextSibling)})(s,e,this.outputUl,this.resultWrap,this.prefix),s.addEventListener("input",this.handleInput),this.showAll&&s.addEventListener("click",this.handleInput),this.onRender({element:s,results:e})},this.cacheAct=(t,e)=>{const s=this.root;this.cache&&("update"===t?s.setAttribute(this.cacheData,e.value):"remove"===t?s.removeAttribute(this.cacheData):s.value=s.getAttribute(this.cacheData))},this.handleInput=t=>{let{target:e,type:s}=t;if("true"===this.root.getAttribute("aria-expanded")&&"click"===s)return;const i=e.value.replace(this.regex,"\\$&");this.cacheAct("update",e);const h=this.showAll?0:this.delay;clearTimeout(this.timeout),this.timeout=setTimeout(()=>{this.searchItem(i.trim())},h)},this.reset=()=>{var e;t(this.root,{"aria-owns":this.id+"-list","aria-expanded":"false","aria-autocomplete":"list","aria-activedescendant":"",role:"combobox",removeClass:"auto-expanded"}),this.resultWrap.classList.remove(this.isActive),(0==(null==(e=this.matches)?void 0:e.length)&&!this.toInput||this.showAll)&&(this.resultList.innerHTML=""),this.index=this.selectFirst?0:-1,this.onClose()},this.searchItem=t=>{this.value=t,this.onLoading(!0),function(t,e){void 0===t&&(t=!1),t&&(t.classList.remove("hidden"),t.addEventListener("click",e))}(this.cBtn,this.destroy),0==t.length&&this.clearButton&&this.cBtn.classList.add("hidden"),this.characters>t.length&&!this.showAll?this.onLoading():this.onSearch({currentValue:t,element:this.root}).then(e=>{const s=this.root.value.length,i=e.length;this.matches=Array.isArray(e)?[...e]:JSON.parse(JSON.stringify(e)),this.onLoading(),this.error(),0==i&&0==s&&this.cBtn.classList.add("hidden"),0==i&&s?(this.root.classList.remove("auto-expanded"),this.reset(),this.noResults({element:this.root,currentValue:t,template:this.results}),this.events()):(i>0||(t=>t&&"object"==typeof t&&t.constructor===Object)(e))&&(this.index=this.selectFirst?0:-1,this.results(),this.events())}).catch(()=>{this.onLoading(),this.reset()})},this.onLoading=t=>this.root.parentNode.classList[t?"add":"remove"](this.isLoading),this.error=()=>this.root.classList.remove(this.err),this.events=()=>{const{root:t,resultList:e}=this;t.addEventListener("keydown",this.handleKeys),t.addEventListener("click",this.handleShowItems),["mousemove","click"].map(t=>{e.addEventListener(t,this.handleMouse)}),document.addEventListener("click",this.handleDocClick)},this.results=e=>{t(this.root,{"aria-expanded":"true",addClass:this.prefix+"-expanded"}),this.resultList.innerHTML=0===this.matches.length?this.onResults({currentValue:this.value,matches:0,template:e}):this.onResults({currentValue:this.value,matches:this.matches,classGroup:this.classGroup}),this.resultWrap.classList.add(this.isActive);const i=this.classGroup?":not(."+this.classGroup+")":"";this.itemsLi=document.querySelectorAll("#"+this.outputUl+" > li"+i),this.selectFirstEl(),this.onOpened({type:"results",element:this.root,results:this.resultList}),(e=>{for(let s=0;s<e.length;s++)t(e[s],{role:"option",tabindex:"-1","aria-selected":"false","aria-setsize":e.length,"aria-posinset":s})})(this.itemsLi),s(this.resultList,this.resultWrap)},this.handleDocClick=t=>{let{target:e}=t,s=null;(e.closest("ul")&&this.disable||e.closest("."+this.prevClosing))&&(s=!0),e.id===this.id||s||this.reset()},this.selectFirstEl=()=>{const{index:e,activeList:s,selectedOption:h,selectFirst:r,root:o}=this;if(this.remAria(document.querySelector("."+s)),!r)return;const{firstElementChild:n}=this.resultList,l=this.classGroup&&this.matches.length>0&&r?n.nextElementSibling:n;t(l,{id:h+"-0",addClass:s,"aria-selected":"true"}),this.onSelected({index:e,element:o,object:this.matches[e]}),i(o,h+"-0")},this.setAttr=(t,e)=>{for(let s in e)"addClass"===s?t.classList.add(e[s]):"removeClass"===s?t.classList.remove(e[s]):t.setAttribute(s,e[s])},this.handleShowItems=()=>{const{root:e,resultWrap:i,resultList:h,isActive:r}=this;h.textContent.length>0&&!i.classList.contains(r)&&(t(e,{"aria-expanded":"true",addClass:this.prefix+"-expanded"}),i.classList.add(r),s(h,i),this.selectFirstEl(),this.onOpened({type:"showItems",element:e,results:h}))},this.handleMouse=t=>{t.preventDefault();const{target:e,type:s}=t,i=e.closest("li"),h=null==i?void 0:i.hasAttribute("role"),r=this.activeList,o=document.querySelector("."+r);i&&h&&("click"===s&&this.getTextFromLi(i),"mousemove"!==s||i.classList.contains(r)||(this.remAria(o),this.setAria(i),this.index=this.indexLiSelected(i),this.onSelected({index:this.index,element:this.root,object:this.matches[this.index]})))},this.getTextFromLi=t=>{const{root:s,index:i,disable:h}=this;t&&0!==this.matches.length?(e(t,s),this.onSubmit({index:i,element:s,object:this.matches[i],results:this.resultList}),h||(this.remAria(t),this.reset()),this.clearButton&&this.cBtn.classList.remove("hidden"),this.cacheAct("remove")):!h&&this.reset()},this.indexLiSelected=t=>Array.prototype.indexOf.call(this.itemsLi,t),this.handleKeys=t=>{const{root:s}=this,{keyCode:h}=t,c=this.resultWrap.classList.contains(this.isActive),d=this.matches.length+1;switch(this.selectedLi=document.querySelector("."+this.activeList),h){case n:case l:if(t.preventDefault(),d<=1&&this.selectFirst||!c)return;h===n?(this.index<0&&(this.index=d-1),this.index-=1):(this.index+=1,this.index>=d&&(this.index=0)),this.remAria(this.selectedLi),d>0&&this.index>=0&&this.index<d-1?(this.onSelected({index:this.index,element:s,object:this.matches[this.index]}),this.setAria(this.itemsLi[this.index]),this.toInput&&c&&e(this.itemsLi[this.index],s)):(this.cacheAct(),i(s));break;case o:this.getTextFromLi(this.selectedLi);break;case a:case r:t.stopPropagation(),this.reset()}},this.setAria=e=>{const s=this.selectedOption+"-"+this.indexLiSelected(e);t(e,{id:s,"aria-selected":"true",addClass:this.activeList}),i(this.root,s),h(e,this.outputUl,this.classGroup,this.resultList)},this.remAria=e=>{e&&t(e,{id:"",removeClass:this.activeList,"aria-selected":"false"})},this.clearbutton=()=>{if(!this.clearButton)return;const{cBtn:e}=this;t(e,{class:this.prefix+"-clear hidden",type:"button","aria-label":this.clearBtnAriLabel}),this.root.insertAdjacentElement("afterend",e)},this.destroy=()=>{const{root:t}=this;this.clearButton&&this.cBtn.classList.add("hidden"),t.value="",t.focus(),this.resultList.textContent="",this.reset(),this.error(),this.onReset(t),t.removeEventListener("keydown",this.handleKeys),t.removeEventListener("click",this.handleShowItems),document.removeEventListener("click",this.handleDocClick)},this.id=c,this.root=document.getElementById(c),this.onSearch=(O=S,Boolean(O&&"function"==typeof O.then)?S:t=>{let{currentValue:e,element:s}=t;return Promise.resolve(S({currentValue:e,element:s}))}),this.onResults=E,this.onRender=w,this.onSubmit=T,this.onSelected=F,this.onOpened=B,this.onReset=k,this.noResults=R,this.onClose=I,this.delay=u,this.characters=p,this.clearButton=m,this.selectFirst=L,this.toInput=v,this.showAll=x,this.classGroup=A,this.prevClosing=b,this.clearBtnAriLabel=C||"clear text from input",this.prefix=y?y+"-auto":"auto",this.disable=g,this.cache=f,this.outputUl=this.prefix+"-"+this.id+"-results",this.cacheData="data-cache-auto-"+this.id,this.isLoading=this.prefix+"-is-loading",this.isActive=this.prefix+"-is-active",this.activeList=this.prefix+"-selected",this.selectedOption=this.prefix+"-selected-option",this.err=this.prefix+"-error",this.regex=/[|\\{}()[\]^$+*?.]/g,this.timeout=null,this.resultWrap=document.createElement("div"),this.resultList=document.createElement("ul"),this.cBtn=document.createElement("button"),this.init()}}}();

/* slideToggle https://github.com/ericbutler555/plain-js-slidetoggle */
function slideToggle(t,e,o){0===t.clientHeight?j(t,e,o,!0):j(t,e,o)}function slideUp(t,e,o){j(t,e,o)}function slideDown(t,e,o){j(t,e,o,!0)}function j(t,e,o,i){void 0===e&&(e=400),void 0===i&&(i=!1),t.style.overflow="hidden",i&&(t.style.display="block");var p,l=window.getComputedStyle(t),n=parseFloat(l.getPropertyValue("height")),a=parseFloat(l.getPropertyValue("padding-top")),s=parseFloat(l.getPropertyValue("padding-bottom")),r=parseFloat(l.getPropertyValue("margin-top")),d=parseFloat(l.getPropertyValue("margin-bottom")),g=n/e,y=a/e,m=s/e,u=r/e,h=d/e;window.requestAnimationFrame(function l(x){void 0===p&&(p=x);var f=x-p;i?(t.style.height=g*f+"px",t.style.paddingTop=y*f+"px",t.style.paddingBottom=m*f+"px",t.style.marginTop=u*f+"px",t.style.marginBottom=h*f+"px"):(t.style.height=n-g*f+"px",t.style.paddingTop=a-y*f+"px",t.style.paddingBottom=s-m*f+"px",t.style.marginTop=r-u*f+"px",t.style.marginBottom=d-h*f+"px"),f>=e?(t.style.height="",t.style.paddingTop="",t.style.paddingBottom="",t.style.marginTop="",t.style.marginBottom="",t.style.overflow="",i||(t.style.display="none"),"function"==typeof o&&o()):window.requestAnimationFrame(l)})}

/*!
* zeynepjs v2.1.4
* A light-weight multi-level jQuery side menu plugin.
* It's fully customizable and is compatible with modern browsers such as Google Chrome, Mozilla Firefox, Safari, Edge and Internet Explorer
* MIT License
* by Huseyin ELMAS
*/
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
			if(ev.which == 27) {
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
	init: function() {
		this.setupProductRemoval();
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

		$(".account-history .history-item").on("click", '.action .btn', function(ev) {

			var details = $(".account-history-order", ev.delegateTarget);

			if(details.length === 0) {

				$.get($(this).attr('href'), function(data) {

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
	init: function() {

		this.setupOrderShow();
		this.setupOrderClose();
	}
};




/**
 * Account profile actions
 */
AimeosAccountProfile = {

	/**
	 * Reset and close the new address form
	 */
	setupAddress: function() {

		$(".account-profile-address .panel").on("show.bs.collapse", ".panel-body", function (ev) {
			$(".act-show", ev.delegateTarget).removeClass("act-show").addClass("act-hide");
		});

		$(".account-profile-address .panel").on("hidden.bs.collapse", ".panel-body", function (ev) {
			$(".act-hide", ev.delegateTarget).removeClass("act-hide").addClass("act-show");
		});
	},


	/**
	 * Adds a new delivery address form
	 */
	setupAddressNew: function() {

		$(".account-profile-address .address-delivery-new").on("show.bs.collapse", ".panel-body", function (ev) {
			$("input,select", ev.delegateTarget).prop("disabled", false);
		});

		$(".account-profile-address .address-delivery-new").on("hidden.bs.collapse", ".panel-body", function (ev) {
			$("input,select", ev.delegateTarget).prop("disabled", true);
		});

		$(".account-profile-address .address-delivery-new").on("click", '.btn-cancel', function(ev) {
			$(".panel-body", ev.delegateTarget).collapse('hide');
		});
	},


	/**
	 * Checks address form for missing or wrong values
	 */
	setupMandatoryCheck: function() {

		$(".account-profile .form-item").on("blur", "input,select", function(ev) {
			var value = $(this).val();
			var node = $(ev.delegateTarget);
			var regex = new RegExp(node.data('regex') || '.*');

			if((value !== '' && value.match(regex)) || (value === '' && !node.hasClass("mandatory"))) {
				node.removeClass("error").addClass("success");
			} else {
				node.removeClass("success").addClass("error");
			}
		});

		$(".account-profile form").on("submit", function(ev) {
			var retval = true;
			var nodes = [];

			var testfn = function(idx, element) {

				var elem = $(element);
				var value = $("input,select", elem).val();

				if(value === null || value.trim() === "") {
					elem.addClass("error");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("error");
				}
			};

			$(".form-list .mandatory", this).each(testfn);

			return retval;
		});
	},


	/**
	 * Initializes the account watch actions
	 */
	init: function() {

		this.setupAddress();
		this.setupAddressNew();
		this.setupMandatoryCheck();
	}
};


/**
 * Account subscription actions
 */
AimeosAccountSubscription = {

	/**
	 * Shows subscription details without page reload
	 */
	setupDetailShow: function() {

		$(".account-subscription .subscription-item").on("click", function(ev) {

			var details = $(".account-subscription-detail", ev.delegateTarget);

			if(details.length === 0) {

				$.get($(this).find('.action a.btn').attr("href"), function(data) {

					var doc = document.createElement("html");
					doc.innerHTML = data;

					var node = $(".account-subscription-detail", doc);
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
	setupDetailClose: function() {

		$(".account-subscription .subscription-item").on("click", ".btn-close", function(ev) {
			$(".account-subscription-detail", ev.delegateTarget).slideUp();
			return false;
		});
	},


	/**
	 * Initializes the account subscription actions
	 */
	init: function() {

		this.setupDetailShow();
		this.setupDetailClose();
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
	init: function() {

		this.setupProductRemoval();
		this.setupProductSave();
	}
};


/**
 * Basket bulk order client actions
 */
AimeosBasketBulk = {

	MIN_INPUT_LEN: 3,
	meta: {},

	/**
	 * Autocomplete for products based on entered text
	 */
	bulkcomplete: function() {

//		$.widget( "custom.bulkcomplete", $.ui.autocomplete, {
//			_create: function() {
//				this._super();
//				this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
//			},
//			_renderMenu: function(ul, items) {
//				var that = this,
//				currentCategory = "";
//				$.each(items, function(index, item) {
//					var li;
//					if(item.category != currentCategory) {
//						ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
//						currentCategory = item.category;
//					}
//					li = that._renderItemData(ul, item);
//					if(item.category) {
//						li.attr("aria-label", item.category + " : " + item.label);
//					}
//				});
//			}
//		});
	},


	/**
	 * Sets up autocompletion for the given node
	 *
	 * @param {object} node
	 */
	autocomplete: function(node) {

//		node.bulkcomplete({
//			minLength : AimeosBasketBulk.MIN_INPUT_LEN,
//			delay : 200,
//			source : function(req, resp) {
//
//				var params = {};
//				var relFilter = {};
//				var langid = AimeosBasketBulk.meta.locale && AimeosBasketBulk.meta.locale['locale.languageid'];
//				relFilter['index.text:relevance("' + langid + '","' + req.term + '")'] = 0;
//
//				var filter = {
//					filter: {'||': [{'=~': {'product.code': req.term}}, {'>': relFilter}]},
//					include: 'attribute,text,price,product'
//				};
//
//				if(AimeosBasketBulk.meta.prefix) {
//					params[AimeosBasketBulk.meta.prefix] = filter;
//				} else {
//					params = filter;
//				}
//
//				if(AimeosBasketBulk.meta.resources && AimeosBasketBulk.meta.resources['product']) {
//
//					$.getJSON(AimeosBasketBulk.meta.resources['product'], params, function(response) {
//
//						var data = [];
//						for(var key in (response.data || {})) {
//							data = data.concat(AimeosBasketBulk.get(response.data[key], response.included));
//						}
//
//						resp(data);
//					});
//				}
//			},
//			select : function(ev, ui) {
//
//				if($(".aimeos.basket-bulk tbody .details .search").last().val() != '') {
//					AimeosBasketBulk.add();
//				}
//
//				var product = $(ev.target).parent();
//				product.find(".productid").val(ui.item.id);
//				product.find(".search").val(ui.item.label);
//
//				var row = product.parent();
//				row.data('prices', ui.item['prices'] || []);
//				row.data('vattributes', ui.item['vattributes'] || []);
//				AimeosBasketBulk.update(product.parent());
//
//				return false;
//			}
//		});
	},


	/**
	 * Adds a new line to the bulk order form
	 */
	add: function() {

		var line = $("tfoot .prototype").clone();
		var len = $(".aimeos.basket-bulk tbody .details").length;

		AimeosBasketBulk.autocomplete($(".search", line));
		$('[disabled="disabled"]', line).removeAttr("disabled");
		$(".aimeos.basket-bulk tbody").append(line.removeClass("prototype"));

		$('[name]', line).each(function() {
			$(this).attr("name", $(this).attr("name").replace('_idx_', len));
		});
	},


	/**
	 * Deletes lines if clicked on the delete icon
	 */
	delete: function() {

		$(".aimeos.basket-bulk").on("click", ".buttons .delete", function(ev) {
			$(ev.currentTarget).parents(".details").remove();
		});
	},


	/**
	 * Returns the data for the current item
	 *
	 * @param {object} attr JSON:API attribute data of one entry
	 * @param {array} included JSON:API included data array
	 * @param {object} Item with "id", "label" and "prices" property
	 */
	get: function(attr, included) {

		var map = {};
		var rel = attr.relationships || {};

		for(var idx in (included || [])) {
			map[included[idx]['type']] = map[included[idx]['type']] || {};
			map[included[idx]['type']][included[idx]['id']] = included[idx];
		}

		var name = attr['attributes']['product.label'];
		var texts = this.getRef(map, rel, 'text', 'default', 'name');
		var prices = this.getRef(map, rel, 'price', 'default', 'default').sort(function(a, b) {
			return a['attributes']['price.quantity'] - b['attributes']['price.quantity'];
		});

		for(var idx in texts) {
			name = texts[idx]['attributes']['text.content'];
		}

		if(attr['attributes']['product.type'] !== 'select') {
			return [{
				'category': '',
				'id': attr.id,
				'label': attr['attributes']['product.code'] + ': ' + name,
				'prices': prices
			}];
		}


		var result = [];
		var variants = this.getRef(map, rel, 'product', 'default');

		for(var idx in variants) {

			var vrel = variants[idx]['relationships'] || {};
			var vattr = this.getRef(map, vrel, 'attribute', 'variant');
			var vprices = this.getRef(map, vrel, 'price', 'default', 'default');
			var vtexts = this.getRef(map, vrel, 'text', 'default', 'name');
			var vname = variants[idx]['attributes']['product.label'];

			for(var idx in vtexts) {
				vname = vtexts[idx]['attributes']['text.content'];
			}

			result.push({
				'category': name,
				'id': attr.id,
				'label': variants[idx]['attributes']['product.code'] + ': ' + vname,
				'prices': !vprices.length ? prices : vprices.sort(function(a, b) {
					return a['attributes']['price.quantity'] - b['attributes']['price.quantity'];
				}),
				'vattributes': vattr
			})
		}

		return result;
	},


	/**
	 * Returns the attributes for the passed domain, type and listtype
	 *
	 * @param {Object} map
	 * @param {Object} rel
	 * @param {String} domain
	 * @param {String} listtype
	 * @param {String} type
	 */
	getRef: function(map, rel, domain, listtype, type) {

		if(!rel[domain]) {
			return [];
		}

		var list = [];

		for(var idx in (rel[domain]['data'] || [])) {

			var entry = rel[domain]['data'][idx];

			if(map[domain][entry['id']] && map[domain][entry['id']]['attributes']
				&& entry['attributes']['product.lists.type'] === listtype
				&& (!type || map[domain][entry['id']]['attributes'][domain + '.type'] === type)) {

				list.push(map[domain][entry['id']]);
			}
		}

		return list;
	},


	/**
	 * Sets up autocompletion for bulk order form
	 */
	setup: function() {
//	    	var jsonurl = $(".aimeos.basket-bulk[data-jsonurl]").data("jsonurl");
//		console.log(jsonurl);

//		if(typeof jsonurl === 'undefined' || jsonurl == '') {
//			return;
//		}

//	    		new Autocomplete('bulk', {
//		    onSearch: ({ currentValue }) => {
//			const api = $(".aimeos.basket-bulk[data-jsonurl]").data("jsonurl");
//		    return new Promise((resolve) => {
//			    fetch(api)
//			    .then((response) => response.json())
//			    .then((data) => {
//			    resolve(data);
//			})
//			.catch((error) => {
//			    console.error(error);
//			});
//		    });
//		},
//
//		onResults: ({ matches }) =>
//		    matches.map((el) => `<li>${el.meta}</li>`).join(''),
//		});



//		$.ajax(jsonurl, {
//			"method": "OPTIONS",
//			"dataType": "json"
//		}).then(function(options) {
//			AimeosBasketBulk.meta = options.meta || {};
//		});

		$(".aimeos.basket-bulk").on("click", ".buttons .add", this.add);
		this.autocomplete($(".aimeos.basket-bulk .details .search"));

		$(".aimeos.basket-bulk").on("change", ".details .quantity input", function(ev) {
			AimeosBasketBulk.update($(ev.currentTarget).parents(".details").first());
		});
	},


	/**
	 * Updates the price of the given row element
	 *
	 * @param {DomElement} row HTML DOM node of the table row to update the price for
	 */
	update: function(row) {
		var qty = $(".quantity input", row).val();
		var prices = $(row).data('prices') || [];
		var vattr = $(row).data('vattributes') || [];

		for(var idx in prices) {
			if(prices[idx]['attributes']['price.quantity'] <= qty) {
				var style = {style: 'currency', currency: prices[idx]['attributes']['price.currencyid']};
				var value = Number(prices[idx]['attributes']['price.value']) * qty;
				$(row).find(".price").html(value.toLocaleString(undefined, style));
			}
		}

		var input = $(".product > .attrvarid", row);
		$(".product .vattributes", row).empty();

		for(var idx in vattr) {
			var elem = input.clone();
			elem.attr("name", input.attr("name").replace('_type_', vattr[idx]['attributes']['attribute.type']));
			elem.val(vattr[idx]['attributes']['attribute.id']);
			$(".product .vattributes", row).append(elem);
		}
	},


	/**
	 * Initializes the basket bulk actions
	 */
	init: function() {

		this.bulkcomplete();
		this.setup();
		this.delete();
	}
}



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

		$.ajax(jsonurl, {
			"method": "OPTIONS",
			"dataType": "json"
		}).then(function(options) {
			$.ajax({
				dataType: "json",
				url: options.meta.resources['basket']
			}).then(function(basket) {
				AimeosBasketMini.updateBasket(basket);
			});
		});
	},


	/**
	 * Updates the basket mini content
	 */
	updateBasket: function(basket) {

		if(!(basket.data && basket.data.attributes)) {
			return;
		}

		var attr = basket.data.attributes;
		var price = Number.parseFloat(attr['order.base.price']);
		var delivery = Number.parseFloat(attr['order.base.costs']);

		var formatter = new Intl.NumberFormat([], {
			currency: attr['order.base.currencyid'],
			style: "currency"
		});

		$(".aimeos .basket-mini-main .value").html(formatter.format(price + delivery));
		$(".aimeos .basket-mini-product .total .price").html(formatter.format(price + delivery));
		$(".aimeos .basket-mini-product .delivery .price").html(formatter.format(delivery));

		if(basket.included) {

			var csrf = '';
			var count = 0;
			var body = $(".aimeos .basket-mini-product .basket-body");
			var prototype = $(".aimeos .basket-mini-product .product.prototype");

			if(basket.meta && basket.meta.csrf) {
				csrf = basket.meta.csrf.name + '=' + basket.meta.csrf.value;
			}

			$(".aimeos .basket-mini-product .product").not(".prototype").remove();

			for(var i=0; i<basket.included.length; i++) {
				var entry = basket.included[i];

				if(entry.type === 'basket/product') {
					var product = prototype.clone();

					product.data("urldata", csrf);
					product.data("url", entry.links && entry.links.self && entry.links.self.href || '');

					$(".name", product).html(entry.attributes['order.base.product.name']);
					$(".quantity", product).html(entry.attributes['order.base.product.quantity']);
					$(".price", product).html(formatter.format(entry.attributes['order.base.product.price']));

					body.append(product.removeClass("prototype"));

					count += Number.parseInt(entry.attributes["order.base.product.quantity"]);
				}
			}

			$(".aimeos .basket-mini-main .quantity").html(count);
		}
	},


	/**
	 * Delete a product without page reload
	 */
	setupBasketDelete: function() {

		$(".aimeos .basket-mini-product").on("click", ".delete", function(ev) {

			var product = $(this).closest(".product");

			$.ajax(product.data("url"), {
				"method": "DELETE",
				"dataType": "json",
				"data": product.data("urldata")
			}).then(function(basket) {
				AimeosBasketMini.updateBasket(basket);
			});

			return false;
		});
	},


	/**
	 * Initializes the basket mini actions
	 */
	init: function() {

		this.setupBasketDelete();
	}
};


/**
 * Basket related client actions
 */
AimeosBasketRelated = {

	/**
	 * Initializes the basket related actions
	 */
	init: function() {
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
			$(".btn-action", ev.delegateTarget).hide();
		});
	},


	/**
	 * Updates basket without page reload
	 */
	setupUpdateSubmit: function() {

		$("body").on("submit", ".basket-standard form", function(ev) {
			var form = $(this);

			Aimeos.createSpinner();
			$.post(form.attr("action"), form.serialize(), function(data) {
				$(".basket-standard").html(AimeosBasketStandard.updateBasket(data).html());
			}).always(function() {
				Aimeos.removeSpinner();
			});

			return false;
		});
	},


	/**
	 * Updates quantity and deletes products without page reload
	 */
	setupUpdateChange: function() {

		$("body").on("click", ".basket-standard a.change", function(ev) {

			Aimeos.createSpinner();
			$.get($(this).attr("href"), function(data) {
				$(".basket-standard").html(AimeosBasketStandard.updateBasket(data).html());
			}).always(function() {
				Aimeos.removeSpinner();
			});

			return false;
		});
	},


	/**
	 * Initializes the basket standard actions
	 */
	init: function() {

		this.setupBasketBack();
		this.setupUpdateHide();
		this.setupUpdateSubmit();
		this.setupUpdateChange();
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

		$(".catalog-detail-basket-selection .selection, .catalog-list-items .items-selection .selection").on("change", ".select-list", function(event) {

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


			$(".select-list", event.delegateTarget).each(function(idx, select) {

				if( event.currentTarget == select ) {
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

		$(".catalog-detail-basket-selection .selection, .catalog-list-items .items-selection .selection").on("change", ".select-list", function(event) {

			var stock = false;
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

		$(".catalog-detail-basket-selection, .catalog-list-items .items-selection").on("click", ".addbasket .btn-action", {}, function(event) {

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

		$(".catalog-detail-basket form, .catalog-list-items form").on("submit", function(ev) {

			Aimeos.createOverlay();
			$.post($(this).attr("action"), $(this).serialize(), function(data) {
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

			$.ajax({
				url: $(this).attr("action"),
				data: new FormData(this),
				processData: false,
				contentType: false,
				method: 'POST'
			}).done(function(data) {

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

			$.ajax({
				url: $(this).attr("action"),
				data: new FormData(this),
				processData: false,
				contentType: false,
				method: 'POST'
			}).done(function(data) {

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
 * Catalog filter actions
 */
AimeosCatalogFilter = {

//	MIN_INPUT_LEN: 3,

	/**
	 * Autocompleter for quick search
	 */
	setupSearchAutocompletion: function() {

//		var aimeosInputComplete = $(".catalog-filter-search .value");
//
//		if(aimeosInputComplete.length) {
//			aimeosInputComplete.autocomplete({
//				minLength : AimeosCatalogFilter.MIN_INPUT_LEN,
//				delay : 200,
//				source : function(req, resp) {
//					var nameTerm = {};
//					nameTerm[aimeosInputComplete.attr("name")] = req.term;
//
//					$.getJSON(aimeosInputComplete.data("url"), nameTerm, function(data) {
//						resp(data);
//					});
//				},
//				select : function(ev, ui) {
//					aimeosInputComplete.val(ui.item.label);
//					return false;
//				}
//			}).autocomplete("instance")._renderItem = function(ul, item) {
//				return $(item.html).appendTo(ul);
//			};
//		}


		$(".catalog-filter-search .value").each(function(idx, el){

			new Autocomplete('complex', {
				//  selectFirst: true,

				// onSearch
				onSearch: ({ currentValue }) => {
					// static file
					const api = $(el).data("url");

					return new Promise((resolve) => {
						fetch(api)
						.then((response) => response.json())
						.then((data) => {
							const result = data
							.sort((a, b) => a.label.localeCompare(b.label))
							.filter((element) => {
								return element.label.match(new RegExp(currentValue, 'gi'));
							});
							resolve(result);
						})
						.catch((error) => {
							console.error(error);
						});
					});
				},

				onResults: ({ currentValue, matches }) => {
					return matches
					.map(({ label, html }) => {
						return `
						<!--<li class="loupe">-->
						<!--<p>${label.replace(
							new RegExp(currentValue, 'gi'),
							(str) => `<b>${str}</b>`
						)}</p>-->
						${html}
						<!--</li>-->`;
					})
					.join('');
				},

				// event onsubmit
//				onSubmit: ({ index, element, object }) => {
//					const { label, html } = object;
//
//					console.table('static-file-data', index, element, object);
//
//					const template = `
//					<p>name - ${label}</p>
//					<div class="desc">${html}</div>`;
//
//				},

				// get index and data from li element after
				// hovering over li with the mouse
//				onSelectedItem: ({ index, element, object }) => {
//					console.log('onSelectedItem:', index, element.value, object);
//				},
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
	 * Sets up the fade out of the catalog list
	 */
	setupListFadeout: function() {

		$(".catalog-filter-tree li.cat-item").on("click", function() {
			$(".catalog-list").fadeTo(1000, 0.5);
		});
	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeToggle: function() {

		const divToToggle = document.querySelector('.attribute-lists');

		document.querySelectorAll('.attr-header').forEach(function(el) {
			el.addEventListener('click', () => {
			      slideToggle(divToToggle, 350, () => {});
			});
		});

	},

	/**
	 * Toggles the Last Seen filters if hover isn't available
	 */

	setupLastSeenToggle: function() {

		// when you click the button, slide the div up and down:
		const divToToggle = document.querySelector('.seen-items');

		document.querySelectorAll('.seen').forEach(function(el) {
		    el.addEventListener('click', () => {
			slideToggle(divToToggle, 350, () => {});
		    });
		});

	},

	/**
	 * Toggles pinned items
	 */

	setupPinnedToggle: function() {

		// when you click the button, slide the div up and down:
		const divToToggle = document.querySelector('.pinned-items');

		document.querySelectorAll('.pinned').forEach(function(el) {
		    el.addEventListener('click', () => {
			slideToggle(divToToggle, 350, () => {});
		    });
		});

	},


	/**
	 * Toggles the attribute filters if hover isn't available
	 */
	setupAttributeListsToggle: function() {

		$(".catalog-filter-attribute .attribute-lists .fieldsets .attr-list").hide();

		// only needed if collapsing other panels when opening
//		const allPanels = document.querySelectorAll('.fieldsets .attr-list');

		document.querySelectorAll(".fieldsets").forEach(function(el) {

			el.addEventListener('click', function(e) {
			    if (e.target.classList.contains('attr-type')) {
				    const nextPanel = e.target.nextElementSibling;
				    slideToggle(nextPanel, 350);

				    //to make other panels collapse when you open one:
//				    allPanels.forEach(function(el) {
//					    if (el.style.display !== "none" && el !== nextPanel) slideUp(el, 350);
//				    });
			    }
			});

		});
	},


	/**
	 * Hides the attribute filter if no products are available for
	 */
	setupAttributeListsEmtpy: function() {

		$(".catalog-filter-attribute .attribute-lists fieldset").hide();

		$(".catalog-filter-attribute .attribute-lists .attr-count").each(function(idx, el) {
			$(el).parent().parent().parent().show();
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

		$(".catalog-filter-price").on("input", ".price-high", function(ev) {
			$(".price-slider", ev.delegateTarget).val($(ev.currentTarget).val());
		});

		$(".catalog-filter-price").on("input", ".price-slider", function(ev) {
			$(".price-high", ev.delegateTarget).val($(ev.currentTarget).val());
		});
	},


	/**
	 * Toggles the price filters if hover isn't available
	 */
	setupPriceToggle: function() {

		// when you click the button, slide the div up and down:
		const divToToggle = document.querySelector('.price-lists');

		document.querySelectorAll('.catalog-filter-price').forEach(function(el) {
		    el.addEventListener('click', () => {
			slideToggle(divToToggle, 350, () => {});
		    });
		});

	},


	/**
	 * Toggles the supplier filters if hover isn't available
	 */
	setupSupplierToggle: function() {

		// when you click the button, slide the div up and down:
		const divToToggle = document.querySelector('.supplier-lists');

		document.querySelectorAll('.catalog-filter-supplier h2').forEach(function(el) {
		    el.addEventListener('click', () => {
			slideToggle(divToToggle, 350, () => {});
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

		$(".catalog-filter-search").on("keyup", ".value", function(ev) {
			if ($(this).val() !== "") {
				$(".reset .symbol", ev.delegateTarget).css("visibility", "visible");
			} else {
				$(".reset .symbol", ev.delegateTarget).css("visibility", "hidden");
			}
		});

		$(".catalog-filter-search").on("click", ".reset", function(ev) {
			$(".symbol", this).css("visibility", "hidden");
			$(".value", ev.delegateTarget).val("");
			$(".value", ev.delegateTarget).focus();
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
		this.setupListFadeout();

		this.setupAttributeItemSubmit();
		this.setupSupplierItemSubmit();

		this.setupFormChecks();
		this.setupSearchTextReset();
		this.setupSearchAutocompletion();
	}
};


/**
 * Catalog home actions
 */
AimeosCatalogHome = {

	/**
	 * Home slider
	 */
	setupSlider: function() {

	},


	/**
	 * Initialize the catalog home actions
	 */
	init: function() {

		this.setupSlider();
	}
};


/**
 * Catalog list actions
 */
AimeosCatalogList = {

	/**
	 * Add to basket
	 */
	setupAddBasket: function() {

		$(".catalog-list-items .list-items:not(.list) .product").on("click", ".btn-primary", function(ev) {

			var empty = true;

			$(".basket .items-selection .selection li, .basket .items-attribute .selection li", ev.delegateTarget).each(function() {
				if($(this).length) {
					empty = false; return false;
				}
			});

			if(!empty) {
				$("form.basket", ev.delegateTarget).on("click", ".btn-primary", function(ev) {
					$.post($(ev.delegateTarget).attr("action"), $(ev.delegateTarget).serialize(), function(data) {
						Aimeos.createContainer(AimeosBasketStandard.updateBasket(data));
					});

					return false;
				});

				Aimeos.createOverlay();
				Aimeos.createContainer($('<div class="catalog-list catalog-list-items">')
					.append($('<div class="list-items list">').append(ev.delegateTarget)) );
				return false;
			}
		});
	},


	/**
	 * Switches product images on hover
	 */
	setupImageSwitch: function() {

//		$(".catalog-list-items .product .media-list").on("mouseenter", function() {
//			var list = $(".media-item", this);
//
//			if( list.length > 1 ) {
//				var second = list.eq(1);
//				var size = $(this).outerHeight();
//				var image = $("img", second);
//
//				$(this).css("background-image", "none"); // Don't let default image shine through
//				image.attr("srcset", image.data("srcset"));
//				image.attr("src", image.data("src"));
//				second.fadeTo(0, 0.33);
//
//				list.first().fadeTo(400, 0.33, function() {
//					list.css('top', -size);
//					second.fadeTo(400, 1);
//				});
//			}
//		});
//
//		$(".catalog-list-items .product .media-list").on("mouseleave", function() {
//			var list = $(".media-item", this);
//
//			if( list.length > 1 ) {
//				list.first().css('opacity', 0.33);
//
//				list.eq(1).fadeTo(400, 0.33, function() {
//					list.css('top', 0);
//					list.first().fadeTo(400, 1);
//				});
//			}
//		});

		$(".catalog-list-items .product .media-list a").on("click", function(ev) {
			window.location.href = $(this).attr('href');
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

					$.ajax({
						url: infiniteUrl
					}).fail( function() {
						list.data('infinite-url', infiniteUrl);
					}).done( function(response) {

						var nextPage = $(response);
						var nextUrl = nextPage.find('.catalog-list-items').data( 'infinite-url' );

						$('.list-items', list).append(nextPage.find('.catalog-list-items .list-items .product'));
						list.data('infinite-url', nextUrl);
						$(nextPage).filter( function (i,a){ return $(a).is('script.catalog-list-stock-script');}).each( function() {
							var script = document.createElement('script');
							script.src = $(this).attr("src");
							document.head.appendChild(script);
						});
						Aimeos.loadImages();

						$(window).trigger('scroll');
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

			$.ajax({
				url: url
			}).done( function(response) {
				var doc = document.createElement("html");
				doc.innerHTML = response;

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
		this.setupImageSwitch();
		this.setupInfiniteScroll();
		this.setupPinned();
	}
};


/**
 * Catalog session actions
 */
AimeosCatalogSession = {

	/**
	 * Initializes the catalog session actions
	 */
	init: function() {
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

		/* Address form slide up/down when selected */
		
		const divToToggle = document.querySelector('.checkout-standard-address-delivery .item-new .form-list');

		document.querySelectorAll('.checkout-standard-address-delivery .header input').forEach(function(el) {
			el.addEventListener('click', (
				) => {
				if( $('.checkout-standard-address-delivery .item-like .header input').is(":checked") ) {
					slideUp(divToToggle, 350, () => {});
				} else {
					slideDown(divToToggle, 350, () => {});
				}
			});
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
			$(elem).find(".item-service").has("input.option:checked").find(".form-list").show();
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
				var value = $("input,select", elem).val();

				if(value === null || value.trim() === "") {
					elem.addClass("error");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("error");
				}
			};

			var item = $(".checkout-standard .item-new, .item-service").each(function(node) {

				if($(".header,label input", this).is(":checked")) {
					$(".form-list .mandatory", this).each(testfn);
				}
			});

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

		var form = $(".checkout-standard form").first();
		var node = $(".checkout-standard-process", form);
		var anchor = $("a.btn-action", node);

		if(anchor.length > 0) {
			window.location = anchor.attr("href");
		} else if(node.length > 0 && node.has(".mandatory").length === 0 && node.has(".optional").length === 0 && form.attr("action") !== '' ) {
			form.submit();
		}
	},


	/**
	 * Initializes the checkout standard section
	 */
	init: function() {

		this.setupAddressForms();
		this.setupServiceForms();

		this.setupCountryState();

		this.setupMandatoryCheck();
		this.setupPaymentRedirect();
	}
};


/**
 * Checkout confirm client actions
 */
AimeosCheckoutConfirm = {

	/**
	 * Initializes the checkout confirm section
	 */
	init: function() {
	}
};


/**
 * CMS page actions
 */
AimeosCmsPage = {

	/**
	 * CMS page sliders
	 */
	setupSlider: function() {

	},


	/**
	 * Initialize the CMS page actions
	 */
	init: function() {

		this.setupSlider();
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

		$(".select-menu .select-dropdown").on('click', function() {
			$("ul", this).toggleClass("active");
			$(this).toggleClass("active");
		});
	},


	/**
	 * Initializes the locale selector actions
	 */
	init: function() {
		this.setupMenuToggle();
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
 * CSS3 support for IE8
 */
document.createElement("nav");
document.createElement("section");
document.createElement("article");


/*
 * Disable CSS rules only necessary if no Javascript is available
 */
$("html").removeClass("no-js");


$(function() {

	Aimeos.init();

	AimeosPage.init();
	AimeosCmsPage.init();
	AimeosLocaleSelect.init();

	AimeosCatalog.init();
	AimeosCatalogHome.init();
	AimeosCatalogFilter.init();
	AimeosCatalogList.init();
	AimeosCatalogSession.init();
	AimeosCatalogStage.init();

	AimeosBasketBulk.init();
	AimeosBasketMini.init();
	AimeosBasketRelated.init();
	AimeosBasketStandard.init();

	AimeosCheckoutStandard.init();
	AimeosCheckoutConfirm.init();

	AimeosAccountProfile.init();
	AimeosAccountSubscription.init();
	AimeosAccountHistory.init();
	AimeosAccountFavorite.init();
	AimeosAccountWatch.init();

	Aimeos.loadImages();
});