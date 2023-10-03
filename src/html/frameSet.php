<?php
$__appvar["module"] = "";
include_once("wwwvars.php");
$content = array();
$content['style'] = '<link href="style/main.css" rel="stylesheet" type="text/css" media="screen">';
$content['initial_content']=base64_decode($_GET['page']);
echo template('templates/kopZonderMenu.inc',$content);
//echo $__appvar["templateFooter"];exit;
echo template('templates/zonderMenuContent_voet.inc',$content);
//echo template($__appvar["templateFooter"],$content);
?>