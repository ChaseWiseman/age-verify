jQuery(document).ready(function($) {
		if(jQuery('#av-overlay-wrap').is(":visible")){
			$('html, body').css('overflow: hidden;');
		} else {
			jQuery('html, body').css('overflow:scroll');
		}

		jQuery('#av_verify_link').click(function(event){
		    event.preventDefault();
		    jQuery('#av_verify_form').submit();
		});

});



