<?php
/**
 * Category Template: Category Gallery
 */

get_header();

$categories = get_the_category();
$categoryId = $categories[0]->cat_ID;
$currentCategoryId = get_query_var('cat');

?>

<!-- Feature Image -->
<?php ah_the_feature_image('feature-normal'); ?>

<!-- Category Posts -->
<?php if (have_posts()) { ?>
	
	<?php
	$galleryBlocks = array("large", "small", "medium right", "medium", "small right", "large fl-right right", "medium fl-right", "small fl-right", "small fl-right", "medium fl-right");
	$galleryImageSizes = array("gallery-large", "gallery-small", "gallery-medium", "gallery-medium", "gallery-small", "gallery-large", "gallery-medium", "gallery-small", "gallery-small", "gallery-medium");
	$count = 0;
	?>

	<!-- Gallery Images -->
	<div class="gallery-thumbs clearfix">

		<?php while (have_posts()) { ?>

			<?php the_post(); ?>
		 
			<?php $image = get_field('feature_image'); ?>
			<?php $image = $image['sizes'][$galleryImageSizes[$count]]; ?>
			<?php $permalink = get_permalink(); ?>
			<?php $title = get_the_title(); ?>
			
			<a class="<?php echo $galleryBlocks[$count]; ?> post-thumb" href="<?php echo $permalink; ?>">
				<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" rel="<?php echo $title; ?>" />
				<span class="title"><?php echo $title; ?></span>
			</a>
	 		
	 		<?php $count = $count < 9 ? $count + 1 : 0; ?>

		<?php } ?>

		<?php wp_reset_postdata(); ?>

	</div>

<?php } ?>

<!-- Category Posts -->
<div class="container white">

	<?php
		$term = 'category_' . $categoryId;
		$categoryContent = get_field('category_content', $term);
	?>
	<?php if ($categoryContent) { ?>
		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span10 content">
				<h1><?php single_cat_title(''); ?></h1>
				<article>
					<?php echo $categoryContent; ?>
				</article>
			</div>
		</div>
	<?php } ?>

	<!--
	<div class="row">
		<div class="span1">&nbsp;</div>
		<?php

		$categoryPosts = get_posts(array(
			'category' => $categoryId,
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page'   => -1,
		));

		$breakCount = floor(sizeof($categoryPosts) / 2);
		$counter = 0;

		?>

		<?php if (have_posts($categoryPosts)) { ?>

			<div class="span5">
				<ul class="link-list clearfix">
					<?php foreach ($categoryPosts as $post) { ?>
						<?php setup_postdata($post); ?>

						<?php $counter++; ?>

						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>

						<?php if ($counter == $breakCount) { ?>
								</ul>
							</div>
							<div class="span5">
								<ul class="link-list clearfix">
						<?php } ?>

					<?php } ?>
				</ul>
			</div>
			<?php wp_reset_postdata(); ?>

		<?php } ?>		
	</div>
	-->

	<div class="row">
		<div class="span1">&nbsp;</div>
		<div class="span10">
			<!-- AddThis Social Buttons -->
			<?php get_template_part('content', 'add-this'); ?>
		</div>
	</div>
</div>

<!-- Listing -->
<div id="category-children" class="container white">
	<div class="row">
		<div class="span12">
			<?php query_posts($query_string . '&orderby=date&order=DESC'); ?>
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


<?php get_footer(); ?>