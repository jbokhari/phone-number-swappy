<?php 
interface iSwappyNotifier {
	public function add($msg = "", $type = "update");
	public function display();
	public function has_messages();

}