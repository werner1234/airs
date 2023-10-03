<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/25 16:41:15 $
 		File Versie					: $Revision: 1.13 $

 		$Log: indexBerekeningSelectie.php,v $
 		Revision 1.13  2020/03/25 16:41:15  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/06/26 15:08:52  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/06/22 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/01/27 17:28:13  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/04/23 16:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2011/01/16 12:10:21  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2010/12/12 15:32:27  rvv
 		*** empty log message ***

 		Revision 1.5  2009/02/28 16:48:33  rvv
 		*** empty log message ***

 		Revision 1.4  2009/02/28 16:42:28  rvv
 		*** empty log message ***

 		Revision 1.3  2009/02/01 10:17:43  rvv
 		*** empty log message ***

 		Revision 1.2  2008/09/05 13:32:50  rvv
 		*** empty log message ***

 		Revision 1.1  2007/07/05 12:23:59  rvv
 		*** empty log message ***

 		Revision 1.32  2007/04/20 12:18:07  rvv
 		*** empty log message ***

 		Revision 1.31  2007/04/03 13:25:22  rvv
 		*** empty log message ***

 		Revision 1.30  2007/02/21 10:57:56  rvv
 		Client / consolidatie toevoeging

 		Revision 1.29  2006/12/14 11:56:39  rvv
 		modelportefeuille via eigen tabel

 		Revision 1.28  2006/12/05 12:24:17  rvv
 		Menu tab toevoeging optie tools

 		Revision 1.27  2006/08/25 12:56:11  cvs
 		inner join uitgeschakeld

 		Revision 1.26  2006/07/05 13:08:41  cvs
 		- bug modelport join na where
 		- allemaal alleen als er resultaten zijn


*/


include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

if($_SESSION['metConsolidatie']=='')
  $_SESSION['metConsolidatie']=0;

if(in_array(strtoupper($USR),$__appvar["homeAdmins"]))// strtolower($__appvar["indexUser"])==strtolower($USR))
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

	if($_POST['dataVerwijderen'] == 'true')
	{

    
    $selectie = new portefeuilleSelectie($selectData);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $verwijderdePortefeuilles=array();
    $portefeuilleSelectie = implode('\',\'',array_keys($portefeuilles));
    
    $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		if(!checkAccess())
		{
		  echo "Geen rechten om records te verwijderen!";
		  exit;
		}

		$query = " DELETE HistorischePortefeuilleIndex ".
						 " FROM (Portefeuilles) JOIN HistorischePortefeuilleIndex ON HistorischePortefeuilleIndex.Portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.consolidatie<>2 AND ".$extraquery." 1 ";

		$DBs = new DB();
    $DBs->SQL($query);
    if($DBs->Query())
    {
		  echo "(".$DBs->mutaties($query).") Records verwijderd.";
    }
		$prb->hide();
		exit;
	}

	include_once('indexBerekening.php');
	$herberekening = new indexHerberekening( $selectData );
	$herberekening->USR = $USR;
	$herberekening->indexSuperUser = $indexSuperUser;
	$herberekening->progressbar = & $prb;
	$herberekening->__appvar = $__appvar;
	$herberekening->Bereken($_POST['weekWaarden']);
	exit;
}
else
{
  // selecteer laatst bekende valutadatum
  $totdatum = getLaatsteValutadatum();
  $jr = substr($totdatum,0,4);
  session_start();
  
  $selectie=new selectOptie();
  $html='<form name="selectForm">';
  $selectie->getInternExternActive();
  $html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
  $html.=$selectie->getInternExternHTML($PHP_SELF);
  $html .="<br>";
  if(method_exists($selectie,'getConsolidatieHTML'))
    $html.=$selectie->getConsolidatieHTML($PHP_SELF);
  $html.='</form>';
  
  
  
  $_SESSION['NAV'] = "";
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($html,"");
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
<input type="hidden" name="dataVerwijderen" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />

<br>
<b>Herberekening Portefeuille index</b>
<br>
<br>
<table border="0">
<tr>
	<td width="540">

<fieldset id="Periode" >
<legend accesskey="R"><u>P</u>eriode</legend>

  <?php
  echo $selectie->createDatumSelectie();
  ?>
<div class="formblock">
<div class="formlinks"> Aanvullen </div>
<div class="formrechts">
<input type="checkbox" name="aanvullen" value="1" checked />
</div>
</div>

<div class="formblock">
<div class="formlinks"> Debug</div>
<div class="formrechts">
<input type="checkbox" name="debug" value="1" />
</div>
</div>

	<div class="formblock">
		<div class="formlinks"> Week waarden</div>
		<div class="formrechts">
			<input type="checkbox" name="weekWaarden" value="1" />
		</div>
	</div>



</fieldset>
<br />
<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
<div id="PortefueilleSelectie" style="">

<fieldset id="Selectie" >
<legend accesskey="S"><u>S</u>electie</legend>
<?
// portefeuille selectie
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
  <table cellspacing="0" border = 1>
    <?
    $DB = new DB();
    $DB->SQL($selectie->queries['ClientPortefeuille']);
    $DB->Query();
    ?>
    <br><br>
    <?
    while($gb = $DB->NextRecord())
      $data[$gb['Portefeuille']]=$gb;
    echo "<br><br>";
    echo $selectie->createEnkelvoudigeSelctie($data,$_SESSION['backofficeSelectie']);
    echo "<br><br>";
    
    ?>
    <br><br>
    <?
}// end portefeuille selectie
else
{
  $DB = new DB();
  $maxVink=25;
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
    $data=$selectie->getData($optie);
    if(count($data) > 1)
    {
      if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
        echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
      else
        echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    }
  }
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

<script type="text/javascript">
selectTab();
</script>
	<?
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
?>