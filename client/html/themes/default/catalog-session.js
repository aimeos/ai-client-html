/**
 * Catalog session client actions
 */
AimeosCatalogSession = {

	/**
	 * Delete a product without page reload
	 */
	onRemoveProduct: function() {

		$("body").on("click", ".catalog-session-pinned .delete", function() {

			var form = $(this).closest("form");
			var prodid = $(this).closest(".product").data('prodid');

			fetch(form.attr("action"), {
				method: "POST",
				body: new FormData(form[0])
			}).then(response => {
				return response.text();
			}).then(html => {
				var doc = document.createElement("html");
				doc.innerHTML = html;

				$(".catalog-session-pinned").replaceWith($(".catalog-session-pinned", doc));
				$('.product[data-prodid="' + prodid + '"] .btn-pin').removeClass('active');
			});

			return false;
		});
	},


	/**
	 * Initializes the catalog session actions
	 */
	init: function() {
		this.onRemoveProduct();
	}
};


$(function() {
	AimeosCatalogSession.init();
});