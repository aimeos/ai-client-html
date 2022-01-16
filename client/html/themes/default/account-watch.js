/**
 * Account watch actions
 */
AimeosAccountWatch = {

	/**
	 * Deletes a watched item without page reload
	 */
	setupRemoveProduct: function() {

		$("body").on("click", ".account-watch .delete", function() {

			var form = $(this).closest("form");
			$(this).closest("watch-item").addClass("loading");

			fetch(form.attr("action"), {
				body: new FormData(form.get(0)),
				method: 'POST'
			}).then(response => {
				return response.text();
			}).then(data => {
				var doc = document.createElement("html");
				doc.innerHTML = data;

				if($(".aimeos.account-watch .watch-items", doc).length) {
					$(".aimeos.account-watch").html($(".aimeos.account-watch", doc).html());
				} else {
					Aimeos.removeOverlay();
				}
			});

			return false;
		});
	},


	/**
	 * Saves a modifed watched item without page reload
	 */
	setupSaveProduct: function() {

		$("body").on("click", ".account-watch .btn-action", function() {

			var form = $(this).closest("form.watch-details");
			form.addClass("loading");

			fetch(form.attr("action"), {
				body: new FormData(form.get(0)),
				method: 'POST'
			}).then(function(response) {
				return response.text();
			}).then(function(data) {
				var doc = document.createElement("html");
				doc.innerHTML = data;

				$(".aimeos.account-watch").html($(".aimeos.account-watch", doc).html());
			});

			return false;
		});
	},


	/**
	 * Initializes the account watch actions
	 */
	init: function() {

		this.setupRemoveProduct();
		this.setupSaveProduct();
	}
};


$(function() {
	AimeosAccountWatch.init();
});