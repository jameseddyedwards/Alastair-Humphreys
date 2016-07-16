<?php
/**
 * Template Name: Full Width Template
 * Description: A template used to provide a completely bespoke full width content page. E.g. Video
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

<?php while (have_posts()) : the_post(); ?>

	<?php the_content(); ?>

<?php endwhile; ?>

<?php get_footer(); ?>