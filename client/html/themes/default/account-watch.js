/**
 * Account watch actions
 */
AimeosAccountWatch = {

	/**
	 * Deletes a watched item without page reload
	 */
	setupProductRemoval: function() {

		$("body").on("click", ".account-watch .delete", function() {

			var form = $(this).parents("form");
			$(this).parents("watch-item").addClass("loading");

			fetch(form.attr("action"), {
				body: new FormData(form.get(0)),
				method: 'POST'
			}).then(response => {
				return response.text();
			}).then(data => {
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

		$("body").on("click", ".account-watch .standardbutton", function() {

			var form = $(this).parents("form.watch-details");
			form.addClass("loading");

			fetch(form.attr("action"), {
				body: new FormData(form.get(0)),
				method: 'POST'
			}).then(function(response) {
				return response.text();
			}).then(function(data) {
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


$(function() {
	AimeosAccountWatch.init();
});