/**
 * Account subscription actions
 */
AimeosAccountSubscription = {

	/**
	 * Shows subscription details without page reload
	 */
	setupDetailShow: function() {

		$(".account-subscription .subscription-item").on("click", function() {

			var target = $(this).parents(".subscription-item");
			var details = $(".account-subscription-detail", target);

			if(details.length === 0) {

				fetch($(this).find('.action a.btn').attr("href")).then(response => {
					return response.text();
				}).then(data => {
					var doc = document.createElement("html");
					doc.innerHTML = data;

					var node = $(".account-subscription-detail", doc);
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
	setupDetailClose: function() {

		$(".account-subscription .subscription-item").on("click", ".btn-close", function() {
			$(".account-subscription-detail", $(this).parents(".subscription-item")).each(function() {
				slideUp(this, 300);
			});
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


$(function() {
	AimeosAccountSubscription.init();
});