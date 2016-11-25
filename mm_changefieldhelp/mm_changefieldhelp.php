<?php
/**
 * mm_changeFieldHelp
 * @version 1.1.2 (2014-05-07)
 * 
 * @desc A widget for ManagerManager plugin that allows to change help text that appears near each document field when the icon or comment below template variable is hovered.
 * 
 * @uses MODXEvo.plugin.ManagerManager >= 0.7.
 * 
 * @param $field {string} — The name of the document field (or TV) this should apply to. @required
 * @param $helptext {string_html} — The new help text. @required
 * @param $roles {string_commaSeparated} — The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $templates {string_commaSeparated} — Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/mm_changefieldhelp/1.1.2
 * 
 * @copyright 2012–2014
 */

function mm_changeFieldHelp(
	$field,
	$helptext = '',
	$roles = '',
	$templates = ''
){
	global $modx;
	$e = &$modx->Event;
	
	if ($helptext == ''){return;}
	
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if (
		$e->name == 'OnDocFormRender' &&
		useThisRule($roles, $templates)
	){
		global $mm_fields;
		
		$output = '//---------- mm_changeFieldHelp :: Begin -----'.PHP_EOL;
		
		// What type is this field?
		if (isset($mm_fields[$field])){
			// Clean up for js output
			$helptext = ddTools::escapeForJS($helptext);
			
			//Is this TV?
			if ($mm_fields[$field]['tv']){
				$output .=
'
$j.ddMM.fields.'.$field.'.$elem.each(function(){
	var $this = $j(this),
		$parent = $this.parents("td:first").prev("td"),
		$parent_comment = $parent.find("span.comment");
	
	if ($parent_comment.length == 0){
		$parent.append("<br />");
		$parent_comment = $j("<span class=\'comment\'></span>").appendTo($parent);
	}
	
	$parent_comment.html("'.$helptext.'");
});
';
			//Or document field
			}else{
				$output .=
'
$j.ddMM.fields.'.$field.'.$elem.each(function(){
	var $this = $j(this),
		$helpIcon = $this.siblings("img[style*=\'cursor:help\']");
	
	$helpIcon.attr("alt", "'.$helptext.'");
	$helpIcon.attr("title", "'.$helptext.'");
});
';
			}
		}
		
		$output .= '//---------- mm_changeFieldHelp :: End -----'.PHP_EOL;
		
		$e->output($output);
	}
}
?>