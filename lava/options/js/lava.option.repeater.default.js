jQuery(document).ready(function($){
	//clone button
	$('.repeater-add').on('click', function(e){
		e.preventDefault();
		var id = $(this).data("id");
		var container = $("#" + id + "-fields");
		var rows = container.find(".repeater-row");
		var clone = rows.last().clone();
		clone.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea").val("");
		clone.find("[type='checkbox'],[type='radio']").removeAttr('checked');
		clone.find(".lava-color-chooser").val('');
		clone.find("select").removeAttr("selected");
		clone.appendTo(container);
	});

	var data = {};
	$("#<?php echo $this->id ?>-fields .repeater-row").each(function(i){
		var rowID = "row_"+i;
		data[rowID] = {};
		var $this = $(this);
		var $inputs = $this.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea");
		var inputData = $inputs.each(function(){
			var $self = $(this);
			var name = $self.name;
			data[rowID][name] = $self.val(); 
		});
		var $boxes = $this.find("[type='checkbox'],[type='radio']");
		$boxes.each(function(){
			var $self = $(this);
			var name = $self.name;
		});
		var $selects = $this.find("select");
	});

});
