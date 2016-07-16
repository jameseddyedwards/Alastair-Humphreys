<?php
/*
YARPP Template: Thumbnails
Description: Responsive thumbnails
Author: James 'Eddy' Edwards
*/
?>

<?php if (have_posts()) { ?>
	<div class="row">
		<?php while (have_posts()) : the_post(); ?>
			<div class="span4">
				<a class="post-thumb" href="<?php the_permalink(); ?>">
					<?php echo ah_get_custom_thumb(); ?>
					<span class="title"><?php the_title(); ?></span>
				</a>
				<span class="excerpt"><?php echo strip_tags(get_the_excerpt()) ?>...</span>
			</div>
		<?php endwhile; ?>
	</div>
<?php } ?>
