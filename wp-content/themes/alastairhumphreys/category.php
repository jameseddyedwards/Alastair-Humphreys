<?php
/**
 * The template for displaying a Category.
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

$currentCategoryId = get_query_var('cat');

?>

<!-- Listing -->
<div id="category-children" class="container white">
	<div class="row">
		<div class="span12">
			<?php if (have_posts()) { ?>
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
					<?php while (have_posts()) { ?>
						<?php the_post(); ?>
						<div class="span3">
							<a class="post-thumb" href="<?php the_permalink(); ?>">
								<?php echo ah_get_custom_thumb(); ?>
								<span class="title"><?php the_title(); ?></span>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<h1><?php echo single_cat_title(); ?><?php _e(' has no posts', 'alastairhumphreys'); ?></h1>
				<p><?php _e('There are currently no posts in this category.', 'alastairhumphreys'); ?></p>
				<?php get_search_form(); ?>
			<?php } ?>

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
