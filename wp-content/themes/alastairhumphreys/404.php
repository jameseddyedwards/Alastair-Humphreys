<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

$category_best = get_cat_ID('Best bits');
$category_blog = get_cat_ID('Blog');
$recentArgs = array(
	'numberposts'		=> 3,
	'cat'				=> $category_blog
);
$bestArgs = array(
	'numberposts'	=> 3,
	'cat'			=> $category_best
);
$bestBits = get_posts($bestArgs);
$recentPosts = get_posts($recentArgs);

get_header(); ?>

	<div class="container white not-found">

		<div class="row">
			<div class="span12">
				<h1><?php _e( 'Sometimes in life we get a little lost...', 'alastairhumphreys' ); ?></h1>
				<p><?php _e( "During various updates to my site some of the link formatting got a bit muddled. <br>Please try this:<br>
If the link you are looking for is something like <em>www.alastairhumphreys.com/2010/05/blahblahblah</em><br>
Try deleting the numbers in the middle to <em>wwww.alastairhumphreys.com/blahblahblah</em><br>
That should fix most problems!<br>
If it doesn't please contact me and I'll try to help.<br>
Otherwise you could just give up and go for a run instead...", "alastairhumphreys"); ?></p>
			</div>
		</div>

		<div class="row">
			<div class="span12">
				<!-- Recent Posts -->
				<h2>Recent Posts</h2>
				<div class="row">
					<?php foreach($recentPosts as $post) : setup_postdata($post); ?>
						<div class="span4">
							<a class="post-thumb" href="<?php the_permalink(); ?>">
								<?php echo ah_get_custom_thumb(); ?>
								<span class="title"><?php the_title(); ?></span>
							</a>
							<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?>...</span>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Best Bits -->
				<h2>Best Bits</h2>
				<div class="row">
					<?php foreach($bestBits as $post) :	setup_postdata($post); ?>
						<div class="span4">
							<a class="post-thumb" href="<?php the_permalink(); ?>">
								<?php echo ah_get_custom_thumb(); ?>
								<span class="title"><?php the_title(); ?></span>
							</a>
							<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?>...</span>
						</div>
					<?php endforeach; ?>
				</div>

				<h2>Search</h2>
				<p>Still can't find what you're looking for? Try searching...</p>
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>

<?php get_footer(); ?>