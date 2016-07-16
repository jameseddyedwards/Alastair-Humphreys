<?php
/**
 * The template for displaying search forms in Alastair Humphreys
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */
?>

<form method="get" class="search cf" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="assistive-text"><?php _e( 'Search through over 1500 posts', 'alastairhumphreys' ); ?></label>
	<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search through over 1500 posts', 'alastairhumphreys' ); ?>" />
	<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'alastairhumphreys' ); ?>" />
</form>
