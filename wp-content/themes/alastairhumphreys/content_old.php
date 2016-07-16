<?php
/**
 * The fall back template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

$featureImageSize = get_field('feature_image_size');

?>

<?php while (have_posts()) : the_post(); ?>
	
	<!-- Feature Image -->
	<?php echo ah_get_feature_image($size = $featureImageSize); ?>

	<div class="container white content<?php echo $featureImageSize == 'feature-normal' ? ' top' : ''; ?>">
		<div class="row">
			<div class="span1">
				&nbsp;
				<!--
				<div class="post-date">
					<?php alastairhumphreys_posted_on(); ?>
				</div>
				-->
			</div>
			<div class="span10">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if (get_field('title_position') != 'image') { ?>
						<header class="clearfix">
							<h1><?php the_title(); ?></h1>
						</header>
					<?php } ?>

					<div class="entry-content">
						<?php the_content(); ?>

						<?php wp_link_pages(array('before' => '<div class="page-link"><span>' . __( 'Pages:', 'alastairhumphreys' ) . '</span>', 'after' => '</div>')); ?>
					</div>

					<footer class="entry-meta">
						<?php if (get_the_author_meta('description') && is_multi_author()) { // If a user has filled out their description and this is a multi-author blog, show a bio on their entries ?>
							<div id="author-info">
								<div id="author-avatar">
									<?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('alastairhumphreys_author_bio_avatar_size', 68)); ?>
								</div>
								<div id="author-description">
									<h2><?php printf( esc_attr__( 'About %s', 'alastairhumphreys' ), get_the_author() ); ?></h2>
									<?php the_author_meta( 'description' ); ?>
									<div id="author-link">
										<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
											<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'alastairhumphreys' ), get_the_author() ); ?>
										</a>
									</div>
								</div>
							</div>
						<?php } ?>
					</footer>
				</article>


				

				<!-- Previous / Next -->
				<div class="next-previous clearfix">
					<div class="previous">
						<?php previous_post_link('%link', 'Previous'); ?>
					</div>
					<div class="next">
						<?php next_post_link('%link', 'Next'); ?>
					</div>
				</div>
				
				<a href="#comments" class="read-comments scrollTo">
					<span class="icon-angle-down"></span>
					<span class="text">Read Comments</span>
				</a>
			</div>
			<div class="span1">&nbsp;</div>
		</div>

		<div class="row newsletter-block">

<!--Donation -->
<div class="span2">&nbsp;</div>
			<div class="span9">
				<h2>Thank You</h2>
<p>Thank you to the many people who have kindly "bought me a coffee" for just £2.50 as encouragement to keep this blog going.
"Yes, I too would like to donate a couple of pounds to this site..!"</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QJVVJZ2AKL3M4">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form><br /><br /></div>

<!--Donation Ends -->
			<div class="span2">&nbsp;</div>
			<div class="span9">
				<h2>Free Occasional Newsletter</h2>
				<p>Join thousands of others in receiving an occasional email with news, updates and the best bits from the blog. No spam, rare enough so as not to annoy, and easy to unsubscribe from...</p>
				
				<!-- Newsletter Form -->
				<?php get_template_part('form', 'newsletter'); ?>
				
			</div>
			<div class="span1">&nbsp;</div>
		</div>
	</div>

	<!-- Recent Posts -->
	<?php get_template_part('content', 'recent-posts'); ?>

	<div id="comments" class="background">
		<div class="container white">
			<!-- Comments -->
			<?php comments_template('', true); ?>

			<!-- Comments Form -->
			<?php get_template_part('content', 'comments-form'); ?>
		</div>
	</div>

<?php endwhile; ?>