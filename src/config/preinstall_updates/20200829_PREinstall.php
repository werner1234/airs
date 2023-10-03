<?php

include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("KeuzePerVermogensbeheerder","waarde",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("toelichtingStortOnttr","toelichting",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("signaleringStortingen","toelichting",array("Type"=>"varchar(50)","Null"=>false));


if(function_exists("get_defined_constants"))
{
  $constants=get_defined_constants();
  if($constants['PHP_VERSION'] !='')
    $logdata['phpVersie']=$constants['PHP_VERSION'];
  else
    $logdata['phpVersie']='none';
  if($constants['PHP_OS'] !='')
    $logdata['besturingssysteem']=$constants['PHP_OS'];
  else
    $logdata['besturingssysteem']='none';
  if($constants['OPTIMIZER_VERSION'] !='')
    $logdata['decoderVersie']=$constants['OPTIMIZER_VERSION'];
  else
    $logdata['decoderVersie']='none';
}
$db = new DB;
$query="select version() as versie";
$db->SQL($query);
$data=$db->lookupRecord();
$logdata['mysqlVersie']=$data['versie'];
$logdata['airshost']=php_uname('n');
$logdata['besturingssysteem']=php_uname('s').' | '.php_uname('r');
if(is_readable('/etc/redhat-release'))
  $logdata['besturingssysteem']=file_get_contents('/etc/redhat-release');

$logdata['extraInfo']="VragenTabel:aantal\n";
$tabellen=array('VragenAntwoorden','VragenIngevuld','VragenLijstenPerRelatie','VragenVragen','VragenVragenlijsten');
foreach($tabellen as $tabel)
{
  $query="select count(id) as aantal FROM $tabel";
  $db->SQL($query);
  $db->query();
  $data=$db->nextRecord();
  $logdata['extraInfo'].=str_replace("\r\n"," ",$tabel.":".$data['aantal'])."\n";
}

$log = new  DB(2);
$query="SELECT id FROM bedrijven WHERE  bedrijf = '".$__appvar['bedrijf']."'";
$log->SQL($query);
$data=$log->lookupRecord();
if(isset($data['id']))
{
  $query = "UPDATE bedrijven SET ";
  $where=" WHERE id='".$data['id']."'";
}
else
{
  $query = "INSERT INTO bedrijven SET bedrijf = '".$__appvar['bedrijf']."',";
  $where='';
}
$query  .= "  queuedate = NOW()";
foreach ($logdata as $key=>$data)
{
  $query  .= ", $key = '".mysql_real_escape_string($data)."'";
}

$query  .= " $where";
$log->SQL($query);
$log->query();

?>