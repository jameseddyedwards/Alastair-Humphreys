
function menu($) {
	var dropdowns = $(".dropdown"),
		menu = $("#menu-primary-menu"),
		navItems = $(".menu-item", menu),
		dropdownClass,
		parentMenuItem;

	dropdowns.each(function() {
		parentMenuId = '#' + $(this).attr("id").split('dropdown-')[1];
		$(parentMenuId).append($(this));
	});

	navItems.hover(function() {
		dropdownClass = '.dropdown.' + $(this).attr("id");
		$(dropdownClass).addClass('active');
	}, function() {
		$(dropdownClass).removeClass('active');
	});
}

function tabbed($, element) {
	var tabs = $("li", element),
		viewAll = $("#view-all"),
		tab,
		tabUrl;
	tabs.click(function(){
		tab = $(this);
		tabUrl = tab.attr("data-url");
		tabs.removeClass("active");
		if (!tab.hasClass("active")){
			$(".tab-row").hide();
			$('.' + tab.attr("id")).show();
			tab.addClass("active");
		}
		if (tabUrl !== "") {
			viewAll.attr("href", tabUrl);
		}
	});
}

function navToggle($) {
	$("#nav-tab").click(function() {
		$("#navigation").toggleClass('visible-phone').toggleClass('visible-tablet');
	});
}


// Sticky Navigation
jQuery(function($) {

	// Do our DOM lookups beforehand
	var sticky_header = $(".sticky-header"),
		main = $(".main");

	main.waypoint(function() {
		sticky_header.slideToggle();
	}, {offset:35});

	function checkWaypoints() {
		if ($(window).width() < 979) {
			$.waypoints('destroy');
			sticky_header.slideUp();
		}
	}

	$(window).resize(function() {
		checkWaypoints();
	});

	checkWaypoints();

});

jQuery(document).ready(function($) {
	navToggle($);
	menu($);
	tabbed($, ".tabs");
});