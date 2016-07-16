function postFilter($) {
	var container = $("#category-links"),
		links = $("a", container),
		pagination = $("#pagination"),
		paginationLinks = $("a", pagination),
		pathname = window.location.href,
		posts = $("#category-posts"),
		thumbs,
		wrapper = $('<div id="wrapper" class="wrapper">'),
		url;

	function initPaginationAjax() {
		$("a", "#pagination").click(function(event){
			event.preventDefault();

			url = $(this).attr("href");
			ajaxPosts(url);
		});
	}

	function ajaxPosts(url) {
		posts.fadeOut(); // Fade post out to allow Ajax replace

		$.get(url, function(data) {
			thumbs = $(data).find("#category-posts").html();
			newPagination = $(data).find("#pagination").html();

			posts.html(thumbs); // Replace posts with Ajax'd posts (while hidden)

			$("#wrapper").animate({height : $("#category-posts").outerHeight()}, 500); // Calculate new post wrapper height and animate

			posts.fadeIn(); // Once wrapper height is the correct size load the new thumbs
			
			// Check if there's pagination - if so show it
			if ($("a", newPagination).length > 0) {					
				pagination.html(newPagination);
				initPaginationAjax();
				pagination.fadeIn();
			} else {
				pagination.fadeOut();
			}
		});
	}

	links.click(function(event){
		event.preventDefault();

		if (!$(this).hasClass("current")) {

			url = $(this).attr("href");

			links.removeClass("current");
			$(this).addClass("current");

			ajaxPosts(url);
		}
	});

	wrapper.css("height", posts.outerHeight());
	posts.wrap(wrapper);

	if (paginationLinks.length > 0) {
		initPaginationAjax();
	}

}

jQuery(document).ready(function($){
	if ($("a", "#category-posts").length > 0) {
		postFilter($);
	}
});