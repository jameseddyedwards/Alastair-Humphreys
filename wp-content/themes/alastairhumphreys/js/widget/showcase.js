(function($) {

	function featureSlider() {
		$('.feature-slider a').click(function(e) {
			$('.featured-posts section.featured-post').css({
				opacity: 0,
				visibility: 'hidden'
			});
			$(this.hash).css({
				opacity: 1,
				visibility: 'visible'
			});
			$('.feature-slider a').removeClass('active');
			$(this).addClass('active');
			e.preventDefault();
		});
	}

	function tabbed(element) {
		var tab = $("li", element);
		tab.click(function(){
			if (!this.hasClass("active")){
				$(".tab-row").hide();
				$('.' + this.attr("id")).show();
			}
		});
	}

	$(document).ready( function() {
		featureSlider();
		tabbed(".tabs");
	});
})(jQuery);