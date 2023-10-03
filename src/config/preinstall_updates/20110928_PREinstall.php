<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/11/23 19:09:29 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110928_PREinstall.php,v $
 		Revision 1.1  2011/11/23 19:09:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***


*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","bestandsvergoedingEdit",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));

$DB = new DB();
$DB2 = new DB();
$query="SELECT
CRM_selectievelden.waarde,
CRM_selectievelden.omschrijving
FROM
CRM_selectievelden
WHERE
module='evenementen'";
$DB->SQL($query); echo $query."<br>\n";
$DB->Query();
while($data=$DB->nextRecord())
{
  if($data['waarde'] <> '' && $data['omschrijving'] <> '' )
  {
    $q="UPDATE CRM_evenementen SET evenement='".$data['omschrijving']."' WHERE evenement='".$data['waarde']."'";
    $DB2->SQL($q); echo $q."<br>\n";
    $DB2->Query();
    $q="UPDATE CRM_naw_adressen SET evenement='".$data['omschrijving']."' WHERE evenement='".$data['waarde']."'";
    $DB2->SQL($q); echo $q."<br>\n";
    $DB2->Query();

  }
}



?>