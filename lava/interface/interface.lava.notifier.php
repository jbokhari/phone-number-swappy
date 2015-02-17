<?php 
interface iSwappyNotifier {
	public function add($msg, $type);
	public function display();
	public function has_messages();

}