var megasubscribepopup_use = false;
var megasubscribepopup_use_event;
var megasubscribepopup_countdown;
var megasubscribepopup_timeout;
var megasubscribepopup_redirect_url;
var megasubscribepopup_scroll_lock = false;
var megasubscribepopup_idle_counter = 0;
var megasubscribepopup_idle_timeout;
var megasubscribepopup_lock = false;
var megasubscribepopup_disable_close = false;

function megasubscribepopup_open() {
	try {
		if (!megasubscribepopup_use) {
			megasubscribepopup_use = true;
			var megasubscribepopup_overlay_style = "style='background: "+megasubscribepopup_value_overlay_bg_color+"; opacity: "+megasubscribepopup_value_overlay_opacity+"; -ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity="+parseInt(100*megasubscribepopup_value_overlay_opacity)+")\"; filter:alpha(opacity=\""+parseInt(100*megasubscribepopup_value_overlay_opacity)+"\";'";
			jQuery("body").append("<div id='megasubscribepopup_overlay' "+megasubscribepopup_overlay_style+"></div><div id='megasubscribepopup_window' style='position: fixed; background:"+megasubscribepopup_value_popup_bg_color+" url("+megasubscribepopup_value_popup_bg_url+") 0 0 repeat;'></div>");
			
			var megasubscribepopup_width = megasubscribepopup_value_width + 30;
			var megasubscribepopup_height = megasubscribepopup_value_height + 40;

			var megasubscribepopup_close_button = "";
			if (!megasubscribepopup_disable_close) {
				jQuery("#megasubscribepopup_overlay").click(megasubscribepopup_close);
				megasubscribepopup_close_button = '<span id="megasubscribepopup_close" onclick="megasubscribepopup_close();"></span>';
			}
			
			var window_width = jQuery(window).width();
			if (window_width > 0 && window_width < megasubscribepopup_width+30) {
				megasubscribepopup_width = window_width - 30;
			}
			
			jQuery("#megasubscribepopup_window").append("<div id='megasubscribepopup_content' style='width:"+parseInt(megasubscribepopup_width-30, 10)+"px; min-height:"+parseInt(megasubscribepopup_height-45, 10)+"px;'></div>"+megasubscribepopup_close_button+"<span id='megasubscribepopup_delay'></span>");

			jQuery("#megasubscribepopup_content").append(jQuery("#megasubscribepopup_container").children());
			jQuery("#megasubscribepopup_window").bind('megasubscribepopup_unload', function () {
				jQuery("#megasubscribepopup_container").append(jQuery("#megasubscribepopup_content").children() );
			});
			var content_height = jQuery("#megasubscribepopup_content").height();
			if (content_height > megasubscribepopup_height-45) {
				megasubscribepopup_height = content_height + 30;
			}

			var window_height = jQuery(window).height();
			if (window_height > 0 && window_height < megasubscribepopup_height+30) {
				megasubscribepopup_height = window_height - 30;
			}
			
			jQuery("#megasubscribepopup_window").css({
				marginLeft: '-'+parseInt((megasubscribepopup_width / 2),10)+'px', 
				width: megasubscribepopup_width+'px',
				marginTop: '-'+parseInt((megasubscribepopup_height / 2),10)+'px',
				height: megasubscribepopup_height+'px'
			});
			jQuery("#megasubscribepopup_window").css({
				"visibility" : "visible"
			});
		}
	} catch(e) {

	}
	return false;
}

function megasubscribepopup_close() {
	megasubscribepopup_use = false;
	megasubscribepopup_disable_close = false;
	clearTimeout(megasubscribepopup_timeout);
	if (megasubscribepopup_use_event == "exit" && megasubscribepopup_countdown == 0) {
		window.location.href = megasubscribepopup_redirect_url;
	} else if (megasubscribepopup_use_event == "idle") {
		megasubscribepopup_idle_counter = 0;
		megasubscribepopup_idle_timeout = setTimeout("megasubscribepopup_idle_counter_handler();", 1000);	
	}
	jQuery("#megasubscribepopup_delay").html("");
	jQuery("#megasubscribepopup_window").fadeOut("fast", function() {
		jQuery("#megasubscribepopup_window, #megasubscribepopup_overlay").trigger("megasubscribepopup_unload").unbind().remove();
	});
	return false;
}

