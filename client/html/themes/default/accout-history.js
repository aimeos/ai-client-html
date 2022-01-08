/**
 * Account history actions
 */
AimeosAccountHistory = {

	/**
	 * Shows order details without page reload
	 */
	setupOrderShow: function() {

		$(".account-history .history-item").on("click", '.action .btn', function() {

			var target = $(this).parents(".history-item");
			var details = $(".account-history-order", target);

			if(details.length === 0) {

				fetch($(this).attr("href")).then(response => {
					return response.text();
				}).then(data => {
					var doc = document.createElement("html");
					doc.innerHTML = data;

					var node = $(".account-history-order", doc);
					node.css("display", "none");
					target.append(node);
					slideDown(node.get(0), 300);
				});

			} else {
				slideToggle(details.get(0), 300);
			}

			return false;
		});
	},


	/**
	 * Closes the order details without page reload
	 */
	setupOrderClose: function() {

		$(".account-history .history-item").on("click", ".btn-close", function() {
			$(".account-history-order", $(this).parents(".history-item")).each(function() {
				slideUp(this, 300);
			});
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


$(function() {
	AimeosAccountHistory.init();
});