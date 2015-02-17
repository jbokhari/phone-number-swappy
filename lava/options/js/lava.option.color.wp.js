jQuery(document).ready(function($){
	var options = {
		defaultColor: false,
		change : function(){},
		clear: function(){},
		hide: true,
		palettes: {}
	}
	$('.colorpicker-wp-ui').wpColorPicker(options);
});