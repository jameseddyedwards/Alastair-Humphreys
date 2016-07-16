<?php
/**
 * The template for displaying the 'blog' page.
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

global $post;
$currentCategoryId = null;
if (get_cat_name(get_query_var('cat')) != "Best bits") {
	$currentCategoryId = get_query_var('cat');
}
$args = array(
	'category' => $currentCategoryId,
	'numberposts' => 6
);
$recentPosts = get_posts($args);
$counter = 0;

?>

<!-- Blog Intro -->
<?php if (get_field('blog_intro', 'option')) { ?>
	<div class="container white recent">
		<div class="row">
			<div class="span twelve">
				<?php the_field('blog_intro', 'option'); ?>
			</div>
		</div>
	</div>
<?php } ?>

<!-- Recent Posts -->
<div class="container white recent">
	<div class="row">
		<div class="span twelve">
			<h2>Recent Posts</h2>
		</div>
	</div>
	<div class="row">
		<?php foreach($recentPosts as $post) : setup_postdata($post); ?>
			<div class="span4">
				<a class="post-thumb" href="<?php the_permalink(); ?>">
					<?php echo ah_get_custom_thumb('', 'gallery-large'); ?>
					<span class="title"><?php the_title(); ?></span>
				</a>
				<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?></span>
				<a href="<?php the_permalink(); ?>" class="read" style="padding-bottom:20px;">Read &raquo;</a>
			</div>
			<?php $counter = $counter + 1; ?>
			<?php if ($counter % 3 == 0) { ?>
				</div>
				<div class="row">
			<?php } ?>
		<?php endforeach; ?>
	</div>
</div>

<!-- Search -->
<div class="container white search">
	<div class="row">
		<div class="span12 large-form">
			<h2>Search</h2>
			<?php get_search_form(); ?>
		</div>
	</div>
</div>

<!-- Listing -->
<div id="category-children" class="container white">
	<div class="row">
		<div class="span12">
			<?php if (have_posts()) : ?>
				<h2>Browse</h2>
				<div class="row category-filter">
					<div class="span3">
						<h3>Browse Blog Posts by Category</h3>
					</div>
					<div class="span9">

						<!-- Category List -->
						<ul id="category-links" class="post-categories">
							<?php
							$categories = get_categories(/*array('number'=>12)*/);
							$best_bits_id = get_cat_ID('Best Bits');

							foreach($categories as $category) {
								$obj = get_object_vars($category);
								$catId = null;
								$catName = null;
								$class = '';

								if (isset($obj["cat_ID"])) {
									// Default to current category
									$catId = $obj["cat_ID"];
									if ($currentCategoryId == $catId) {
										$class = ' class="current"';
									}
								}

								if (isset($obj["name"])) {
									$catName = $obj["name"];
								}

								$catURL = esc_url(home_url('/')) . '?cat=' . $catId;

								?>

								<li>
									<a<?php echo $class; ?> rel="category" title="View latest posts in <?php echo $catName; ?>" href="<?php echo $catURL; ?>"><?php echo $catName; ?></a>
								</li>

								<?php
							}
							?>
						</ul>
					</div>
				</div>
				<div id="category-posts" class="row posts">
					<?php while (have_posts()) : the_post(); ?>
						<div class="span3">
							<a class="post-thumb" href="<?php the_permalink(); ?>">
								<?php echo ah_get_custom_thumb(); ?>
								<span class="title"><?php the_title(); ?></span>
							</a>
						</div>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<h1><?php echo single_cat_title(); ?><?php _e(' has no posts', 'alastairhumphreys'); ?></h1>
				<p><?php _e('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'alastairhumphreys'); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>

			<?php if (function_exists('wp_paginate') || $wp_query->max_num_pages > 1) { ?>
				<!-- Pagination -->
				<div id="pagination" class="row">
					<div class="span12">
						<?php 
							if (function_exists('wp_paginate')) {
								wp_paginate();
							} else if ($wp_query->max_num_pages > 1) {
								alastairhumphreys_content_nav('nav-below');
							}
						?>
					</div>
				</div>
			<?php } ?>

		</div>
	</div>
</div>

<?php

get_footer();

?>
