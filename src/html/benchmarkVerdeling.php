<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/05/23 13:45:27 $
 		File Versie					: $Revision: 1.7 $

 		$Log: benchmarkVerdeling.php,v $
 		Revision 1.7  2018/05/23 13:45:27  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/08/09 16:09:48  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/07/19 19:24:02  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/12/24 16:33:40  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/08 07:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/06/04 16:12:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/12/05 09:52:09  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2010/11/21 13:04:55  rvv
 		*** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/benchmarkverdelingBerekening.php");
include_once("../classes/benchmarkverdelingBerekeningV2.php");

if($_GET['version']==2)
{
  $berekening = new benchmarkverdelingBerekeningV2();
  $versie=2;
  $benchmarks=$berekening->getBenchmarks();
}
else
{
  $berekening = new benchmarkverdelingBerekening();
  if($_GET['version']=='MM-indices')
  {
    $versie = $_GET['version'];
    $tmp=$berekening->getEuriborFondsen();
    $benchmarks=array();
    foreach($tmp as $benchmarkData)
      $benchmarks[]=$benchmarkData['indexFonds'];
  }
  else
  {
    $versie = 1;
    $benchmarks=$berekening->getBenchmarks();
  }
}

$cfg=new AE_config();
$lockDatum=$cfg->getData('fondskoersLockDatum');

if($_GET['posted'])
{
  
  if($_GET['benchmark'] <> '')
  {
    if($_GET['overschrijven']==1)
    {
      $vanaf=date('Y-m-d',form2jul($_GET['vanaf']));
    }
    else
    {
      $vanaf='';
    }

    if($_GET['version']=='MM-indices')
    {
      $berekening->calulateEuribor($_GET['benchmark'], $vanaf,true);
    }
    else
    {
      $berekening->bereken($_GET['benchmark'], $vanaf);
      $berekening->updateKoersen();
      $txtmelding = $berekening->toonOngecontroleerd();
    }
  }
  else
  {
    $txtmelding="Geen benchmark geselecteerd";
  }
}

echo template($__appvar["templateContentHeader"],$content);



foreach($benchmarks as $benchmark)
{
	$options .= "<option value=\"".$benchmark."\" ".($benchmark==$_GET['benchmark']?"selected":"").">".$benchmark."</option>\n";
}

?>
<form action="benchmarkVerdeling.php" method="GET" name="controleForm">
<input type="hidden" name="posted" value="1" />
<input type="hidden" name="version" value="<?=$versie?>" />

<div class="formblock">
<div class="formlinks">Benchmark <?=$versie?></div>
<div class="formrechts">
<select name="benchmark">
<option value="">--</option>
<?=$options?>
</select>
</div>
</div>


  <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">


<div class="formblock">
<div class="formlinks">Herberekenen koers vanaf</div>
<div class="formrechts">
<input type="checkbox" name="overschrijven" onclick="$('#datumDiv').toggle();" value="1"/>
</div>
</div>

<div class="formblock" id="datumDiv" style="display:none">
<div class="formlinks">Vanaf </div>
<div class="formrechts">
  <input  class="AIRSdatepicker" type="text" size="24" value="<?=date('d-m-Y',db2jul($lockDatum))?>" name="vanaf" id="vanaf" onchange="date_complete(this);">
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" name="submit" value="Verwerken"/>
</div>
</div>

</form>
<?
if(is_array($berekening->error) && count($berekening->error) > 0)
{
  foreach($berekening->error as $melding)
    echo $melding."<br>\n";
  echo $txtmelding;
}


echo template($__appvar["templateRefreshFooter"],$content);

?>