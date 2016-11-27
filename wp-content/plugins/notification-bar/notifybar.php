<?php
/*
Plugin Name:Notification Bar
Plugin URI: http://www.wpfruits.com/downloads/wp-plugins/notification-bar-plugin/
Description: This plugin will show notification at top of the header.
Author: WPFruits
Version: 2.1.3
Author URI: http://www.wpfruits.com
*/
// ----------------------------------------------------------------------------------

// ADD Styles and Script in head section
add_action('admin_init', 'notifybar_backend_scripts');
add_action('wp_enqueue_scripts', 'notifybar_frontend_scripts');

function notifybar_backend_scripts() {
	if(is_admin()){
		wp_enqueue_script ('jquery');
		wp_enqueue_script('farbtastic');
		wp_enqueue_script('notifybar_backend_scripts',plugins_url('admin/notifybar_admin.js',__FILE__), array('jquery'));
		wp_enqueue_style('notifybar_backend_scripts',plugins_url('admin/notifybar_admin.css',__FILE__), false, '1.0.0' );
		wp_enqueue_style('farbtastic');	
	}
}

function notifybar_frontend_scripts() {	
	if(!is_admin()){
		wp_enqueue_script('jquery');
		wp_enqueue_script('notifybar',plugins_url('js/notifybar.js',__FILE__), array('jquery'));
		wp_enqueue_style('notifybar',plugins_url('css/notifybar.css',__FILE__));
	}
}
//-------------------------------------------------------------------------------------

// Hook for adding admin menus
add_action('admin_menu', 'notifybar_plugin_admin_menu');
function notifybar_plugin_admin_menu() {
     add_menu_page('notifybar', 'Notification Bar','administrator', 'notifybar', 'notifybar_backend_menu',plugins_url('images/icon.png',__FILE__));
}


//This function will create new database fields with default values
function notifybar_defaults(){
	$default = array(
		'defaultposition' => 'top',
		'staytime'        => '6000',
		'color_scheme'    => '#0F67A1',
        'text_field'      => 'Get my Subscription',
		'msgtxt_color'    => '#FFFFFF',
    	'link_url'        => 'http://www.wpfruits.com',
    	'link_text'       => 'Subscribe',
		'linktxt_color'   => '#FFFFFF',
		'link_bgcolor'    => '#0F67A1'
    );
return $default;
}

// Runs when plugin is activated and creates new database field
register_activation_hook(__FILE__,'notifybar_plugin_install');
function notifybar_plugin_install() {
    add_option('notifybar_options', notifybar_defaults());
}	

// update the notifybar options
if(isset($_POST['notifybar_update'])){
	update_option('notifybar_options', notifybar_updates());
}

function notifybar_updates() {
	$options = $_POST['notifybar_options'];
	    $update_val = array(
		'defaultposition' => $options['defaultposition'],
		'staytime' => $options['staytime'],
		'color_scheme' => $options['color_scheme'],
    	'text_field' =>$options['text_field'],
    	'msgtxt_color' =>$options['msgtxt_color'],
    	'link_url' =>$options['link_url'],
    	'link_text' => $options['link_text'],
		'linktxt_color' => $options['linktxt_color'],
		'link_bgcolor' => $options['link_bgcolor']
    );
return $update_val;
}

