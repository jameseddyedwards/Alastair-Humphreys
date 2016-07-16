<?php
/**
 * Alastair Humphreys functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, alastairhumphreys_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We arLeave a Replye providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'alastairhumphreys_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

/* Includes */
include TEMPLATEPATH . '/inc/shortcodes.php';
//include TEMPLATEPATH . '/inc/styles.php';
include TEMPLATEPATH . '/inc/scripts.php';

/* Images */
include TEMPLATEPATH . '/functions/images/feature-image.php';


/**
 * Tell WordPress to run alastairhumphreys_setup() when the 'after_setup_theme' hook is run.
 */
add_action('after_setup_theme', 'alastairhumphreys_setup');
add_theme_support('menus');

if (!function_exists('alastairhumphreys_setup')):
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 *
	 * To override alastairhumphreys_setup() in a child theme, add your own alastairhumphreys_setup to your child theme's
	 * functions.php file.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_editor_style() To style the visual editor.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
	 * @uses register_nav_menus() To add support for navigation menus.
	 * @uses add_custom_background() To add support for a custom background.
	 * @uses add_custom_image_header() To add support for a custom header.
	 * @uses register_default_headers() To register the default custom header images provided with the theme.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Alastair Humphreys 1.0
	 */
	function alastairhumphreys_setup() {

		/* Make Alastair Humphreys available for translation.
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Alastair Humphreys, use a find and replace
		 * to change 'alastairhumphreys' to the name of your theme in all the template files.
		 */
		load_theme_textdomain('alastairhumphreys', TEMPLATEPATH . '/languages');

		$locale = get_locale();
		$locale_file = TEMPLATEPATH . "/languages/$locale.php";
		if (is_readable($locale_file)) {
			require_once($locale_file);

			// Add default posts and comments RSS feed links to <head>.
			add_theme_support('automatic-feed-links');

			// This theme uses wp_nav_menu() in one location.
			register_nav_menu('primary', __('Primary Menu', 'alastairhumphreys'));

			// Add Alastair Humphreys's custom image sizes
			add_image_size('Feature Wide', 1600, 9999); // Used for Wide feature images
			add_image_size('Feature Normal', 1230, 9999); // Feature image that is only the width of the page container
			add_image_size('Thumbnail', 370, 240, true); // Used for post thumbnail images
			add_image_size('Gallery Small', 310, 280, true); // Used for small gallery images
			add_image_size('Gallery Medium', 475, 280, true); // Used for large feature (header) images
			add_image_size('Gallery Large', 795, 570, true); // Used for post thumbnail images
			add_image_size('Full Post Width', 970, 9999); // Used for inline post images to span the full width
		}
	}
endif; // alastairhumphreys_setup

/**
 * Creates the thumbnails for the whole site. If no thumbnail is found then a default one is used.
 */
function ah_get_custom_thumb($pageID = '', $size = 'thumbnail') {
	$imageAlt = '';
	$imageUrl = '';
	$imageSizes = null;

	$imageObj = get_field('thumbnail', $pageID);
	if (!$imageObj) {
		$imageObj = get_field("default_thumbnail", 'option');
	}

	// Get image sizes
	if (isset($imageObj['sizes'])) {
		$imageSizes = $imageObj['sizes'];
	}

	// Set image URL
	if ($size == 'original') {
		$imageUrl = $imageObj['url'];
	} else {
		$imageUrl = $imageSizes[$size];
	}

	// Set image alt
	if (isset($imageObj['alt'])) {
		$imageAlt = $imageObj['alt'];
	}

	// Create image html
	if ($imageUrl) {
		$image = '<img src="' . $imageUrl . '" alt="' . $imageAlt . '" />';
	} else {
		$image = '<em>No image found</em>';
	}

	return $image;
}

/*
 * Creates a size specifc image 
 * $size = Any image size shown above
*/
function ah_get_image($size = 'thumbnail') {
	$imageObj = get_field('feature_image', get_the_id());

	if ($imageObj != '') {
		$imageUrl = $imageObj["sizes"][$size];
		$imageTitle = $imageObj["title"];
		$imageHtml = '<img src="' . $imageUrl . '" alt="' . $imageTitle . '" />';
		return $imageHtml;
	} else {
		return;
	}
}


function ah_get_dropdown($categories, $menuId) {
	$i = 1;
	$categoryCount = count($categories);
	?>
	<div class="dropdown">
		<?php
		foreach ($categories as $category) {
			$categoryId = get_cat_ID($category);
			$categoryPosts = get_posts(array('numberposts'=>10, 'cat'=>$categoryId));
			$featureImagePost = get_posts(array('numberposts'=>1, 'cat'=>$categoryId));
			$categoryURL = esc_url(home_url('/')) . '?cat=' . $categoryId;
			$categoryClass = strtolower(str_replace(" ", "-", $category));
			?>
		
			<div class="category clearfix <?php echo $categoryClass ?>">
				<a href="<?php echo $categoryURL ?>" class="cat-title"><?php echo $category ?></a>
				<?php foreach($featureImagePost as $post) : setup_postdata($post); ?>
					<a class="feature-image" href="<?php the_permalink(); ?>">
						<?php echo ah_get_custom_thumb(); ?>
					</a>
				<?php endforeach; ?>
				<ul>
					<?php foreach($categoryPosts as $post) : setup_postdata($post); ?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a href="<?php echo $categoryURL ?>" class="view-all">view all <?php echo $category ?></a>
			</div>
			<?php if ($i != $categoryCount) { ?>
				<hr />
			<?php } ?>
			<?php
			$i = $i + 1;
		}
		?>
	</div>
	<?php
}



