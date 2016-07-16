<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

$counter = 0;

?>

<div class="container white">
	<?php if (have_posts()) : ?>
		
		<div class="row">
			<div class="span12">
				<h1 class="page-title">Search Results</h1>
				<p>
					<?php
				if (isset($_GET['s']) && $_GET['s'] == "" || $_GET['s'] == " ") {
					printf( __( 'You appear to have entered an empty search. Try searching again or try searching by category below.', 'alastairhumphreys' ), get_search_query());
				} else {
					printf( __( 'Your search for <strong>"%s"</strong> has found these results:', 'alastairhumphreys' ), get_search_query());
				}
				?>
				</p>
			</div>
		</div>

		<!-- Post List -->
		<div class="row">
			<?php while (have_posts()) : the_post(); ?>
				<?php $counter = $counter + 1; ?>
				<div class="span4">
					<a class="post-thumb" href="<?php the_permalink(); ?>">
						<?php echo ah_get_custom_thumb(); ?>
						<span class="title"><?php the_title(); ?></span>
					</a>
					<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?>...</span>
				</div>
				<?php if ($counter % 3 == 0) { ?>
					</div><div class="row">
				<?php } ?>
			<?php endwhile; ?>
		</div>

		<!-- Pagination -->
		<div class="row">
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

	<?php else : ?>
		<div class="row">
			<div class="span12">
				<h1>No Results Found</h1>
				<p><?php _e('Unfortunately nothing can be found relating to your search. Why not try searching for a more generic keyword:', 'alastairhumphreys'); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div>
	<?php endif; ?>
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

<div class="container white">
	<div class="row category-filter">
		<div class="span12">
			<h2>Search by Category</h2>
			<p>Can't find what you're looking for? Try searching by category:</p>

			<!-- Category List -->
			<ul class="post-categories">
				<?php
				$categories = get_categories();
				foreach($categories as $category) {
					$obj = get_object_vars($category);
					$catId = $obj['cat_ID'];
					$catName = $obj['name'];
					$catURL = get_category_link($catId);

					if ($catName != "Uncategorized") {
					?>

					<li>
						<a rel="category" title="View latest posts in <?php echo $catName; ?>" href="<?php echo $catURL; ?>"><?php echo $catName; ?></a>
					</li>

					<?php
					}
				}
				?>
			</ul>
		</div>
	</div>

</div>

<?php get_footer(); ?>