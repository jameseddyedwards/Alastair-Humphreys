<?php
/**
 * Template Name: Shop
 *
 * @package WordPress
 * @subpackage Paycircle
 * @since Paycircle 1.0
 */

get_header();

$numberOfBooks = 50;
$counter = 0;
$books = array(
	'post_type'       => 'page',
	'post_parent'     => $post->ID,
	'numberposts'     => $numberOfBooks,
	'orderby'		  => 'menu_order',
	'order'           => 'ASC'
);
$books = get_posts($books);
$featureImageSize = get_field('feature_image_size');

?>

<!-- Feature Image -->
<?php echo ah_get_feature_image($size = $featureImageSize); ?>

<?php if (have_posts()) : the_post(); ?>
	<!--
	<div class="container white content<?php echo $featureImageSize == 'normal' ? ' top' : ''; ?>">
		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span9">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
			<div class="span2">&nbsp;</div>
		</div>
	</div>
-->
<?php endif; ?>


<!-- Books -->
<div class="container books">
	<div class="row">
		<?php foreach($books as $book) : setup_postdata($book); ?>
			<div class="span4">
				<div class="book">
					<a class="book-cover" href="<?php echo esc_url(home_url('/')) . '?page_id=' . $book->ID; ?>"><?php echo ah_get_custom_thumb($book->ID, $size='original'); ?></a>

					<div class="info">
						<!--<h2><a class="title" href="<?php echo esc_url(home_url('/')) . '?page_id=' . $book->ID; ?>"><?php echo $book->post_title; ?></a></h2>-->
						<div class="content">
							<?php
								if (get_field('book_summary', $book->ID) != '') {
									the_field('book_summary', $book->ID);
								} else {
									the_excerpt();
								}
							?>
						</div>
						<div class="summary"><?php the_field('book_meta', $book->ID) ?></div>
						<a class="box-button" href="<?php echo esc_url(home_url('/')) . '?page_id=' . $book->ID; ?>">Buy Now</a>
					</div>
				</div>
			</div>
			
			<?php $counter = $counter + 1; ?>

			<?php if ($counter % 3 === 0) { ?>
				</div>
				<div class="row">
			<?php } ?>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</div>


	<!-- Further Reading -->
	<?php if (get_field('further_reading') != '') { ?>
		<div class="row">
			<div class="span12">
				<div class="container content further-reading white">
					<div class="span1">&nbsp;</div>
					<div class="span9">
						<h2>Further Reading</h2>
						<?php the_content(); ?>
						<?php the_field('further_reading'); ?>
					</div>
					<div class="span2">&nbsp;</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<?php get_footer(); ?>
