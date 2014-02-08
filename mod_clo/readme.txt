The hightlight of this new release are:
  * Add image background support for menu item
  * Enhance to work better with SEF enable
  * Enhance to validate with XHTML
  * Upgrade to jQuery 1.3
  * Enhance for compatible with J1.015 and J1.5.8
  * Minor fix in flyout.css
  * Add param to show new/update image or not
  * Language encoding is now changable, defualt is UTF-8
  * Minor bug fix
	
Description:
	* MosCmenuTree will show you content in a nice menu without the labor of updating each menu item one by one.  MosCmenuTree is a very comprehensive menu system for content compatible with all version of Mambo, Joomla 1.0 series and 1.5 series in Legacy mode

  
Installation:
	* Use Mambo module install manager to do the install of MosCmenuTree. 
	* Make sure you publish this module
	* At the minimum you need to set the section ID so MosCmenuTree has something to show on the menu
	* Optional: You could also include an additional categories to be part of the menu

  
Features:
	* The content will show on Pathway correctly when click on MosCmenuTree item.
	* Any new content will show "New" image to indicate the content is "New" for 14 days
	* Several menu options to choose: dTree, List, ByCat, JSCook, or Flyout with respective css
	* Enhance to able to run more than one copy
  * Add image background support for menu item
	* Enhance to work better with SEF enable
  * Enhance to validate with XHTML
	* There are many general parameters to set, for example:
		# Set to select "Section Order by": Id, Title, Ordering, none (default)
		# Set to select "Category Order by": Id, Title, Ordering
		# Set to select "Articles Order by": Id, Title, Ordering
		# Use Maxlength to set the maximum length of the title to show before it got truncated as the menu item
		# Set to select "ShowArrow" on menu item.
		# Use "useCSS" to set use respective menu type CSS or site default template CSS
		# Set to show or hiden "catid" on URL to be compatible with opensef
		# Option to turn on/off debug code
	

More specific option for each menu type:
	* For dTree option: 
		+ Many parameters to customize the menu look for the menu tree and also via the dtree.css
	* For JSCook option: 
		+ You can change use the theme to change how it look and set the menu orientation.  
		 (Note: JSCook menu with several style option: hbr, hbl, hur, hrl, vbr, vbl, vur, vrl)
	* For ByCatJ option: 
		+ "showitem" to set "Number of Menu Items to show"
			

Addtional Usage:
	* Several new css add for list, bycat, jscook, flyout, dtree so each menu type can be customized
	* Unique id for the purpose use creating several instant of the menu for different section or category in conjunction with mosModule mambots in content or module
	* Steps to show "Upd" image to indicate the content has been update:
		# You need to update "Title Alias" to have a date with the format of "yyyy-mm-dd".
		# "showupdated" field is set with a value then MosCmenuTree will only show article that has been updated recently with the "Title Alias" containing the date value less then num of days
		
	For examples: If "showupdated=14" then "upd" image will only show content that has been update within the last 14 days

Credit:
	* dTree from http://www.destroydrop.com
	* JSCook menu from http://jscook.sourceforge.net/JSCookMenu/
	* Flyout menu from http://www.washington.edu/webinfo/case/flyout/
	* JQuery from http://jquery.com

	* Cem for some css code in flyout.css
	* Mark Joyce for some css code in bycat.css
	* Mark Miller for some contribution with jscookmenu type

Info:
    * Wiki: http://wiki.ongetc.com 
    * Support: http://support.ongetc.com/index.php?option=com_phorum&Itemid=175
    * Download: http://support.ongetc.com/index.php?option=com_content&task=category&sectionid=1&id=149&Itemid=192

Any donation$ would be greatly appreciated. Thanks 