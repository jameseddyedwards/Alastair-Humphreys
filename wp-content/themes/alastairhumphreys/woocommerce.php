
<?php
/**
 * Description: The template used to display the WooCommerce shop page
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

$featureImageSize = get_field('feature_image_size');

?>

<!-- Feature Image -->
<?php echo ah_get_feature_image($size = $featureImageSize); ?>

<div class="container white content<?php echo $featureImageSize == 'feature-normal' ? ' top' : ''; ?>">
	<div class="row">
		<div class="span1">&nbsp;</div>
		<div class="span10">
			<?php woocommerce_content(); ?>
		</div>
		<div class="span1">&nbsp;</div>
	</div>
	<div class="row newsletter-block">
		<div class="span1">&nbsp;</div>
		<div class="span10">
			<h2>Free Occasional Newsletter</h2>
			<p>Join thousands of others in receiving an occasional email with news, updates and the best bits from the blog. No spam, rare enough so as not to annoy, and easy to unsubscribe from...</p>
			
			<!-- Newsletter Form -->
			<?php get_template_part('form', 'newsletter'); ?>
			
		</div>
		<div class="span1">&nbsp;</div>
	</div>
</div>

<?php get_footer(); ?>