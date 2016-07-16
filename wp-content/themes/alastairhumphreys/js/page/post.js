(function($){

	var featureImageCont = $("#feature-wide"),
		imageHeight, newHeight;

    function updateTitlePadding() {

    }

	function updateImageHeight() {
		imageHeight = $("img", featureImageCont).height();

		newHeight = imageHeight < 320 ? imageHeight - 35 : imageHeight - 150;
		featureImageCont.height(newHeight);
        updateTitlePadding(newHeight);
	}

	if (featureImageCont.length > 0) {
		
        featureImageCont.imagesLoaded(function() {
			updateImageHeight();
		});

        $(window).resize(function() {
            updateImageHeight();
        });

	}

})(jQuery);


/* Smooth Scrolling Animation */
function smoothScroll(el, to, duration) {
    if (duration < 0) {
        return;
    }

    var difference = to - jQuery(window).scrollTop(),
    	perTick = difference / duration * 10;

    this.scrollToTimerCache = setTimeout(function() {
        if (!isNaN(parseInt(perTick, 10))) {
            window.scrollTo(0, jQuery(window).scrollTop() + perTick);
            smoothScroll(el, to, duration - 10);
        }
    }.bind(this), 10);
}

jQuery('.scrollTo').on('click', function(e) {
    e.preventDefault();
    
    var offset = "100",
        position = jQuery(jQuery(e.currentTarget).attr('href')).offset().top - offset;
    
    if (position !== null) {
        smoothScroll(jQuery(window), position, 400);
    }
});