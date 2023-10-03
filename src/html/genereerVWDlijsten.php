<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/04/04 15:13:41 $
File Versie					: $Revision: 1.2 $

$Log: genereerVWDlijsten.php,v $
Revision 1.2  2015/04/04 15:13:41  rvv
*** empty log message ***


*/

include_once("wwwvars.php");
include_once('../classes/AE_cls_xls.php');

$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();
echo template($__appvar["templateContentHeader"],$content);
$inp = array ('name' =>"datum_van",'value' =>date("d-m-Y",mktime(0,0,0,date('m'),date('d')-3,date('Y'))),'size'  => "11");

?>
  <form method="POST" name="controleForm">
    <input type="hidden" name="posted" value="true"/>
    <b><?=vt("Genereer VWD-Lijsten")?></b><br><br>

    <div class="formblock">
      <div class="formlinks"> &nbsp;</div>
      <div class="formrechts">
        <input type="submit" value="<?=vt("Aanmaken")?>">
  </form>
  </div>
  </div>
<?

if($_POST['posted']==true)
{
    $db=new DB();
 $query="Select
vwd.identifierVWD,
vwd.FondsImportCode,
vwd.Fonds,
vwd.koersmemo,
vwd.Fondssoort,
CASE
  WHEN RIGHT(vwd.identifierVWD,2) IN('.N','.Q') THEN 'USA'
  WHEN vwd.Fondssoort='OBL' THEN 'OBL'
  WHEN vwd.Fondssoort='OPT' THEN 'OPT'
  ELSE 'AAND'
END
as sortering

from (

SELECT
Fn.FondsImportCode,
Fn.Fonds,
Fn.ISINCode,
Fn.Valuta,
Fn.fondssoort,
Fn.identifierVWD,
Fn.identifierFactSet,
Fn.koersmethodiek,
Fn.koersmemo
from Fondsen Fn where Fn.KoersAltijdAanvragen = 1

UNION

SELECT
Fn.FondsImportCode,
Fn.Fonds,
Fn.ISINCode,
Fn.Valuta,
Fn.fondssoort,
Fn.identifierVWD,
Fn.identifierFactSet,
Fn.koersmethodiek,
Fn.koersmemo
from Fondsen Fn
where (Fn.Einddatum > now() or Fn.Einddatum = '00000000' )
and (Fn.Lossingsdatum > now() or Fn.Lossingsdatum = '00000000' )
and Fn.koersmethodiek <> 0 ) vwd
where vwd.koersmethodiek = 1 
order by sortering, vwd.Fonds ";

  $db->SQL($query);
  $db->Query();
  $csvData=array();
  while($data=$db->nextRecord())
  {
    $type=$data['sortering'];
    if(isset($laatsteType) && $type!=$laatsteType)
    {
      $teller=0;
      $volgNummer='';


    }
    if($teller>=1500)
    {
      $volgNummer++;
      $teller=0;
    } 
    $csvData[$type.$volgNummer][]=array($data['identifierVWD'],$data['FondsImportCode'],$data['Fonds']);
    $teller++;
    $laatsteType=$type;
  }
  
  
  foreach($csvData as $file=>$data)
  {
    $fp = fopen($__appvar['tempdir'].'/'.$file.'.csv', 'w');
    foreach ($data as $fields) 
    {
      fputcsv($fp, $fields,';');
      $teller++;
    }
    fclose($fp);
    echo '<br><a href="showTempfile.php?show=1&filename='.$file.'.csv">'.vt("download").' '.$file.'.csv</a> <br>';
  }
  /*
        $fp = fopen($__appvar['tempdir']'/'.$type.$volgNummer.'.csv', 'w');

      */
    
   
?>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
  <? 
    
  ?>
</form>
</div>
</div>

<?   
echo template($__appvar["templateRefreshFooter"],$content);
}

?>