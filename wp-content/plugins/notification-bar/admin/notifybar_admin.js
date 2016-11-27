/*-- Admin Script
------------------------------------------*/
jQuery(document).ready(function(){
	jQuery('#notifybar_admin .handlediv,.hndle').click(function(){
		jQuery(this).parent().find('.inside').slideToggle("fast");
	});
	
	if (jQuery("#notifybar_admin").length){
		jQuery('.notifybar_colorScheme').farbtastic('#notifybar_colorScheme');
		jQuery('.notifybar_linkbg').farbtastic('#notifybar_linkbg');
		jQuery('.notifybar_txtclr').farbtastic('#notifybar_txtclr');
		jQuery('.notifybar_linktxtclr').farbtastic('#notifybar_linktxtclr');
	}
	jQuery('html').click(function() {jQuery("#notifybar_admin .farbtastic").fadeOut('fast');});
	
	jQuery('#notifybar_admin .notifybar_colsel').click(function(event){
		jQuery("#notifybar_admin .farbtastic").hide();
		jQuery(this).find(".farbtastic").fadeIn('fast');event.stopPropagation();
	});
	
	jQuery('.nbarlite-wrapper #mce-EMAIL').focus(function(){
		jQuery(this).css({'border-color':'#777','color':'#000','background':'transparent'});
	});
	
});

function nbar_wp_jsvalid(){
	var reg= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var a  = document.getElementById('mce-EMAIL').value;
	if( a == ""){
		jQuery('#mce-EMAIL').css({'border-color':'red','color':'red'});
		return false;
	}else{
		if(reg.test(a)==false){
			jQuery('#mce-EMAIL').css({'border-color':'red','color':'red','background':'#F7DAD9'});
			return false;
		}	
	}		
	return true;
}
	
	
