<?php
/**
* @version 
* @package COToolKit.php
* @copyright (C) 2007 ongetc.com
* @info ongetc@ongetc.com http://ongetc.com
* @license GNU/GPL http:/ongetc.com/gpl.html.
*/ 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
class CO_Toolkit {
function CO_Toolkit () { // contructor
}

// Test code of my tool kit
/*
$tm=time();
co_prt($tm);
co_prt(co_dte($tm));
co_prt(co_t2days($tm));
co_prt(co_t2days($tm)-1);
co_prt(co_t2days($tm)+1);
$test=co_t2days($tm)-10;
$ttime=co_d2t($test);
co_prt(co_diffdays($tm,$ttime));
co_prt(co_diffdaysindate($tm,-10));
co_prt(co_setnamewithmaxlength("a very long name to see what happen",20));
*/
// function that use low level function from low level
function co_diffdaysindate($tm,$days) { 
	$diff = $this->co_d2t($this->co_t2days($tm)+$days);
	return $this->co_dte($diff);
}
function co_diffdays($tm,$dte) { 
	if ($dte>$tm) { $diff=$dte-$tm; } else { $diff=$tm-$dte;} 
	return $this->co_t2days($diff);
}
function co_mk_imgsrc($img) { 
	($img) ? $output = "<img src=\"$img\" alt=\"image\" />" : $output="";
	return $output;
}
function co_d2t($dte) { return ($dte*60*60*24); }
function co_t2days($tm) { return round($tm/60/60/24); }
function co_dte($tm) { return date( "Y-m-d H:i:s",$tm); }
function co_prt($il) { echo $il."\n<br />"; }

function co_modid($iname) {
	// ID name Randomizer to avoid conflict in case of multiple modules 
	srand ((double) microtime() * 1000000);
	$uniqid = rand( 1, 99 );
	return $iname.$uniqid;
}

}
?>