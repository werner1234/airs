<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/03/02 17:12:41 $
 		File Versie					: $Revision: 1.1 $

 		$Log: telFix.php,v $
 		Revision 1.1  2013/03/02 17:12:41  rvv
 		*** empty log message ***
 		


*/
include_once("wwwvars.php");
$velden=array('tel1','tel2','tel3','tel4','tel5','tel6');
$update='';
foreach($velden as $veld)
{
  if($update <> '')
    $update.=',';
  $update.=" $veld=REPLACE(REPLACE($veld, ' ', ''), '-', '') ";
}
$DB=new DB();
$query="UPDATE CRM_naw SET $update ";

if($_GET['update']=='ja')
{
  $DB->SQL($query);
  if($DB->Query())
    echo "Query uitgevoerd.<br>\n";
}
else
  echo "Klik <a href=\"?update=ja\"> hier </a> Om de onderstaande query uit te voeren. <br>\n $query <br>\n "


?>