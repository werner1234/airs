<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/09/13 14:37:42 $
 		File Versie					: $Revision: 1.12 $

 		$Log: bestandsvergoedingSelectie.php,v $
 		Revision 1.12  2014/09/13 14:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/05/26 13:52:44  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/05/12 11:17:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/07/25 15:59:25  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2011/12/11 10:57:35  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/28 18:44:42  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.5  2011/05/18 16:50:14  rvv
 		*** empty log message ***

 		Revision 1.4  2011/05/15 14:29:10  rvv
 		*** empty log message ***

 		Revision 1.3  2011/04/17 08:57:57  rvv
 		*** empty log message ***

 		Revision 1.2  2011/03/26 16:51:06  rvv
 		*** empty log message ***

 		Revision 1.1  2011/03/23 16:58:56  rvv
 		*** empty log message ***

 		Revision 1.41  2010/01/13 16:57:28  rvv
 		*** empty log message ***

*/
//$AEPDF2=true;
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFRapport.php");

include_once("rapport/PDFOverzicht.php");
include_once("rapport/bestandsvergoedingDetailPerEmittent.php");
include_once("rapport/bestandsvergoedingFactuurvoorstel.php");

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");


$selectie=new selectOptie();
$content['javascript'] .= $selectie->getSelectJava();
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content[calendar] = $kal->get_load_files_code();

if ($_GET['selectie'])
  $_SESSION['selectieMethode'] = $_GET['selectie'];
if($_SESSION['selectieMethode'] == 'portefeuille')
  $selectiePortefeuille = 'checked';
elseif($_SESSION['selectieMethode'] == 'vink')
  $selectieVink = 'checked';
else
  $selectieAlles = 'checked';

$html .= "<form name=\"selectForm2\"><b>Selectie methode</b> <table>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" value=\"alles\"        $selectieAlles        onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=alles'\"></td><td style='font-size: 12px;'><label for=\"selectieall\" title=\"multiselectie\"> multiselectie</label></td></tr>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" value=\"portefeuille\" $selectiePortefeuille onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=portefeuille'\"></td><td style='font-size: 12px;'>  <label for=\"selectieport\" title=\"enkelvoudige\"> enkelvoudige selectie </label> </td></tr>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" value=\"vink\" $selectieVink onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=vink'\"></td><td style='font-size: 12px;'>  <label for=\"selectievink\" title=\"vink\"> aangepaste selectie </label> </td></tr>";
$html .= '</table></form>';

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");


echo template($__appvar["templateContentHeader"],$content);
$totdatum = getLaatsteValutadatum();

