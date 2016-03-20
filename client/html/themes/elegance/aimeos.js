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
hide_or_show_catalog_filter_search_reset_button: function() {
	if ($(this).val() !== '')
		this.nextElementSibling.style.display = 'inline-block';
	else
		this.nextElementSibling.style.display = 'none';
}



/**
 * Resets the currently catalog filter search.
 * In the simplest case the entered search term was not submitted
 * yet, such that the field is just cleared.
 * The major purpose of this field is to reset the search without
 * having to manually empty the input field and submit it to show
 * all items of all categories again.
 */
reset_catalog_filter_search: function() {
//	var catalog_filter_search_input = $('.catalog-filter-search input:nth-of-type(1)');
//	catalog_filter_search_input.val('');
	$(this.previousElementSibling).val('');
	this.parentNode.parentNode.submit();
	this.style.display = 'none';
}



/* Register events: */
$('.catalog-filter-search input:nth-of-type(1)').on('keyup', hide_or_show_catalog_filter_search_reset_button);
$('.catalog-filter-search div:nth-of-type(1)').on('click', reset_catalog_filter_search);

