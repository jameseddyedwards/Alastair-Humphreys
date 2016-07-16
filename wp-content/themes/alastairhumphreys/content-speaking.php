<?php
/**
 * The template for displaying simple page content e.g. Speaking
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

$featureImageSize = get_field('feature_image_size');

?>

<div class="split-layout background">


	<!-- Feature Image -->
	<?php echo ah_get_feature_image($size = $featureImageSize); ?>
	

	<div class="container white content">
		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span10">
				<?php while (have_posts()) : the_post(); ?>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>

					<?php if (get_field('information_resources') != '') { ?>
						<div class="information-resources">
							<!-- Information & Resources -->
							<h2>Information and Resources</h2>
							<?php the_field('information_resources'); ?>
						</div>
					<?php } ?>
				<?php endwhile; ?>
			</div>
			<div class="span1">&nbsp;</div>
		</div>
	</div>

	<!-- Testimonials -->
	<?php if (get_field('testimonials') != '') { ?>
		<div class="container white content testimonials">
			<div class="row">
				<div class="span1">&nbsp;</div>
				<div class="span10">
					<h2>Testimonials</h2>
					<?php the_field('testimonials'); ?>
				</div>
				<div class="span1">&nbsp;</div>
			</div>
		</div>
	<?php } ?>

	<!-- Book Alastair -->
	<div id="booking-form" class="container white content booking-form">
		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span10">
				<h1>Book Alastair</h1>
				<p>If you would like to book Alastair for a speaking event, or for more information, please fill out the form below.<br />Alternatively, email <a href="mailto:speaking@alastairhumphreys.com">speaking@alastairhumphreys.com</a>.</p>
				<?php echo do_shortcode('[contact-form-7 id="9520" title="Book Alastair"]'); ?>
			</div>
			<div class="span1">&nbsp;</div>
		</div>
	</div>

</div>
