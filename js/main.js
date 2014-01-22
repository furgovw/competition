(function($){
	$(function() {
		$('.most-viewed').hide();

		$('.most-viewed-toggle').on('click', function() {
			$('.most-viewed').toggle();
			return false;
		});

		$('.options').hide();

		$('.options-toggle').on('click', function() {
			$('.options').toggle();
			return false;
		});

		$('.added-topics').hide();

		$('.added-topics-toggle').on('click', function() {
			$('.added-topics').toggle();
			return false;
		});
	});
})(jQuery);