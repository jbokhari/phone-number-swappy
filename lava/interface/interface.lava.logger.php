<?php
/**
 * 
 * Logger interface for loggin object
 * @package Lava
 * @author Jameel Bokhari
 * @license GPL22
 * 
 **/
interface SwappyLogger {
	public function display_logs($echo = true, $verbose = false );
	public function display_errors($echo = true, $verbose = false );
	public function _log($string);
	public function _error($string);
}