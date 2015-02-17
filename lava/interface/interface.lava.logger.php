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
	public function display_logs($echo, $verbose);
	public function display_errors($echo, $verbose);
	public function _log($string);
	public function _error($string);
}