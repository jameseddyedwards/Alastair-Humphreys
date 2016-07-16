<?php

function button_shortcode($atts, $content = null) {
	extract(shortcode_atts(array(
		'url' => '/'
	), $atts));

	return '<a class="box-button" href="' . $url . '">' . do_shortcode($content) . '</a>';
}

function format_shortcode($atts, $content = null) {
	return '<div class="format clearfix">' . do_shortcode($content) . '</div>';
}

function format_image_shortcode($atts, $content = null) {
	return '<div class="format-image">' . do_shortcode($content) . '</div>';
}

function format_info_shortcode($atts, $content = null) {
	return '<div class="format-info">' . do_shortcode($content) . '</div>';
}

function paypal_add_to_cart($atts, $content = null) {
	if ($content != null) {
		return '
		<form class="paypal-add-to-cart" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="paypal">
			<fieldset>
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="' . do_shortcode($content) . '">
				<input type="image" alt="PayPal — The safer, easier way to pay online." name="submit" src="https://www.paypal.com/en_GB/i/btn/btn_cart_LG.gif">
				<img alt="" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1" border="0">
			</fieldset>
		</form>';
	}
}

function paypal_donate($atts, $content = null) {
	return '
	<form class="paypal-donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<fieldset>
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="B7VUAB2WUXT5G">
			<input type="image" alt="PayPal - The safer, easier way to pay online." name="submit" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif">
			<img alt="" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" border="0">
		</fieldset>
	</form>';
}

function video_shortcode($atts, $content = null) {
	return '<div class="video-container">' . do_shortcode($content) . '</div>';
}

function ignore_shortcode($atts, $content = null) {
	return '<div class="ignore">' . do_shortcode($content) . '</div>';
}

add_shortcode('button', 'button_shortcode');
add_shortcode('format', 'format_shortcode');
add_shortcode('format-image', 'format_image_shortcode');
add_shortcode('format-info', 'format_info_shortcode');
add_shortcode('add-to-cart', 'paypal_add_to_cart');
add_shortcode('donate', 'paypal_donate');
add_shortcode('ignore', 'ignore_shortcode');
add_shortcode('video', 'video_shortcode');

?>
