jQuery(document).ready(function($){

	$(".textarea-ui-code").each(function(){
		var $this = $(this);
		var mode = $this.data("codemirrorMode");
		var theme = $this.data("codemirrorTheme");
		console.log("theme", theme);
		console.log("mode", mode);
		var args = {
			lineNumbers: true,
			theme: theme,
			indentUnit: 4,
			viewportMargin: Infinity //for auto height
		};
		if (mode != "htmlmixed") //temporary fixed
			args.mode = mode;
		var editor = CodeMirror.fromTextArea(this, args);
		$this.css("height", "auto");
	    editor.setOption("theme", theme );
	});


});
