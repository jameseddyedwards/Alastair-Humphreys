<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */
?>

<!-- Adventures -->
<!-- Types - grahical, single-post, categories -->

<?php

$subNavs = array(
	'Blog' => array(
		'menu_item' => 17709,
		//'menu_item' => 14358,
		'type' => 'blog',
		'categories' => array(
			'Blog'
		)
	),
	/*'Adventures' => array(
		'menu_item' => 8483,
		'type' => 'adventures',
		'page_parents' => array(
			//'Grand Adventures',
			'Adventures',
			'Microadventures'
		),
		'categories' => array(
			//'grandadventures',
			'adventures',
			'microadventures'
		)
	),
	'Books' => array(
		'menu_item' => 8529, // Local
		'type' => 'books',
		'page_parents' => array(
			'Books'
		)
	),*/
	/*
	'More' => array(
		'menu_item' => 9206, // Local
		'type' => 'more',
		'page_parents' => array(
			'More'
		)
	)
	*/
);

?>
<?php if (count($subNavs) > 0) { ?>
	<?php foreach ($subNavs as $subNav) { ?>
		<?php $menu_class = 'menu-item-' . $subNav['menu_item']; ?>
		<div id="dropdown-<?php echo $menu_class; ?>" class="dropdown <?php echo $menu_class; ?>">
			<?php
			$subNavType = $subNav['type'];
			$i = 1;
			
			// Blog Dropdown
			// Single Post dropdown showing latest post and all sub-categories
			if ($subNavType == 'blog') {
				$subNavCategories = $subNav['categories'];
				$categoryCount = sizeof($subNavCategories);
				foreach ($subNavCategories as $subNavCategory) {
					$categoryId = get_cat_ID($subNavCategory);
					$categoryPosts = get_posts(array('numberposts'=>1, 'cat'=>$categoryId));
					$categoryURL = esc_url(home_url('/')) . '?cat=' . $categoryId;
					$categoryClass = strtolower(str_replace(" ", "-", $subNavCategory));
					?>
				
					<div class="category clearfix <?php echo $categoryClass ?>">

						<!-- Latest Post -->
						<div class="latest-post clearfix">
							<span class="sub-title">Latest Post</span>

							<?php foreach($categoryPosts as $categoryPost) : setup_postdata($categoryPost); ?>
								<a class="feature-image" href="<?php echo get_permalink($categoryPost->ID); ?>">
									<?php echo ah_get_custom_thumb($categoryPost->ID); ?>
								</a>
								<div class="post-text">
									<a href="<?php echo get_permalink($categoryPost->ID); ?>" class="sub-title"><?php echo $categoryPost->post_title ?></a>
									<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?></span>
									<a class="arrow-link continue-reading" href="<?php echo get_permalink($categoryPost->ID); ?>">continue reading</a>
								</div>
							<?php endforeach; ?>
						</div>

						
						<?php

						$blogCategories = get_field('blog_categories', 'option');

						if ($blogCategories) {

							$blogCategoryTotal = sizeof($blogCategories);
							$blogCategoryCount = 0;
							$columnCount = 3;
							$listCount = 8;

							foreach ($blogCategories as $blogCatId) {
								$blogCategoryCount = $blogCategoryCount + 1;
								$blogCatName = get_the_category_by_ID($blogCatId);
								$blogCatUrl = get_category_link($blogCatId);
								$last = $blogCategoryCount >= ($listCount * ($columnCount - 1)) ? " last" : "";

								if ($blogCategoryCount % $listCount == 1) {
									echo '<ul class="category-list' . $last . '">';
								}

								echo '<li><a href="' . $blogCatUrl . '">' . $blogCatName . '</a></li>';
								
								if ($blogCategoryCount % $listCount == 0) {
									echo '</ul>';
								}
							}
						} else {
						?>
							<!-- Category List -->
							<span class="sub-title"><?php echo $categoryClass; ?> Categories</span>
							<ul class="category-list">
								<?php
									$categoryListArgs = array('child_of'=>$categoryId,'number'=> '24');
									$subNavSubCategories = get_categories($categoryListArgs);
								?>
								<?php $subCategoryCount = 1; ?>
								<?php foreach ($subNavSubCategories as $subNavSubCategory) { ?>
									<li>
										<a href="<?php echo get_category_link($subNavSubCategory->term_id); ?>"><?php echo $subNavSubCategory->name ?></a>
									</li>
									<?php if ($subCategoryCount == 8 || $subCategoryCount == 16) { ?>
										</ul>
										<ul class="clearfix<?php echo $subCategoryCount == 16 ? ' last' : ''; ?>">
									<?php } ?>
									<?php $subCategoryCount = $subCategoryCount + 1; ?>
								<?php } ?>
							</ul>

						<?php 
						}

						?>

						<a href="/category/blog/" class="arrow-link view-all">browse all posts</a>
					</div>
					<?php if ($i != $categoryCount) { ?>
						<hr />
					<?php } ?>
					<?php $i = $i + 1; ?>
					<?php
				}
			}

			// Adventures Dropdown
			else if ($subNavType == 'adventures') {

				$subNavCategories = $subNav['categories'];
				$numberOfCategories = sizeof($subNavCategories);

				// Loop over categories
				foreach ($subNavCategories as $subNavCategory) {

					$categoryId = '';
					$categoryVars = get_object_vars(get_category_by_slug($subNavCategory));

					if (isset($categoryVars["cat_ID"])) {
						$categoryId = $categoryVars["cat_ID"];
					}

					if ($categoryId != '') {

						// Get category posts / permalinks
						$categoryPosts = get_posts(array(
							'category'     		=> $categoryId,
							'numberposts'     	=> 10
						));
						
						if (!empty($categoryPosts)) {

							$mostRecentPost = $categoryPosts[0]; // Get most recent post in category to feature
							$categoryURL = get_category_link($categoryId);
							$categoryName = get_cat_name($categoryId);
							$categoryClass = strtolower(str_replace(" ", "-", $subNavCategory));

						?>
					
							<div class="category clearfix <?= $categoryClass; ?>">
								<a href="<?= $categoryURL; ?>" class="sub-title"><?= $categoryName; ?></a>
								
								<?php setup_postdata($mostRecentPost); ?>
								<a class="feature-image" href="<?php echo get_page_link($mostRecentPost->ID); ?>">
									<?php echo ah_get_custom_thumb($mostRecentPost->ID); ?>
								</a>
								<?php wp_reset_postdata(); ?>
								
								<ul class="clearfix">
									<?php $postCount = 1; ?>

									<?php foreach ($categoryPosts as $post) { ?>
										<?php setup_postdata($post); ?>
										<li>
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</li>
										<?php if ($postCount == 5) { ?>
											</ul>
											<ul class="clearfix last">
										<?php } ?>
										<?php $postCount++; ?>
									<?php } ?>
									<?php wp_reset_postdata(); ?>
								</ul>
								<a href="<?= $categoryURL; ?>" class="arrow-link view-all">view all <?= $categoryName; ?></a>
							</div>
							<?php if ($i != $numberOfCategories) { ?>
								<hr />
							<?php } ?>
							<?php $i = $i + 1; ?>
						<?php } ?>
					<?php } ?>
				<?php }
			}

			// Books Dropdown
			else if ($subNavType == 'books') {

				$bookArgs = array(
					'post_type'       => 'page',
					'post_parent'     => 803,
					'numberposts'     => 7
				);
				$bookPages = get_posts($bookArgs);
				$counter = 0;
				$booksURL = esc_url(home_url('/')) . '?page_id=803';

				?>
				<div class="books clearfix <?php echo $categoryClass; ?>">
					<?php foreach ($bookPages as $bookPage) { ?>
						<?php $counter = $counter + 1; ?>
						<a class="feature-image<?php echo $counter == 7 ? ' last' : ''; ?>" href="<?php echo get_page_link($bookPage->ID); ?>">
							<?php echo ah_get_custom_thumb($bookPage->ID, $size='original'); ?>
							<span><?php echo $bookPage->post_title; ?></span>
						</a>
					<?php } ?>
				</div>
				<hr />
				<div class="view-all-link">
					<a href="<?php echo $booksURL; ?>" class="arrow-link view-all">view all Books</a>
				</div>
			<?php }


			// More Dropdown
			else if ($subNavType == 'more') {
				$subNavPages = $subNav['page_parents'];
				foreach ($subNavPages as $subNavPage) {
					$pageVars = get_object_vars(get_page_by_title($subNavPage));
					$pageId = '';
					
					if (isset($pageVars["ID"])) {
						$pageId = $pageVars["ID"];
					}

					if ($pageId != "") {
						$moreArgs = array(
							'numberposts'		=> -1,
							'post_type'			=> 'page',
							'post_parent'		=> $pageId
						);
						$moreLinks = get_posts($moreArgs);
					}
					$counter = 0;
					?>
					<div class="more clearfix <?php echo $categoryClass; ?>">
						<?php foreach ($moreLinks as $moreLink) { ?>
							<?php $counter = $counter + 1; ?>
							<a href="<?php echo get_page_link($moreLink->ID); ?>"><?php echo $moreLink->post_title; ?></a>
						<?php } ?>
					</div>
				<?php }
			} ?>
		</div>
	<?php } ?>
<?php } ?>
