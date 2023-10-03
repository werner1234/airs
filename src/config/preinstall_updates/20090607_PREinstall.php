<?php
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Portefeuilles",'Vastetegenrekening',array("Type"=>'varchar(20)',"Null"=>false));

$skipFields=array('id','rel_id','change_user','change_date','add_user','add_date','verzendAanhef','verzendAdres','verzendPc','verzendPlaats','verzendLand');
/*
$fields=array();
$DB=new DB();
$query="SHOW FIELDS FROM CRM_naw_cf";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  if(!in_array($data['Field'],$skipFields))
  {
    $tst->changeField("CRM_naw",$data['Field'],array("Type"=>$data['Type'],"Null"=>false));
    $fields[]=$data['Field'];
  }

}

$records=array();
$query="SELECT rel_id FROM CRM_naw_cf";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  $records[]=$data['rel_id'];
}

foreach ($records as $rel_id)
{
  foreach ($fields as $field)
  {
    $query="SELECT $field FROM CRM_naw WHERE id='$rel_id'";
    $DB->SQL($query);
    $oudeWaarde=$DB->lookupRecord();
    if($oudeWaarde[$field] == '' || $oudeWaarde[$field]='0000-00-00') //Alleen wanneer het doelveld leeg is de waarde kopieeren uit de cf.
    {
      $query="SELECT $field FROM CRM_naw_cf WHERE rel_id='$rel_id'";
      $DB->SQL($query);
      $nieuweWaarde=$DB->lookupRecord();

      $query="UPDATE CRM_naw SET $field='".$nieuweWaarde[$field]."' WHERE id='$rel_id'";
      $DB->SQL($query);
      $DB->Query();
    }
  }
}
*/
?>