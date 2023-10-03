<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$db=new DB();
$query="SELECT filename,filesize,datastore,dd_id,portefeuille,CRM_naw.id,CRM_naw.email,CRM_naw.wachtwoord,CRM_naw.naam,CRM_naw.naam1,
dd_reference.add_date,dd_reference.add_user
FROM dd_reference 
JOIN CRM_naw ON module_id=CRM_naw.id 
WHERE categorie = 'rapportage' and module='CRM_naw'";
$db->SQL($query);
$db->Query();
$pdfKoppelingen=array();
while($data=$db->nextRecord())
{
  $pdfKoppelingen[]=$data;
}
foreach($pdfKoppelingen as $koppeldata)
{
  $query="INSERT INTO portaalQueue values(null,".$koppeldata['id'].",'aangemaakt','','','".mysql_escape_string($koppeldata['portefeuille'])."','".
  mysql_escape_string($koppeldata['email'])."','".mysql_escape_string($koppeldata['wachtwoord'])."','".mysql_escape_string($koppeldata['naam'])."','".mysql_escape_string($koppeldata['naam1'])."','".mysql_escape_string($koppeldata['filename'])."',".
  "(SELECT blobdata FROM ".$koppeldata['datastore']." WHERE id=".$koppeldata['dd_id']." ),'".$koppeldata['add_date']."','".
  $koppeldata['add_user']."','". $koppeldata['add_date']."','".$koppeldata['add_user']."')";
  
  $db->SQL($query);
  $db->Query();
}



?>