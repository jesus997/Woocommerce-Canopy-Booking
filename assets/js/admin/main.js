(function($){
	function toggleChildPriceInput() {
		var sl = $("select#product-type[name=\"product-type\"]"), ci = $(".second_price_field");
		if(sl.val() === "canopytour" || sl.val() === "variable_canopytour") {
			ci.show();
		} else {
			ci.hide();
		}
	}
	$(document).ready(function() {
		//$("#canopytour_options .options_group").append($("#acf-group_5b8804f5a1b49"));
		$("#product-type[name=\"product-type\"]").on("change", function() {
			toggleChildPriceInput();
		});
		toggleChildPriceInput();
	});
})(jQuery);