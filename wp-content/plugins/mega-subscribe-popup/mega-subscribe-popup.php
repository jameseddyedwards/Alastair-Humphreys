<?php
/*
Plugin Name: Multi Events Subscription Pop
Plugin URI: http://codecanyon.net/item/allinone-subscribe-popup/2764578?ref=halfdata
Description: This plugin adds multiple events subscribe popup to your website.
Author: Halfdata, Inc.
Author URI: http://codecanyon.net/user/halfdata?ref=halfdata
Version: 2.22
*/
define('MEGASUBSCRIBEPOPUP_VERSION', 2.22);
define('MEGASUBSCRIBEPOPUP_COOKIE', 'ilovelencha');
define('MEGASUBSCRIBEPOPUP_RECORDS_PER_PAGE', 40);
define('MEGASUBSCRIBEPOPUP_AWEBER_APPID', '15504cd4');

register_activation_hook(__FILE__, array("megasubscribepopup_class", "install"));

class megasubscribepopup_class {
	var $options;
	var $modes = array("none", "all", "homepost", "post");
	var $font_schemes = array("light" => "Light", "dark" => "Dark");

	function __construct() {
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('megasubscribepopup', false, dirname(plugin_basename(__FILE__)).'/languages/');
		}
		$this->options = array (
			"version" => MEGASUBSCRIBEPOPUP_VERSION,
			"message" => __('Dear visitor! This is demonstration of <a href="http://www.icprojects.net/mega-subscribe-popup.html">Multi Events Subscription Pop</a> plugin.', 'megasubscribepopup'),
			"width" => 450,
			"height" => 140,
			"popup_bg_color" => "#FEB",
			"popup_bg_url" => plugins_url('/images/default2_bg.jpg', __FILE__),
			"overlay_bg_color" => "#AAAAAA",
			"overlay_opacity" => 0.80,
			"font_scheme" => "dark",
			"disable_mobile" => "off",
			"disable_name" => "off",
			"css" => "",
			"load_mode" => "none",
			"load_start_delay" => 0,
			"load_delay" => 30,
			"load_once_per_visit" => "off",
			"load_disable_close" => "off",
			"exit_mode" => "none",
			"exit_delay" => 30,
			"exit_excluded_links" => "",
			"copy_mode" => "none",
			"copy_block" => "off",
			"context_mode" => "none",
			"idle_mode" => "none",
			"idle_delay" => 30,
			"scroll_mode" => "none",
			"scroll_offset" => 600,
			"scroll_once_per_visit" => "off",
			"csv_separator" => ";",
			"mailchimp_enable" => "off",
			"mailchimp_api_key" => "",
			"mailchimp_list_id" => "",
			"mailchimp_double" => "off",
			"mailchimp_welcome" => "off",
			"icontact_enable" => "off",
			"icontact_appid" => "",
			"icontact_apiusername" => "",
			"icontact_apipassword" => "",
			"icontact_listid" => "",
			'campaignmonitor_enable' => "off",
			'campaignmonitor_api_key' => '',
			'campaignmonitor_list_id' => '',
			'getresponse_enable' => "off",
			'getresponse_api_key' => '',
			'getresponse_campaign_id' => '',
			'aweber_enable' => "off",
			'aweber_consumer_key' => "",
			'aweber_consumer_secret' => "",
			'aweber_access_key' => "",
			'aweber_access_secret' => "",
			'aweber_listid' => "",
			'mymail_enable' => "off",
			'mymail_listid' => "",
			'mymail_double' => "off"
		);

		if (defined('WP_ALLOW_MULTISITE')) $this->install();
		$this->get_options();
		
