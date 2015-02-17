<?php
/**
 * LavaLogging, basic class for error logging and human friendly display
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */

class SwappyLogging implements SwappyLogger {
	public $id;
	public $error = array();
	public $log = array();
	public function __construct($id = "Unknown Object"){
		$this->id = $id;
	}
	public function display_logs( $echo = true, $verbose = false ){
		$html  = "<h3>Logs [$this->id]:</h3>";
		$html .= "<ul>";
		foreach($this->log as $log){
			$html .= "<li>$log</li>";
		}
		$html .= "</ul>";
		if ($echo == true){
			echo $html;
		} else {
			return $html;
		}
	}
	public function display_errors($echo = true, $verbose = false){
		$count = count( $this->error );
		if ($count < 1)
			return;
		$html  = "";
		$html .= "<div class='lava-logging-errors'>";
		$html .= "<h3>Errors [$this->id]:</h3>";
		$html .= "<ul>";
		foreach($this->error as $error){
			$html .= "<li>$error</li>";
		}
		$html .= "</ul>";
		$html .= "</div>";
		if ($echo == true){
			echo $html;
		} else {
			return $html;
		}
	}
	public function _log($string){
		$this->log[] = $string;
	}
	public function _error($string){
		$this->error[] = $string;
	}
}