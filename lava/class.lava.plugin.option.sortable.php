<?php
final class SwappyOption_sortable extends SwappyOption {
	public function init_tasks($options){
		if ( isset( $options['sortable']) ){
			$this->sortable = $options['sortable'];
		}
		$this->add_class("lava-sortable");
	}
	// public function get_single_instance_footer_scripts(){
	// 	if ( $this->ui == "rgba" && empty(self::$single_instance_scripts[$this->ui]) ){
	// 		self::$single_instance_scripts[$this->ui] = true;
	// 		return "jQuery('input.rgbacolorpicker').rgbacolorpicker();";
	// 	}
	// 	return false; //default return false
	// }
	public function get_option_field_html(){
		$fieldhtml = '';
		$classes = $this->input_classes();
		if ( empty( $this->sortable ) ){
			$this->_error("Error creating sortable field, setting missing sortable option for {$this->name}");
			return 'Error creating sortable field, setting missing sortable option.';
		} else if ( empty( $this->sortable["post_type"] ) ){ 
			$this->_error( "Error creating sortable field, setting missing sortable post_type option for $this->name" );
			return 'Error creating sortable field, setting missing sortable post_type option.';
		}
		$value = $this->get_value();
		$args = array(
			'post_type' => $this->sortable["post_type"],
			'posts_per_page' => -1
		);
		$fieldhtml .= "<div id='add-post-container-{$this->fieldnumber}' class='add-post'>";

		$fieldhtml .= "<input id='posts-" . $this->fieldnumber . "' type='text' autocomplete='false' class='add-field'><button id='add-post-" . $this->fieldnumber . "' href='' class='button button-primary add-btn' disabled='disabled'>Add</button>";

		$fieldhtml .= "</div>";

		$query = new WP_Query( $args );

		$fieldhtml .= "<ul id='sortable-{$this->fieldnumber}' class='sortable {$classes}'>";
		$pc = 0; // post count for array indexis
		if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
		// $fieldhtml .= '<li class="post-' . get_the_id() . '">' . get_the_title() . '</li>';
		$sortposts[$pc]['title'] = get_the_title();
		$sortposts[$pc]['id'] = get_the_id();
		$sortposts[$pc]['id'] = get_the_id();
		$sortposts[$pc]['status'] = get_post_status();
		$indices[get_the_id()] = $pc;
		$pc++;
		endwhile; endif;
		$items = array();
		if (!empty($value)){
			$items = explode(",", $value);

		} else {
			// $fieldhtml .= '<div class="dragtome">Start typing in a post title above to add it to the list.</div>';
		}
		// print_r($value);
		// print_r($items);
		foreach ($items as $id){
			$currentpost = $sortposts[$indices[$id]];
			$status = ($currentpost['status'] !== "publish") ? " <span class='status'>&#8212;{$currentpost['status']}</span>" : '';
			$fieldhtml .= '<li data-order="'.$indices[$id].'" data-id="'.$currentpost['id'].'" class="post-' . $currentpost['id'] . '">' . $currentpost['title'] . $status . '<div class="viewpost"><a href="'.admin_url("post.php").'?action=edit&post='.$currentpost['id'].'">edit</a></div><div class="exout">delete</div></li>';
		}

		$fieldhtml .= '</ul>';
		$fieldhtml .= '<div id="sortable-order-' . $this->fieldnumber . '">';
		$fieldhtml .= '<input type="hidden" name="'.$this->name.'" value="'.$value.'">';
		$fieldhtml .= '</div>';
		ob_start();
		?>
		<script>
			jQuery(document).ready(function($){
				var sortables = $('.lava-sortable');
				sortables.each(function(i){
					$(this).sortable({
						stop: function(){
							var $self = $(this); // <ul class=sortable>
							updateOrder($self);
						},
						containment: "parent"
					});
					
				});
				$(".exout").on("click", function(){
					removeLi(this);
				});
				var hiddenfield = $("#sortable-order-<?php echo $this->fieldnumber ?>");
				var removeLi = function(orgin){
					var $this = $(orgin);
					var parent = $this.parent();
					parent.fadeOut({
						complete: function(){
							$(this).remove();
							var sortable = parent.parent();
							updateOrder(sortable);
						} 
					});
				}
				var updateOrder = function(){
					var lis = sortable.find("li");
					var l = lis.length - 1;
					var input,
						order = "";
					lis.each(function(i){
						var $this = $(this); // <li>
						$this.data("order", i);
						order += $this.data("id");
						if (i < l){ // if NOT last element
							order += ",";
						}
					});
					input = "<input type='hidden' name='<?php echo $this->id ?>' value='"+order+"' />";
					// console.log(input);
					hiddenfield.html($(input));
				}
				var ready = false; //if auto'plete has been used
				var posts = [
			<?php 
			$count = count($sortposts);
			foreach($sortposts as $post){
					$count --;
					echo "{
						label : '{$post['title']}',
						id : '{$post['id']}',
						status : '{$post['status']}' 
					}";
					if ($count > 0){
						echo ",";
					} 
			} ?>

				];

				var newfield = $( "#posts-<?php echo $this->fieldnumber ?>" );
				var addbtn = $("#add-post-<?php echo $this->fieldnumber ?>");
				// console.log(addbtn);
				addbtn.on("click", function(e){
					e.preventDefault();
					if (!ready) return; 
					var $this = $(this);
					if ($this.attr("disabled") == "disabled") return; 
					var lis = sortable.find("li");
					if (newfield.data("id")) {
						var stat = newfield.data("status");
						var status = ( stat != "publish" ) ? '<span class="status">&#8212;'+stat+'</span>' : "";
						var item = $('<li data-id="' + newfield.data("id") + '" data-order="' + lis.length + '" class="post-' + newfield.data("id") + '">' + newfield.data("label") + status + '<div class="viewpost"><a href="' + '<?php echo admin_url("post.php") ?>' + '?action=edit&post=' + newfield.data("id") +'">edit</a></div><div class="exout">delete</div></li>');
						item.find('.exout').click(function(){
							removeLi(this);
						});
						sortable.append(item);
						
						/*Reset stuff*/
							newfield.data("id", "");
							newfield.data("label", "");
							newfield.val("");
							newfield.removeClass("ready");
							addbtn.attr("disabled", "disabled");
							ready = false;
						/**/
						updateOrder(); // update hidden field
					} else {
						alert("Post data found, please try again.");
					}
				});
				// $( "#posts-<?php echo $this->fieldnumber ?>" ).suggest(posts); //???
				newfield.autocomplete({
					source : posts,
					select: function( event, ui ) {
						var $this = $(this);
						$this.data("id", ui.item.id);
						$this.data("label", ui.item.label);
						$this.data("status", ui.item.status);
						$this.addClass("ready");
						addbtn.removeAttr("disabled");
						ready = true;
					}
				});
				newfield.change(function(){ready = false});
				newfield.blur(function(){
					if (!ready) {// if NOT auto'pleted, clear the field
						newfield.val("");
						newfield.removeClass("ready");
					}
				});
			});
		</script>
		<?php
		$fieldhtml .= ob_get_clean();
		wp_reset_query();
		return $fieldhtml;

	}
	public function validate($newValue = ""){
		return $newValue;
	}
}