/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in alastairhumphreys_setup().
 *
 * @since Alastair Humphreys 1.0
 */
if (!function_exists( 'alastairhumphreys_admin_header_image')) :
	function alastairhumphreys_admin_header_image() { ?>
		<div id="headimg">
			<?php
			if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
				$style = ' style="display:none;"';
			else
				$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
			?>
			<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
			<?php $header_image = get_header_image();
			if ( ! empty( $header_image ) ) : ?>
				<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
			<?php endif; ?>
		</div>
	<?php }
endif; // alastairhumphreys_admin_header_image

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function alastairhumphreys_excerpt_length($length) {
	return 40;
}
add_filter('excerpt_length', 'alastairhumphreys_excerpt_length');

/**
 * Removes automatic html tags in post text
 */
//remove_filter('the_content', 'wpautop');
//remove_filter('the_excerpt', 'wpautop');
remove_filter('term_description', 'wpautop');

$filters = array('pre_term_description', 'term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description');
foreach ($filters as $filter) {
    remove_filter($filter, 'wp_filter_kses');
}



/**
 * Display navigation to next/previous pages when applicable
 */
if (!function_exists('alastairhumphreys_content_nav')) :

	function alastairhumphreys_content_nav($nav_id) {
		global $wp_query;

		if ($wp_query->max_num_pages > 1) : ?>
			<div id="<?php echo $nav_id; ?>" class="next-previous">
				<h3 class="assistive-text"><?php _e( 'Post navigation', 'alastairhumphreys' ); ?></h3>
				<div class="previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'alastairhumphreys' ) ); ?></div>
				<div class="next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'alastairhumphreys' ) ); ?></div>
			</div>
		<?php endif;
	}

endif;



/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own alastairhumphreys_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Alastair Humphreys 1.0
 */
if (!function_exists('alastairhumphreys_comment')) :
	
	function alastairhumphreys_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		switch ($comment->comment_type) :
			case 'pingback' :
			case 'trackback' :
		?>
		<!-- Do nothing for pingbacks & trackbacks
		<li class="post pingback">
			<p>
				<?php _e('Pingback:', 'alastairhumphreys'); ?>
				<?php comment_author_link(); ?>
				<?php edit_comment_link( __('| Edit', 'alastairhumphreys'), '<span class="edit-link">', '</span>'); ?>
			</p>
		</li>
		-->
		<?php
				break;
			default :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			
			<div class="comment-author vcard">
				<?php
					$avatar_size = 68;
					echo get_avatar($comment, $avatar_size);
				?>
				<div class="author-meta">
					<?php
						/* translators: 1: comment author, 2: date and time */
						printf(__('<span class="author">%1$s</span> <span class="posted">Posted %2$s</span>', get_comment_author_link()),
							sprintf(get_comment_author_url() != '' ? '<a href="%1$s" target="_new">%2$s</a>' : '%2$s', get_comment_author_url(), get_comment_author()),
							sprintf('<a class="date" href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url(get_comment_link($comment->comment_ID)),
								get_comment_time('c'),
								sprintf(__('%1$s<br />at %2$s', 'alastairhumphreys'), get_comment_date(), get_comment_time())
							)
						);
					?>
				</div>

			</div>

			<div class="comment-content">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'alastairhumphreys' ); ?></em>
				<?php endif; ?>
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'alastairhumphreys' ), '<span class="edit-link">', '</span>' ); ?>
				<?php comment_reply_link(array_merge($args,array(
					'reply_text' => __( 'Reply', 'alastairhumphreys' ),
					'depth' => $depth,
					'max_depth' => $args['max_depth']
				))); ?>
			</div>
			<div class="clear"></div>
		</li>
		<?php
			break;
		endswitch;
	}

endif; // ends check for alastairhumphreys_comment()



/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own alastairhumphreys_posted_on to override in a child theme
 *
 * @since Alastair Humphreys 1.0
 */
if (!function_exists('alastairhumphreys_posted_on')) {
	function alastairhumphreys_posted_on() {
		printf( __('<a href="%1$s" title="%2$s" rel="bookmark"><time class="month" datetime="%3$s" pubdate>%4$s</time><span class="year">%5$s</span></a>'),
			esc_url(get_permalink()),
			esc_attr(get_the_time()),
			esc_attr(get_the_date('c')),
			esc_html(get_the_date('d/m')),
			esc_html(get_the_date('Y')),
			esc_url(get_author_posts_url( get_the_author_meta('ID'))),
			sprintf(esc_attr__('View all posts by %s', 'alastairhumphreys'), get_the_author()),
			esc_html(get_the_author())
		);
	}
};


/**
 * Prevents WordPress from forwarding empty searches to the index.php template or home page.
 * By replacing an empty search with a single space search.
 *
 * @since Alastair Humphreys 1.0
 */
function my_request_filter($query_vars) {
    if (isset($_GET['s']) && empty($_GET['s'])) {
    	$query_vars['s'] = " ";
    }
    return $query_vars;
}
add_filter('request', 'my_request_filter');

