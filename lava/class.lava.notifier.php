<?php
class SwappyNotifier implements iSwappyNotifier {
	public $messages = array();
	public function __construct($name = "default-notifier"){
		$this->name = $name;
		add_action("admin_notices", array( $this, "display" ) );
	}
	function add( $msg = "", $type = "update" ){
		array_push( $this->messages, array( 'msg' => $msg, 'type' => $type ) );
	}
	function display(){
		foreach ( $this->messages as $msg ){ ?>

			<div class='<?php echo $this->name; ?>_message <?php echo $msg['type'] ?>'><p><?php echo $msg["msg"] ?></p></div>

		<?php
		}
	}
	function has_messages(){
		return !empty( $this->messages );
	}
}