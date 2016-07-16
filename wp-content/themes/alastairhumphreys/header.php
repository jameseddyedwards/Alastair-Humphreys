<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and the sites header and navigation menu
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
	<html class="ie ie6 ltie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
	<html class="ie ie7 ltie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
	<html class="ie ie8 ltie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
	<html class="ie ie9" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
	<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>

<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<title>
<?php
	// Print the <title> tag based on what is being viewed.
	global $page, $paged;

	wp_title('|', true, 'right');

	// Add the blog name.
	bloginfo('name');

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo('description', 'display');
	if ($site_description && (is_home() || is_front_page())) {
		echo " | $site_description";
	}

	// Add a page number if necessary:
	if ($paged >= 2 || $page >= 2) {
		echo ' | ' . sprintf(__('Page %s', 'alastairhumphreys'), max($paged, $page));
	}
?>
</title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/style.css" media="all" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<?php get_template_part('content', 'body-scripts'); ?>

	<header class="header clearfix">
		<div class="container">
			<div class="row">
				<div class="span span12">
					<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
						<img src="<?php bloginfo('template_url'); ?>/images/alastair-humphreys-logo.png" alt="<?php bloginfo( 'name' ); ?>" />
					</a>

					<div id="navigation" class="visible-desktop phone-nav clearfix">
						<ul class="social-icons inline clearfix">
							<li><a href="https://www.pinterest.com/alhumphreys/"><img src="http://www.alastairhumphreys.com/wp-content/uploads/2015/03/pinterest.jpg" /></a>
							<!--<li><a href="http://feeds.feedburner.com/alastairhumphreys.com"><img src="<?php bloginfo('template_url'); ?>/images/icon/rss.png" /></a></li>-->
							<li><a href="http://instagram.com/al_humphreys"><img src="<?php bloginfo('template_url'); ?>/images/icon/instagram.png" /></a></li>
							<!--<li><a href="https://vimeo.com/channels/alastairhumphreys"><img src="<?php bloginfo('template_url'); ?>/images/icon/vimeo.png" /></a></li>
							<li><a href="http://www.youtube.com/user/englishwildman"><img src="<?php bloginfo('template_url'); ?>/images/icon/youtube.png" /></a></li>
							<li><a href="https://twitter.com/Al_Humphreys"><img src="<?php bloginfo('template_url'); ?>/images/icon/twitter.png" /></a></li>-->
							<li><a href="http://www.facebook.com/pages/Alastair-Humphreys/149963098097"><img src="<?php bloginfo('template_url'); ?>/images/icon/facebook.png" /></a></li>
							<!--<li><a href="http://www.flickr.com/photos/alastairhumphreys/"><img src="<?php bloginfo('template_url'); ?>/images/icon/flickr.png" /></a></li>-->
							<li><a href="https://twitter.com/Al_Humphreys" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @Al_Humphreys</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></li>
						</ul>

						<!-- Newsletter Signup Form -->
						<?php get_search_form(); ?>

						<nav class="access" role="navigation">
							<?php /* Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
							<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'alastairhumphreys' ); ?>"><?php _e( 'Skip to secondary content', 'alastairhumphreys' ); ?></a></div>
							<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
							<?php wp_nav_menu(array(
								'theme_location'	=> 'primary',
								'menu_class'		=> 'menu clearfix'
							)); ?>

							<?php get_template_part('content', 'dropdown'); ?>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</header>

	<header class="sticky-header clearfix">
		<div class="container">
			<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
				<img class="sticky-logo" src="<?php bloginfo('template_url'); ?>/images/alastair-humphreys-logo-small.png" alt="<?php bloginfo( 'name' ); ?>" />
			</a>
			<nav>
				<?php wp_nav_menu(array(
					'theme_location'	=> 'primary',
					'menu_class'		=> 'menu clearfix'
				)); ?>
			</nav>
		</div>
	</header>

	<div class="skip-link">
		<a class="assistive-text" href="#content" title="<?php esc_attr_e('Skip to primary content', 'alastairhumphreys'); ?>">
			<?php _e('Skip to primary content', 'alastairhumphreys'); ?>
		</a>
	</div>

	<div class="main">
		<img id="nav-tab" src="<?php bloginfo('template_url'); ?>/images/button/tab-nav.gif" class="nav-tab hidden-desktop" alt="Show/Hide Navigation" />