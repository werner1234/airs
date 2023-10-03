<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/06 14:56:43 $
 		File Versie					: $Revision: 1.3 $
*/


include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("./rapport/factuur/FactuurRekenClass.php");
include_once("rapport/rapportRekenClass.php");


define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

if($_SESSION['metConsolidatie']=='')
  $_SESSION['metConsolidatie']=0;

if(in_array(strtoupper($USR),$__appvar["homeAdmins"]))//if(strtolower($__appvar["indexUser"])==strtolower($USR))
  $indexSuperUser=true;
else
  $indexSuperUser=false;


if($_GET['actief'] == "inactief" )
{
  $inactiefChecked = "checked";
  $actief = "inactief";
  $alleenActief = " ";
}
elseif($_GET['actief'] == "positie" )
{
  $positieChecked='checked';
  $actief = "positie";
  
}
else
{
  $actiefChecked = "checked";
  $actief = "actief";
  $alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}



$editcontent['javascript'] = $content['javascript'];
$type='portefeuille';
$maxVink=25;

$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();



echo template($__appvar["templateContentHeader"],$editcontent);
flush();
if($_POST['posted'])
{
  $start = getmicrotime();
  if(!empty($_POST['datumTm']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datumTm']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>Fout: ongeldige datum opgegeven!</b>";
			exit;
		}
	}
	else
	{
		echo "<b>Fout: geen datum opgegeven!</b>";
		exit;
	}

	// maak progressbar
	$prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();
  
  $selectData=$_POST;
  $selectData['datumVan'] 							= form2jul($_POST['datumVan']);
  $selectData['datumTm'] 								= form2jul($_POST['datumTm']);
 
  
  $selectie = new portefeuilleSelectie($selectData);
  $selectie->getRecords();
  $portefeuilles = $selectie->getSelectie();
echo "Aantal portefeuilles: ".count($portefeuilles)."<br>\n";
  if(count($portefeuilles) <= 0)
  {
    echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
    if($prb)
      $prb->hide();
    exit;
  }
  
  if($prb)
  {
    $prb->moveStep(0);
    $pro_step = 0;
    $pro_multiplier = 100 / count($portefeuilles);
  }

  $db=new DB();
  foreach($portefeuilles as $pdata)
  {
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    
    echo "Berekenen voor ".$pdata['Portefeuille']." van ".jul2db($selectData['datumVan'])." tot ".jul2db($selectData['datumTm'])."<br>\n";
    
    $berekening=new factuurBerekening($pdata['Portefeuille'],jul2db($selectData['datumVan']),jul2db( $selectData['datumTm']),0);
    if($selectData['datumVan'] < db2jul($berekening->portefeuilledata['Startdatum']))
    {
      $start=db2jul($berekening->portefeuilledata['Startdatum']);
    }
    else
    {
      $start=$selectData['datumVan'];
    }

    if($selectData['datumVan'] > db2jul($berekening->portefeuilledata['Einddatum']))
    {
      $stop =db2jul($berekening->portefeuilledata['Einddatum']);
    }
    else
    {
      $stop= $selectData['datumTm'];
    }
    $berekening->julrapportVanaf=$start;
    $berekening->julrapportTm=$stop;
    $berekening->getDagGemidelde('',false);
   
    foreach($berekening->dagWaarden as $datum=>$grondslag)
    {
      $liq=doubleval($berekening->liquiditeitenWaardeOpDatum[$datum]);
      $huisfonds=mysql_real_escape_string(serialize($berekening->huisfondsWaardeOpDatum[$datum]));
      $totaleWaarde=doubleval($berekening->dagTotalen[$datum]);
      $query="select id FROM HistorischeDagelijkseWaarden WHERE portefeuille='".mysql_real_escape_string($pdata['Portefeuille'])."' AND datum='".mysql_real_escape_string($datum)."'";
      $db->SQL($query);
      $besaandeId=$db->lookupRecord();
      $setVelden="change_date=now(),change_user='$USR',datum='$datum',portefeuille='".mysql_real_escape_string($pdata['Portefeuille'])."',
          eindvermogen='$totaleWaarde',liquiditeiten='$liq',huisfondsen='$huisfonds',grondslag='".doubleval($grondslag)."'";
      if($besaandeId['id']>0)
      {
        if($selectData['overschrijven']==1)
        {
          $query = "UPDATE HistorischeDagelijkseWaarden SET $setVelden WHERE id='".$besaandeId['id']."'";
        }
        else
        {
          $query="select 'geen update'";
        }
      }
      else
      {
        $query = "INSERT INTO HistorischeDagelijkseWaarden SET $setVelden ,add_date=now(),add_user='$USR'";
      }
     // echo $query."<br>\n";
      $db->SQL($query);
      $db->Query();
    }
  }
  $prb->hide();
	exit;
}
else
{
  // selecteer laatst bekende valutadatum
  $totdatum = getLaatsteValutadatum();
  $jr = substr($totdatum,0,4);
  session_start();
  
  $selectie=new selectOptie();
  
  $_SESSION['NAV'] = "";
  $_SESSION['submenu'] = New Submenu();
 //$_SESSION['submenu']->addItem($html,"");
  session_write_close();

	?>
	<script type="text/javascript">

		<?=$selectie->getSelectJava();?>

	function selectSelected()
	{
	  if(document.selectForm['inFields[]'])
	  {
		var inFields  			= document.selectForm['inFields[]'];
		var selectedFields 	= document.selectForm['selectedFields[]'];

		for(j=0; j < selectedFields.options.length; j++)
		  {
 			selectedFields.options[j].selected = true;
		  }
	  }
	}

	function bereken()
	{
	  document.selectForm.target = "generateFrame";
	  document.selectForm.dataVerwijderen.value='false';
		selectSelected();
		document.selectForm.submit();
	}

	function verwijderen()
	{
	  document.selectForm.target = "generateFrame";
	  if(confirm ('Weet u het zeker?'))
	  {
	  document.selectForm.dataVerwijderen.value='true';
		selectSelected();
		document.selectForm.submit();
	  }
	}

</script>



<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="dataVerwijderen" value="" />

<br>
<b><?= vt('Historische dagelijkse fee berekening'); ?></b>
<br>
<br>
<table border="0">
<tr>
	<td width="540">

<fieldset id="Periode" >
<legend accesskey="R"><?= vt('Periode'); ?></legend>

  <?php
  echo $selectie->createDatumSelectie();
  ?>
<div class="formblock">
<div class="formlinks"> <?= vt('Overschrijven'); ?> </div>
<div class="formrechts">
<input type="checkbox" name="overschrijven" value="1" checked />
</div>
</div>


</fieldset>
<br />
<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
<div id="PortefueilleSelectie" style="">

<fieldset id="Selectie" >
<legend accesskey="S"><?= vt('Selectie'); ?></legend>
<?

  $DB = new DB();
  $maxVink=25;
 // $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder');
  $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
      echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    else
      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
  }

  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data = $selectie->getData($optie);
    if (count($data) > 1)
      if ($_SESSION['selectieMethode'] == 'vink' && count($data) < $maxVink)
        echo $selectie->createCheckBlok($optie, $data, $_SESSION['backofficeSelectie'], $omschrijving);
      else
        echo $selectie->createSelectBlok($optie, $data, $_SESSION['backofficeSelectie'], $omschrijving);
  }

?>

</fieldset>

</div>

</td>
<td valign="top" width="400">
<br />
<input type="button" onclick="javascript:bereken();" 				value=" Berekenen " style="width:100px"><br><br>
<?
if($indexSuperUser)
  echo '<input type="button" onclick="javascript:verwijderen();" 				value=" Verwijderen " style="width:100px"><br><br>';
?>
</td>
</tr>
</table>


</form>


	<?
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
?>