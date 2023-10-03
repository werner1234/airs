<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/20 08:25:45 $
 		File Versie					: $Revision: 1.4 $

 		$Log: importSaldi.php,v $
 		Revision 1.4  2010/01/20 08:25:45  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/01/14 12:50:37  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2008/06/30 07:57:51  rvv
 		*** empty log message ***

 		Revision 1.1  2008/06/30 06:53:04  rvv
 		*** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/importSaldiClass.php");
include_once("rapport/rapportRekenClass.php");
include_once("../classes/AE_cls_progressbar.php");


$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>" . vt('Genereer rekeningmutaties via saldi') . "</b></div><br><br>";
echo template($__appvar["templateContentHeader"],$content);

if($_POST['step'])
  $step = $_POST['step'];
else
  $step = 'upload';

if($step == 'upload')
{
  $postDate = explode("-",$_POST["datum"]);
  if($_POST)
  {
    if (($postDate[0] < 1 OR $postDate[0] > 31) OR
        ($postDate[1] < 1 OR $postDate[1] > 12) OR
        ($postDate[2] < 2000 OR $postDate[2] > 2098) )
    {
      $error .= 'ongeldige boekdatum '.$postDate[0].'-'.$postDate[1].'-'.$postDate[2].'<BR>';
    }
  }

  $importfile = $__appvar['tempdir']."/saldi_".$USR.".xls";
  if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
  {
    $_POST['tmpXls'] = $importfile;
    $step = 'preview';
  }
  else
  {
    if($_POST)
      $error .= '' . vt('Upload van file mislukt!') . '<BR>';
    $step = 'upload';
  }
}

if ($step == 'preview')
{
  $import = new importSaldi();

  $xlsData = $import->readXLS($_POST['tmpXls']);
  $cleanData = $import->cleanXLSdata($xlsData);


  $import->datum = formdate2db($_POST['datum']);


  $portefeuilles = $import->getPortefeuilles($import->datum);
  $portefeuilleVerschillen = $import->vergelijkPortefeuilles($cleanData[portefeuilles],$portefeuilles);
}