function megasubscribepopup_read_cookie(key) {
	var pairs = document.cookie.split("; ");
	for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
		if (pair[0] === key) return pair[1] || "";
	}
	return null;
}

function megasubscribepopup_write_cookie(key, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = key+"="+value+expires+"; path=/";
}

function megasubscribepopup_countdown_string(value) {
	var result = '';
	var hours = Math.floor(value/3600);
	var minutes = Math.floor((value - 3600*hours)/60);
	var seconds = value - 3600*hours - 60*minutes;
	if (hours > 0) {
		if (hours > 9) result = hours.toString() + ":";
		else result = "0" + hours.toString() + ":";
	}
	if (minutes > 9) result = result + minutes.toString() + ":";
	else result = result + "0" + minutes.toString() + ":";
	if (seconds > 9) result = result + seconds.toString();
	else result = result + "0" + seconds.toString();
	return result;
}
function megasubscribepopup_get_query_parameter(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
	var results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function megasubscribepopup_init() {
	jQuery(".megasubscribepopup_click, .megasubscribepopup").click(function() {
		megasubscribepopup_open("click", 0);
		return false;
	});
	var nmsp = megasubscribepopup_get_query_parameter("nmsp");
	if (nmsp == "1") return;
	else if (nmsp == "2") {
		megasubscribepopup_write_cookie("megasubscribepopup", megasubscribepopup_value_cookie, 180);
		return;
	}
	var megasubscribepopup_cookie = megasubscribepopup_read_cookie("megasubscribepopup");
	if (megasubscribepopup_cookie != megasubscribepopup_value_cookie) {
		var window_width = jQuery(window).width();
		if (megasubscribepopup_value_disable_mobile != "on" || window_width <= 0 || window_width >= megasubscribepopup_value_width+30+30) {
			megasubscribepopup_use = false;
			if (megasubscribepopup_value_load_enable) {
				var megasubscribepopup_tmp = megasubscribepopup_read_cookie("megasubscribepopup_load");
				if (megasubscribepopup_tmp != megasubscribepopup_value_cookie) {
					if (megasubscribepopup_value_load_start_delay == 0) megasubscribepopup_init_open("load", megasubscribepopup_value_load_delay);
					else setTimeout(function() {megasubscribepopup_init_open("load", megasubscribepopup_value_load_delay);}, megasubscribepopup_value_load_start_delay);
				}
			}
			if (megasubscribepopup_value_exit_enable) {
				jQuery("a").each(function() {
					var href = this.href;
					var domain = document.domain;
					if (href.match("[http|https]://") != null && href.match("[http|https]://"+document.domain) == null) {
						for (var i=0; i<megasubscribepopup_value_exit_excluded.length; i++) {
							if (href.match(megasubscribepopup_value_exit_excluded[i]) != null) {
								return;
							}
						}
						jQuery(this).click(function() {
							megasubscribepopup_redirect_url = this.href;
							return !megasubscribepopup_init_open("exit", megasubscribepopup_value_exit_delay);
						});
					}
				});
			}
			if (megasubscribepopup_value_scroll_enable) {
				jQuery(window).scroll(function() {
					var position = jQuery(window).scrollTop();
					var megasubscribepopup_tmp = megasubscribepopup_read_cookie("megasubscribepopup_scroll");
					if (megasubscribepopup_tmp != megasubscribepopup_value_cookie) {
						if (!megasubscribepopup_use) {
							if (position >= megasubscribepopup_value_scroll_offset && !megasubscribepopup_scroll_lock) {
								megasubscribepopup_init_open("scroll", 0);
							}
						}
					}
				});
			}
			if (megasubscribepopup_value_copy_enable) {
				jQuery(window).bind("copy", function(e) {
					if (megasubscribepopup_lock == false) {
						if (megasubscribepopup_value_copy_block == "on") e.preventDefault();
						megasubscribepopup_init_open("copy", 0);
						if (megasubscribepopup_value_copy_block == "on") return false;
						return true;
					} else return true;
				});
			}
			if (megasubscribepopup_value_context_enable) {
				jQuery(window).bind("contextmenu", function(e) {
					if (megasubscribepopup_lock == false) {
						e.preventDefault();
						megasubscribepopup_init_open("context", 0);
						return false;
					} else return true;
				});
			}
			if (megasubscribepopup_value_idle_enable) {
				jQuery(window).mousemove(function(event) {
					megasubscribepopup_idle_counter = 0;
				});
				jQuery(window).click(function(event) {
					megasubscribepopup_idle_counter = 0;
				});
				jQuery(window).keypress(function(event) {
					megasubscribepopup_idle_counter = 0;
				});
				jQuery(window).scroll(function(event) {
					megasubscribepopup_idle_counter = 0;
				});
				megasubscribepopup_idle_timeout = setTimeout("megasubscribepopup_idle_counter_handler();", 1000);
			}
		}
	}
	jQuery(".megasubscribepopup_exit").click(function() {
		var megasubscribepopup_cookie = megasubscribepopup_read_cookie("megasubscribepopup");
		if (megasubscribepopup_cookie != megasubscribepopup_value_cookie) {
			megasubscribepopup_redirect_url = this.href;
			megasubscribepopup_use_event = "exit";
			megasubscribepopup_open();
			return false;
		} else return true;
	});	
}

function megasubscribepopup_idle_counter_handler() {
	if (megasubscribepopup_idle_counter >= megasubscribepopup_value_idle_delay) {
		megasubscribepopup_init_open("idle", 0);
	} else {
		megasubscribepopup_idle_counter = megasubscribepopup_idle_counter + 1;
		megasubscribepopup_idle_timeout = setTimeout("megasubscribepopup_idle_counter_handler();", 1000);
	}
}

function megasubscribepopup_init_open(event, delay) {
	if (megasubscribepopup_use == false && megasubscribepopup_lock == false) {
		megasubscribepopup_use_event = event;
		megasubscribepopup_countdown = delay;
		if (event == "load" && megasubscribepopup_value_load_disable_close == "on") megasubscribepopup_disable_close = true;
		if (event == "load" && megasubscribepopup_value_load_once_per_visit == "on") megasubscribepopup_write_cookie("megasubscribepopup_load", megasubscribepopup_value_cookie, 0);
		else if (event == "scroll" && megasubscribepopup_value_scroll_once_per_visit == "on") megasubscribepopup_write_cookie("megasubscribepopup_scroll", megasubscribepopup_value_cookie, 0);
		if (event == "scroll") megasubscribepopup_scroll_lock = true;
		if (delay > 0) {
			megasubscribepopup_timeout = setTimeout("megasubscribepopup_counter();", 1000);
		}
		megasubscribepopup_open();
		return true;
	} else return false;
}

function megasubscribepopup_counter() {
	if (megasubscribepopup_countdown == 0) {
		clearTimeout(megasubscribepopup_timeout);
		megasubscribepopup_close();
	} else {
		megasubscribepopup_countdown = megasubscribepopup_countdown - 1;
		jQuery("#megasubscribepopup_delay").html(megasubscribepopup_countdown_string(megasubscribepopup_countdown));
		megasubscribepopup_timeout = setTimeout("megasubscribepopup_counter();", 1000);
	}
}

function megasubscribepopup_subscribe() {
	var megasubscribepopup_name = "";
	if (megasubscribepopup_value_disable_name != "on") megasubscribepopup_name = jQuery("#megasubscribepopup_name").val();
	var megasubscribepopup_email = jQuery("#megasubscribepopup_email").val();
	var data = {name: megasubscribepopup_name, email: megasubscribepopup_email, action: "megasubscribepopup_submit"};
	jQuery("#megasubscribepopup_name").removeClass("megasubscribepopup_redborder");
	jQuery("#megasubscribepopup_email").removeClass("megasubscribepopup_redborder");
	jQuery("#megasubscribepopup_submit").attr("disabled","disabled");
	jQuery("#megasubscribepopup_loading").css("display", "inline-block");
	jQuery.post(megasubscribepopup_action, data, function(data) {
		if(data.match("ERROR") != null) {
			jQuery("#megasubscribepopup_submit").removeAttr("disabled");
			jQuery("#megasubscribepopup_loading").css("display", "none");
			if(data.match("name") != null) jQuery("#megasubscribepopup_name").addClass("megasubscribepopup_redborder");
			if(data.match("email") != null) jQuery("#megasubscribepopup_email").addClass("megasubscribepopup_redborder");
		} else {
			megasubscribepopup_write_cookie("megasubscribepopup", megasubscribepopup_value_cookie, 180);
			clearTimeout(megasubscribepopup_timeout);
			megasubscribepopup_countdown = 0;
			megasubscribepopup_lock = true;
			megasubscribepopup_close();
		}
	});
}