if($_POST['posted'])
{
	include_once("rapport/rapportRekenClass.php");
	include_once('../classes/excel/Writer.php');
  $selectData=$_POST;
  if($_POST['datumVan']=='')
  {
    echo "Ongeldige datum opgegeven.<br>\n";
    exit;
  }
	$selectData['datumVan'] 							= form2jul($_POST['datumVan']);
	$selectData['datumTm'] 								= form2jul($_POST['datumTm']);

	if($_POST['soort']=='uitbetaling')
	  $rapport = new bestandsvergoedingFactuurvoorstel( $selectData );
	else
    $rapport = new bestandsvergoedingDetailPerEmittent( $selectData );

  $rapport->writeRapport();

  if(count($rapport->pdf->excelData) < 1)
  {
    echo "Geen data om een xls file aan te maken.";
    exit;
  }
	switch($_POST['filetype'])
	{
		case "cvs" :
			$filename = $USR.mktime()."a.csv";
			$filetype = "csv";
			if ($_POST['nullenOnderdrukken'])
			  $rapport->pdf->nullenOnderdrukken = 1;
			$rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			$filename = $USR.mktime()."a.xls";
			$filetype = "xls";
		  $rapport->pdf->OutputXls($__appvar['tempdir'].$filename,"S");
		break;
	}
	?>
<script type="text/javascript">
function pushpdf(file,save)
{
	var width='800';
	var height='600';
	var target = '_blank';
	var location = 'pushFile.php?filetype=<?=$filetype?>&file=' + file;
	if(save == 1)
	{
		// opslaan als bestand
		document.location = location + '&action=attachment';
	}
	else
	{
		// pushen naar PDF reader
		var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
		doc.document.location = location;
	}
}
pushpdf('<?=$filename?>',<?=$save?>);
</script>
<?
	exit();
}
else
{
	session_start();
	$_SESSION[NAV] = "";
	session_write_close();
?>
<script type="text/javascript">

function csv()
{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="cvs";
		document.selectForm.save.value="1";
		document.selectForm.submit();
}


function xls()
{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="xls";
		document.selectForm.save.value="1";
		document.selectForm.submit();
}


function RapportChanged()
{

	if(document.selectForm.soort.selectedIndex == 2 ) //DB records
	{
			$('#buttonA').hide();
			$('#buttonB').attr('value','Aanmaken');
	}
	else
	{
	  	$('#buttonA').show();
			$('#buttonB').attr('value',' XLS-export ');
	}
}
</script>

<table border="0">
 <tr>
  <td >
   <form  method="POST" target="_blank" name="selectForm">
   <input type="hidden" name="posted" value="true" />
   <input type="hidden" name="save" value="" />
   <input type="hidden" name="filetype" value="PDF" />
   <table border="0">
   <tr>
     <td width="540" valign="top">
      <fieldset id="Rapport" >
      <legend accesskey="R"><u>R</u>apport</legend>
      <div class="formblock">
      <div class="formlinks"> Rapport </div>
      <div class="formrechts">
      <select name="soort" style="width:200px" onchange="javascript:RapportChanged();">
	      <option value="detail">Detailoverzicht per emittent</option>
	      <option value="fonds">Fondsen per emittent</option>
	      <option value="db">Database records aanmaken</option>
	      <option value="uitbetaling">Geplande uitbetaling</option>
      </select>
      </div>
      </div>
<?
echo $selectie->createKwartaalSelectie();//$_SESSION['backofficeSelectie']
?>
     </fieldset>
     <fieldset id="Selectie" >
     <legend accesskey="S"><u>S</u>electie</legend>
<?
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<script language="Javascript">

</script>
<table cellspacing="0" border = 1>

<?
  $DB = new DB();
  if(checkAccess($type))
  {
  	$join = "";
  	$beperktToegankelijk = '';
  }
  else
  {
  	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
  	         JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  	$beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }
	$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client ";

  $DB->SQL($query);
  $DB->Query();
  while($gb = $DB->NextRecord())
    $data[$gb['Portefeuille']]=$gb;
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($data,$_SESSION['backofficeSelectie']);
  echo "<br><br>";
}
else
{
  $DB = new DB();
  $maxVink=25;
  $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
  foreach ($opties as $optie=>$omschrijving)
  { 
    $data=$selectie->getData($optie);
    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
      echo $selectie->createSelectBlokOpenSluiten($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    else
      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
  }
  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier');
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
     </td>
     <td valign="top">
      <input type="button" onclick="javascript:csv();" 		id="buttonA"			value=" CSV-export " style="width:100px"><br><br>
      <input type="button" onclick="javascript:xls();" 		id="buttonB"			value=" XLS-export " style="width:100px"><br><br>
      <?=$extraAfdrukKnop?>
  <!-- Fonds selectie -->
      <div id="Emintentselectie" >
      <fieldset id="Selectie">
      <legend accesskey="e">S<u>e</u>lectie</legend>
<?
$DB=new DB();
$DB->SQL("SELECT emittenten.emittent,emittenten.naam FROM emittenten ORDER BY emittenten.emittent");
$DB->Query();
$aantal = $DB->records();
$t=0;
$items='';
while($em = $DB->NextRecord())
{
	$items .= "<option value=\"".$em['emittent']."\" >".$em['naam']."</option>\n";
}
?>
      <div class="formblock">
      <div class="formlinks"> Emittent </div>
      <div class="formrechts">
      <select name="emittent" style="width:200px">
      <option value="">Alles</option>
      <?=$items?>
      </select>
      </div>
      </div>
      </fieldset>
      <!-- end Fonds selectie -->
   <!-- Fonds selectie -->
      <div id="Emintentselectie" >
      <fieldset id="Selectie">
      <legend accesskey="e">S<u>e</u>lectie</legend>
<?
$DB=new DB();
$DB->SQL("SELECT Fondsen.Fonds, Fondsen.Omschrijving FROM emittentPerFonds JOIN Fondsen ON emittentPerFonds.fonds=Fondsen.Fonds GROUP BY emittentPerFonds.fonds ORDER BY Omschrijving");
$DB->Query();
$aantal = $DB->records();
$t=0;
$items='';
while($em = $DB->NextRecord())
{
	$items .= "<option value=\"".$em['Fonds']."\" >".$em['Omschrijving']."</option>\n";
}
?>
      <div class="formblock">
      <div class="formlinks"> Fonds </div>
      <div class="formrechts">
      <select name="fonds" style="width:200px">
      <option value="">Alles</option>
      <?=$items?>
      </select>
      </div>
      </div>
      </fieldset>
      <!-- end Fonds selectie -->


      <!-- Fonds selectie -->
      <div id="Emintentselectie" >
      <fieldset id="Selectie">
      <legend accesskey="e">S<u>e</u>lectie</legend>
      <div class="formblock">
      <div class="formlinks"> Filter </div>
      <div class="formrechts">
      <input type="radio" name="alleClienten" value="0" checked> Clienten voor uitbetaling <br>
      <input type="radio" name="alleClienten" value="1"> Alle Clienten
      </div>
      </div>
      </fieldset>
      <!-- end Fonds selectie -->
     </td>
    </tr>
   </table>
    <tr>
	   <td colspan="2">
<?echo progressFrame();?>
	   </td>
    </tr>
   </table>
<?php
}
echo template($__appvar["templateRefreshFooter"],$content);
?>