if($step == 'upload')
{
?>
<form enctype="multipart/form-data" method="POST" name="invoerForm" >
<input type="hidden" name="step" value="<?=$step?>" >
<div class="form">
 <div class="formblock">
  <div class="formlinks"> <?= vt('xls bestand'); ?> </div>
  <div class="formrechts">
    <input type="file" name="importfile" size="30" value="<?=$_POST['importfileName']?>" >
    <input type="hidden" name="importfileName" size="30" value="" >
  </div>
</div>

<div class="formblock">
  <div class="formlinks"> <?= vt('Boekdatum'); ?>: </div>
  <div class="formrechts">
    <input type="text"  name="datum" size="10" value="<?=$_POST['datum']?>" > DD-MM-JJJJ
  </div>
</div>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Importeren" onClick="document.invoerForm.importfileName.value=document.invoerForm.importfile.value;document.invoerForm.submit();">
<br><?=$error?>
</div>
</div>
</form>
<?
}
elseif ($step == 'preview')
{
  $step = 'update';
?>
<form method="POST" name="invoerForm" >
<input type="hidden" name="step" value="<?=$step?>" >

<div class="formblock">
  <div class="formlinks"> &nbsp; </div>
  <div class="formrechts">
   &nbsp;
  </div>
</div>

<div class="form">
 <div class="formblock">
  <div class="formlinks"> <?= vt('Te verwerken xls bestand'); ?>: </div>
  <div class="formrechts">
    <input type="text"  name="importfileName" size="30" value="<?=$_POST['importfileName']?>" readonly >
    <input type="hidden"  name="tmpXls" value="<?=$_POST['tmpXls']?>" readonly >
  </div>
</div>
<div class="formblock">
  <div class="formlinks"> <?= vt('Boekdatum'); ?>: </div>
  <div class="formrechts">
    <input type="text"  name="datum" size="10" value="<?=$_POST['datum']?>" > DD-MM-JJJJ
  </div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Verwerken" onClick="document.invoerForm.submit();">
<br><?=$error?>

</div>
</div>
</form>
<?

	$prb 						= new ProgressBar(600,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	50;
	$prb->show();
	$prb->moveStep(0);
	$pro_step = 0;
	$pro_multiplier = 100 / count($portefeuilles);
  flush();
  ob_flush();
  $db= new DB();


 foreach ($portefeuilleVerschillen['XlsWaardenBeiden'] as $portefeuille=>$xlsdata)
 {
   $portefeuilleKort = substr($portefeuille,strlen($portefeuilles[$portefeuille]['PortefeuilleVoorzet']));
   $waardes = berekenPortefeuilleWaarde($portefeuilleKort,getLaatsteValutadatum());
   foreach ($waardes as $waarde)
   {
    $portefeuilleWaardes[$portefeuille]['airsWaarde'] += $waarde['actuelePortefeuilleWaardeEuro'];
   }

    $query = "SELECT Rekeningmutaties.*	FROM Rekeningmutaties, Rekeningen WHERE	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	            Rekeningen.Portefeuille = '$portefeuilleKort' AND	Rekeningmutaties.Verwerkt = '1' ORDER BY Rekeningmutaties.Boekdatum DESC LIMIT 1 ";
    $db->SQL($query);
    $Rekeningmutaties = $db->lookupRecord();
    $portefeuilleWaardes[$portefeuille]['LaatsteRekeningmutatie']=$Rekeningmutaties;
    $portefeuilleWaardes[$portefeuille]['xlsWaarde']= $xlsdata['waarde'];
    $portefeuilleWaardes[$portefeuille]['airsnaam']= $portefeuilles[$portefeuille]['Client'];
    $portefeuilleWaardes[$portefeuille]['xlsNaam']= $xlsdata['naam'];
    $portefeuilleWaardes[$portefeuille]['PortefeuilleVoorzet']= $portefeuilles[$portefeuille]['PortefeuilleVoorzet'];
    $pro_step += $pro_multiplier;
	  $prb->moveStep($pro_step);
	  flush();
	  ob_flush();
 }
 $prb->hide();

$data = $import->genereerMutaties($portefeuilleWaardes);
echo "<h4> Mutatie voorstel </h4>";
echo $data['html'];


foreach ($data['fouten'] as $portefeuille => $fout)
{
  unset($data['queries'][$portefeuille]);
}
$_SESSION['saldiMutaties'] = array();
$_SESSION['saldiMutaties'] = $data['queries'];


//listarray($data['queries']);
if(count($portefeuilleVerschillen['alleenInAirs']) >0)
{
echo "<br><br><b>Volgende Portefeuilles zijn alleen in Airs gevonden</b><br>\n";
foreach ($portefeuilleVerschillen['alleenInAirs'] as $airsPortefeuille)
 echo " -$airsPortefeuille- ";
}
if(count($portefeuilleVerschillen['alleenInXls']) >0)
{
echo "<br><b>Volgende Portefeuilles zijn alleen in de XLS gevonden</b><br>\n";
foreach ($portefeuilleVerschillen['alleenInXls'] as $XlsPortefeuille)
 echo " -$XlsPortefeuille- ";
}

}
elseif ($step == 'update')
{
  $db=new DB();
  $i=0;
  foreach ($_SESSION['saldiMutaties'] as $portefeuille=>$queries)
  {
    foreach ($queries as $query)
    {
      $db->SQL($query);
      if(!$db->Query())
      {
        echo "Query fout in: ".$query."<br>";
      }
      else
       $i++;

    }
  }
  echo "$i queries uitgevoerd.";
  $_SESSION['saldiMutaties'] = array();
  unlink($_POST['tmpXls']);
}
//listarray($portefeuilleVerschillen);
//listarray($_POST);
//listarray($_FILES);
echo template($__appvar["templateRefreshFooter"],$content);





?>