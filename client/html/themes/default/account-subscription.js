/**
 * Account subscription actions
 */
AimeosAccountSubscription = {

	/**
	 * Shows subscription details without page reload
	 */
	onToggleDetail() {

		$(".account-subscription").on("click", ".subscription-item .btn.show, .subscription-item .btn.close", (ev) => {

			const target = $(ev.currentTarget).closest(".subscription-item");
			const details = $(".account-subscription-detail", target);

			if(details.length === 0) {

				fetch(target.data("url")).then(response => {
					return response.text();
				}).then(data => {
					const doc = $("<html/>").html(data);
					const node = $(".account-subscription-detail", doc);

					if(node.length) {
						node.css("display", "none");
						target.append(node);
						slideDown(node[0], 300);
					}
				});

			} else {
				slideToggle(details[0], 300);
			}

			$(".btn.show", target).toggleClass('hidden');
			$(".btn.close", target).toggleClass('hidden');

			return false;
		});
	},


	/**
	 * Initializes the account subscription actions
	 */
	init() {
		this.onToggleDetail();
	}
};


$(() => {
	AimeosAccountSubscription.init();
});