/**
 * Specific JS for the elegance theme
 * 
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */



/**
 * Hides or shows the catalog filter search reset button
 * depending on if the input text field is empty or not.
 */
AimeosCatalogFilter.hideOrShowCatalogFilterTextSearchResetButton = function() {
	if ($(this).val() !== '') {
		$(this.nextElementSibling).css('display', 'inline-block');
	}
	else {
		$(this.nextElementSibling).css('display', 'none');
	}
};



/**
 * Resets the currently catalog filter search.
 * In the simplest case the entered search term was not submitted
 * yet, such that the field is just cleared.
 * The major purpose of this field is to reset the search without
 * having to manually empty the input field and submit it to show
 * all items of all categories again.
 */
AimeosCatalogFilter.resetCatalogFilterTextSearch = function() {
//	var catalog_filter_search_input = $('.catalog-filter-search input:nth-of-type(1)');
//	catalog_filter_search_input.val('');
	$(this.previousElementSibling).val('');
	//this.parentNode.parentNode.submit();
	$(this).css('display', 'none');
};



/**
 * Registers events for the catalog filter search input reset.
 */
AimeosCatalogFilter.setupCatalogFilterTextSearchReset = function() {
	$('.catalog-filter-search input:first-of-type').on('keyup', AimeosCatalogFilter.hideOrShowCatalogFilterTextSearchResetButton);
	$('.catalog-filter-search div:first-of-type').on('click', AimeosCatalogFilter.resetCatalogFilterTextSearch);
};



/**
 * Initalize preserving existing init without duplicating code.
 */
AimeosCatalogFilter.init = (function(fcn) {
	return function() {
		fcn(this);
		this.setupCatalogFilterTextSearchReset();
	}
})(AimeosCatalogFilter.init);
