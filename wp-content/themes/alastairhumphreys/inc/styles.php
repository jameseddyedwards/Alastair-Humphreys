<?php
	/**
	 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
	 */

	/**
	add_action('wp_enqueue_scripts', 'ahumphreys_add_my_stylesheet');

	 * Enqueue plugin style-file
	function ahumphreys_add_my_stylesheet() {

		$my_theme = wp_get_theme();
		$theme_version = $my_theme->get('Version');

		// Global Stylesheets
		wp_register_style('reset', get_template_directory_uri() . '/css/reset.css', null, $theme_version);
		wp_register_style('fonts', get_template_directory_uri() . '/css/fonts.css', null, $theme_version);
		wp_register_style('structure', get_template_directory_uri() . '/css/structure.css', null, $theme_version);
		wp_register_style('global', get_template_directory_uri() . '/css/global.css', null, $theme_version);
		wp_register_style('lists', get_template_directory_uri() . '/css/lists.css', null, $theme_version);
		wp_register_style('forms', get_template_directory_uri() . '/css/forms.css', null, $theme_version);
		wp_register_style('ah_buttons', get_template_directory_uri() . '/css/buttons.css', null, $theme_version);
		wp_register_style('icons', get_template_directory_uri() . '/css/icons.css', null, $theme_version);
		wp_register_style('utilities', get_template_directory_uri() . '/css/utilities.css', null, $theme_version);
		
		wp_enqueue_style('reset');
		wp_enqueue_style('fonts');
		wp_enqueue_style('structure');
		wp_enqueue_style('global');
		wp_enqueue_style('lists');
		wp_enqueue_style('forms');
		wp_enqueue_style('ah_buttons');
		wp_enqueue_style('icons');
		wp_enqueue_style('utilities');


		// Home Page
		if (is_front_page()) {
			wp_register_style('home', get_template_directory_uri() . '/css/page/home.css', null, $theme_version);
			wp_enqueue_style('home');
		}

		// Pages
		else if (is_page()) {
			wp_register_style('page', get_template_directory_uri() . '/css/page.css', null, $theme_version);	
			wp_register_style('gallery', get_template_directory_uri() . '/css/gallery.css', null, $theme_version);	
			wp_register_style('comments', get_template_directory_uri() . '/css/comments.css', null, $theme_version);	
			wp_enqueue_style('page');
			wp_enqueue_style('gallery');
			wp_enqueue_style('comments');

			if (get_the_title() == 'Books and Film' || is_page_template( 'page-templates/shop.php' )) {
				wp_register_style('books', get_template_directory_uri() . '/css/books.css', null, $theme_version);	
				wp_enqueue_style('books');
			}
		}

		// Category Pages
		else if (is_category()) {
			wp_register_style('category', get_template_directory_uri() . '/css/category.css', null, $theme_version);
			wp_register_style('gallery', get_template_directory_uri() . '/css/gallery.css', null, $theme_version);	
			wp_enqueue_style('category');
			wp_enqueue_style('gallery');
		}

		// Blog Article
		else if (is_single()) {
			wp_register_style('post', get_template_directory_uri() . '/css/post.css', null, $theme_version);
			wp_register_style('recommended', get_template_directory_uri() . '/css/recommended.css', null, $theme_version);
			wp_register_style('comments', get_template_directory_uri() . '/css/comments.css', null, $theme_version);
			wp_enqueue_style('post');
			wp_enqueue_style('recommended');
			wp_enqueue_style('comments');
		}

		// Search
		else if (is_search()) {
			wp_register_style('search', get_template_directory_uri() . '/css/search.css', null, $theme_version);
			wp_enqueue_style('search');
		}


	}
	 */
?>