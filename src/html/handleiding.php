<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/07/23 17:24:57 $
 		File Versie					: $Revision: 1.1 $

*/

include_once("wwwvars.php");
$nomenu=true;
include_once("mainmenu.php");

unset($_SESSION['NAV']);

//<script type="text/javascript" src="javascript/menu.js"></script>

$content['jsincludes']='<script type="text/javascript" src="javascript/menu.js"></script>';
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>Handleiding</b>".$subHeader."</div><br><br>";

if($__appvar['master'] ==true)
{
  $mnu->edit=true;
  $_SESSION['submenu'] = New Submenu();
  if($_GET['view']==1)
  {
    $mnu->edit=false;
    $_SESSION['submenu']->addItem('Handleiding bewerken',basename($PHP_SELF)."?view=0");
  }
  else
    $_SESSION['submenu']->addItem('Handleiding bekijken',basename($PHP_SELF)."?view=1");
}
else
  $mnu->edit=false;


echo template($__appvar["templateContentHeader"],$content);

echo ' <style type="text/css">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
/*/*/border:1px solid #000000;/* */
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
}
</style>';

?>

<?
$mnu->buildTree();
echo '<div id="masterdiv">';
echo $mnu->showTree();
echo '</div>';
echo template($__appvar["templateRefreshFooter"],$content);

?>