<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */

/********************* META BOX DEFINITIONS ***********************/

/**
 * Prefix of meta keys (optional)
 * Use underscore (_) at the beginning to make keys hidden
 * Alt.: You also can make prefix empty to disable it
 */
// Better has an underscore as last sign
$prefix = '';

global $meta_boxes;

$meta_boxes = array();

// Post Images
$meta_boxes[] = array(
	'id'    => 'post-images',
	'title' => 'Post Images',
	'pages' => array('post', 'film', 'slider', 'category'),
	'priority' => 'high',
	'fields' => array(
		// THICKBOX IMAGE UPLOAD (WP 3.3+)
		array(
			'name' => 'Main Image',
			'desc' => 'Image shown at the top of every post (1600x790)',
			'id'   => "{$prefix}mainimage",
			'type' => 'thickbox_image',
		),
		// RADIO BUTTONS
		array(
			'name' => 'Image Map',
			'desc' => 'What image size do you want this post to show in the block of images?',
			'id'   => "{$prefix}imagemap",
			'type' => 'radio',
			// Array of 'value' => 'Label' pairs for radio options.
			// Note: the 'key' is stored in meta field, not the 'value'
			'options'	=> array(
				's'			=> 'Small (310x280)',
				'm'			=> 'Medium (480x280)',
				'l'			=> 'Large (790x570)',
			),
			'std'  => 's',
		),
		// THICKBOX IMAGE UPLOAD (WP 3.3+)
		array(
			'name' => 'Image Map Image',
			'desc' => 'The image that will be shown in the block of images for a category.',
			'id'   => "{$prefix}imagemapimage",
			'type' => 'thickbox_image',
		)
	)
);

/********************* META BOX REGISTERING ***********************/

/**
 * Register meta boxes
 *
 * @return void
 */
function YOUR_PREFIX_register_meta_boxes() {
	global $meta_boxes;

	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if (class_exists('RW_Meta_Box')) {
		foreach ($meta_boxes as $meta_box) {
			new RW_Meta_Box($meta_box);
		}
	}
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action('admin_init', 'YOUR_PREFIX_register_meta_boxes');