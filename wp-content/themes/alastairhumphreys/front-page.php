<?php get_header(); ?>

<?php get_template_part('block', 'carousel'); ?>

<div class="container white">
	<?php if (have_posts()) { ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="row">
				<div class="span4">
					<?php echo ah_get_custom_thumb(); ?>
				</div>
				<div class="span8 content">
					<?php the_content(); ?>
				</div>
			</div>
			<hr />
		<?php endwhile; ?>
	<?php } ?>

	<?php get_template_part('content', 'best-bits'); ?>
</div>

<?php get_footer(); ?>