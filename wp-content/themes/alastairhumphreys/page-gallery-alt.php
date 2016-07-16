<?php
/**
 * Template Name: Gallery Template Alternative
 * Description: A Page Template that shows a large feature image with a small gallery area below
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

?>

<?php while (have_posts()) : the_post(); ?>
	<?php
		$args = array(
			'depth'			=> 1,
			'child_of'		=> $post->ID,
			'title_li'		=> '',
		);
		$galleryImageCount = 20;
		$count = 0;

		// Feature Image
		$featureImageObj = get_field('feature_image');

		if ($featureImageObj != '' && isset($featureImageObj["sizes"]) && isset($featureImageObj["title"])) {
			$featureImageUrl = $featureImageObj["sizes"]['feature-wide'];
			$featureImageTitle = $featureImageObj["title"];
			$featureImageHtml = '<div class="feature-normal"><img src="' . $featureImageUrl . '" alt="' . $featureImageTitle . '" /></div>';
			echo $featureImageHtml;
		}
	?>

	<?php if (get_field('image_blocks')): ?>

		<!-- Gallery Images -->
		<div class="gallery-thumbs clearfix">
			<?php
				$galleryBlocks = array("large", "small", "medium right", "medium", "small right", "large fl-right right", "medium fl-right", "small fl-right", "small fl-right", "medium fl-right");
				$galleryImageSizes = array("gallery-large", "gallery-small", "gallery-medium", "gallery-medium", "gallery-small", "gallery-large", "gallery-medium", "gallery-small", "gallery-small", "gallery-medium");
			?>
		 
			<?php while(has_sub_field('image_blocks')): ?>
				<?php $image = get_sub_field('block_image'); ?>
				<?php $image = $image['sizes'][$galleryImageSizes[$count]]; ?>
				<?php $permalink = get_permalink(get_sub_field('block_post')); ?>
				<?php $title = get_the_title(get_sub_field('block_post')); ?>
				
				<a class="<?php echo $galleryBlocks[$count]; ?> post-thumb" href="<?php echo $permalink; ?>">
					<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" rel="<?php echo $title; ?>" />
					<span class="title"><?php echo $title; ?></span>
				</a>
		 		
		 		<?php $count = $count < 9 ? $count + 1 : 0; ?>
			<?php endwhile; ?>
		 
		</div>
	<?php endif; ?>
	
	<!-- Category Posts -->
	<div class="container white">
		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span10 content">
				<?php the_content(); ?>
				<hr />
			</div>
		</div>

		<div class="row">
			<div class="span1">&nbsp;</div>
			<div class="span10">
				<h2><?php the_title(); ?></h2>
				<ul class="link-list three-column clearfix">
					<?php wp_list_pages($args); ?>
				</ul>

				<!-- AddThis Social Buttons -->
				<?php get_template_part('content', 'add-this'); ?>
				
			</div>
		</div>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>