/**
 * Account favorite actions
 */
AimeosAccountFavorite = {

	/**
	 * Deletes a favorite item without page reload
	 */
	setupProductRemoval: function() {

		$("body").on("click", ".account-favorite .delete", function() {

			var form = $(this).parents("form");
			$(this).parents("favorite-item").addClass("loading");

			fetch(form.attr("action"), {
				body: new FormData(form.get(0)),
				method: 'POST'
			}).then(response => {
				return response.text();
			}).then(data => {
				var doc = document.createElement("html");
				doc.innerHTML = data;

				if($(".aimeos.account-favorite .favorite-items", doc).length) {
					$(".aimeos.account-favorite").html($(".aimeos.account-favorite", doc).html());
				} else {
					Aimeos.removeOverlay();
				}
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


$(function() {
	AimeosAccountFavorite.init();
});