<?php
/**
 * Template Name: Book Template
 * Description: A Template used to display a single Book
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

wp_register_style('books', get_template_directory_uri() . '/css/books.css', __FILE__);	
wp_enqueue_style('books');

get_header();

$featureImageSize = get_field('feature_image_size');

$paypalId = get_field('paypal_add_to_cart_id');
$bookPageId = get_the_ID();

?>

<?php while (have_posts()) : the_post(); ?>

	<!-- Feature Image -->
	<?php echo ah_get_feature_image($size = $featureImageSize); ?>

	<div class="book">
		<div class="container white content<?php echo $featureImageSize == 'feature-normal' ? ' top' : ''; ?>">
			<div class="row">
				<div class="span1">&nbsp;</div>
				<div class="span3 book-thumb">
					<?php echo ah_get_custom_thumb($bookPage->ID, $size='original'); ?>

					<?php echo do_shortcode('[add-to-cart]' . $paypalId . '[/add-to-cart]'); ?>

				</div>
				<div class="span7">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header>
							<h1><?php the_title(); ?></h1>
							<span class="summary"><?php the_field('book_meta') ?></span>
							<span class="quotes"><?php the_field('quotes') ?></span>
						</header>

						<div class="entry-content">
							<?php the_content(); ?>
							<?php echo do_shortcode('[add-to-cart]' . $paypalId . '[/add-to-cart]'); ?>
						</div>
					</article>
				</div>
				<div class="span1">&nbsp;</div>
			</div>
		</div>
		
		<?php if (get_field('has_multiple_formats') && get_field('book_formats') != '') { ?>
			<div class="container white formats">
				<div class="row">
					<div class="span1">&nbsp;</div>
					<div class="span10">
						<h2>Available Formats</h2>
					</div>
					<div class="span1">&nbsp;</div>
				</div>
				<div class="row">
					<div class="span1">&nbsp;</div>
					<div class="span10">
						<?php the_field('book_formats'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>