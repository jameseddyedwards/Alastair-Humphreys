<?php
/*
 * Loads all JavaScript based on pages and other options
 */
 
function ahumphreys_load_javascript_files() {

	$my_theme = wp_get_theme();
	$theme_version = $my_theme->get('Version');

	/* Global Scripts */
	wp_register_script('ah_global', get_template_directory_uri() . '/js/global.js', array('jquery'), $theme_version, true);
	wp_register_script('waypoints_js', get_template_directory_uri() . '/js/plugin/waypoints.min.js', array('jquery'), $theme_version, true);
	
	/* Page Specific */
	wp_register_script('ah_category', get_template_directory_uri() . '/js/category.js', array('jquery'), $theme_version, true);
	wp_register_script('ah_post', get_template_directory_uri() . '/js/page/post.js', array('jquery', 'ah_imagesloaded'), $theme_version, true);
	wp_register_script('flickity', get_template_directory_uri() . '/js/flickity.min.js', array('jquery'), $theme_version, true);

	/* Plugin Specific */
	//wp_register_script('fitvids_js', get_template_directory_uri() . '/js/plugin/fitvids.js', array('jquery'), $theme_version, true);
	//wp_register_script('ah_carousel', get_template_directory_uri() . '/js/plugin/jquery.carouFredSel.js', array('jquery'), $theme_version, true);
	
	/* Widget Specific */
	//wp_register_script('ah_imagesloaded', get_template_directory_uri() . '/js/widget/imagesloaded.js', array('jquery'), $theme_version, true);
	

	wp_enqueue_script('jquery');

	// Comments
	// Page, Post or Attachment with Multiple Comments enabled
	// We add some JavaScript to pages with the comment form to support sites with threaded comments (when in use).
	if (is_singular() && get_option('thread_comments')) {
		wp_enqueue_script('ah_imagesloaded');
		wp_enqueue_script('ah_post');
		wp_enqueue_script('comment-reply');
	}

	// Home
	if (is_front_page()) {
		wp_enqueue_script('flickity');
		//wp_enqueue_script('ah_carousel');
		//wp_enqueue_script('ah_imagesloaded');
	}

	// Category
	if (is_category()) {
		wp_enqueue_script('ah_category');
	}
	
	//wp_enqueue_script('fitvids_js');
	wp_enqueue_script('waypoints_js');
	wp_enqueue_script('ah_global');

}
add_action('wp_enqueue_scripts', 'ahumphreys_load_javascript_files');


?>