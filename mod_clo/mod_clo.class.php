<?php 
/**
* @version 
* @package MosCmenuTree - COAddOns for Mambo & Jommla 
* @copyright (C) 2009 ongetc.com
* @info ongetc@ongetc.com http://ongetc.com
* @license GNU/GPL http:/ongetc.com/gpl.html.
*/ 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
class CO_cMenu {
var $myToolkit;
var $cfg;
function CO_cMenu($params, $modDir="") {  // constructor
	$this->params=$params;
	// general vars
//	$this->cfg['clopath'] = "modules/".$this->getModule()."mod_clo/";
	$this->cfg['livesite'] = $this->fixDupInPathRtrim($GLOBALS['mosConfig_live_site'])."/";
	$this->cfg['clopath'] = "modules/".$modDir."mod_clo/";
	$this->cfg['livesitepath'] = $this->cfg['livesite'].$this->cfg['clopath'];
	require_once( $this->cfg['clopath']."cotoolkit.php" );
	$this->myToolkit = new CO_Toolkit();
	return;
}
function main() {
	global $database;
	$output="";
	$this->mk_sqlStr();
	$database->setQuery( $this->cfg['query'] );
	$rows = $database->loadObjectList();
	if ($rows) {
	  switch ($this->params->ltype) {
	    case "bycat": 
			$output = $this->co_mk_bycat($rows); 
			break; 
	    case "bycatj": 
			$output = $this->co_mk_bycatj($rows); 
			break; 
	    case "dtree": 
			$output = $this->co_mk_tree($rows); 
			break;
	    case "jscookmenu": 
			$output = $this->co_mk_jscook($rows); 
			break;
	    case "flyout": 
			$output = $this->co_mk_flyout($rows); 
			break;
	    case "list": 
	    default : 
			$output = $this->co_mk_list($rows); 
	  }
	}

  $link = '<script src="%s" type="text/javascript"></script>';
  $filepath = $this->cfg['livesitepath']."mosclojquery.js";
  $url = $this->toHead(sprintf($link, $filepath));
//  echo "filepath".$filepath;
//  echo "<br />url:".$url;
  
//	$ohdr = "<script type='text/javascript' src='".$this->cfg['livesitepath']."mosclojquery.js'></script>
	$ohdr = "
	<script type='text/javascript'>
	var \$mosclo = jQuery.noConflict();
	</script>";
	echo "\n<div class='clo_outter'>".$ohdr.$output."</div>";
	if ($this->params->debug == "1") {
		echo $this->debug($rows,"yes");
		echo $this->debug($this->cfg['query'],"yes");
	}
}
function co_escEntities($istr) { return htmlentities($istr, ENT_QUOTES,$this->params->encoding); }
// low level function w/o dependency
function co_setnamewithmaxlength($iname,$imax) {
//  $iname=stripslashes($iname);
  if (get_magic_quotes_gpc()) $iname = stripslashes($iname);
  if (strlen($iname) > $imax and $imax>0) { $iname = substr($iname,0,$imax-4)." ...";   }
  if (get_magic_quotes_gpc()) $iname = addslashes($iname);	
	$iname = $this->co_escEntities($iname);
//  return addslashes($iname);
  return $iname;
}
function debug($ivar,$hide="no") {
($ivar) ? $output="<pre>".print_r($ivar,true)."</prev>" : $output="";
if ($hide=="yes" and $output) 
	$output="<!-- $output //-->";
return $output;
}
function mk_sqlStr() {
	$selsecid=$selcatid=$orderby="";
	if ($this->params->secid != "") { 
		$selsecid = "c.id in (".$this->params->secid.")"; 
	}
	if ($this->params->catid != "") { 
		$selcatid = "OR a.catid in (".$this->params->catid.")"; 
	}
  
	if ($selsecid != "" or $seccatid!= "") 
		$secorcat = "\nAND (" . $selsecid . $selcatid . ")";

	if ($this->params->secorderby != "") { 
		$orderby = "c.".$this->params->secorderby.", "; 
	}
  
  global $mosConfig_offset;
  $now = date( "Y-m-d H:i:s", time()+$mosConfig_offset*60*60 );
  if ($this->params->showupdated != "") { $tmpshowdate = "\nAND (a.title_alias >= '". $this->myToolkit->co_diffdaysindate(time(),-($this->params->showupdated)) ."')";}
  else { $tmpshowdate="";  }
  
//  $this->cfg['query'] = "SELECT c.title as section, c.id as sectionid, b.id as categoryid, b.title as name, a.catid, a.id, a.title, a.title_alias as cmsfolder, UNIX_TIMESTAMP(a.created) AS created, UNIX_TIMESTAMP(a.modified) AS modified, c.id as sid FROM #__content as a, #__categories as b, #__sections as c WHERE (a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '".$now."') AND (a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '".$now."') AND a.state='1' AND a.checked_out='0' ".$secorcat . $tmpshowdate . " AND c.id=a.sectionid AND c.id=b.section AND a.catid=b.id ORDER BY b.".$this->params->catorderby." ASC, a.".$this->params->articlesorderby." ASC";

  	//    . "\nWHERE (a.id IS NULL) OR ("
  $this->cfg['query'] =  "SELECT c.title as section, c.id as sectionid, b.id as categoryid, b.title as name, a.catid, a.id, a.title, a.title_alias as cmsfolder, UNIX_TIMESTAMP(a.created) AS created, UNIX_TIMESTAMP(a.modified) AS modified, c.id as sid     FROM #__sections as c  LEFT JOIN #__categories as b ON b.section = c.id LEFT JOIN #__content as a ON a.sectionid = c.id AND a.catid = b.id WHERE ( (a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '".$now."') AND (a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '".$now."') AND a.state='1' AND a.checked_out='0')" . $secorcat . $tmpshowdate."  ORDER BY $orderby b.".$this->params->catorderby." ASC, a.".$this->params->articlesorderby." ASC";

//  $this->cfg['query'] =  "SELECT b.id as categoryid, b.title as name, a.catid, a.id, a.title, a.title_alias as cmsfolder, UNIX_TIMESTAMP(a.created) AS created, UNIX_TIMESTAMP(a.modified) AS modified, c.id as sid     FROM #__sections as c  LEFT JOIN #__categories as b ON b.section = c.id LEFT JOIN #__content as a ON a.sectionid = c.id AND a.catid = b.id WHERE ( (a.publish_up = '0000-00-00 00:00:00' OR a.publish_up <= '".$now."') AND (a.publish_down = '0000-00-00 00:00:00' OR a.publish_down >= '".$now."') AND a.state='1' AND a.checked_out='0')" . $secorcat . $tmpshowdate."  ORDER BY b.".$this->params->catorderby." ASC, a.".$this->params->articlesorderby." ASC";

  return $this->cfg['query'];
}

function setCSS($fname) {
  ($this->params->useCSS == "1") 
//  ? $usecss="<link rel='StyleSheet' href='".$this->cfg['livesitepath'].$fname."' type='text/css' />"
  ? $usecss=$this->cssLink($this->cfg['livesitepath'].$fname)
  : $usecss="";
return $usecss;
}

function get_catname($irows) {
$output="";
	$name = $this->co_setnamewithmaxlength($irows[0]->name,$this->params->maxlength);
	switch ($this->params->ltype) {
		case "bycat": 
		case "bycatj": 
			$output = "<p class='clocat'>$name</p>";
			break;
		case "dtree": 
		case "jscookmenu": 
		case "flyout": 
			$output = $name;
			break;
		case "list": 
		default : 
	}
	return $output;
}

function process_rows($irows,$clomodid) {
	if ( ($this->params->secorderby == "") // not by section
		or ( ($this->params->secorderby <> "") and 
			($this->params->ltype == "jscookmenu" or $this->params->ltype == "dtree")  ) // or by section but not dtree or jscook
	) {
		$output = $this->processByCategory($irows,$clomodid);
	} else {
		$output = $this->processBySection($irows,$clomodid);
	}
	return $output;
}

function processBySection($irows,$clomodid) {
	$secname=$onesection=$output="";
	foreach ($irows as $irow){
		if ( ($secname <> $irow->section) ) {
			$secname = $irow->section;
			$output .= $this->process_onesection($onesection,$clomodid);
			$onesection=""; // clear the section to process the next section
		}
		$onesection[]=$irow;
	}
	$output .= $this->process_onesection($onesection,$clomodid);	// get the last group
	return $output;
}

function process_onesection($irows,$clomodid) {
	$secname=$catname=$output=$onecat=$irow=$catimgid=$newcat="";
	if ($irows) {
		$output = "
<div class='clobysec'>
<p class='closec'>".$irows[0]->section."</p>
".$this->processByCategory($irows,$clomodid)."
</div>"; 
	}
	return $output;
}
		
function processByCategory($irows,$clomodid) {
	$secname=$catname=$output=$onecat=$irow=$catimgid=$newcat="";
	$lineid=1;
	foreach ($irows as $irow){
		if ($catname <> $irow->name) {
			$catname = $irow->name;
			if ($onecat) {
				$output .= $this->process_onecat($catimgid,$onecat,$clomodid,$lineid);
			}
		} 
		$onecat[]=$irow;
	}
	$output .= $this->process_onecat($catimgid,$onecat,$clomodid,$lineid);	// get the last group
	return $output;
}

function process_onecat(&$catImgId,&$irows,$clomodid,&$lineid) {
	if (!$irows) return;
	$output=$buff=$catName=$i=$tohide=$lessMore=$catid="";
	
	$target=$itmid="";	// jscook
	$newcat=true;
	$catImgId=$this->get_nextImgId($catImgId);
	
    $catid = $lineid; // dtree
	foreach ($irows as $irow){
		switch ($this->params->ltype) {
			case "bycatj": 
				if ($newcat==true && $i>=$this->params->showitem) { 
					$lessMore = $this->mk_lessAndMoreBar($irow);
					$newcat=false;
				} 
				if ($lessMore) {	// after less more links
					$tohide .= $this->co_get_href_and_img($irow,$catImgId);
				} else { // before less more links
					$output .= $this->co_get_href_and_img($irow,$catImgId);
				}
				$i++;
				break;
			case "list": 
			case "bycat": 
			case "flyout": 
				$output .= $this->co_get_href_and_img($irow,$catImgId);
				break;
			case "jscookmenu": 
				$catname = $irow->name;
				$name = $this->co_setnamewithmaxlength($irow->title,$this->params->maxlength);
				
				if ($itemid=$this->mc_getItemid($irow)) { $itmid = "&amp;Itemid=".$itemid; }
				$url = sefRelToAbs($this->cfg['livesite']."index.php?option=com_content&amp;task=view&amp;id=".$irow->id.$itmid);
				$icon = $this->co_new_or_upd($irow->created,$irow->cmsfolder);
				// detail menu item
				$output .= "['$icon', '$name', '$url', '$target', '$name'],\n";
				break;
			case "dtree": 
				$url = "''";
				$title = "''";
				$target = "''";
				$icon = "''";
				$iconOpen = "''";
				$openorclose = "false";

				$catname = $irow->name;
				$lineid = $lineid + 1;
				$name = "'".$this->co_setnamewithmaxlength($irow->title,$this->params->maxlength)."'";
				$url = "'".$this->co_set_url($irow)."'";
				$img = $this->co_set_new_or_upd($irow->created,$irow->cmsfolder);
				($img == "") ? $icon = "'none'" : $icon = "'".$img."'";
				$output .=  $clomodid.".add($lineid,$catid,$name,$url,$title,$target,$icon,$iconOpen,$openorclose);\n";
				break;
			default : 
		}
	}
	$catName=$this->get_catname($irows);
	switch ($this->params->ltype) {
		case "bycatj": 
			if ($tohide) {
				$output = $output.$lessMore."<div class='clotohide' id='clotohide".$irows[0]->catid."'>$tohide</div>";
			}
			$output = "\n<div class='clobycat'>$catName$output</div>"; 
			break;
		case "list": 
		case "bycat": 
			$output = "\n<div class='clobycat'>$catName$output</div>"; 
			break;
		case "flyout": 
			$cmenuname=$clomodid.$irows[0]->catid;  // make sure it is unique
			$output = "
<div class=\"clomenu_flyout\">
<a id=\"$cmenuname\" href=\"javascript:void(0)\" onmouseover=\"mIn ('$cmenuname')\" onmouseout=\"mOut ('$cmenuname')\"  class=\"clomenui_flyout\">$catName</a>
<div id=\"l_".$cmenuname."\" class=\"flyout clo\">
<div class='clomenuh_flyout'>$catName</div>$output</div></div>"; 
			break;
		case "jscookmenu": 
			$url = sefRelToAbs($this->cfg['livesite']."index.php?option=com_content&amp;task=category&amp;sectionid=".$irows[0]->sectionid."&amp;id=".$irows[0]->categoryid);
			// ['icon', 'title', 'url', 'target', 'description', 'class'],  // a menu item 
			// category header
			$output = substr($output,0,strlen($output)-2); // strip out the last comma and line feed
			$output = "\n['', '$catName', '$url', '', '$catName',\n".$output."\n],_cmSplit,";
			break;
		case "dtree": 
			$lineid = $lineid + 1;
		    $name = "'".$catName."'";
		    $output =  "\n".$clomodid.".add($catid,0,$name,'','','','','',$openorclose);\n".$output;
			break;
		default:
	}

	$irows="";	// clear the buffer for the next batch of items
	return $output;
}

function co_mk_flyout($irows) {
	global $mosConfig_live_site;
	$itmid=$output=$ohdr="";
	$livesite=$GLOBALS['mosConfig_live_site'];
	$ohdr = $this->setCSS("flyout.css")."
<script type='text/javascript' src='".$this->cfg['livesitepath']."flyout.js'></script>
<script type='text/javascript' >
var newDefs = new Object;
newDefs.useclass = \"menutext\";
flyDefs (newDefs);
</script>
";
	$clomodid=$this->myToolkit->co_modid("clo_flyout_");
	$output = $this->process_rows($irows,$clomodid);
	$output = "<div class='clomodule' id='".$clomodid."'>".$ohdr.$output."</div>";
	return $output;
}

function co_mk_setpath($path) {
	$livesite=$this->fixDupInPathRtrim($GLOBALS['mosConfig_live_site']);
	$path = str_replace("%LS%", $livesite, $path);
	$path = str_replace("%MOD%", $this->cfg['clopath'], $path);
	$path = str_replace("%TEM%", $GLOBALS['cur_template'], $path);
	return $path;
}

function co_get_currentId() {
	global $database;
	$task = mosGetParam( $_REQUEST, 'task', '' );
	$currentId=array();
	$currentId['catid'] = 0;
	$currentId['aid'] = 0;

	switch ( $task ) {
		case 'category':
			$currentId['catid'] = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
			break;
		default:
			$currentId['aid'] = intval( mosGetParam( $_REQUEST, 'id', 0 ) );
			if ($currentId['aid'] > 0) {
				$query = "SELECT catid"
				. "\n FROM #__content"
				. "\n WHERE id = " . (int) $currentId['aid'];
				$database->setQuery( $query );
				$currentId['catid'] = $database->loadResult();
			}
			break;
	}
	return $currentId;
}

function co_mk_jscook($irows) {
	global $mosConfig_live_site;
	global $database;

	$itmid="";
	$livesite=$GLOBALS['mosConfig_live_site'];
	$output="";

	$clomodid=$this->myToolkit->co_modid("clo_jscook");
	$themejs = $this->co_mk_setpath($this->params->jscookthemefile);
	$ohdr = $this->setCSS("jscook.css")."
<script type='text/javascript' src='".$this->cfg['livesitepath'].$this->params->ltype.".js'></script>
<script type='text/javascript' src='".$themejs."'></script>
";
/*
	$catname = '';
	$name = '';
	$icon = '';
	$imgid=$lineid="";
*/
	$currentId=$this->co_get_currentId();
	$output = $this->process_rows($irows,$clomodid);
	
	$jsoutput="<script type='text/javascript'>
var m_".$clomodid." = \n[".$output."\n];
cmDraw ('".$clomodid."', m_".$clomodid.", '".$this->params->jscookmenutype."', cmThemeOffice, 'ThemeOffice');
</script>
";
	return "<div class='clomodule' id='".$clomodid."'>".$ohdr.$jsoutput."</div>";
}

function co_mk_tree($irows) {
	// dtree vars
	$this->cfg['openAll'] = $this->params->openAll;
	$this->cfg['basetext'] = $this->params->basetext;
	$output="";
	$clomodid="clo_dtree_".$this->params->moduleid;
	$output = $this->setCSS("dtree.css")."
<script type='text/javascript' src='".$this->cfg['livesitepath']."dtree.js'></script>
<p align=\"center\"><a href=\"javascript: ".$clomodid.".openAll();\">Open All</a> | <a href=\"javascript: ".$clomodid.".closeAll();\">Close All</a></p>
<script type=\"text/javascript\">
var $clomodid = new dTree('$clomodid','".$this->cfg['livesitepath']."');
$clomodid.config.useSelection=".$this->params->useSelection.";
$clomodid.config.useLines=".$this->params->useLines.";
$clomodid.config.useIcons=".$this->params->useIcons.";
$clomodid.config.useStatusText=".$this->params->useStatusText.";
$clomodid.config.closeSameLevel=".$this->params->closeSameLevel.";
$clomodid.add(0,-1,'MosCmenuTree','http://ongetc.com','','');
".
$this->process_rows($irows,$clomodid)."
document.write(".$clomodid.");
//document.write($clomodid.toString());
</script>
";
	return "<div class='clomodule' id='$clomodid'>$output</div>";
}

function get_nextImgId($imgid) {			
	$imgid = $imgid + 1;
	if ($imgid==10) {
		$imgid=rand(1,10);
	}
	return $imgid;
}

function co_mk_bycat($irows) {
	$secname=$catname=$output=$ohdr=$onecat=$irow=$catid="";
	$clomodid=$this->myToolkit->co_modid("clo_bycat_"); 
	$ohdr = $this->setCSS("bycat.css");
	$output = $this->process_rows($irows,$clomodid);
	return "<div class='clomodule' id='".$clomodid."'>".$ohdr.$output."</div>";
}

function co_mk_bycatj($irows) {
	$output=$catname=$buff=$ohdr="";
	$i=$catid=$catimgid=0;
	$newcat=true;
	$ohide="";

	$id = mosGetParam( $_REQUEST, 'id', 0 );
	$cat = mosGetParam( $_REQUEST, 'catid', 0 );

	$clomodid=$this->myToolkit->co_modid("clo_bycatj_"); 
	$ohdr = $this->setCSS("bycatj.css");
	$icatid=0;
	foreach ($irows as $irow){
		if ($catname=="" or $catname <> $irow->name) {
			$icatid = $icatid + 1;
			$catname = $irow->name;
			if ($id==0 || ($irow->id<>$id && $irow->catid<>$cat)) { // hide only what need to hide
				$buff .= "\$mosclo('div#clotohide".$irow->catid."').hide();";
			} 
			$buff .= $this->mk_bycatj_showHide($irow);
		}
	}
	$ohdr .= "
<script type='text/javascript'>
\$mosclo(document).ready(function(){
$buff
});
</script>
";

	$output = $this->process_rows($irows,$clomodid);
	return "<div class='clomodule' id='".$clomodid."'>".$ohdr.$output."</div>";
}

function mk_bycatj_showHide($irow) {
return "
\$mosclo('a#closhowme".$irow->catid."').click(function(){
\$mosclo('div#clotohide".$irow->catid."').show();
return false;
});
\$mosclo('a#clohideme".$irow->catid."').click(function(){
\$mosclo('div#clotohide".$irow->catid."').hide();
return false;
});
";
}

function mk_lessAndMoreBar($irow) {
	$imgup = sefRelToAbs($this->cfg['livesitepath']."images/up.gif"); 
	$imgdown = sefRelToAbs($this->cfg['livesitepath']."images/down.gif"); 
	$imgup = $this->myToolkit->co_mk_imgsrc($imgup);
	$imgdown = $this->myToolkit->co_mk_imgsrc($imgdown);
	$tviewlessurl = "$imgup <a href=\"".sefRelToAbs($this->co_set_url($irow))."#\" id='clohideme".$irow->catid."'>Less...</a>";
	$tviewallurl = "$imgdown <a href=\"".sefRelToAbs($this->co_set_url($irow))."#\" id='closhowme".$irow->catid."'>More...</a>";
	$output = "<p class='cloviewall'>".$tviewlessurl."&nbsp;".$tviewallurl."</p>";
	return $output;
}

function co_mk_list($irows) {
	$catid=$catname=$secname=$buff=$ohdr="";
	$output="";
	$clomodid=$this->myToolkit->co_modid("clo_list_"); 
	$ohdr = $this->setCSS("list.css");
	$output = $this->process_rows($irows,$clomodid);
	return "<div class='clomodule' id='".$clomodid."'>".$ohdr.$output."</div>";
}

function co_get_href_and_img($irow,$catid) { 
	$iname=$this->co_set_href_and_img($irow,$catid);
	if (get_magic_quotes_gpc()) $iname = stripslashes($iname);
	return "\n<div class='clolink'>$iname</div>"; 
}

function co_set_href_and_img($irow,$catid) {
	$id = mosGetParam( $_REQUEST, 'id', 0 );
	$newimg = $this->co_new_or_upd($irow->created,$irow->cmsfolder);
	$catimg="";
	
	if ($this->params->showArrow=="1") {
		$catimg = sefRelToAbs($this->cfg['livesitepath']."arrow/".$this->co_set_image($catid));
//		$catimg = $this->cfg['clopath']."arrow/".$this->co_set_image($catid); 
		$catimg = $this->myToolkit->co_mk_imgsrc($catimg)." ";
	} 
	$iname=$this->co_setnamewithmaxlength($irow->title,$this->params->maxlength);
	($irow->id==$id) ? $activemenu=" id='cloactive'" : $activemenu="";
	$ret = "$catimg<a $activemenu href=\"".$this->co_set_url($irow)."\">$iname $newimg</a>";
	return $ret;
}

function co_set_url($irow) {
	$itmid=$cat="";
	if ( ($irow->catid) && ($this->params->showCatid == "1") ) { 
		$cat="&amp;catid=$irow->catid";
	} 
	if ($itemid=$this->mc_getItemid($irow)) { $itmid = "&amp;Itemid=".$itemid; }
	$co_index = $this->cfg['livesite']."index.php?option=com_content&amp;task=view&amp;id=$irow->id".$itmid.$cat;
	return sefRelToAbs($co_index);
}

//  set the image file for each content category 
function co_set_image($pcat=0) {
	($pcat<10 and $pcat>-1) ? $inum=$pcat : $inum=0;
	return "arrow$inum.gif"; 
}

function co_set_new_or_upd($newdate,$upddate) {
  $img = "";
  if ($this->params->shownew == 0) return $img;
//  if ($this->co_more_than_14days($newdate) == 0) $img = sefRelToAbs($this->cfg['livesitepath'].$this->cfg['clopath']."images/new.gif");  
  if ($this->co_more_than_14days($newdate) == 0) $img = sefRelToAbs($this->cfg['livesitepath']."images/new.gif");
  else {
    if ($upddate <> "") {
      $change_date = strtotime($upddate);
      $more_than_14days = $this->co_more_than_14days($change_date);
      if ($more_than_14days == 0) { $img = sefRelToAbs($this->cfg['livesitepath']."images/upd.gif"); }
    }
  }
  return $img;
}    

function xco_new_or_upd($newdate,$upddate) {
	$output="";
	$newimg = $this->co_set_new_or_upd($newdate,$upddate);
	if ($newimg <> "") { $output = $this->myToolkit->co_mk_imgsrc($newimg); }
	return $output;
}

function co_new_or_upd($newdate,$upddate) {
	($newimg = $this->co_set_new_or_upd($newdate,$upddate)) ? $output = $this->myToolkit->co_mk_imgsrc($newimg): $output="";
	return $output;
}

function co_more_than_14days($idate) {
  $diff = $this->myToolkit->co_diffdays(time(),$idate);
  if ($diff < 14)  $diff = 0; 
  return $diff;
}

function mc_getItemid($row) {
	global $mainframe;
	global $type;
	$bs=$bc=$gbs="";
	if ( ( $type == 1 ) || ( $type == 3 ) ) {
		$bs 	= $mainframe->getBlogSectionCount();
		$bc 	= $mainframe->getBlogCategoryCount();
		$gbs 	= $mainframe->getGlobalBlogSectionCount();
	}

	// get Itemid
	switch ( $type ) {
		case 2:
			$query = "SELECT id"
			. "\n FROM #__menu"
			. "\n WHERE type = 'content_typed'"
			. "\n AND componentid = " . (int) $row->id
			;
			$database->setQuery( $query );
			$Itemid = $database->loadResult();
			break;

		case 3:
			if ( $row->sectionid ) {
				$Itemid = $mainframe->getItemid( $row->id, 0, 0, $bs, $bc, $gbs );
			} else {
				$query = "SELECT id"
				. "\n FROM #__menu"
				. "\n WHERE type = 'content_typed'"
				. "\n AND componentid = " . (int) $row->id
				;
				$database->setQuery( $query );
				$Itemid = $database->loadResult();
			}
			break;

		case 1:
		default:
			$Itemid = $mainframe->getItemid( $row->id, 0, 0, $bs, $bc, $gbs );
			break;
	}

	return $Itemid;
}  

function fixDupInPathRtrim($ivar,$ds='/') { 	// mambo h:/path/mos joomla path/joomla
	str_replace($ds.$ds, $ds, $ivar);	// remove dup
	return rtrim($ivar,$ds);	// trim last slash
}

  function toHead($url) {
    global $mainframe;
    global $_VERSION;
    $return = "";
    if (function_exists("addCustomHeadTag")) {
      addCustomHeadTag($url);
    }
    else if (method_exists($mainframe,"addCustomHeadTag") ) {
      if ($_VERSION->PRODUCT == "Joomla!" and !$this->isJ15()) {
        echo $url;  // work around J1.015 problem
        $return = $url;
      }
      else $mainframe->addCustomHeadTag($url);
    }
    else {
      $return = $url;
    }
    return $return;
  }
  function cssLink($cssURL) {
    $css = sefRelToAbs($cssURL);
    $link = '<link href="%s" rel="stylesheet" type="text/css" />';
    $url = sprintf($link, $css);
    return $this->toHead($url);
  }
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
// end of CO_cMenu
?>