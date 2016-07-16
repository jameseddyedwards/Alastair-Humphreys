<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */


// Most Popular - Best Bits Category
$popularArgs = array(
	'numberposts'     => 3,
	'category'        => 'best-bits',
);
$popularPosts = get_posts($popularArgs);


// Blog Categories - All categories from the blog
$blogCategoriesArgs = array(
	'child_of'	=>	'blog',
);
$blogCategories = get_categories($blogCategoriesArgs);


// Categories
$args = array(
	'orderby' => 'name',
	'order' => 'ASC',
);
$categories = get_categories($args);


?>

</div> <!-- Close 'main' container <div> -->

<div class="footer full-section white">
	<div class="container">
		<div class="row">

			<!-- Newsletter Sign Up & Support -->
			<div class="span4">
				<section>
					<h4>Newsletter Sign Up</h4>
					
					<!-- Newsletter Form -->
					<?php get_template_part('form', 'newsletter'); ?>

				</section>

				<?php if (get_field('support', 'option')) { ?>
					<section>
						<h4>Support</h4>
						<p><?php the_field('support', 'option'); ?></p>
						<?php echo do_shortcode('[donate]'); ?>
					</section>
				<?php } ?>
			</div>

			<!-- Upcoming Events 
			<div class="span3">
				<?php if (get_field('affiliates', 'option')) { ?>
					<section>
						<h4>Affiliated Links</h4>
						<ul class="unstyled">
							<?php while( has_sub_field('affiliates', 'option') ): ?>
								<li><a rel="nofollow" href="<?php the_sub_field('affiliate_url'); ?>"><?php the_sub_field('affiliate_name'); ?></a></li>
							<?php endwhile; ?>
						</ul>
					</section>
				<?php } ?>
			</div>
			-->

			<!-- Blog Topics -->
			<div class="span4 clearfix">
				<section>
					<h4>Blog Topics</h4>
					<span class="blog-topics">
						<?php foreach($blogCategories as $category) : ?>
							<a href="<?php get_category_link($category -> term_id); ?>"><?php $category->name ?></a>
						<?php endforeach; ?>
						<?php foreach($categories as $category) { 
							echo '<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a>';
						} ?>
					</span>
				</section>
			</div>

			<!-- Social -->
			<div class="span4 social">

				<!-- Twitter -->
				<a class="twitter-timeline" href="https://twitter.com/Al_Humphreys" data-widget-id="309373700933816321">Tweets by @Al_Humphreys</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				<br />
				<!-- Follow -->
				<a href="https://twitter.com/Al_Humphreys" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @Al_Humphreys</a>

				<!-- Like -->
				<div class="fb-like" data-href="http://www.alastairhumphreys.com/" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false"></div>
				
			</div>
		</div>
	</div>
</div>

<div class="credits full-section">
	<div class="container">
		<div class="row">
			<div class="span12">
				<?php do_action('alastairhumphreys_credits'); ?>
				<span class="copyright" title="<?php esc_attr_e( 'Proudly powered by WordPress', 'alastairhumphreys' ); ?>"><?php printf( __( '&copy; Copyright 2012 Alastair Humphreys. All rights reserved.', 'alastairhumphreys' ), 'WordPress' ); ?></span>
				<a class="credit" href="<?php echo esc_url( __( 'http://www.jsummerton.co.uk/', 'alastairhumphreys' ) ); ?>" title="<?php esc_attr_e( 'A Worcester, UK based web designer specialising in clear & compelling, fast & functional web sites.', 'alastairhumphreys' ); ?>" rel="generator"><?php printf( __( 'Site design by JSummerton', 'alastairhumphreys' ), 'WordPress' ); ?></a>
			</div>
		</div>
	</div>
</div>

<?php wp_footer(); ?>

</body>
</html>