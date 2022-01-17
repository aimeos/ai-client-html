/**
 * Account history actions
 */
AimeosAccountHistory = {

	/**
	 * Shows history details without page reload
	 */
	onToggleDetail() {

		$(".account-history").on("click", ".history-item .btn.show, .history-item .btn.close", (ev) => {

			const target = $(ev.currentTarget).closest(".history-item");
			const details = $(".account-history-detail", target);

			if(details.length === 0) {

				fetch(target.data("url")).then(response => {
					return response.text();
				}).then(data => {
					const doc = $("<html/>").html(data);
					const node = $(".account-history-detail", doc);

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
	 * Initializes the account history actions
	 */
	init() {
		this.onToggleDetail();
	}
};


$(() => {
	AimeosAccountHistory.init();
});