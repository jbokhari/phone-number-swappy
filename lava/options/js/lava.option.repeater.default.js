jQuery(document).ready(function($){
	//clone button
	console.log('msgsfds');
	$('.repeater-add').each(function(){
		var $this = $(this);
		var id = $this.data("id");
		var repeatercount = $(id + '__meta_rows');
		var container = $(id + "-fields ul");
		console.log(container);
		container.find("li").each(function(){
			var exout = $("<span class='exout'>&#10006;</span>").on("click", function(){
				var r = confirm("Are you sure you want to delete this field?");
				if (r)
					$(this).parent().remove();
				else
					return;
				var rows = parseInt( repeatercount.val() ) - 1;
				repeatercount.val(rows);
			});
			$(this).append(exout);
		})
		$this.on('click', function(e){
			e.preventDefault();
			var id = $(this).data("id");
			var rows = container.find(".repeater-row");

			console.log(id);
			clone(id);
			
		}).after(repeatercount);
		// var sortables = $('.lava-sortable');
		container.sortable({
			stop: function(){
				var $self = $(this); // <ul class=sortable>
				updateOrder($self);
			},
     		handle: ".handle",
			containment: "parent"
		});
	});
	$('.repeater-csv-importer').on("click", function(e){
		e.preventDefault();
		var $this = $(this);
		// try {
			var buddy = $this.data("buddy");
			var target = $this.data("target");
			var $target = $(target + "-fields");
			var $rows = $target.find('.repeater-row');
			var $rowsbutfirst = $rows.not($rows.first());
			$rowsbutfirst.remove();
			var lines = $(buddy).val().split('\n');
			var data = $.map(lines, function(i){
				return [i.split(',')];
			});
			console.log(data);
			var datalength = metarows = data.length;
			while (datalength > 1) {
				// remove // Going to create elements for each row
				clone(target);
				datalength--;
			};
			var repeatercount = $(target + "__meta_rows");
			var rows = metarows;
			repeatercount.val(rows);
			for ( var numrows = data.length, row = 0; row < numrows; row++ ) {
				for (var numcols = data[row].length, col = 0; col < numcols; col++) {
					var nthrowvalue = row + 1,
					    nthcolvalue = col + 1;
					var currentelement = $( ".repeater-row:nth-child(" + nthrowvalue + ") .row-fields .repeater-col:nth-child(" + nthcolvalue + ") input " ).val(data[row][col], $target );
					// console.log( currentelement );
					// console.log( data[row][col] );
				};
			};
		// } catch (e){
		// 	console.error(e);
		// 	alert("There was an error in the script. Check the format of your data.");
		// }
	});
	function clone(id){
		var $id = $(id);
		var container = $(id + "-container");
		var repeatercount = $( id + '__meta_rows', container );
		var ul = $( id + "-fields ul.repeater-list", container);
		var rows = ul.find(".repeater-row", container);
		console.log(repeatercount,id,container,rows);
		var clone = rows.last().clone(true);
		clone.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea").val("");
		clone.find("[type='checkbox'],[type='radio']").removeAttr('checked');
		clone.find(".lava-color-chooser").val('');
		clone.find("select").removeAttr("selected");
		clone.appendTo( ul );
		var rows = parseInt( repeatercount.val() ) + 1;
		repeatercount.val( rows );
		postCloneCleanup( clone );
	}
	function postCloneCleanup(el){
		var image = $(".image-container", el);
		image.each(function(){
			var imagecontainer = $(this);
			var id = imagecontainer.data("image-id");
			var newid = "new-image_" + Math.floor( Math.random() * 1000000 ).toString() + Math.floor( Math.random() * 1000000 ).toString();
			// console.log(newid);
			imagecontainer.data("image-id", newid);
			$(imagecontainer).find(".image-preview").attr("src", "");
			$(imagecontainer).find(".image-source").val("");
		});
	}
	function updateOrder(){
		return;
	}
	var data = {};
	// $("#<?php echo $this->id ?>-fields .repeater-row").each(function(i){
	// 	var rowID = "row_"+i;
	// 	data[rowID] = {};
	// 	var $this = $(this);
	// 	var $inputs = $this.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea");
	// 	var inputData = $inputs.each(function(){
	// 		var $self = $(this);
	// 		var name = $self.name;
	// 		data[rowID][name] = $self.val(); 
	// 	});
	// 	var $boxes = $this.find("[type='checkbox'],[type='radio']");
	// 	$boxes.each(function(){
	// 		var $self = $(this);
	// 		var name = $self.name;
	// 	});
	// 	var $selects = $this.find("select");
	// });

});
