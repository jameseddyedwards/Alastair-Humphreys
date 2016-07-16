<?php
/**
 * The snippet of code which displays the Best Bits / Recent Posts block
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

$bestBitsCategoryId = get_cat_ID('Best bits');
$blogCategoryId = get_cat_ID('Blog');
$bestBitsCategoryUrl = get_category_link($bestBitsCategoryId) . "#category-children";
$blogCategoryUrl = get_category_link($blogCategoryId) . "#category-children";

$bestBits = get_posts(array(
	'numberposts'	=> 3,
	'cat'			=> $bestBitsCategoryId
));
$recentPosts = get_posts(array(
	'numberposts'		=> 3,
	'orderby'           => 'date',
	'order'             => 'DESC',
	'cat'				=> $blogCategoryId
));

?>

<div class="best-bits">

	<!-- Navigation -->
	<div class="head-bar clearfix">
		<ul id="post-view" class="tabs clearfix">
			<li id="recent" class="active" data-url="<?php echo $blogCategoryUrl; ?>">Latest posts</li>
			<li id="best" data-url="<?php echo $bestBitsCategoryUrl; ?>">Best bits</li>
		</ul>
		<a id="view-all" class="view-all" href="<?php echo $blogCategoryUrl; ?>">view all</a>
	</div>

	<!-- Best Bits -->
	<div class="row tab-row best">
		<?php foreach($bestBits as $post) :	setup_postdata($post); ?>
			<div class="span4">
				<a class="post-thumb" href="<?php the_permalink(); ?>">
					<?php echo ah_get_custom_thumb(); ?>
					<span class="title"><?php the_title(); ?></span>
				</a>
				<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- Recent Posts -->
	<div class="row tab-row recent active">
		<?php foreach($recentPosts as $post) : setup_postdata($post); ?>
			<div class="span4">
				<a class="post-thumb" href="<?php the_permalink(); ?>">
					<?php echo ah_get_custom_thumb(); ?>
					<span class="title"><?php the_title(); ?></span>
				</a>
				<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?></span>
			</div>
		<?php endforeach; ?>
	</div>
