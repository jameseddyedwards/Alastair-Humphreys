<?php
/**
 * The template for displaying the comment form on any post
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

global $current_user;
get_currentuserinfo();

?>

<?php if ('open' == $post->comment_status) : ?>
	<?php $req = get_option('require_name_email'); ?>

	<div id="respond" class="row comment-form">
		<div class="span2">&nbsp;</div>
		<div class="span3">
			<h3><?php comment_form_title('Post a Comment', 'Post a reply to %s'); ?></h3>
			<p>
				<strong>HTML tags you can use:</strong>
				<code><?php echo allowed_tags(); ?></code>
			</p>
			<?php if (function_exists("show_subscription_checkbox")) { show_subscription_checkbox(); } ?>
		</div>
		<div class="span6">

			<?php if (get_option('comment_registration') && !$user_ID) : ?>
				<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=">logged in</a> to post a comment.</p>
			<?php else : ?>
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
					<fieldset>
						<?php if ($user_ID) : ?>
							<p class="logged-in">Logged in and commenting as <a href="<?php echo admin_url('profile.php'); ?>"><?php echo $current_user->display_name ?></a>. Not you? <a title="Log out of this account" href="<?php echo wp_logout_url(get_permalink()); ?>">Log out</a>.</p>
						<?php else : ?>
							<label for="author">Name <?php if ($req) echo "<span class='required'>*</span>"; ?></label>
							<input id="author" name="author" type="text" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />

							<label for="email">Email (will not be published) <?php if ($req) echo "<span class='required'>*</span>"; ?></label>
							<input id="email" name="email" type="text" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />

							<label for="url">Website</label>
							<input type="text" name="url" id="url" value="" size="22" tabindex="3" />
						<?php endif; ?>

						<label for="comment">Your Comment <?php if ($req) echo "<span class='required'>*</span>"; ?></label>
						<textarea id="comment" name="comment" tabindex="4"></textarea>
						<?php comment_id_fields(); ?>

						<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" class="button" />
						<?php cancel_comment_reply_link("Cancel reply"); ?>
						<?php do_action('comment_form', $post->ID); ?>
					</fieldset>
				</form>
			<?php endif; ?>

		</div>
		<div class="span1">&nbsp;</div>
	</div>

<?php endif; ?>