// get notifybar version
function notifybar_get_version(){
	if ( ! function_exists( 'get_plugins' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

function notifybar_backend_menu()
{
wp_nonce_field('update-options'); $options = get_option('notifybar_options'); 
$options['defaultposition']

?>

<div class="wrap">
<div id="icon-themes" class="icon32"></div>
<h2><?php _e('Notification Bar '.notifybar_get_version().' Setting\'s','notifybar'); ?></h2>
</div>

<div class="nbarlite-wrapper">
	<!-- WP-Banner Starts Here -->
		<div id="wp_banner" class="nbar-lite">
			<!-- Top Section Starts Here -->
			<div class="top_section">
				<!-- Begin MailChimp Signup Form -->
				<link type="text/css" rel="stylesheet" href="http://cdn-images.mailchimp.com/embedcode/classic-081711.css">
				<style type="text/css">
					#mc_embed_signup{ clear:left; font:14px Helvetica,Arial,sans-serif; }
					/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
					   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
				</style>
				<div id="mc_embed_signup">
					<form novalidate="" target="_blank" class="validate" name="mc-embedded-subscribe-form" id="mc-embedded-subscribe-form" method="post" action="http://wpfruits.us6.list-manage.com/subscribe/post?u=166c9fed36fb93e9202b68dc3&amp;id=bea82345ae">
						<div class="mc-field-group">
							<input type="email" id="mce-EMAIL" class="required email" name="EMAIL" value="" placeholder="Our Newsletter Just Enter Your Email Here" />
							<input type="submit" class="button" id="mc-embedded-subscribe" name="subscribe" value="" onclick="return nbar_wp_jsvalid();">
							<div style="clear:both;"></div>
						</div>
						<div class="clear" id="mce-responses">
							<div style="display:none" id="mce-error-response" class="response"></div>
							<div style="display:none" id="mce-success-response" class="response"></div>
						</div>	
						
					</form>
				</div>
				<script type="text/javascript">
					var fnames = new Array();var ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';
					try {
						var jqueryLoaded=jQuery;
						jqueryLoaded=true;
					} catch(err) {
						var jqueryLoaded=false;
					}
					var head= document.getElementsByTagName('head')[0];
					if (!jqueryLoaded) {
						var script = document.createElement('script');
						script.type = 'text/javascript';
						script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
						head.appendChild(script);
						if (script.readyState &amp;&amp; script.onload!==null){
							script.onreadystatechange= function () {
								  if (this.readyState == 'complete') mce_preload_check();
							}    
						}
					}
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.src = 'http://downloads.mailchimp.com/js/jquery.form-n-validate.js';
					head.appendChild(script);
					var err_style = '';
					try{
						err_style = mc_custom_error_style;
					} catch(e){
						err_style = '#mc_embed_signup input.mce_inline_error{border-color:#6B0505;} #mc_embed_signup div.mce_inline_error{margin: 0 0 1em 0; padding: 5px 10px; background-color:#6B0505; font-weight: bold; z-index: 1; color:#fff;}';
					}
					var head= document.getElementsByTagName('head')[0];
					var style= document.createElement('style');
					style.type= 'text/css';
					if (style.styleSheet) {
						style.styleSheet.cssText = err_style;
					} else {
						style.appendChild(document.createTextNode(err_style));
					}
					head.appendChild(style);
					setTimeout('mce_preload_check();', 250);

					var mce_preload_checks = 0;
					function mce_preload_check(){
						if (mce_preload_checks&gt;40) return;
						mce_preload_checks++;
						try {
							var jqueryLoaded=jQuery;
						} catch(err) {
							setTimeout('mce_preload_check();', 250);
							return;
						}
						try {
							var validatorLoaded=jQuery("#fake-form").validate({});
						} catch(err) {
							setTimeout('mce_preload_check();', 250);
							return;
						}
						mce_init_form();
					}
					function mce_init_form()
					{
						jQuery(document).ready( function($) 
						{
						  var options = { errorClass: 'mce_inline_error', errorElement: 'div', onkeyup: function(){}, onfocusout:function(){}, onblur:function(){}  };
						  var mce_validator = $("#mc-embedded-subscribe-form").validate(options);
						  $("#mc-embedded-subscribe-form").unbind('submit');//remove the validator so we can get into beforeSubmit on the ajaxform, which then calls the validator
						  options = { url: 'http://wpfruits.us6.list-manage.com/subscribe/post-json?u=166c9fed36fb93e9202b68dc3&amp;id=bea82345ae&amp;c=?', type: 'GET', dataType: 'json', contentType: "application/json; charset=utf-8",
										beforeSubmit: function(){
											$('#mce_tmp_error_msg').remove();
											$('.datefield','#mc_embed_signup').each(
												function(){
													var txt = 'filled';
													var fields = new Array();
													var i = 0;
													$(':text', this).each(
														function(){
															fields[i] = this;
															i++;
														});
													$(':hidden', this).each(
														function(){
															var bday = false;
															if (fields.length == 2){
																bday = true;
																fields[2] = {'value':1970};//trick birthdays into having years
															}
															if ( fields[0].value=='MM' &amp;&amp; fields[1].value=='DD' &amp;&amp; (fields[2].value=='YYYY' || (bday &amp;&amp; fields[2].value==1970) ) ){
																this.value = '';
															} else if ( fields[0].value=='' &amp;&amp; fields[1].value=='' &amp;&amp; (fields[2].value=='' || (bday &amp;&amp; fields[2].value==1970) ) ){
																this.value = '';
															} else {
																if (/\[day\]/.test(fields[0].name)){
																	this.value = fields[1].value+'/'+fields[0].value+'/'+fields[2].value;									        
																} else {
																	this.value = fields[0].value+'/'+fields[1].value+'/'+fields[2].value;
																}
															}
														});
												});
											return mce_validator.form();
										}, 
										success: mce_success_cb
									};
						  $('#mc-embedded-subscribe-form').ajaxForm(options);

						});
					}
					function mce_success_cb(resp){
						$('#mce-success-response').hide();
						$('#mce-error-response').hide();
						if (resp.result=="success"){
							$('#mce-'+resp.result+'-response').show();
							$('#mce-'+resp.result+'-response').html(resp.msg);
							$('#mc-embedded-subscribe-form').each(function(){
								this.reset();
							});
						} else {
							var index = -1;
							var msg;
							try {
								var parts = resp.msg.split(' - ',2);
								if (parts[1]==undefined){
									msg = resp.msg;
								} else {
									i = parseInt(parts[0]);
									if (i.toString() == parts[0]){
										index = parts[0];
										msg = parts[1];
									} else {
										index = -1;
										msg = resp.msg;
									}
								}
							} catch(e){
								index = -1;
								msg = resp.msg;
							}
							try{
								if (index== -1){
									$('#mce-'+resp.result+'-response').show();
									$('#mce-'+resp.result+'-response').html(msg);            
								} else {
									err_id = 'mce_tmp_error_msg';
									html = '&lt;div id="'+err_id+'" style="'+err_style+'"&gt; '+msg+'&lt;/div&gt;';
									
									var input_id = '#mc_embed_signup';
									var f = $(input_id);
									if (ftypes[index]=='address'){
										input_id = '#mce-'+fnames[index]+'-addr1';
										f = $(input_id).parent().parent().get(0);
									} else if (ftypes[index]=='date'){
										input_id = '#mce-'+fnames[index]+'-month';
										f = $(input_id).parent().parent().get(0);
									} else {
										input_id = '#mce-'+fnames[index];
										f = $().parent(input_id).get(0);
									}
									if (f){
										$(f).append(html);
										$(input_id).focus();
									} else {
										$('#mce-'+resp.result+'-response').show();
										$('#mce-'+resp.result+'-response').html(msg);
									}
								}
							} catch(e){
								$('#mce-'+resp.result+'-response').show();
								$('#mce-'+resp.result+'-response').html(msg);
							}
						}
					}

				</script>
				<!--End mc_embed_signup-->
			</div>
			<!-- Top Section Ends Here -->
			
			<!-- Bottom Section Starts Here -->
			<div class="bot_section">
				<a href="http://www.wpfruits.com/" class="wplogo" target="_blank" title="WFruits.com"></a>
				<a href="https://www.facebook.com/pages/WPFruitscom/443589065662507" class="fbicon" target="_blank" title="Facebook"></a>
				<a href="http://www.twitter.com/wpfruits" class="twicon" target="_blank" title="Twitter"></a>
				<div style="clear:both;"></div>
			</div>
			<!-- Bottom Section Ends Here -->
		</div>
	<!-- WP-Banner Ends Here -->
</div>

	
<div id="poststuff" style="position:relative;">
	<div class="postbox" id="notifybar_admin">
		<div class="handlediv" title="Click to toggle"><br/></div>
		<h3 class="hndle"><span><?php _e("Enter Notification values:",'notifybar'); ?></span></h3>
		<div class="inside" style="padding: 15px;margin: 0;">
			<form method="post">
				<table>
				
					<tr>
						<td><?php _e("Default Position",'notifybar'); ?> :</td>
						<td><select name="notifybar_options[defaultposition]"><option value="top" <?php selected('top', $options['defaultposition']); ?>>Top</option><option value="bottom" <?php selected('bottom', $options['defaultposition']); ?>>Bottom</option></select></td>
					</tr>
					
					<?php
						$options['staytime'] = ($options['staytime'] !="") ? $options['staytime'] : '6000';
					?>
					
					<tr>
						<td><?php _e("Staytime", 'notifybar'); ?> :</td>
						<td><input type="text" name="notifybar_options[staytime]" value="<?php echo $options['staytime'] ?>" /><small>( in MSec. )</small></td>
					</tr>
							
					<tr>
						<td><?php _e("Color Scheme",'notifybar'); ?> :</td>
						<td>
							<div class="notifybar_colwrap">
								<input type="text" id="notifybar_colorScheme" class="notifybar_color_inp" value="<?php if($options['color_scheme']) echo $options['color_scheme']; else echo "#0F67A1"; ?>" name="notifybar_options[color_scheme]" />
								<div class="notifybar_colsel notifybar_colorScheme"></div>
							</div>
						</td>
					</tr>

					<tr>
						<td><?php _e("Text Message", 'notifybar'); ?> :</td>
						<td><textarea col="10" name="notifybar_options[text_field]"><?php echo $options['text_field'] ?></textarea></td>
					</tr>
					
					<tr>
						<td><?php _e("Text Message Color",'notifybar'); ?> :</td>
						<td>
							<div class="notifybar_colwrap">
								<input type="text" id="notifybar_txtclr" class="notifybar_color_inp" value="<?php if($options['msgtxt_color']) echo $options['msgtxt_color']; else echo "#FFFFFF"; ?>" name="notifybar_options[msgtxt_color]" />
								<div class="notifybar_colsel notifybar_txtclr"></div>
							</div>
						</td>
					</tr>

					<tr>
						<td><?php _e("Link URL", 'notifybar'); ?> :</td>
						<td><input type="text" name="notifybar_options[link_url]" value="<?php echo $options['link_url'] ?>" /></td>
					</tr>
					<tr>
						<td><?php _e("Link Button Text", 'notifybar'); ?> :</td>
						<td><input type="text" name="notifybar_options[link_text]" value="<?php echo $options['link_text'] ?>" /></td>
					</tr>
					
					<tr>
						<td><?php _e("Link Text Color",'notifybar'); ?> :</td>
						<td>
							<div class="notifybar_colwrap">
								<input type="text" id="notifybar_linktxtclr" class="notifybar_color_inp" value="<?php if($options['linktxt_color']) echo $options['linktxt_color']; else echo "#FFFFFF"; ?>" name="notifybar_options[linktxt_color]" />
								<div class="notifybar_colsel notifybar_linktxtclr"></div>
							</div>
						</td>
					</tr>
					
					<tr>
						<td><?php _e("Link Bg-Color",'notifybar'); ?> :</td>
						<td>
							<div class="notifybar_colwrap">
								<input type="text" id="notifybar_linkbg" class="notifybar_color_inp" value="<?php if($options['link_bgcolor']) echo $options['link_bgcolor']; else echo "#0F67A1"; ?>" name="notifybar_options[link_bgcolor]" />
								<div class="notifybar_colsel notifybar_linkbg"></div>
							</div>
						</td>
					</tr>

				</table>

				<p class="button-controls">
					<input type="submit" value="<?php _e('Save Settings','notifybar'); ?>" class="button-primary" id="notifybar_update" name="notifybar_update">	
				</p>
			</form>
		</div>
	</div>
	<iframe class="notifybar_iframe" src="http://www.sketchthemes.com/sketch-updates/plugin-updates/nbar-lite/nbar-lite.php" width="250px" height="370px" scrolling="no" ></iframe> 
</div>
<?php
}
//--------------------------------------------------------------------------------------------------------------------------------------

function notifybar(){
$options = get_option('notifybar_options'); 
?>  
<style type="text/css">
#notifybar{<?php echo $options['defaultposition'] ?>:0px;}
#notifybar .notifybar_topsec .notifybar_center .notifybar_block {color:<?php echo $options['msgtxt_color'] ?>;}
#notifybar .notifybar_topsec .notifybar_center .notifybar_button {color:<?php echo $options['linktxt_color'] ?>;}

<?php if($options['defaultposition'] =="bottom"){ ?> 
#notifybar .notifybar_topsec{border-top:3px solid #fff;border-bottom:0;}
#notifybar a.notifybar_close{top: 17px;background-image:url("<?php echo plugins_url('images/sprite_bot.png',__FILE__); ?>");background-position:0 center;}
#notifybar a.notifybar_close:hover{background-position:0 center;opacity:0.6;filter:alpha(opacity = 60);}
<?php } ?>
#nbar_downArr.notifybar_botsec{<?php echo $options['defaultposition']; ?>:-41px;<?php if($options['defaultposition'] =="bottom"){ ?> background-image:url("<?php echo plugins_url('images/up_arrow.png',__FILE__); ?>");<?php } ?>}
</style>

	<div id="notifybar">
		<a class="nbar_downArr" href="#nbar_downArr" style="display:none;"></a>
		<div class="notifybar_topsec" style="background-color:<?php echo $options['color_scheme'] ?>">
		<?php $from_this = "http://www.wpfruits.com/notification-bar-pro/?utm_refs=".$_SERVER['SERVER_NAME']; ?>
		<a title="Get the Notification Bar PRO for your website and Attract visitors to your page" class="nb_fromthis" target="_blank" href="<?php echo $from_this; ?>" title=""><img src="<?php echo plugins_url('images/NB_32x32.png',__FILE__) ?>" /></a>
			<div class="notifybar_center">
				<?php if($options['text_field']){ ?><div class="notifybar_block"><?php echo $options['text_field'] ?></div><?php } ?>
				<?php if($options['link_url']){ ?><a href="<?php echo $options['link_url'] ?>" target="_blank" class="notifybar_block notifybar_button" style="background-color:<?php echo $options['link_bgcolor'] ?>"><?php echo $options['link_text'] ?></a><?php } ?>
			</div>
			<a href="JavaScript:void(0);" class="notifybar_close"></a>
		</div>
	</div>
	<a href="JavaScript:void(0);" class="notifybar_botsec" id="nbar_downArr" style="background-color:<?php echo $options['color_scheme'] ?>"></a>
	<?php $options['staytime'] = ($options['staytime'] !="") ? $options['staytime'] : '6000'; ?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php 
		if($options['defaultposition'] =="bottom"){ ?>jQuery('body').append('<div class="notifybar_push"></div>');<?php } 
		else{ ?>jQuery('body').prepend('<div class="notifybar_push"></div>');<?php } ?>
		jQuery("#notifybar").notifybar({staytime:'<?php echo $options['staytime']; ?>'});
	});
</script>

<?php
}
add_action('wp_footer', 'notifybar');

?>