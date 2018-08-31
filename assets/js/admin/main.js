(function($){
	function toggleChildPriceInput() {
		var sl = $("#product-type[name=\"product-type\"]"), ci = $(".price_children_field");
		if(sl.val() === "canopytour") {
			ci.show();
		} else {
			ci.hide();
		}
	}
	$(document).ready(function() {
		$("#canopytour_options .options_group").append($("#acf-group_5b8804f5a1b49"));
		$("#product-type[name=\"product-type\"]").on("change", function() {
			toggleChildPriceInput();
		});
		toggleChildPriceInput();
	});
})(jQuery);