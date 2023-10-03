<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/09/23 17:44:09 $
File Versie					: $Revision: 1.2 $

$Log: bepaalOntbrekendeIdentifiers.php,v $
Revision 1.2  2017/09/23 17:44:09  rvv
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
    <b><?=vt("OntbrekendeIdentifiers bepalen")?></b><br><br>
    <div class="form">
      <div class="formblock">
        <div class="formlinks"> <?=vt("Gebruik nieuwe fondsen vanaf")?> </div>
        <div class="formrechts">
          <?=$kal->make_input_field("", $inp, "")?>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts">
          <input type="submit" value="<?=vt("Genereer XLS bestand")?>">
  </form>
  </div>
  </div>
<?

if($_POST['posted']==true)
{
  include('bepaalActieveFondsen.php');
  
  $datum=date('Y-m-d',form2jul($_POST['datum_van']));
  $xls=new AE_xls();
  $db=new DB();
  $query="Select
Nw.Id,
Nw.FondsImportCode,
Nw.Fonds,
Nw.ISINCode,
Nw.Valuta,
Nw.fondssoort,
Nw.identifierVWD,
Nw.identifierFactSet,
Nw.koersmethodiek,
Nw.koersmemo,
Nw.add_date,
Nw.add_user
from (
        ##Alle actieve fondsen
            SELECT
            Fn.*
            from ActieveFondsen af
            inner join Fondsen Fn on af.fonds = Fn.Fonds
            where Fn.koersmethodiek = 0 and koersmemo ='' AND af.InPositie=1
            UNION
    ## NIeuwe Fondsen
            SELECT
            Fn.*
            from Fondsen Fn
            where (Fn.add_date > '$datum')
            ) Nw";

  $db->SQL($query);
  $db->Query();
  $header=array('Id','FondsImportCode','Fonds','ISINCode','Valuta','fondssoort','identifierVWD','identifierFactSet','koersmethodiek','koersmemo','add_date','add_user');
  $xlsData=array();
  $xlsData[]=$header;
  while($data=$db->nextRecord())
  {
    $tmp=array();
    foreach($header as $veld)
      $tmp[]=$data[$veld];
    $xlsData[]=$tmp;  
  }

  $xls->setData($xlsData);
  $xls->OutputXls($__appvar['tempdir'].'/ontbrekendeIdentifiers.xls',true);
   
?>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
  <? 
    echo '<br><a href="showTempfile.php?show=1&filename=ontbrekendeIdentifiers.xls">'.vt("XLS uitvoer").'</a> ontbrekendeIdentifiers.xls<br><br>';
  ?>
</form>
</div>
</div>

<?   

}

