<?php
/**
* @version 
* @package MosCmenuTree - COAddOns for Mambo & Jommla 
* @copyright (C) 2009 Chanh Ong
* @info ongetc@ongetc.com http://ongetc.com
* @license GNU/GPL http:/ongetc.com/gpl.html.
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
# quick fix for old module in 4.5.1
//$params = mosParseParams( $module->params );

$moddir="";

//if (!defined('_MOS_MODULE_LOADED')){
// tiny class to deal with J15 compatibility issue
if (!class_exists('CLOTinyIsJClass')) {
	class CLOTinyIsJClass {
		function CLOTinyIsJClass() { } // empty constructor
		function isJ15() {
			( (defined('JVERSION')) and 
				($this->is1stNewer2nd(substr(JVERSION,0,3),'1.0') ) ) ? $ret=true : $ret=false;
			return $ret;
		}
		function is1stNewer2nd( $first,$second ) {
		   (version_compare($first,$second)=="1") ? $newer=true : $newer=false;
		   return $newer;
		}
		function getModule() {
			($this->isJ15()) ? $ret="mod_clo/" : $ret="";
			return $ret;
		}
	}
}

$clotiny = new CLOTinyIsJClass();
$modDir=$clotiny->getModule();
require_once( "modules/".$modDir."mod_clo/mod_clo.class.php" );
$myMenu = new CO_cMenu(mosParseParams( $module->params), $modDir );
echo $myMenu->main();

?>