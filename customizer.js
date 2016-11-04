(function($){
	wp.customize("gaproperty", function(value) {
		value.bind(function(newval) {
			$("#gaproperty").html(newval);
		} );
	});
})(jQuery);
