/**
 * Account profile actions
 */
AimeosAccountProfile = {

	/**
	 * Reset and close the new address form
	 */
	setupAddress: function() {

		document.querySelectorAll(".account-profile-address .panel").forEach((el) => {
			el.addEventListener("show.bs.collapse", function(ev) {
				$(".act-show", ev.currentTarget).removeClass("act-show").addClass("act-hide");
			});
		});

		document.querySelectorAll(".account-profile-address .panel").forEach((el) => {
			el.addEventListener("hidden.bs.collapse", function(ev) {
				$(".act-hide", ev.currentTarget).removeClass("act-hide").addClass("act-show");
			});
		});
	},


	/**
	 * Adds a new delivery address form
	 */
	setupAddressNew: function() {

		document.querySelectorAll(".account-profile-address .address-delivery-new").forEach((el) => {
			el.addEventListener("show.bs.collapse", function(ev) {
				$("input,select", ev.currentTarget).prop("disabled", false);
			});
		});

		document.querySelectorAll(".account-profile-address .address-delivery-new").forEach((el) => {
			el.addEventListener("hidden.bs.collapse", function(ev) {
				$("input,select", ev.currentTarget).prop("disabled", true);
			});
		});

		document.querySelectorAll(".account-profile-address .address-delivery-new .btn-cancel").forEach((el) => {
			el.addEventListener("click", function(ev) {
				var node = $(".panel-body", $(ev.currentTarget).parents(".address-delivery-new")).get(0);
				bootstrap.Collapse.getInstance(node).hide();
			});
		});
	},


	/**
	 * Checks address form for missing or wrong values
	 */
	setupMandatoryCheck: function() {

		$(".account-profile .form-item").on("blur", "input,select", function() {
			var value = $(this).val();
			var node = $(this).parents(".form-item");
			var regex = new RegExp(node.data('regex') || '.*');

			if((value !== '' && value.match(regex)) || (value === '' && !node.hasClass("mandatory"))) {
				node.removeClass("error").addClass("success");
			} else {
				node.removeClass("success").addClass("error");
			}
		});

		$(".account-profile form").on("submit", function() {
			var retval = true;
			var nodes = [];

			var testfn = function(idx, element) {

				var elem = $(element);
				var value = $("input,select", elem).val();

				if(value === null || value.trim() === "") {
					elem.addClass("error");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("error");
				}
			};

			$(".form-list .mandatory", this).each(testfn);

			return retval;
		});
	},


	/**
	 * Initializes the account watch actions
	 */
	init: function() {

		this.setupAddress();
		this.setupAddressNew();
		this.setupMandatoryCheck();
	}
};


$(function() {
	AimeosAccountProfile.init();
});