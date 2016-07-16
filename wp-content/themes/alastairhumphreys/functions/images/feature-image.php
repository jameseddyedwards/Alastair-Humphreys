<?php

/*
 * Creates the HTML for featured images across the whole site. They can either be a background image or a standard image.
 * $size = Any image size shown above
*/

if (!function_exists('get_feature_image')) {

	function ah_get_feature_image($size = 'feature-normal') {
		$obj = get_field('feature_image', get_the_id());

		if ($obj != '') {
			$sizes = $obj["sizes"];
			$url = $sizes[$size];
			$title = $obj["title"];
			$html = '<div id="' . $size . '" class="' . $size . '" data-image-size="' . $size . '">';
				$html .= '<img src="' . $url . '" alt="' . $title . '" />';
				if (!is_category()) {
					$html .= '<div class="title-block">';
						if (get_field('title_position') == 'image') {
							$html .= '<h1 class="title">' . get_the_title() . '</h1>';
							if (get_field('subtitle')) {
								$html .= '<span class="subtitle">' . get_field('subtitle') . '</span>';
							}
						}
					$html .= '</div>';
				}
			$html .= '</div>';
			return $html;
		} else {
			return;
		}
	}
} else {
	echo "Function Already Exists: get_feature_image";
}

if (!function_exists('the_feature_image')) {
	function ah_the_feature_image($size = 'feature-normal') {

		echo ah_get_feature_image($size);
	}

} else {
	echo "Function Already Exists: the_feature_image";
}