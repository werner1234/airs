<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$templateArray=array(
'rap_m'=>array(),
'rap_k'=>array(),
'rap_h'=>array(),
'rap_j'=>array(),
'verzending'=>array(),
'aantal'=>array());

$db=new DB();
$query="SELECT portefeuille,rapportageVinkSelectie FROM CRM_naw WHERE portefeuille <> '' ";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $vinkselectie=unserialize($data['rapportageVinkSelectie']);
  if(!is_array($vinkselectie))
    $vinkselectie=$templateArray;

  $vinkselectie['verzending']['rap_k']['papier']=0;
	$vinkselectie['verzending']['rap_k']['email']=1;

  $portefeuilles[$data['portefeuille']]=$vinkselectie;
}


foreach ($portefeuilles as $portefeuille=>$vinkselectie)
{
  $query="UPDATE CRM_naw SET rapportageVinkSelectie='".mysql_escape_string(serialize($vinkselectie))."' WHERE portefeuille='$portefeuille'";
  $db->SQL($query);
  $db->Query();
}

?>