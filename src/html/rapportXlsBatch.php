<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/10/20 18:03:18 $
 		File Versie					: $Revision: 1.9 $

 		$Log: rapportXlsBatch.php,v $
 		Revision 1.9  2018/10/20 18:03:18  rvv
 		*** empty log message ***
 		
*/

$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");
include_once('../classes/excel/Writer.php');
$selectie=new selectOptie();


echo template($__appvar["templateContentHeader"],$content);

if($_POST['posted']=='true')
{
  include_once("../classes/AE_cls_fpdf.php");
  include_once("../classes/portefeuilleSelectieClass.php");
  include_once("rapport/PDFRapport.php");
  include_once("rapport/PDFOverzicht.php");
	include_once("rapport/Managementoverzicht.php");
  include_once("rapport/Vermogensverloop.php");
  include_once("rapport/PortefeuilleParameters.php");
  include_once("rapport/rapportIXPbatch.php");
  include_once("rapport/rapportIXPbatch2.php");

  $selectData=$_POST;
	$selectData['datumVan'] 							= form2jul($_POST['datumVan']);
	$selectData['datumTm'] 								= form2jul($_POST['datumTm']);
 // $selectData['VermogensbeheerderVan']  = $selectData['vermogensbeheerder'];
 // $selectData['VermogensbeheerderTm']   = $selectData['vermogensbeheerder'];
  $selectData['typeInvoer']             = 'alles';
  $selectData['invoer']                 = 'alles';
  $selectData['uitvoer']                = 'categorien';
  $selectData['filterType']             = 'nietGelijk';
  $selectData['filterWaarde']           = 0;
  $selectData['percentages']            = 'true';
  $selectData['portefeuilleIntern']     = "10";

  $vermogensbeheerderSelected=array();
  foreach($selectData['Vermogensbeheerder'] as $verm=>$checked)
  {
    if($checked==1)
      $vermogensbeheerderSelected[]=$verm;
  }
  
 // $selectData['PortefeuilleVan'] = 'AAB_123456';
  //$selectData['PortefeuilleTm']  = 'AAB_123456';
  
  $prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();   
  $filenames=array();

  if($_POST['periode']=='maanden')
   $perioden=getMaanden($selectData['datumVan'],$selectData['datumTm']);
  else
   $perioden[]=array('start'=>date('Y-m-d',$selectData['datumVan']),'stop'=>date('Y-m-d',$selectData['datumTm'])); 
 
  if($_POST['oneXls']==1)
    $oneXls=1;
  else
    $oneXls=0;  
  
  $alleXlsData=array();
  foreach($perioden as $periode)
  {
    $selectData['datumVan'] = db2jul($periode['start']);
    $selectData['datumTm']  = db2jul($periode['stop']);
 	  $filename = "batch_".implode("_",$vermogensbeheerderSelected)."_".$periode['start']."_".$periode['stop'].".xls";
    $workbook = new Spreadsheet_Excel_Writer($__appvar['tempdir'].$filename);
    $i=0;
    if($_POST['PortefeuilleParameters']==1)
    {
      logScherm("PortefeuilleParameters ".$periode['start']."->".$periode['stop'],true);
      $worksheet[$i] =& $workbook->addWorksheet('PortefeuilleParameters');
 			$rapport = new PortefeuilleParameters( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar =  &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
      if($oneXls==1)
         $alleXlsData['PortefeuilleParameters'][$periode['start']."->".$periode['stop']]=$rapport->pdf->excelData;
      else   
         $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      $i++;
    }
    if($_POST['Managementoverzicht']==1)
    {
      logScherm("Managementoverzicht ".$periode['start']."->".$periode['stop'],true);
      $worksheet[$i] =& $workbook->addWorksheet('Managementoverzicht');
  		$rapport = new Managementoverzicht( $selectData );
      $rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
      if($oneXls==1)
        $alleXlsData['Managementoverzicht'][$periode['start']."->".$periode['stop']]=$rapport->pdf->excelData;
      else  
        $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      $i++;
    }
    if($_POST['Vermogensverloop']==1)
    {
      logScherm("Vermogensverloop ".$periode['start']."->".$periode['stop'],true);
      $worksheet[$i] =& $workbook->addWorksheet('Vermogensverloop');
 			$rapport = new Vermogensverloop( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport(); 
      if($oneXls==1)
        $alleXlsData['Vermogensverloop'][$periode['start']."->".$periode['stop']]=$rapport->pdf->excelData;
      else  
        $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      $i++;
    }
    if($_POST['IXP']==1)
    {
      logScherm("IXP ".$periode['start']."->".$periode['stop'],true);
      $worksheet[$i] =& $workbook->addWorksheet('IXP');
 			$rapport = new rapportIXPbatch( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport(); 
      if($oneXls==1)
        $alleXlsData['IXP'][$periode['start']."->".$periode['stop']]=$rapport->pdf->excelData;
      else  
        $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      $i++;
    }
    if($_POST['IXP2']==1)
    {

      logScherm("IXP2 ".$periode['start']."->".$periode['stop'],true);
      $worksheet[$i] =& $workbook->addWorksheet('IXP');
      $rapport = new rapportIXPbatch2( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      if($oneXls==1)
      {
        $alleXlsData['IXP'][$periode['start'] ."->".$periode['stop']] = $rapport->pdf->excelData;
        $alleXlsData['IXP2'][$periode['start']."->".$periode['stop']] = $rapport->pdf->excelData2;
      }
      else
      {
        $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
        $rapport->pdf->excelData=$rapport->pdf->excelData2;
        $i++;
        $worksheet[$i] =& $workbook->addWorksheet('IXP2');
        $rapport->pdf->fillXlsSheet($worksheet[$i], $workbook);
      }
      $i++;
    }

    $workbook->close();
    $filenames[]=$filename;
    //echo "<br><a href='showTempfile.php?show=1&filename=".$filename."'><b> download $filename </b></a><br>\n";
  }
  echo "<br>";
  if($oneXls==1)
  {
    $rapport = new rapportIXPbatch( $selectData );
    $filenames=array();
    $filename = "batch_".implode("_",$vermogensbeheerderSelected)."_alles.xls";
    $workbook = new Spreadsheet_Excel_Writer($__appvar['tempdir'].$filename);
    $filenames[]=$filename;
    $rapport->pdf->excelOpmaak['getal']=array('setNumFormat'=>'2');
    foreach($alleXlsData as $rapportNaam=>$periodeData)
    {
      $i=0;
      $worksheet[$i] =& $workbook->addWorksheet($rapportNaam);
      $tmpXls=array();
      foreach($periodeData as $periode=>$xlsRegels)
      {
        $tmpXls[]=array($periode); 
        foreach($xlsRegels as $regel)
        {
          $tmpXls[]=$regel;
        }
      }
      $rapport->pdf->excelData=$tmpXls;
      $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      $i++;
    }
    $workbook->close();
    
  }
  
  foreach($filenames as $filename)
  {
    echo "<a href='showTempfile.php?show=1&filename=" . $filename . "'><b> " . vt("download") . " $filename </b></a><br>\n";
  }
  exit();  
}

/*
$vermogensbeheerders='<div class="formblock">
<div class="formlinks"> Vermogensbeheerder </div>
<div class="formrechts">
<select name="vermogensbeheerder" style="width:200px" >'.$selectie->getOptions('Vermogensbeheerder').'</select>
</div>';
*/
$verm=$selectie->getData('Vermogensbeheerder');
$db=new DB();
$query="SELECT Vermogensbeheerder FROM Vermogensbeheerders WHERE ixpVerwerking=1 AND Vermogensbeheerder IN('".implode("','",$verm) ."')";
$db->SQL($query);
$db->Query();
$selection=array();
$titel='';
while($data=$db->nextRecord())
{
  $selection['Vermogensbeheerder'][$data['Vermogensbeheerder']]=1;
}
if(count($selection['Vermogensbeheerder'])==0)
  $selection=array();
else
{
  if(count($verm)<>count($selection['Vermogensbeheerder']))
  {
    $titel= vt("Vermogensbeheerder (Selectie aanwezig")." ".count($selection['Vermogensbeheerder'])." / ".count($verm)." )";
  }
}


$vermogensbeheerders=$selectie->createCheckBlok('Vermogensbeheerder',$selectie->getData('Vermogensbeheerder'),$selection,$titel);

?>
<link rel="stylesheet" type="text/css" media="all" href="javascript/calendar/calendar-win2k-1.css" />
<script type="text/javascript" src="javascript/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="javascript/calendar/lang/calendar-nl.js"></script>
<script type="text/javascript" src="javascript/calendar/calendar-setup_stripped.js"></script>
<script>
<?php
echo $selectie->getSelectJava();
?>
</script>
<form action="<?=$PHP_SELF?>" method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="save" value="" />


<table border="0">
<tr><td><? echo $selectie->createDatumSelectie()?></td></tr>
<tr><td>
<div class="formblock">
<div class="formlinks"> <?=vt("Periode")?> </div>
<div class="formrechts">
<select name="periode" style="width:200px" >
<option value="alles"><?=vt("Één periode")?> </option>
<option value="maanden"><?=vt("Maandelijks")?> </option>

</select>
</div>
</td></tr>
<tr><td>

<? echo $vermogensbeheerders;?>

</td></tr>
<tr><td>
<div class="formblock">
<div class="formlinks"> <?=vt("Rapporten")?> </div>
<div class="formrechts">
<input type="checkbox" value="1" checked name="PortefeuilleParameters"/> <?=vt("Portefeuille-parameters")?> <br />
<input type="checkbox" value="1" checked name="Managementoverzicht"/> <?=vt("Managementoverzicht")?> <br />
<input type="checkbox" value="1" checked name="Vermogensverloop"/> <?=vt("Vermogensverloop")?> <br />
<input type="checkbox" value="1" name="IXP"/> <?=vt("IXP")?> <br />
<input type="checkbox" value="1" name="IXP2"/> <?=vt("IXP II")?><br />
</div>
</td></tr>

<tr><td>
<div class="formblock">
<div class="formlinks"> <?=vt("Uitvoer")?> </div>
<div class="formrechts">
<input type="checkbox" value="1"  name="oneXls"/> <?=vt("Één Excel bestand")?> <br />
</div>
</td></tr>

</table>

<input type="submit" name="verwerk" value="<?=vt("verwerk")?> " />

</form>
<?
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}
