/**
 * Basket standard client actions
 */
AimeosBasketStandard = {

	/**
	 * Goes back to underlying page when back or close button of the basket is clicked
	 */
	setupBasketBack: function() {

		$("body").on("click", ".basket-standard .btn-back", function() {
			return Aimeos.removeOverlay();
		});
	},


	/**
	 * Hides the update button and show only on quantity change
	 */
	setupUpdateHide: function() {

		$(".basket-standard .btn-update").hide();

		$("body").on("focusin", ".basket-standard .basket .product .quantity .value", {}, function() {
			$(".btn-update").show();
			$(".btn-action").hide();
		});
	},


	/**
	 * Updates basket without page reload
	 */
	setupUpdateSubmit: function() {

		$("body").on("submit", ".basket-standard form", function() {

			Aimeos.createSpinner();
			fetch(product.data("url"), {
				body: new FormData(this),
				method: 'POST'
			}).then(function(response) {
				return response.text();
			}).then(function(data) {
				$(".basket-standard").html(AimeosBasket.updateBasket(data).html());
			}).finally(() => {
				Aimeos.removeSpinner();
			});

			return false;
		});
	},


	/**
	 * Updates quantity and deletes products without page reload
	 */
	setupUpdateChange: function() {

		$("body").on("click", ".basket-standard a.change", function(ev) {

			Aimeos.createSpinner();
			fetch($(this).attr("href")).then(response => {
				return response.text();
			}).then(data => {
				$(".basket-standard").html(AimeosBasket.updateBasket(data).html());
			}).finally(function() {
				Aimeos.removeSpinner();
			});

			return false;
		});
	},


	/**
	 * Initializes the basket standard actions
	 */
	init: function() {

		this.setupBasketBack();
		this.setupUpdateHide();
		this.setupUpdateSubmit();
		this.setupUpdateChange();
	}
};


$(function() {
	AimeosBasketStandard.init();
});