<?php
/**
 * class SwappyFactory creates a LavaOption based on the type specified
 * @package Lava
 * @author Jameel Bokhari
 * @license GPL22
 */
final class SwappyFactory {
	static public $no = -1;
	static function create($prefix, array $options, $scriptmgmt, &$jsvars ){
		self::$no++;
		$type = $options['type'];
		if (!$type)
			return;
		require_once "class.lava.plugin.option." . $type . ".php";
		$object = "SwappyOption_{$type}";
		$no = self::$no;
		if ( isset( $options['subfield'] ) ) {
			$no .= "_{$options['subfield']}";
		}
		$return = new $object($prefix, $options, $no, $scriptmgmt, $jsvars);
		return $return;

	}
}