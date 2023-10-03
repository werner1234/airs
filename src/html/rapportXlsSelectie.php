<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/09/13 14:37:42 $
 		File Versie					: $Revision: 1.8 $

 		$Log: rapportXlsSelectie.php,v $
 		Revision 1.8  2014/09/13 14:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/09/03 15:55:22  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/05/26 13:52:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2011/12/11 10:57:35  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2010/11/17 17:15:58  rvv
 		*** empty log message ***

 		Revision 1.3  2008/06/30 06:53:04  rvv
 		*** empty log message ***

 		Revision 1.2  2007/08/30 12:03:15  rvv
 		Portefeuille parameters aangepast

 		Revision 1.1  2007/08/02 14:39:32  rvv
 		*** empty log message ***



*/



$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");
$selectie=new selectOptie();

session_start();
$DB = new DB();
$_SESSION['NAV'] ='';

if(is_array($_POST) || $_POST['fonds1'] != '---')
  $_POST['fonds'] = $_POST['fonds1'];

$editScript = "portefeuillesEdit.php";

if($_POST['action'] == 'add' && $_POST['posted'] == 'true')
{
  $_SESSION['xlsBatch'][] = $_POST;
}
elseif ($_GET['action'] == 'clean')
{
$_SESSION['xlsBatch'] = array();
}
elseif ($_GET['action'] == 'delete' && $_GET['key'] != '')
{
  unset($_SESSION['xlsBatch'][$_GET['key']]);
}
elseif($_POST['action'] == 'generate')
{
  include('rapportXlsAfdrukken.php');
  exit;
}
elseif($_POST['action'] == 'opslaan' && $_POST['save'] == 'true' )
{
  if ($_POST['naam'] =='')
  {
    $data = array();
    $data['naamfout'] = 'Naam mag niet leeg zijn.';
    $data['waarde'] = $_POST['naam'];
  }

  $query = "SELECT id FROM RapportXlsQuery WHERE Gebruiker = '$USR' AND Naam = '".$_POST['naam']."'";
  $DB->SQL($query);
  $DB->Query();
  if ($DB->records() > 0)
  {
    $data = array();
    $data['naamfout'] = 'Naam bestaat al.';
    $data['waarde'] = $_POST['naam'];
  }

  if (is_array($data))
  {
    echo template($__appvar["templateContentHeader"],$content);
    saveForm($data);
    echo template($__appvar["templateContentFooter"],$content);
    exit;
  }
  else
  {
    $query= "SELECT Vermogensbeheerder FROM VermogensbeheerdersPerGebruiker WHERE Gebruiker = '$USR' ";
    $DB->SQL($query);
    $DB->Query();
    $vermogensbeheerder = $DB->lookupRecord();
    $vermogensbeheerder = $vermogensbeheerder['$vermogensbeheerder'];

    $query = "INSERT INTO RapportXlsQuery SET  Naam = '".$_POST['naam']."', Gebruiker = '$USR', add_user = '$USR', change_user = '$USR', add_date = NOW(), change_date = NOW(),
    Vermogensbeheerder = '$vermogensbeheerder', Omschrijving = '".$_POST['omschrijving']."', `Type` = 'excel',
    `Data` = '".serialize($_SESSION['xlsBatch'])."' ;";
    $DB->SQL($query);
    $DB->Query();
  }
}
elseif($_POST['action'] == 'opslaan')
{
  echo template($__appvar["templateContentHeader"],$content);
  saveForm();
  echo template($__appvar["templateContentFooter"],$content);
  exit;
}
elseif($_POST['action'] == 'laden' && $_POST['rapport'] != '')
{
  $query = "SELECT Data FROM RapportXlsQuery WHERE Gebruiker = '$USR' AND Naam = '".$_POST['rapport']."' ";
  $DB->SQL($query);
  $DB->Query();
  $loadedData = $DB->lookupRecord();
  $_SESSION['xlsBatch'] = unserialize($loadedData['Data']);
}


if (is_array($_SESSION['xlsBatch']) && count($_SESSION['xlsBatch']) > 0)
$opslaanMogelijk = true;

if($_GET['actief'] == "inactief" )
{
	$inactiefChecked = "checked";
	$actief = "inactief";
}
else
{
	$actiefChecked = "checked";
	$actief = "actief";
	$alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() ";
}

$htmlMenu ='
<script type="text/javascript">
function loadItem()
{
  if (document.selectForm.rapport.value != "")
  {
		document.selectForm.submit();
  }
}
</script>
';

$query= "SELECT Naam FROM RapportXlsQuery WHERE Gebruiker = '$USR' ";
$DB->SQL($query);
$DB->Query();


$htmlMenu .= "<form name=\"selectForm\" method=\"POST\" target=\"content\"  action=\"rapportXlsSelectie.php\" >";
$htmlMenu .= "<input type=\"hidden\" name=\"action\" value=\"laden\" />";
$htmlMenu .= "<select name=\"rapport\" style=\"width:120px\" onChange=\"loadItem();\">";
$htmlMenu .= "<option value=\"\">---</option>";
while ($data = $DB->nextRecord())
{
  $htmlMenu .= "<option value=\"".$data['Naam']."\">".$data['Naam']."</option>";
}
$htmlMenu .= "</select>";
$htmlMenu .= "</form>";


$query = "SELECT OptieTools FROM Vermogensbeheerders WHERE OptieTools = 1 ";
$DB->SQL($query);
$DB->Query();
if ($DB->records() > 0)
  $OptiesActivated = true;




