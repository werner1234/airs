<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/07/26 07:42:38 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20060614_PREinstall.php,v $
 		Revision 1.1  2006/07/26 07:42:38  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/09 07:50:48  cvs
 		*** empty log message ***
 		


 		Definitiefile voor CRM en ORDER module
*/
echo "start CRM/ORDER configuratie<hr>";
include("wwwvars.php");

$defaults = array(
"DELETE FROM `ae_modulecfg` ;",
"INSERT INTO `ae_modulecfg` VALUES (null, 'CRM', '240834-45652-1357500', '2010-12-31', 'dgc', null, null);",
"INSERT INTO `ae_modulecfg` VALUES (null, 'ORDER', '240834-46258-1357500', '2010-12-31', 'dgc', null, null);"
);


$db = new DB;
for($x=0;$x < count($defaults);$x++)
{
  $db->SQL($defaults[$x]);
  if ($db->Query())
    echo "v";
  else
    echo "X";
}

echo "<hr>script klaar";

?>