		if (is_admin()) {
			if ($this->check_options() !== true) add_action('admin_notices', array(&$this, 'admin_warning'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('init', array(&$this, 'admin_request_handler'));
			add_action('admin_menu', array(&$this, 'add_meta'));
			add_action('save_post', array(&$this, 'save_meta'));
			add_action('wp_ajax_megasubscribepopup_submit', array(&$this, "megasubscribepopup_submit"));
			add_action('wp_ajax_nopriv_megasubscribepopup_submit', array(&$this, "megasubscribepopup_submit"));
			add_action('wp_ajax_megasubscribepopup_aweber_connect', array(&$this, "aweber_connect"));
			add_action('wp_ajax_megasubscribepopup_aweber_disconnect', array(&$this, "aweber_disconnect"));
		} else {
			if ($this->check_options() === true) {
				add_action('wp', array(&$this, 'front_init'));
			}
		}
	}

	function install () {
		global $wpdb;
		$table_name = $wpdb->prefix."sp_users";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
				id int(11) NOT NULL auto_increment,
				name varchar(255) collate utf8_unicode_ci NOT NULL,
				email varchar(255) collate utf8_unicode_ci NOT NULL,
				registered int(11) NOT NULL,
				deleted int(11) NOT NULL default '0',
				UNIQUE KEY  id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	function get_options() {
		$exists = get_option('megasubscribepopup_version');
		if ($exists) {
			foreach ($this->options as $key => $value) {
				$this->options[$key] = get_option('megasubscribepopup_'.$key);
			}
		}
	}

	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('megasubscribepopup_'.$key, $value);
			}
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['megasubscribepopup_'.$key])) {
				$this->options[$key] = stripslashes($_POST['megasubscribepopup_'.$key]);
			}
		}
	}

	function check_options() {
		$errors = array();
		if (!is_numeric($this->options['width']) || intval($this->options['width']) < 400) $errors[] = __('Width of popup box must be at least 400px', 'megasubscribepopup');
		if (!is_numeric($this->options['height']) || intval($this->options['height']) < 100) $errors[] = __('Height of popup box must be at least 100px', 'megasubscribepopup');
		if ($this->get_rgb($this->options['popup_bg_color']) === false) $errors[] = __('Popup box color must be valid value', 'megasubscribepopup');
		if ($this->get_rgb($this->options['overlay_bg_color']) === false) $errors[] = __('Overlay color must be valid value', 'megasubscribepopup');
		if (!is_numeric($this->options['overlay_opacity']) || floatval($this->options['overlay_opacity']) < 0 || floatval($this->options['overlay_opacity']) > 1) $errors[] = __('Overlay opacity must be between 0 and 1', 'megasubscribepopup');
		if (!empty($this->options['popup_bg_url'])) {
			if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->options['popup_bg_url'])) $errors[] = __('Popup box background URL must be valid URL', 'megasubscribepopup');
		}
		if (!empty($this->options['url'])) {
			if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->options['url'])) $errors[] = __('Website URL must be valid URL', 'megasubscribepopup');
		}
		if (!is_numeric($this->options['load_start_delay']) || intval($this->options['load_start_delay']) < 0) $errors[] = __('Start delay for open event must be valid value', 'megasubscribepopup');
		if (!is_numeric($this->options['load_delay']) || intval($this->options['load_delay']) < 0) $errors[] = __('Autoclose delay for OnPageLoad event must be valid value', 'megasubscribepopup');
		if (!is_numeric($this->options['exit_delay']) || intval($this->options['exit_delay']) < 0) $errors[] = __('Autoclose delay for OnClickExternalLink event must be valid value', 'megasubscribepopup');
		if (!is_numeric($this->options['idle_delay']) || intval($this->options['idle_delay']) < 0) $errors[] = __('Idle delay for OnIdle event must be valid value', 'megasubscribepopup');
		if (!is_numeric($this->options['scroll_offset']) || intval($this->options['scroll_offset']) < 200) $errors[] = __('Top offset for OnScrollDown event must be higher then 200px', 'megasubscribepopup');
		if ($this->options['mailchimp_enable'] == 'on') {
			if (empty($this->options['mailchimp_api_key']) || strpos($this->options['mailchimp_api_key'], '-') === false) $errors[] = __('Invalid MailChimp API Key', 'megasubscribepopup');
			if (empty($this->options['mailchimp_list_id'])) $errors[] = __('Invalid MailChimp List ID', 'megasubscribepopup');
		}
		if ($this->options['icontact_enable'] == 'on') {
			if (empty($this->options['icontact_appid'])) $errors[] = __('Invalid iContact AppID', 'megasubscribepopup');
			if (empty($this->options['icontact_apiusername'])) $errors[] = __('Invalid iContact API Username', 'megasubscribepopup');
			if (empty($this->options['icontact_apipassword'])) $errors[] = __('Invalid iContact API Password', 'megasubscribepopup');
			if (empty($this->options['icontact_listid'])) $errors[] = __('Invalid iContact List ID', 'megasubscribepopup');
		}
		if ($this->options['campaignmonitor_enable'] == 'on') {
			if (empty($this->options['campaignmonitor_api_key'])) $errors[] = __('Invalid Campaign Monitor API Key', 'megasubscribepopup');
			if (empty($this->options['campaignmonitor_list_id'])) $errors[] = __('Invalid Campaign Monitor List ID', 'megasubscribepopup');
		}
		if ($this->options['getresponse_enable'] == 'on') {
			if (empty($this->options['getresponse_api_key'])) $errors[] = __('Invalid GetResponse API Key', 'megasubscribepopup');
			if (empty($this->options['getresponse_campaign_id'])) $errors[] = __('Invalid GetResponse Campaign ID', 'megasubscribepopup');
		}
		if ($this->options['aweber_enable'] == 'on') {
			if (empty($this->options['aweber_access_secret'])) $errors[] = __('Invalid AWeber Connection', 'megasubscribepopup');
			else if (empty($this->options['aweber_listid'])) $errors[] = __('Invalid AWeber List ID', 'megasubscribepopup');
		}
		if ($this->options['mymail_enable'] == 'on') {
			if (empty($this->options['mymail_listid'])) $errors[] = __('Invalid MyMail List ID', 'megasubscribepopup');
		}
		
		if (empty($errors)) return true;
		return $errors;
	}

	function get_meta($post_id) {
		$meta = array();
		$meta["load_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_load_active', true));
		$meta["exit_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_exit_active', true));
		$meta["copy_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_copy_active', true));
		$meta["scroll_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_scroll_active', true));
		$meta["idle_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_idle_active', true));
		$meta["context_active"] = htmlspecialchars_decode(get_post_meta($post_id, 'megasubscribepopup_context_active', true));
		return $meta;
	}

	function add_meta() {
		add_meta_box("megasubscribepopup", '<img class="megasubscribepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Multi Events Subscription Pop" title="Multi Events Subscription Pop"> Multi Events Subscription Pop', array(&$this, 'show_meta'), "post", "normal", "high");
		add_meta_box("megasubscribepopup", '<img class="megasubscribepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Multi Events Subscription Pop" title="Multi Events Subscription Pop"> Multi Events Subscription Pop', array(&$this, 'show_meta'), "page", "normal", "high");
		$post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and'); 
		foreach ($post_types as $post_type ) {
			add_meta_box("megasubscribepopup", '<img class="megasubscribepopup_icon" src="'.plugins_url('/images/popup.png', __FILE__).'" alt="Multi Events Subscription Pop" title="Multi Events Subscription Pop"> Multi Events Subscription Pop', array(&$this, 'show_meta'), $post_type, "normal", "high");
		}		
	}
	
	function show_meta() {
		global $post;
		$meta = $this->get_meta($post->ID);
		//wp_nonce_field(basename(__FILE__), 'megasubscribepopup-nonce');
		print ('
			<table class="megasubscribepopup_useroptions">
			<tr>
				<th style="width: 140px;">'.__('Subscribe Popup Options', 'megasubscribepopup').':</th>
				<td><input type="checkbox" id="megasubscribepopup_load_active" name="megasubscribepopup_load_active" '.($meta["load_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnPageLoad popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnPageLoad subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			<tr>
				<th style="width: 140px;"></th>
				<td><input type="checkbox" id="megasubscribepopup_exit_active" name="megasubscribepopup_exit_active" '.($meta["exit_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnClickExternalLink popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnClickExternalLink subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			<tr>
				<th style="width: 140px;"></th>
				<td><input type="checkbox" id="megasubscribepopup_copy_active" name="megasubscribepopup_copy_active" '.($meta["copy_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnCopyContent popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnCopyContent subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			<tr>
				<th style="width: 140px;"></th>
				<td><input type="checkbox" id="megasubscribepopup_scroll_active" name="megasubscribepopup_scroll_active" '.($meta["scroll_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnScrollDown popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnScrollDown subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			<tr>
				<th style="width: 140px;"></th>
				<td><input type="checkbox" id="megasubscribepopup_idle_active" name="megasubscribepopup_idle_active" '.($meta["idle_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnIdle popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnIdle subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			<tr>
				<th style="width: 140px;"></th>
				<td><input type="checkbox" id="megasubscribepopup_context_active" name="megasubscribepopup_context_active" '.($meta["context_active"] == 'on' ? ' checked="checked"' : '').'> '.__('Enable OnContextMenu popup', 'megasubscribepopup').'<br /><em>'.__('Please tick checkbox if you would like to activate OnContextMenu subscribe popup for this post.', 'megasubscribepopup').'</em></td>
			</tr>
			</table>');
	}

	function save_meta($post_id) {
		if (isset($_POST['post_type'])) $post_type = $_POST['post_type'];
		else $_POST['post_type'] = null;
		$post_type_object = get_post_type_object($_POST['post_type']);

		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		|| (!isset($_POST['post_ID']) || $post_id != $_POST['post_ID'])
		//|| (!check_admin_referer(basename(__FILE__), 'megasubscribepopup-nonce'))
		|| (!current_user_can($post_type_object->cap->edit_post, $post_id))) {
			return $post_id;
		}
		$value_new = (isset($_POST["megasubscribepopup_load_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_load_active");
		else update_post_meta($post_id, "megasubscribepopup_load_active", htmlspecialchars($value_new));
		$value_new = (isset($_POST["megasubscribepopup_exit_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_exit_active");
		else update_post_meta($post_id, "megasubscribepopup_exit_active", htmlspecialchars($value_new));
		$value_new = (isset($_POST["megasubscribepopup_copy_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_copy_active");
		else update_post_meta($post_id, "megasubscribepopup_copy_active", htmlspecialchars($value_new));
		$value_new = (isset($_POST["megasubscribepopup_context_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_context_active");
		else update_post_meta($post_id, "megasubscribepopup_context_active", htmlspecialchars($value_new));
		$value_new = (isset($_POST["megasubscribepopup_idle_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_idle_active");
		else update_post_meta($post_id, "megasubscribepopup_idle_active", htmlspecialchars($value_new));
		$value_new = (isset($_POST["megasubscribepopup_scroll_active"]) ? "on" : "");
		if (empty($value_new)) delete_post_meta($post_id, "megasubscribepopup_scroll_active");
		else update_post_meta($post_id, "megasubscribepopup_scroll_active", htmlspecialchars($value_new));
		return $post_id;
	}

	function admin_menu() {
		add_menu_page(
			__('Multi Events Subscription Pop', 'megasubscribepopup')
			, __('Multi Events Subscription Pop', 'megasubscribepopup')
			, 'manage_options'
			, 'megasubscribepopup'
			, array(&$this, 'admin_settings')
		);
		add_submenu_page(
			'megasubscribepopup'
			, __('Settings', 'megasubscribepopup')
			, __('Settings', 'megasubscribepopup')
			, 'manage_options'
			, 'megasubscribepopup'
			, array(&$this, 'admin_settings')
		);
		add_submenu_page(
			'megasubscribepopup'
			, __('Subscribers', 'megasubscribepopup')
			, __('Subscribers', 'megasubscribepopup')
			, 'manage_options'
			, 'megasubscribepopup-users'
			, array(&$this, 'admin_users')
		);
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script("jquery");
		if (isset($_GET['page']) && $_GET['page'] == 'megasubscribepopup') {
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script("thickbox");
			wp_enqueue_style("thickbox");
		}
		wp_enqueue_style('megasubscribepopup_admin', plugins_url('/css/admin.css', __FILE__), array(), MEGASUBSCRIBEPOPUP_VERSION);
	}

	function admin_settings() {
		global $wpdb;
		$message = "";
		$errors = $this->check_options();
		if (is_array($errors)) $message = '<div class="error"><p>'.__('The following error(s) exists:', 'megasubscribepopup').'<br />- '.implode('<br />- ', $errors).'</p></div>';
		echo '
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div><h2>'.__('Multi Events Subscription Pop - Settings', 'megasubscribepopup').'</h2><br />
			'.$message.'
			<form enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
			<div class="postbox-container" style="width: 100%;">
				<div class="metabox-holder">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Popup Box Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Window content', 'megasubscribepopup').':</th>
										<td>';
		if (function_exists('wp_editor')) {
			wp_editor($this->options['message'], "megasubscribepopup_message", array('wpautop' => false, 'tabindex' => 1));
		} else {
			echo '
											<textarea class="widefat" id="megasubscribepopup_message" name="megasubscribepopup_message" style="height: 120px;">'.htmlspecialchars($this->options['message'], ENT_QUOTES).'</textarea><br />';
		}
		echo '									
											<em>'.__('Please enter content of the window. HTML allowed. Subscription form is inserted below this content.', 'megasubscribepopup').'</em>
										</td>
									</tr> 
									<tr>
										<th>'.__('Window size (px)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_width" name="megasubscribepopup_width" value="'.htmlspecialchars($this->options['width'], ENT_QUOTES).'" style="width: 80px; text-align: right;"> x
											<input type="text" id="megasubscribepopup_height" name="megasubscribepopup_height" value="'.htmlspecialchars($this->options['height'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set window size (width x height). Window height is calculated automatically. Here you set minimum height value.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Window color', 'megasubscribepopup').':</th>
										<td>
											<input type="text" class="megasubscribepopup_color" id="megasubscribepopup_popup_bg_color" name="megasubscribepopup_popup_bg_color" value="'.htmlspecialchars($this->options['popup_bg_color'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set popup box background color.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Background image URL', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_popup_bg_url" name="megasubscribepopup_popup_bg_url" value="'.htmlspecialchars($this->options['popup_bg_url'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your URL of background image. Leave this field blank if you do not need background image.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Font color scheme', 'megasubscribepopup').':</th>
										<td>
											<select id="megasubscribepopup_font_scheme" name="megasubscribepopup_font_scheme" style="min-width: 80px;">';
				foreach ($this->font_schemes as $key => $value) {
					echo '
												<option value="'.$key.'"'.($this->options['font_scheme'] == $key ? ' selected="selected"' : '').'>'.$value.'</option>';
				}
				echo '
											</select>
											<br /><em>'.__('Please select font color scheme.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Overlay color', 'megasubscribepopup').':</th>
										<td>
											<input type="text" class="megasubscribepopup_color" id="megasubscribepopup_overlay_bg_color" name="megasubscribepopup_overlay_bg_color" value="'.htmlspecialchars($this->options['overlay_bg_color'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set overlay color.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Overlay opacity', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_overlay_opacity" name="megasubscribepopup_overlay_opacity" value="'.htmlspecialchars($this->options['overlay_opacity'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set overlay opacity. Value must be between 0 and 1.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Small screen devices', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_disable_mobile" name="megasubscribepopup_disable_mobile" '.($this->options['disable_mobile'] == "on" ? 'checked="checked"' : '').'"> '.__('Disable popup for small screen devices', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to disable popup window for small screen devices.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('CSV column separator', 'megasubscribepopup').':</th>
										<td>
											<select id="megasubscribepopup-csv_separator" name="megasubscribepopup-csv_separator">
												<option value=";"'.($this->options['csv_separator'] == ';' ? ' selected="selected"' : '').'>'.__('Semicolon - ";"', 'megasubscribepopup').'</option>
												<option value=","'.($this->options['csv_separator'] == ',' ? ' selected="selected"' : '').'>'.__('Comma - ","', 'megasubscribepopup').'</option>
												<option value="tab"'.($this->options['csv_separator'] == 'tab' ? ' selected="selected"' : '').'>'.__('Tab', 'megasubscribepopup').'</option>
											</select>
											<br /><em>'.__('Please select CSV column separator.', 'megasubscribepopup').'</em></td>
									</tr>
									<tr>
										<th>'.__('Disable name field', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_disable_name" name="megasubscribepopup_disable_name" '.($this->options['disable_name'] == "on" ? 'checked="checked"' : '').'"> '.__('Disable "Name" field in opt-in form', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to disable "Name" field in opt-in form.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Stylesheet', 'megasubscribepopup').':</th>
										<td><textarea id="megasubscribepopup_css" name="megasubscribepopup_css" class="widefat" style="height: 120px;">'.htmlspecialchars($this->options['css'], ENT_QUOTES).'</textarea><br /><em>'.__('Customize widgets stylesheet.', 'megasubscribepopup').'</em></td>
									</tr>
								</table>
								<div class="alignright">
								<input type="hidden" name="action" value="megasubscribepopup-update-options" />
								<input type="hidden" name="megasubscribepopup_version" value="'.MEGASUBSCRIBEPOPUP_VERSION.'" />
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('MailChimp Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Enable MailChimp', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_mailchimp_enable" name="megasubscribepopup_mailchimp_enable" '.($this->options['mailchimp_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to MailChimp', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to MailChimp. <strong>CURL required!</strong>', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('MailChimp API Key', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_mailchimp_api_key" name="megasubscribepopup_mailchimp_api_key" value="'.htmlspecialchars($this->options['mailchimp_api_key'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your MailChimp API Key. You can get it <a href="https://admin.mailchimp.com/account/api-key-popup" target="_blank">here</a>.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_mailchimp_list_id" name="megasubscribepopup_mailchimp_list_id" value="'.htmlspecialchars($this->options['mailchimp_list_id'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get it <a href="https://admin.mailchimp.com/lists/" target="_blank">here</a> (click <strong>Settings</strong>).', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Double opt-in', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_mailchimp_double" name="megasubscribepopup_mailchimp_double" '.($this->options['mailchimp_double'] == "on" ? 'checked="checked"' : '').'"> '.__('Ask users to confirm their subscription', 'megasubscribepopup').'
											<br /><em>'.__('Control whether a double opt-in confirmation message is sent.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Send Welcome', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_mailchimp_welcome" name="megasubscribepopup_mailchimp_welcome" '.($this->options['mailchimp_welcome'] == "on" ? 'checked="checked"' : '').'"> '.__('Send Lists Welcome message', 'megasubscribepopup').'
											<br /><em>'.__('If your <strong>Double opt-in</strong> is disabled and this is enabled, MailChimp will send your lists Welcome Email if this subscribe succeeds. If <strong>Double opt-in</strong> is enabled, this has no effect.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('iContact Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Enable iContact', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_icontact_enable" name="megasubscribepopup_icontact_enable" '.($this->options['icontact_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to iContact', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to iContact. <strong>CURL required!</strong>', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('AppID', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_icontact_appid" name="megasubscribepopup_icontact_appid" value="'.htmlspecialchars($this->options['icontact_appid'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Obtained when you <a href="http://developer.icontact.com/documentation/register-your-app/" target="_blank">Register the API application</a>. This identifier is used to uniquely identify your application.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Username', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_icontact_apiusername" name="megasubscribepopup_icontact_apiusername" value="'.htmlspecialchars($this->options['icontact_apiusername'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('The iContact username for logging into your iContact account.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Password', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_icontact_apipassword" name="megasubscribepopup_icontact_apipassword" value="'.htmlspecialchars($this->options['icontact_apipassword'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('The API application password set when the application was registered. This API password is used as input when your application authenticates to the API. This password is not the same as the password you use to log in to iContact.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_icontact_listid" name="megasubscribepopup_icontact_listid" value="'.esc_attr($this->options['icontact_listid']).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get List ID from', 'megasubscribepopup').' <a href="'.admin_url('admin.php').'?action=megasubscribepopup-icontact-lists&appid='.esc_attr($this->options['icontact_appid']).'&user='.esc_attr($this->options['icontact_apiusername']).'&pass='.esc_attr($this->options['icontact_apipassword']).'" class="thickbox" id="icontact_lists" title="'.__('Available Lists', 'megasubscribepopup').'">'.__('this table', 'megasubscribepopup').'</a>.</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('GetResponse Details', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Enable GetResponse', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_getresponse_enable" name="megasubscribepopup_getresponse_enable" '.($this->options['getresponse_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to GetResponse', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to GetResponse. <strong>CURL required!</strong>', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_getresponse_api_key" name="megasubscribepopup_getresponse_api_key" value="'.esc_attr($this->options['getresponse_api_key']).'" class="widefat" onchange="getresponse_handler();">
											<br /><em>'.__('Enter your GetResponse API Key. You can get your API Key <a href="https://app.getresponse.com/my_api_key.html" target="_blank">here</a>.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Campaign ID', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_getresponse_campaign_id" name="megasubscribepopup_getresponse_campaign_id" value="'.esc_attr($this->options['getresponse_campaign_id']).'" class="widefat">
											<br /><em>'.__('Enter your Campaign ID. You can get Campaign ID from', 'megasubscribepopup').' <a href="'.admin_url('admin.php').'?action=megasubscribepopup-getresponse-campaigns&key='.esc_attr($this->options['getresponse_api_key']).'" class="thickbox" id="getresponse_campaigns" title="'.__('Available Campaigns', 'megasubscribepopup').'">'.__('this table', 'megasubscribepopup').'</a>.</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Campaign Monitor Details', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Enable Campaign Monitor', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_campaignmonitor_enable" name="megasubscribepopup_campaignmonitor_enable" '.($this->options['campaignmonitor_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Campaign Monitor', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to Campaign Monitor. <strong>CURL required!</strong>', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_campaignmonitor_api_key" name="megasubscribepopup_campaignmonitor_api_key" value="'.esc_attr($this->options['campaignmonitor_api_key']).'" class="widefat">
											<br /><em>'.__('Enter your Campaign Monitor API Key. You can get your API Key from the Account Settings page when logged into your Campaign Monitor account.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'megasubscribepopup').':</th>
										<td>
											<input type="text" id="megasubscribepopup_campaignmonitor_list_id" name="megasubscribepopup_campaignmonitor_list_id" value="'.esc_attr($this->options['campaignmonitor_list_id']).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get List ID from the list editor page when logged into your Campaign Monitor account.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('AWeber Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr>
										<th>'.__('Enable AWeber', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_aweber_enable" name="megasubscribepopup_aweber_enable" '.($this->options['aweber_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to AWeber', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to AWeber.', 'megasubscribepopup').'</em>
										</td>
									</tr>';
		$account = null;
		if ($this->options['aweber_access_secret']) {
			if (!class_exists('AWeberAPI')) {
				require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
			}
			try {
				$aweber = new AWeberAPI($this->options['aweber_consumer_key'], $this->options['aweber_consumer_secret']);
				$account = $aweber->getAccount($this->options['aweber_access_key'], $this->options['aweber_access_secret']);
			} catch (AWeberException $e) {
				$account = null;
			}
		}
		if (!$account) {
			echo '
									<tbody id="megasubscribepopup-aweber-group">
										<tr>
											<th>'.__('Authorization code', 'megasubscribepopup').':</th>
											<td>
												<input type="text" id="megasubscribepopup_aweber_oauth_id" value="" class="widefat" placeholder="AWeber authorization code">
												<br />Get your authorization code <a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/'.MEGASUBSCRIBEPOPUP_AWEBER_APPID.'">'.__('here', 'megasubscribepopup').'</a>.
											</td>
										</tr>
										<tr>
											<th></th>
											<td style="vertical-align: middle;">
												<input type="button" class="megasubscribepopup_button button-secondary" value="'.__('Make Connection', 'megasubscribepopup').'" onclick="return megasubscribepopup_aweber_connect();" >
												<img id="megasubscribepopup-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
											</td>
										</tr>
									</tbody>';
		} else {
			echo '
									<tbody id="megasubscribepopup-aweber-group">
										<tr>
											<th>'.__('Connected', 'megasubscribepopup').':</th>
											<td>
												<input type="button" class="megasubscribepopup_button button-secondary" value="'.__('Disconnect', 'megasubscribepopup').'" onclick="return megasubscribepopup_aweber_disconnect();" >
												<img id="megasubscribepopup-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
												<br /><em>'.__('Click the button to disconnect.', 'megasubscribepopup').'</em>
											</td>
										</tr>
										<tr>
											<th>'.__('List ID', 'megasubscribepopup').':</th>
											<td>
												<select name="megasubscribepopup_aweber_listid" style="width: 40%;">
													<option value="">'.__('--- Select List ID ---', 'megasubscribepopup').'</option>';
				$lists = $account->lists;
				foreach ($lists as $list) {
					echo '
													<option value="'.$list->id.'"'.($list->id == $this->options['aweber_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				echo '
												</select>
												<br /><em>'.__('Select your List ID.', 'megasubscribepopup').'</em>
											</td>
										</tr>
									</tbody>';
		}
		echo '
								</table>
								<div id="megasubscribepopup-aweber-message"></div>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>';
		if (function_exists('mymail_subscribe') || function_exists('mymail')) {
			echo '
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('MyMail Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">';
			if (function_exists('mymail')) {
				$lists = mymail('lists')->get();
				$create_list_url = 'edit.php?post_type=newsletter&page=mymail_lists';
			} else {
				$lists = get_terms('newsletter_lists', array('hide_empty' => false));
				$create_list_url = 'edit-tags.php?taxonomy=newsletter_lists&post_type=newsletter';
			}
			if (sizeof($lists) == 0) {
				echo '
									<tr>
										<th>'.__('Enable MyMail', 'megasubscribepopup').':</th>
										<td>'.__('Please', 'megasubscribepopup').' <a href="'.$create_list_url.'">'.__('create', 'megasubscribepopup').'</a> '.__('at least one list.', 'megasubscribepopup').'</td>
									</tr>';
			} else {
				echo '
									<tr>
										<th>'.__('Enable MyMail', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_mymail_enable" name="megasubscribepopup_mymail_enable" '.($this->options['mymail_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to MyMail', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to MyMail.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'megasubscribepopup').':</th>
										<td>
											<select name="megasubscribepopup_mymail_listid" class="ic_input_m">';
				foreach ($lists as $list) {
					if (function_exists('mymail')) $id = $list->ID;
					else $id = $list->term_id;
					echo '
												<option value="'.$id.'"'.($id == $this->options['mymail_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				echo '
											</select>
											<br /><em>'.__('Select your List ID.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Double Opt-In', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" id="megasubscribepopup_mymail_double" name="megasubscribepopup_mymail_double" '.($this->options['mymail_double'] == "on" ? 'checked="checked"' : '').'"> '.__('Enable Double Opt-In', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to enable double opt-in feature.', 'megasubscribepopup').'</em>
										</td>
									</tr>';
			}
			echo '
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>';
		}
		echo '
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnPageLoad Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnPageLoad popup appears once user open page in browser.', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_load_mode">
											<option value="all"'.($this->options['load_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['load_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['load_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['load_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Start delay (seconds)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" name="megasubscribepopup_load_start_delay" value="'.htmlspecialchars($this->options['load_start_delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Popup appears with this delay after page loaded. Set "0" for immediate start.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Autoclose delay (seconds)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" name="megasubscribepopup_load_delay" value="'.htmlspecialchars($this->options['load_delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Popup is automatically closed after this period of time. Set "0", if you do not need this functionality.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Once per visit', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" name="megasubscribepopup_load_once_per_visit" '.($this->options['load_once_per_visit'] == "on" ? 'checked="checked"' : '').'"> '.__('Show popup box once per visit', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to show popup once per visit (session).', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Disable close button', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" name="megasubscribepopup_load_disable_close" '.($this->options['load_disable_close'] == "on" ? 'checked="checked"' : '').'"> '.__('Do not display close button', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox disable close button.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnClickExternalLink Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnClickExternalLink popup appears once user click any external link on page.', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_exit_mode">
											<option value="all"'.($this->options['exit_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['exit_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['exit_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['exit_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Redirect delay (seconds)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" name="megasubscribepopup_exit_delay" value="'.htmlspecialchars($this->options['exit_delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please enter delay in seconds. Visitors have to wait this period of time or share your page to be redirected.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Excluded links', 'megasubscribepopup').':</th>
										<td><textarea class="widefat" name="megasubscribepopup_exit_excluded_links" style="height: 120px;">'.htmlspecialchars($this->options['exit_excluded_links'], ENT_QUOTES).'</textarea><br /><em>'.__('Please enter list of links that must be excluded from consideration (one item per line). You can enter part of link. For example, if you enter "wordpress.org", all links that contains this string will be excluded from consideration. Links are case insensitive.', 'megasubscribepopup').'</em></td>
									</tr> 
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnCopyContent Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnCopyContent popup appears once user copy part of your page into clipboard (using Ctrl+C or through context menu).', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_copy_mode">
											<option value="all"'.($this->options['copy_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['copy_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['copy_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['copy_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Block copying content', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" name="megasubscribepopup_copy_block" '.($this->options['copy_block'] == "on" ? 'checked="checked"' : '').'"> '.__('Do not copy content into clipboard if not shared', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you do not want to allows copying content into clipboard until user shared your page.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnScrollDown Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnScrollDown popup appears once user scroll down current page.', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_scroll_mode">
											<option value="all"'.($this->options['scroll_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['scroll_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['scroll_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['scroll_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Once per visit', 'megasubscribepopup').':</th>
										<td>
											<input type="checkbox" name="megasubscribepopup_scroll_once_per_visit" '.($this->options['scroll_once_per_visit'] == "on" ? 'checked="checked"' : '').'"> '.__('Show popup box once per visit', 'megasubscribepopup').'
											<br /><em>'.__('Please tick checkbox if you want to show popup once per visit (session).', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Scrolling offset (px)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" name="megasubscribepopup_scroll_offset" value="'.htmlspecialchars($this->options['scroll_offset'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set scrolling offset. Subscribe Popup appears only if user scroll down this number of pixels.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnIdle Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnIdle popup appears after defined period of inactivity.', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_idle_mode">
											<option value="all"'.($this->options['idle_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['idle_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['idle_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['idle_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Idle period (seconds)', 'megasubscribepopup').':</th>
										<td>
											<input type="text" name="megasubscribepopup_idle_delay" value="'.htmlspecialchars($this->options['idle_delay'], ENT_QUOTES).'" style="width: 80px; text-align: right;">
											<br /><em>'.__('Please set idle period. Subscribe Popup appears after this period of inactivity.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
						<div class="postbox megasubscribepopup_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('OnContextMenu Popup Settings', 'megasubscribepopup').'</span></h3>
							<div class="inside">
								<table class="megasubscribepopup_useroptions">
									<tr><th colspan="2">'.__('OnContextMenu popup appears once user call context menu (click right mouse button).', 'megasubscribepopup').'</th></tr>
									<tr>
										<th>'.__('Display mode', 'megasubscribepopup').':</th>
										<td>
										<select name="megasubscribepopup_context_mode">
											<option value="all"'.($this->options['context_mode'] == 'all' ? ' selected="selected"' : '').'> '.__('All website pages', 'megasubscribepopup').'</option>
											<option value="homepost"'.($this->options['context_mode'] == 'homepost' ? ' selected="selected"' : '').'> '.__('Homepage and selected posts/pages', 'megasubscribepopup').'</option>
											<option value="post"'.($this->options['context_mode'] == 'post' ? ' selected="selected"' : '').'> '.__('Selected posts/pages', 'megasubscribepopup').'</option>
											<option value="none"'.($this->options['context_mode'] == 'none' ? ' selected="selected"' : '').'> '.__('Disable popup', 'megasubscribepopup').'</option>
										</select>
										<br /><em>'.__('Select display mode for popup box. You can assign selected pages on post/page editor.', 'megasubscribepopup').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="megasubscribepopup_button button-primary" name="submit" value="'.__('Update Settings', 'megasubscribepopup').'">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</form>
			<script type="text/javascript">
				function megasubscribepopup_icontact_validate() {
					var icontact_appid = jQuery("#megasubscribepopup_icontact_appid").val();
					var icontact_apiusername = jQuery("#megasubscribepopup_icontact_apiusername").val();
					var icontact_apipassword = jQuery("#megasubscribepopup_icontact_apipassword").val();
					jQuery("#megasubscribepopup_icontact_progress").fadeIn(300);
					jQuery("#megasubscribepopup_icontact_status").fadeOut(300);
					var data = {icontact_appid: icontact_appid, icontact_apiusername: icontact_apiusername, icontact_apipassword: icontact_apipassword, action: "megasubscribepopup_icontact_validate"};
					jQuery.post("'.admin_url('admin-ajax.php').'", data, function(data) {
						jQuery("#megasubscribepopup_icontact_status").html(data);
						jQuery("#megasubscribepopup_icontact_progress").fadeOut(300);
						jQuery("#megasubscribepopup_icontact_status").fadeIn(300);
					});
				}
				function getresponse_handler() {
					jQuery("#getresponse_campaigns").attr("href", "'.admin_url('admin.php').'?action=megasubscribepopup-getresponse-campaigns&key="+jQuery("#megasubscribepopup_getresponse_api_key").val());
				}
				function icontact_handler() {
					jQuery("#icontact_lists").attr("href", "'.admin_url('admin.php').'?action=megasubscribepopup-icontact-lists&appid="+jQuery("#megasubscribepopup_icontact_appid").val()+"&user="+jQuery("#megasubscribepopup_icontact_apiusername").val()+"&pass="+jQuery("#megasubscribepopup_icontact_apipassword").val());
				}
				function megasubscribepopup_aweber_connect() {
					jQuery("#megasubscribepopup-aweber-loading").fadeIn(350);
					jQuery("#megasubscribepopup-aweber-message").slideUp(350);
					var data = {action: "megasubscribepopup_aweber_connect", megasubscribepopup_aweber_oauth_id: jQuery("#megasubscribepopup_aweber_oauth_id").val()};
					jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
						jQuery("#megasubscribepopup-aweber-loading").fadeOut(350);
						try {
							//alert(return_data);
							var data = jQuery.parseJSON(return_data);
							var status = data.status;
							if (status == "OK") {
								jQuery("#megasubscribepopup-aweber-group").slideUp(350, function() {
									jQuery("#megasubscribepopup-aweber-group").html(data.html);
									jQuery("#megasubscribepopup-aweber-group").slideDown(350);
								});
							} else if (status == "ERROR") {
								jQuery("#megasubscribepopup-aweber-message").html(data.message);
								jQuery("#megasubscribepopup-aweber-message").slideDown(350);
							} else {
								jQuery("#megasubscribepopup-aweber-message").html("Service is not available.");
								jQuery("#megasubscribepopup-aweber-message").slideDown(350);
							}
						} catch(error) {
							jQuery("#megasubscribepopup-aweber-message").html("Service is not available.");
							jQuery("#megasubscribepopup-aweber-message").slideDown(350);
						}
					});
					return false;
				}
				function megasubscribepopup_aweber_disconnect() {
					jQuery("#megasubscribepopup-aweber-loading").fadeIn(350);
					var data = {action: "megasubscribepopup_aweber_disconnect"};
						jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
						jQuery("#megasubscribepopup-aweber-loading").fadeOut(350);
						try {
							//alert(return_data);
							var data = jQuery.parseJSON(return_data);
							var status = data.status;
							if (status == "OK") {
								jQuery("#megasubscribepopup-aweber-group").slideUp(350, function() {
									jQuery("#megasubscribepopup-aweber-group").html(data.html);
									jQuery("#megasubscribepopup-aweber-group").slideDown(350);
								});
							} else if (status == "ERROR") {
								jQuery("#megasubscribepopup-aweber-message").html(data.message);
								jQuery("#megasubscribepopup-aweber-message").slideDown(350);
							} else {
								jQuery("#megasubscribepopup-aweber-message").html("Service is not available.");
								jQuery("#megasubscribepopup-aweber-message").slideDown(350);
							}
						} catch(error) {
							jQuery("#megasubscribepopup-aweber-message").html("Service is not available.");
							jQuery("#megasubscribepopup-aweber-message").slideDown(350);
						}
					});
					return false;
				}
				jQuery(document).ready(function($){
					jQuery(".megasubscribepopup_color").wpColorPicker();
				});
			</script>
		</div>';
	}

	function aweber_connect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['megasubscribepopup_aweber_oauth_id']) || empty($_POST['megasubscribepopup_aweber_oauth_id'])) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = __('Authorization Code not found.', 'megasubscribepopup');
				echo json_encode($return_object);
				exit;
			}
			$code = trim(stripslashes($_POST['megasubscribepopup_aweber_oauth_id']));
			if (!class_exists('AWeberAPI')) {
				require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
			}
			$account = null;
			try {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID($code);
			} catch (AWeberAPIException $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			} catch (AWeberOAuthDataMissing $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			} catch (AWeberException $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			}
			if (!$access_secret) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = __('Invalid Authorization Code!', 'megasubscribepopup');
				echo json_encode($return_object);
				exit;
			} else {
				try {
					$aweber = new AWeberAPI($consumer_key, $consumer_secret);
					$account = $aweber->getAccount($access_key, $access_secret);
				} catch (AWeberException $e) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = __('Can not access AWeber account!', 'megasubscribepopup');
					echo json_encode($return_object);
					exit;
				}
			}
			$this->options['aweber_consumer_key'] = $consumer_key;
			$this->options['aweber_consumer_secret'] = $consumer_secret;
			$this->options['aweber_access_key'] = $access_key;
			$this->options['aweber_access_secret'] = $access_secret;
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = '
										<tr>
											<th>'.__('Connected', 'megasubscribepopup').':</th>
											<td>
												<input type="button" class="megasubscribepopup_button button-secondary" value="'.__('Disconnect', 'megasubscribepopup').'" onclick="return megasubscribepopup_aweber_disconnect();" >
												<img id="megasubscribepopup-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
												<br /><em>'.__('Click the button to disconnect.', 'megasubscribepopup').'</em>
											</td>
										</tr>
										<tr>
											<th>'.__('List ID', 'megasubscribepopup').':</th>
											<td>
												<select name="megasubscribepopup_aweber_listid" style="width: 40%;">
													<option value="">'.__('--- Select List ID ---', 'megasubscribepopup').'</option>';
				$lists = $account->lists;
				foreach ($lists as $list) {
					$return_object['html'] .= '
													<option value="'.$list->id.'"'.($list->id == $this->options['aweber_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				$return_object['html'] .= '
												</select>
												<br /><em>'.__('Select your List ID.', 'megasubscribepopup').'</em>
											</td>
										</tr>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}
	
	function aweber_disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$this->options['aweber_consumer_key'] = '';
			$this->options['aweber_consumer_secret'] = '';
			$this->options['aweber_access_key'] = '';
			$this->options['aweber_access_secret'] = '';
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = '
					<tr>
						<th>'.__('Authorization code', 'megasubscribepopup').':</th>
						<td>
							<input type="text" id="megasubscribepopup_aweber_oauth_id" value="" class="widefat" placeholder="AWeber authorization code">
							<br />Get your authorization code <a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/'.MEGASUBSCRIBEPOPUP_AWEBER_APPID.'">'.__('here', 'megasubscribepopup').'</a>.
						</td>
					</tr>
					<tr>
						<th></th>
						<td style="vertical-align: middle;">
							<input type="button" class="megasubscribepopup_button button-secondary" value="'.__('Make Connection', 'megasubscribepopup').'" onclick="return megasubscribepopup_aweber_connect();" >
							<img id="megasubscribepopup-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
						</td>
					</tr>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}
	
	function admin_users() {
		global $wpdb;

		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."sp_users WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND (name LIKE '%".addslashes($search_query)."%' OR email LIKE '%".addslashes($search_query)."%')" : ""), ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/MEGASUBSCRIBEPOPUP_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $this->page_switcher(get_bloginfo("wpurl")."/wp-admin/admin.php?page=megasubscribepopup-users".((strlen($search_query) > 0) ? "&s=".rawurlencode($search_query) : ""), $page, $totalpages);

		$sql = "SELECT * FROM ".$wpdb->prefix."sp_users WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND (name LIKE '%".addslashes($search_query)."%' OR email LIKE '%".addslashes($search_query)."%')" : "")." ORDER BY registered DESC LIMIT ".(($page-1)*MEGASUBSCRIBEPOPUP_RECORDS_PER_PAGE).", ".MEGASUBSCRIBEPOPUP_RECORDS_PER_PAGE;
		$rows = $wpdb->get_results($sql, ARRAY_A);
		if (isset($_GET['deleted'])) $message = "<div class='updated'><p>".__('Record successfully deleted!', 'megasubscribepopup')."</p></div>";
		else $message = '';

		echo '
			<div class="wrap admin_megasubscribepopup_wrap">
				<div id="icon-users" class="icon32"><br /></div><h2>'.__('Multi Events Subscription Pop - Users', 'megasubscribepopup').'</h2><br />
				'.$message.'
				<form action="'.admin_url('admin.php').'" method="get" style="margin-bottom: 10px;">
				<input type="hidden" name="page" value="megasubscribepopup-users" />
				'.__('Search:', 'megasubscribepopup').' <input type="text" name="s" value="'.htmlspecialchars($search_query, ENT_QUOTES).'">
				<input type="submit" class="button-secondary action" value="'.__('Search', 'megasubscribepopup').'" />
				'.((strlen($search_query) > 0) ? '<input type="button" class="button-secondary action" value="'.__('Reset search results', 'megasubscribepopup').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=megasubscribepopup-users\';" />' : '').'
				</form>
				<div class="megasubscribepopup_buttons"><a class="button" href="'.admin_url('admin.php').'?action=megasubscribepopup-csv">'.__('CSV Export', 'megasubscribepopup').'</a></div>
				<div class="megasubscribepopup_pageswitcher">'.$switcher.'</div>
				<table class="megasubscribepopup_users">
				<tr>
					<th>'.__('Name', 'megasubscribepopup').'</th>
					<th>'.__('E-mail', 'megasubscribepopup').'</th>
					<th style="width: 120px;">'.__('Registered', 'megasubscribepopup').'</th>
					<th style="width: 25px;"></th>
				</tr>';
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				echo '
				<tr>
					<td>'.(empty($row['name']) ? '-' : esc_attr($row['name'])).'</td>
					<td>'.esc_attr($row['email']).'</td>
					<td>'.date("Y-m-d H:i", $row['registered']).'</td>
					<td style="text-align: center;">
						<a href="'.admin_url('admin.php').'?action=megasubscribepopup-delete&id='.$row['id'].'" title="'.__('Delete record', 'megasubscribepopup').'" onclick="return megasubscribepopup_submitOperation();"><img src="'.plugins_url('/images/delete.png', __FILE__).'" alt="'.__('Delete record', 'megasubscribepopup').'" border="0"></a>
					</td>
				</tr>';
			}
		} else {
			echo '
				<tr><td colspan="4" style="padding: 20px; text-align: center;">'.((strlen($search_query) > 0) ? __('No results found for', 'megasubscribepopup').' "<strong>'.htmlspecialchars($search_query, ENT_QUOTES).'</strong>"' : __('List is empty.', 'megasubscribepopup')).'</td></tr>';
		}
		echo '
				</table>
				<div class="megasubscribepopup_buttons">
					<a class="button" href="'.admin_url('admin.php').'?action=megasubscribepopup-deleteall" onclick="return megasubscribepopup_submitOperation();">'.__('Delete All', 'megasubscribepopup').'</a>
					<a class="button" href="'.admin_url('admin.php').'?action=megasubscribepopup-csv">'.__('CSV Export', 'megasubscribepopup').'</a>
				</div>
				<div class="megasubscribepopup_pageswitcher">'.$switcher.'</div>
				<div class="megasubscribepopup_legend">
				<strong>'.__('Legend:', 'megasubscribepopup').'</strong>
					<p><img src="'.plugins_url('/images/delete.png', __FILE__).'" alt="'.__('Delete record', 'megasubscribepopup').'" border="0"> '.__('Delete record', 'megasubscribepopup').'</p>
				</div>
			</div>
			<script type="text/javascript">
				function megasubscribepopup_submitOperation() {
					var answer = confirm("'.__('Do you really want to continue?', 'megasubscribepopup').'");
					if (answer) return true;
					else return false;
				}
			</script>';
	}

	function admin_request_handler() {
		global $wpdb;
		if (!empty($_POST['action'])) {
			switch($_POST['action']) {
				case 'megasubscribepopup-update-options':
					$this->populate_options();
					if (isset($_POST["megasubscribepopup_disable_name"])) $this->options['disable_name'] = "on";
					else $this->options['disable_name'] = "off";
					if (isset($_POST["megasubscribepopup_scroll_once_per_visit"])) $this->options['scroll_once_per_visit'] = "on";
					else $this->options['scroll_once_per_visit'] = "off";
					if (isset($_POST["megasubscribepopup_disable_mobile"])) $this->options['disable_mobile'] = "on";
					else $this->options['disable_mobile'] = "off";
					if (isset($_POST["megasubscribepopup_load_disable_close"])) $this->options['load_disable_close'] = "on";
					else $this->options['load_disable_close'] = "off";
					if (isset($_POST["megasubscribepopup_load_once_per_visit"])) $this->options['load_once_per_visit'] = "on";
					else $this->options['load_once_per_visit'] = "off";
					if (isset($_POST["megasubscribepopup_copy_block"])) $this->options['copy_block'] = "on";
					else $this->options['copy_block'] = "off";
					if (isset($_POST["megasubscribepopup_mailchimp_double"])) $this->options['mailchimp_double'] = "on";
					else $this->options['mailchimp_double'] = "off";
					if (isset($_POST["megasubscribepopup_mailchimp_welcome"])) $this->options['mailchimp_welcome'] = "on";
					else $this->options['mailchimp_welcome'] = "off";
					if (isset($_POST["megasubscribepopup_mailchimp_enable"])) $this->options['mailchimp_enable'] = "on";
					else $this->options['mailchimp_enable'] = "off";
					if (isset($_POST["megasubscribepopup_icontact_enable"])) $this->options['icontact_enable'] = "on";
					else $this->options['icontact_enable'] = "off";
					if (isset($_POST["megasubscribepopup_campaignmonitor_enable"])) $this->options['campaignmonitor_enable'] = "on";
					else $this->options['campaignmonitor_enable'] = "off";
					if (isset($_POST["megasubscribepopup_getresponse_enable"])) $this->options['getresponse_enable'] = "on";
					else $this->options['getresponse_enable'] = "off";
					if (isset($_POST["megasubscribepopup_aweber_enable"])) $this->options['aweber_enable'] = "on";
					else $this->options['aweber_enable'] = "off";
					if (isset($_POST["megasubscribepopup_mymail_enable"])) $this->options['mymail_enable'] = "on";
					else $this->options['mymail_enable'] = "off";
					if (isset($_POST["megasubscribepopup_mymail_double"])) $this->options['mymail_double'] = "on";
					else $this->options['mymail_double'] = "off";

					$this->update_options();
					$errors = $this->check_options();
					if (!is_array($errors)) header('Location: '.admin_url('admin.php').'?page=megasubscribepopup&updated=true');
					else header('Location: '.admin_url('admin.php').'?page=megasubscribepopup');
					die();
					break;
				default:
					break;
			}
		}
		if (isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'megasubscribepopup-delete':
					$id = intval($_GET["id"]);
					$user_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sp_users WHERE id = '".$id."' AND deleted = '0'", ARRAY_A);
					if (intval($user_details["id"]) == 0) {
						header('Location: '.admin_url('admin.php').'?page=megasubscribepopup-users');
						die();
					}
					$sql = "UPDATE ".$wpdb->prefix."sp_users SET deleted = '1' WHERE id = '".$id."'";
					if ($wpdb->query($sql) !== false) {
						header('Location: '.admin_url('admin.php').'?page=megasubscribepopup-users&deleted=1');
					} else {
						header('Location: '.admin_url('admin.php').'?page=megasubscribepopup-users');
					}
					die();
					break;
				case 'megasubscribepopup-csv':
					$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sp_users WHERE deleted = '0' ORDER BY registered DESC", ARRAY_A);
					if (sizeof($rows) > 0) {
						if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-type: application-download");
							header("Content-Disposition: attachment; filename=\"emails.csv\"");
							header("Content-Transfer-Encoding: binary");
						} else {
							header("Content-type: application-download");
							header("Content-Disposition: attachment; filename=\"emails.csv\"");
						}
						$separator = $this->options['csv_separator'];
						if ($separator == 'tab') $separator = "\t";
						echo '"Name"'.$separator.'"E-Mail"'.$separator.'"Registered"'."\r\n";
						foreach ($rows as $row) {
							echo '"'.str_replace('"', '', $row["name"]).'"'.$separator.'"'.str_replace('"', "", $row["email"]).'"'.$separator.'"'.date("Y-m-d H:i:s", $row["registered"]).'"'."\r\n";
						}
						die();
		            }
		            header("Location: ".get_bloginfo('wpurl')."/wp-admin/admin.php?page=megasubscribepopup");
					die();
					break;
				case 'megasubscribepopup-deleteall':
					$sql = "UPDATE ".$wpdb->prefix."sp_users SET deleted = '1' WHERE deleted = '0'";
					if ($wpdb->query($sql) !== false) {
						header('Location: '.admin_url('admin.php').'?page=megasubscribepopup-users&deleted=1');
					} else {
						header('Location: '.admin_url('admin.php').'?page=megasubscribepopup-users');
					}
					die();
					break;
				case 'megasubscribepopup-getresponse-campaigns':
					if (isset($_GET["key"]) && !empty($_GET["key"])) {
						$key = $_GET["key"];
						$request = json_encode(
							array(
								'method' => 'get_campaigns',
								'params' => array(
									$key
								),
								'id' => ''
							)
						);

						$curl = curl_init('https://api2.getresponse.com/');
						curl_setopt($curl, CURLOPT_POST, 1);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
						$header = array(
							'Content-Type: application/json',
							'Content-Length: '.strlen($request)
						);
						curl_setopt($curl, CURLOPT_PORT, 443);
						curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
						curl_setopt($curl, CURLOPT_TIMEOUT, 10);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
						curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
						curl_setopt($curl, CURLOPT_HEADER, 0);
									
						$response = curl_exec($curl);
						
						if (curl_error($curl)) die('<div style="text-align: center; margin: 20px 0px;">'.__('API call failed.','megasubscribepopup').'</div>');
						$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
						if ($httpCode != '200') die('<div style="text-align: center; margin: 20px 0px;">'.__('API call failed.','megasubscribepopup').'</div>');
						curl_close($curl);
						
						$post = json_decode($response, true);
						if(!empty($post['error'])) die('<div style="text-align: center; margin: 20px 0px;">'.__('API Key failed','megasubscribepopup').': '.$post['error']['message'].'</div>');
						
						if (!empty($post['result'])) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('GetResponse Campaigns', 'megasubscribepopup').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('Campaign ID', 'megasubscribepopup').'</td>
			<td style="font-weight: bold;">'.__('Campaign Name', 'megasubscribepopup').'</td>
		</tr>';
							foreach ($post['result'] as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value['name'])).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'megasubscribepopup').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'megasubscribepopup').'</div>';
					die();
					break;
				case 'megasubscribepopup-icontact-lists':
					if (isset($_GET["appid"]) && isset($_GET["user"]) && isset($_GET["pass"])) {
						$this->options['icontact_appid'] = $_GET["appid"];
						$this->options['icontact_apiusername'] = $_GET["user"];
						$this->options['icontact_apipassword'] = $_GET["pass"];
						
						$lists = $this->icontact_getlists();
						if (!empty($lists)) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('GetResponse Campaigns', 'megasubscribepopup').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('List ID', 'megasubscribepopup').'</td>
			<td style="font-weight: bold;">'.__('List Name', 'megasubscribepopup').'</td>
		</tr>';
							foreach ($lists as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value)).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'megasubscribepopup').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'megasubscribepopup').'</div>';
					die();
					break;
				default:
					break;
			}
		}
	}

	function admin_warning() {
		echo '
		<div class="updated"><p>'.__('<strong>Multi Events Subscription Pop plugin almost ready.</strong> You must do some <a href="admin.php?page=megasubscribepopup">settings</a> for it to work.', 'megasubscribepopup').'</p></div>';
	}

	function front_init() {
		add_action('wp_enqueue_scripts', array(&$this, 'front_enqueue_scripts'));
		add_action("wp_head", array(&$this, 'front_header'));
		add_action("wp_footer", array(&$this, 'front_footer'));
	}

	function front_enqueue_scripts() {
		wp_enqueue_script("jquery");
		wp_enqueue_style('megasubscribepopup', plugins_url('/css/style.css', __FILE__), array(), MEGASUBSCRIBEPOPUP_VERSION);
		wp_enqueue_script('megasubscribepopup', plugins_url('/js/script.js', __FILE__), array(), MEGASUBSCRIBEPOPUP_VERSION);
	}

	function front_header() {
		global $post;
		if (!empty($this->options['url'])) $url = $this->options['url'];
		else $url = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://').$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$md5_url = md5($url);
		if (is_singular()) $meta = $this->get_meta($post->ID);
		if ($this->options["load_mode"] == "all") $load_enable = 'true';
		else if ($this->options["load_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["load_active"] == "on"))) $load_enable = 'true';
		else if ($this->options["load_mode"] == "post" && isset($meta) && $meta["load_active"] == "on")  $load_enable = 'true';
		else $load_enable = 'false';
		if ($this->options["exit_mode"] == "all") $exit_enable = 'true';
		else if ($this->options["exit_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["exit_active"] == "on"))) $exit_enable = 'true';
		else if ($this->options["exit_mode"] == "post" && isset($meta) && $meta["exit_active"] == "on")  $exit_enable = 'true';
		else $exit_enable = 'false';
		if ($this->options["copy_mode"] == "all") $copy_enable = 'true';
		else if ($this->options["copy_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["copy_active"] == "on"))) $copy_enable = 'true';
		else if ($this->options["copy_mode"] == "post" && isset($meta) && $meta["copy_active"] == "on")  $copy_enable = 'true';
		else $copy_enable = 'false';
		if ($this->options["idle_mode"] == "all") $idle_enable = 'true';
		else if ($this->options["idle_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["idle_active"] == "on"))) $idle_enable = 'true';
		else if ($this->options["idle_mode"] == "post" && isset($meta) && $meta["idle_active"] == "on")  $idle_enable = 'true';
		else $idle_enable = 'false';
		if ($this->options["scroll_mode"] == "all") $scroll_enable = 'true';
		else if ($this->options["scroll_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["scroll_active"] == "on"))) $scroll_enable = 'true';
		else if ($this->options["scroll_mode"] == "post" && isset($meta) && $meta["scroll_active"] == "on")  $scroll_enable = 'true';
		else $scroll_enable = 'false';
		if ($this->options["context_mode"] == "all") $context_enable = 'true';
		else if ($this->options["context_mode"] == "homepost" && (is_home() || (isset($meta) && $meta["context_active"] == "on"))) $context_enable = 'true';
		else if ($this->options["context_mode"] == "post" && isset($meta) && $meta["context_active"] == "on")  $context_enable = 'true';
		else $context_enable = 'false';
		$excluded = array("twitter.com", "pinterest.com");
		$tmp = explode("\n", $this->options['exit_excluded_links']);
		foreach ($tmp as $link) {
			$link = str_replace('"', '', trim(strtolower($link)));
			if ($link != '') $excluded[] = $link;
		}
		
		echo '
		<style type="text/css">
			'.$this->options["css"].'
		</style>
		<script type="text/javascript">
			var megasubscribepopup_value_cookie = "'.MEGASUBSCRIBEPOPUP_COOKIE.'";
			var megasubscribepopup_value_overlay_bg_color = "'.$this->options['overlay_bg_color'].'";
			var megasubscribepopup_value_overlay_opacity = "'.$this->options['overlay_opacity'].'";
			var megasubscribepopup_value_popup_bg_color = "'.$this->options['popup_bg_color'].'";
			var megasubscribepopup_value_popup_bg_url = "'.$this->options['popup_bg_url'].'";
			var megasubscribepopup_value_width = '.$this->options['width'].';
			var megasubscribepopup_value_height = '.$this->options['height'].';
			var megasubscribepopup_value_disable_mobile = "'.$this->options['disable_mobile'].'";
			var megasubscribepopup_value_load_delay = '.$this->options['load_delay'].';
			var megasubscribepopup_value_load_start_delay = 1000*'.$this->options['load_start_delay'].';
			var megasubscribepopup_value_load_once_per_visit = "'.$this->options['load_once_per_visit'].'";
			var megasubscribepopup_value_load_disable_close = "'.$this->options['load_disable_close'].'";
			var megasubscribepopup_value_exit_delay = '.$this->options['exit_delay'].';
			var megasubscribepopup_value_exit_excluded = new Array("'.implode('", "', $excluded).'");
			var megasubscribepopup_value_copy_block = "'.$this->options['copy_block'].'";
			var megasubscribepopup_value_idle_delay = '.$this->options['idle_delay'].';
			var megasubscribepopup_value_scroll_once_per_visit = "'.$this->options['scroll_once_per_visit'].'";
			var megasubscribepopup_value_scroll_offset = '.$this->options['scroll_offset'].';
			var megasubscribepopup_value_load_enable = '.$load_enable.';
			var megasubscribepopup_value_exit_enable = '.$exit_enable.';
			var megasubscribepopup_value_copy_enable = '.$copy_enable.';
			var megasubscribepopup_value_idle_enable = '.$idle_enable.';
			var megasubscribepopup_value_scroll_enable = '.$scroll_enable.';
			var megasubscribepopup_value_context_enable = '.$context_enable.';
			var megasubscribepopup_value_disable_name = "'.$this->options['disable_name'].'";
			var megasubscribepopup_action = "'.admin_url('admin-ajax.php').'";
		</script>';
	}
	
	function front_footer() {
		$message = do_shortcode($this->options['message']);
		if (!empty($this->options['url'])) $url = $this->options['url'];
		else $url = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://').$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		echo '
		<div id="megasubscribepopup_container" style="display: none;">
			<div class="megasubscribepopup_box megasubscribepopup_font_'.($this->options['font_scheme']).'">
				<div class="megasubscribepopup_message">
				'.$message.'
				</div>
				<div id="megasubscribepopup_form">
					'.($this->options['disable_name'] == 'on' ? '' : '<input required="required" tabindex="1" class="megasubscribepopup_input" id="megasubscribepopup_name" type="text" placeholder="'.__('Enter your name', 'megasubscribepopup').'" value="'.__('Enter your name', 'megasubscribepopup').'" onfocus="if (this.value == \''.__('Enter your name', 'megasubscribepopup').'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.__('Enter your name', 'megasubscribepopup').'\';}" title="'.__('Please enter your name.', 'megasubscribepopup').'" />').'
					<input required="required" tabindex="2" class="megasubscribepopup_input" id="megasubscribepopup_email" type="text" placeholder="'.__('Enter your e-mail', 'megasubscribepopup').'" value="'.__('Enter your e-mail', 'megasubscribepopup').'" onfocus="if (this.value == \''.__('Enter your e-mail', 'megasubscribepopup').'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.__('Enter your e-mail', 'megasubscribepopup').'\';}" title="'.__('Please enter your e-mail.', 'megasubscribepopup').'" />
					<input type="button" id="megasubscribepopup_submit" value="'.__('Subscribe', 'megasubscribepopup').'" onclick="megasubscribepopup_subscribe();">
					<img id="megasubscribepopup_loading" class="megasubscribepopup_loading" src="'.plugins_url('/images/loading.gif', __FILE__).'" alt="">
				</div>
			</div>
		</div>
		<script type="text/javascript">
			megasubscribepopup_init();
		</script>';
	}

	function megasubscribepopup_submit() {
		global $wpdb;
		$email = trim($_POST['email']);
		if (get_magic_quotes_gpc()) {
			$email = stripslashes($email);
		}
		$errors = array();
		if ($this->options['disable_name'] != 'on') {
			$name = trim($_POST['name']);
			if (get_magic_quotes_gpc()) {
				$name = stripslashes($name);
			}
			if (strlen($name) > 127 || strlen($name) == 0 || $name == __('Enter your name', 'megasubscribepopup')) $errors[] = 'name';
		} else $name = '';
		if ($email == '' || !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) $errors[] = 'email';
		if (sizeof($errors) == 0) {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."sp_users WHERE deleted = '0' AND email = '".mysql_real_escape_string($email)."'", ARRAY_A);
			if ($tmp["total"] > 0) {
				$sql = "UPDATE ".$wpdb->prefix."sp_users SET
					name = '".mysql_real_escape_string($name)."',
					registered = '".time()."'
					WHERE deleted = '0' AND email = '".mysql_real_escape_string($email)."'";
				$wpdb->query($sql);
			} else {
				$sql = "INSERT INTO ".$wpdb->prefix."sp_users (
					name, email, registered, deleted) VALUES (
					'".mysql_real_escape_string($name)."',
					'".mysql_real_escape_string($email)."',
					'".time()."', '0'
				)";
				$wpdb->query($sql);
			}
			if (empty($name)) $name = substr($email, 0, strpos($email, '@'));
			if ($this->options['mailchimp_enable'] == 'on') {
				$list_id = $this->options['mailchimp_list_id'];
				$dc = "us1";
				if (strstr($this->options['mailchimp_api_key'], "-")) {
					list($key, $dc) = explode("-", $this->options['mailchimp_api_key'], 2);
					if (!$dc) $dc = "us1";
				}
				$url = 'http://'.$dc.'.api.mailchimp.com/1.3/?method=listSubscribe&apikey='.$this->options['mailchimp_api_key'].'&id='.$list_id.'&email_address='.urlencode($email).'&merge_vars[FNAME]='.urlencode($name).'&merge_vars[LNAME]='.urlencode($name).'&merge_vars[NAME]='.urlencode($name).'&merge_vars[OPTIN_IP]='.$_SERVER['REMOTE_ADDR'].'&output=php&double_optin='.($this->options['mailchimp_double'] == 'on' ? '1' : '0').'&send_welcome='.($this->options['mailchimp_welcome'] == 'on' ? '1' : '0');

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_ENCODING, "");
				curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: MCAPI/1.3');
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, null);
				$data  = curl_exec( $ch );
				curl_close( $ch );
			}
			if ($this->options['campaignmonitor_enable'] == 'on') {
				$options['EmailAddress'] = $email;
				$options['Name'] = $name;
				$options['Resubscribe'] = 'true';
				$options['RestartSubscriptionBasedAutoresponders'] = 'true';
				$post = json_encode($options);

				$curl = curl_init('http://api.createsend.com/api/v3/subscribers/'.urlencode($this->options['campaignmonitor_list_id']).'.json');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
				
				$header = array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($post),
					'Authorization: Basic '.base64_encode($this->options['campaignmonitor_api_key'])
					);

				//curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
				//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
				//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
					
				$response = curl_exec($curl);
				curl_close($curl);
			}

			if ($this->options['getresponse_enable'] == 'on') {
				$request = json_encode(
					array(
						'method' => 'add_contact',
						'params' => array(
							$this->options['getresponse_api_key'],
							array(
								'campaign' => $this->options['getresponse_campaign_id'],
								'action' => 'standard',
								'name' => $name,
								'email' => $email,
								'cycle_day' => 0,
								'ip' => $_SERVER['REMOTE_ADDR']
							)
						),
						'id' => ''
					)
				);

				$curl = curl_init('http://api2.getresponse.com/');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
							
				$header = array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($request)
				);

				//curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
				//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_HEADER, 0);
							
				$response = curl_exec($curl);
				curl_close($curl);
			}
			
			if ($this->options['icontact_enable'] == 'on') {
				$this->icontact_addcontact($name, $email);
			}
			
			if ($this->options['aweber_access_secret']) {
				if ($this->options['aweber_enable'] == 'on') {
					$account = null;
					if (!class_exists('AWeberAPI')) {
						require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
					}
					try {
						$aweber = new AWeberAPI($this->options['aweber_consumer_key'], $this->options['aweber_consumer_secret']);
						$account = $aweber->getAccount($this->options['aweber_access_key'], $this->options['aweber_access_secret']);
						$subscribers = $account->loadFromUrl('/accounts/' . $account->id . '/lists/' . $this->options['aweber_listid'] . '/subscribers');
						$subscribers->create(array(
							'email' => $email,
							'ip_address' => $_SERVER['REMOTE_ADDR'],
							'name' => $name,
							'ad_tracking' => 'Subscribe & Download',
						));
					} catch (Exception $e) {
						$account = null;
					}
				}
			}
			if (function_exists('mymail_subscribe') || function_exists('mymail')) {
				if ($this->options['mymail_enable'] == 'on') {
					if (function_exists('mymail')) {
						$list = mymail('lists')->get($this->options['mymail_listid']);
					} else {
						$list = get_term_by('id', $this->options['mymail_listid'], 'newsletter_lists');
					}
					if (!empty($list)) {
						try {
							if ($this->options['mymail_double'] == "on") $double = true;
							else $double = false;
							if (function_exists('mymail')) {
								$entry = array(
									'firstname' => $name,
									'email' => $email,
									'status' => $double ? 0 : 1,
									'ip' => $_SERVER['REMOTE_ADDR'],
									'signup_ip' => $_SERVER['REMOTE_ADDR'],
									'referer' => $_SERVER['HTTP_REFERER'],
									'signup' =>time()
								);
								$subscriber_id = mymail('subscribers')->add($entry, true);
								if (is_wp_error( $subscriber_id )) return;
								$result = mymail('subscribers')->assign_lists($subscriber_id, array($list->ID));
							} else {
								$result = mymail_subscribe($email, array('firstname' => $name), array($term->slug), $double);
							}
						} catch (Exception $e) {
						}
					}
				}
			}
			//setcookie("megasubscribepopup", MEGASUBSCRIBEPOPUP_COOKIE, time()+3600*24*180, "/");
		} else {
			echo "ERROR: ".implode(", ", $errors);
		}
		exit;
	}

	function icontact_getlists() {
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/', null, 'accounts');
		if (!empty($data['errors'])) return array();
		$account = $data['response'][0];
		if (empty($account) || intval($account->enabled != 1)) return;
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/', null, 'clientfolders');
		if (!empty($data['errors'])) return array();
		$client = $data['response'][0];
		if (empty($client)) return array();
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/lists', array(), 'lists');
		if (!empty($data['errors'])) return array();
		if (!is_array($data['response'])) return array();
		$lists = array();
		foreach ($data['response'] as $list) {
			$lists[$list->listId] = $list->name;
		}
		return $lists;
	}

	function icontact_addcontact($name, $email) {
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/', null, 'accounts');
		if (!empty($data['errors'])) return;
		$account = $data['response'][0];
		if (empty($account) || intval($account->enabled != 1)) return;
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/', null, 'clientfolders');
		if (!empty($data['errors'])) return;
		$client = $data['response'][0];
		if (empty($client)) return;
		$contact['email'] = $email;
		$contact['firstName'] = $name;
		$contact['status'] = 'normal';
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/contacts', array($contact), 'contacts');
		if (!empty($data['errors'])) return;
		$contact = $data['response'][0];
		if (empty($contact)) return;
		$subscriber['contactId'] = $contact->contactId;
		$subscriber['listId'] = $this->options['icontact_listid'];
		$subscriber['status'] = 'normal';
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/subscriptions', array($subscriber), 'subscriptions');
	}

	function icontact_makecall($appid, $apiusername, $apipassword, $resource, $postdata = null, $returnkey = null) {
		$return = array();
		$url = "https://app.icontact.com/icp".$resource;
		$headers = array(
			'Except:', 
			'Accept:  application/json', 
			'Content-type:  application/json', 
			'Api-Version:  2.2',
			'Api-AppId:  '.$appid, 
			'Api-Username:  '.$apiusername, 
			'Api-Password:  '.$apipassword
		);
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		if (!empty($postdata)) {
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($postdata));
		}
		curl_setopt($handle, CURLOPT_URL, $url);
		if (!$response_json = curl_exec($handle)) {
			$return['errors'][] = __('Unable to execute the cURL handle.', 'megasubscribepopup');
		}
		if (!$response = json_decode($response_json)) {
			$return['errors'][] = __('The iContact API did not return valid JSON.', 'megasubscribepopup');
		}
		curl_close($handle);
		if (!empty($response->errors)) {
			foreach ($response->errors as $error) {
				$return['errors'][] = $error;
			}
		}
		if (!empty($return['errors'])) return $return;
		if (empty($returnkey)) {
			$return['response'] = $response;
		} else {
			$return['response'] = $response->$returnkey;
		}
		return $return;
	}

	function page_switcher ($_urlbase, $_currentpage, $_totalpages) {
		$pageswitcher = "";
		if ($_totalpages > 1) {
			$pageswitcher = '<div class="tablenav bottom"><div class="tablenav-pages">'.__('Pages:', 'megasubscribepopup').' <span class="pagiation-links">';
			if (strpos($_urlbase,"?") !== false) $_urlbase .= "&amp;";
			else $_urlbase .= "?";
			if ($_currentpage == 1) $pageswitcher .= "<a class='page disabled'>1</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=1'>1</a> ";

			$start = max($_currentpage-3, 2);
			$end = min(max($_currentpage+3,$start+6), $_totalpages-1);
			$start = max(min($start,$end-6), 2);
			if ($start > 2) $pageswitcher .= " <b>...</b> ";
			for ($i=$start; $i<=$end; $i++) {
				if ($_currentpage == $i) $pageswitcher .= " <a class='page disabled'>".$i."</a> ";
				else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$i."'>".$i."</a> ";
			}
			if ($end < $_totalpages-1) $pageswitcher .= " <b>...</b> ";

			if ($_currentpage == $_totalpages) $pageswitcher .= " <a class='page disabled'>".$_totalpages."</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$_totalpages."'>".$_totalpages."</a> ";
			$pageswitcher .= "</span></div></div>";
		}
		return $pageswitcher;
	}

	function get_rgb($_color) {
		if (strlen($_color) != 7 && strlen($_color) != 4) return false;
		$color = preg_replace('/[^#a-fA-F0-9]/', '', $_color);
		if (strlen($color) != strlen($_color)) return false;
		if (strlen($color) == 7) list($r, $g, $b) = array($color[1].$color[2], $color[3].$color[4], $color[5].$color[6]);
		else list($r, $g, $b) = array($color[1].$color[1], $color[2].$color[2], $color[3].$color[3]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}
}
$megasubscribepopup = new megasubscribepopup_class();
?>