$rapporten=array();
$rapporten['fonds'] = array("Mutatievoorstel Fondsen"=>"Mutatievoorstel Fondsen",
                            "Fondsoverzicht" =>"Fondsoverzicht",
                            "Geaggregeerd Portefeuille Overzicht"=>"Geaggregeerd Portefeuille Overzicht",
                            "Modelcontrole"=>"Modelcontrole",
                            "MutatievoorstelPortefeuille"=>"MutatievoorstelPortefeuille");

$rapporten['management'] = array("CashPosities"=>"CashPosities",
                                 "Managementoverzicht"=>"Managementoverzicht",
                                 "Valuta Risico"=>"Valuta Risico",
                                 "Risicometing"=>"Risicometing",
                                 "Risicoanalyse"=>"Risicoanalyse",
                                 "Zorgplichtcontrole"=>"Zorgplichtcontrole",
                                 "PortefeuilleIndex"=>"PortefeuilleIndex",
                                 "PortefeuilleParameters"=>"PortefeuilleParameters" );
if ($OptiesActivated == true)
$rapporten['optietools'] = array("OptieExpiratieLijst"=>"Optie Expiratie Lijst",
                                 "OptieGeschrevenPositie"=>"Optie Geschreven Positie",
                                 "OptieOngedektePositie"=>"Optie Ongedekte Positie",
                                 "OptieVrijePositie"=>"Optie Vrije Positie",
                                 "OptieLiquideRuimte"=>"Optie Liquide Ruimte" );



$selectBox .= "<select name=\"rapport\" style=\"width:200px\" onChange=\"selectTab();\">";

while (list($groepNaam, $rapportGroep) = each($rapporten))
{
  $selectBox .= "<option value=\"\">-------  $groepNaam  -------</b></option>";
  while (list($rapport, $omschrijving) = each($rapportGroep))
  {
		$selectBox .= "<option value=\"$groepNaam"."__"."$rapport\">$omschrijving</option>";
  }
}
$selectBox .= "</select>";

$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem("Nieuwe Selectie",$PHP_SELF."?action=clean");
$_SESSION[submenu]->addItem("<br>","");
$_SESSION[submenu]->addItem("Selecties aanpassen","rapportxlsqueryList.php");
$_SESSION[submenu]->addItem("<br>","");
$_SESSION[submenu]->addItem("$htmlMenu <br>","");


$content[calendarinclude] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content[calendar] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();
?>

<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />

<table border="0">
<tr>
<td width="540">
<div class="form">

<fieldset id="Rapport" style="hight:200px">
<legend accesskey="R"><u>R</u>apport</legend>

<div class="formblock">
<div class="formlinks"> Rapport </div>
<div class="formrechts">
<?=$selectBox?>
</div>
</div>

<?
echo $selectie->createDatumSelectie();
?>


<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
<br><br>

</fieldset>

</td>
<td>

<br><input type="button"  id="addButton" onclick="javascript:add();" 			value=" Toevoegen " style="width:120px; visibility:hidden;">
<br><br>	<input type="button" id="xlsButton" onclick="javascript:generate();" value=" XLS-export " 	style="width:120px">
  <?
if($opslaanMogelijk)
{
 ?><br><br>	<input type="button" id="xlsButton" onclick="javascript:opslaan();" value=" Opslaan " 	style="width:120px"> <?
}
?>

</td>
</tr>


<tr>
<td width="540">
<fieldset id="Selectie" >
<legend accesskey="S"><u>S</u>electie</legend>
<?

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
?>

</div>
</td>

<td rowspan="3">
<?include('rapportXlsSide.php'); ?>
</td>
</tr>

<tr>
  <td width="540">

<fieldset id="InSelectie" style="hight:200px">
<legend accesskey="I">R<u>a</u>pporten in selectie</legend>
<?

while (list($key, $record) = each($_SESSION['xlsBatch']))
{
$rapport =  explode('__',$record['rapport']);
//echo $rapporten[$rapport[0]] [$rapport[1]] ."<br>";

?>
<div class="formblock">
<div class="formlinks">  </div>
<div class="formrechts">
<a href="<?=$PHP_SELF?>?action=delete&key=<?=$key?>" title="klik om te verwijderen." > <?=$rapporten[$rapport[0]][$rapport[1]]?> </a>
</div>
</div>
<?
}
?>
<br><br>
</fieldset>


  </td>
</tr>
<tr><td height="150" ></td></tr>

</table>

</form>
<?
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

function saveForm($data)
{
?>
<form action="<?=$PHP_SELF?>" method="POST" target="_self" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="action" value="opslaan" />
<input type="hidden" name="save" value="true" />
<input type="hidden" name="rapport_types" value="" />
<fieldset id="Rapport" style="width:540px">
<legend accesskey="O"><u>O</u>pslaan</legend>
<div class="formblock">
<div class="formlinks"> naam </div>
<div class="formrechts">
<input type="text" name="naam" size="20" value="<?=$data['waarde']?>" > <?=$data['naamfout']?>
</div>
</div>
<div class="formblock">
<div class="formlinks"> omschrijving </div>
<div class="formrechts">
<input type="text" name="omschrijving" size="30" value="">
</div>
</div>
<div class="formblock">
<div class="formlinks">  </div>
<div class="formrechts">
<input type="submit" name="opslaan" value="Opslaan" size="20">
</div>
</div>
</fieldset>
</form>
<?

}
?>