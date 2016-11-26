<?php
/**
 * mm_changeFieldHelp
 * @version 1.1.2 (2014-05-07)
 * 
 * @desc A widget for ManagerManager plugin that allows to change help text that appears near each document field when the icon or comment below template variable is hovered.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo.plugin.ManagerManager >= 0.7.
 * 
 * @param $params {array_associative|stdClass} — The object of params. @required
 * @param $params['fields'] {string_commaSeparated} — The name(s) of the document field (or TV) this should apply to. @required
 * @param $params['helpText'] {string_html} — The new help text. If equals to '', help element will be hidden. @required
 * @param $params['roles'] {string_commaSeparated} — The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $params['templates'] {string_commaSeparated} — Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/mm_changefieldhelp/1.1.2
 * 
 * @copyright 2012–2014
 */

function mm_changeFieldHelp($params){
	//For backward compatibility
	if (
		!is_array($params) &&
		!is_object($params)
	){
		//Convert ordered list of params to named
		$params = ddTools::orderedParamsToNamed([
			'paramsList' => func_get_args(),
			'compliance' => [
				'fields',
				'helpText',
				'roles',
				'templates'
			]
		]);
	}
	
	//Defaults
	$params = (object) array_merge([
// 		'fields' => '',
// 		'helpText' => '',
		'roles' => '',
		'templates' => ''
	], (array) $params);
	
	global $modx;
	$e = &$modx->Event;
	
	// if the current page is being edited by someone in the list of roles, and uses a template in the list of templates
	if (
		$e->name == 'OnDocFormRender' &&
		useThisRule($params->roles, $params->templates)
	){
		$output = '//---------- mm_changeFieldHelp :: Begin -----'.PHP_EOL;
		
		// Clean up for js output
		$params->helpText = ddTools::escapeForJS($params->helpText);
		
		$output .=
'
$j.each($j.ddMM.makeArray("'.$params->fields.'"), function(){
	var field = $j.ddMM.fields[this],
		helpText = "'.$params->helpText.'";
	
	//If the field exists
	if ($j.isPlainObject(field)){
		//Is this TV?
		if (field.tv){
			var $parent = field.$elem.parents("td:first").prev("td"),
				$parent_comment = $parent.find("span.comment");
			
			if (helpText != ""){
				if ($parent_comment.length == 0){
					$parent.append("<br />");
					$parent_comment = $j("<span class=\'comment\'></span>").appendTo($parent);
				}
				
				$parent_comment.html(helpText);
			}else{
				$parent_comment.hide();
			}
		//Or document field
		}else{
			var $helpIcon = field.$elem.siblings("img[style*=\'cursor:help\']");
			
			if (helpText != ""){
				$helpIcon.attr("alt", helpText);
				$helpIcon.attr("title", helpText);
			}else{
				$helpIcon.hide();
			}
		}
	}
});
';
		
		$output .= '//---------- mm_changeFieldHelp :: End -----'.PHP_EOL;
		
		$e->output($output);
	}
}
?>