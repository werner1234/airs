<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

unlink("../html/rapport/include/RapportVHO_L12.php");

$tst->changeField("Vermogensbeheerders","geenStandaardSector",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","orderControleEmail",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));

$melding='';
$db=new DB();
$query="SELECT `value` as orderEmail FROM ae_config WHERE `field`='orderEmail'";
$db->SQL($query);
$db->query();
while($data=$db->NextRecord())
{
  if($melding <> '')
    $melding.="\n";
  $melding.="orderEmail:[".$data['orderEmail']."]";
}
  
$log = new  DB(2);
$query = "INSERT INTO terugmelding SET ";
$query  .= "  datum = NOW()";
$query  .= ", bedrijf = '".$__appvar['bedrijf']."'";
$query  .= ", txt = '".mysql_escape_string($melding)."'";
$log->SQL($query);
$log->query();

?>