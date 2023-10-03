<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.27 $

 		$Log: rapportBackofficeKwartaalSelectie.php,v $
 		Revision 1.27  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2010/03/10 19:55:29  rvv
 		*** empty log message ***

 		Revision 1.25  2009/04/15 14:29:08  rvv
 		*** empty log message ***

 		Revision 1.24  2008/12/17 13:41:59  rvv
 		*** empty log message ***

 		Revision 1.23  2008/06/30 06:53:04  rvv
 		*** empty log message ***

 		Revision 1.22  2008/05/29 07:22:51  rvv
 		*** empty log message ***

 		Revision 1.21  2008/05/16 08:04:00  rvv
 		*** empty log message ***

 		Revision 1.20  2007/11/05 15:41:52  rvv
 		*** empty log message ***

 		Revision 1.19  2007/08/02 14:42:19  rvv
 		*** empty log message ***

 		Revision 1.18  2007/07/05 12:23:59  rvv
 		*** empty log message ***

 		Revision 1.17  2007/02/21 10:57:56  rvv
 		Client / consolidatie toevoeging

 		Revision 1.16  2007/01/16 14:56:40  rvv
 		*** empty log message ***

 		Revision 1.15  2007/01/12 12:51:09  rvv
 		*** empty log message ***

 		Revision 1.14  2007/01/11 11:17:19  rvv
 		Toevoeging factuur nummer

 		Revision 1.13  2006/07/26 07:30:34  cvs
 		*** empty log message ***

 		Revision 1.12  2006/05/03 07:16:37  jwellner
 		*** empty log message ***

 		Revision 1.11  2006/04/27 08:57:58  jwellner
 		*** empty log message ***

 		Revision 1.10  2006/04/12 07:54:47  jwellner
 		*** empty log message ***

 		Revision 1.9  2006/01/18 11:58:28  jwellner
 		no message

 		Revision 1.8  2006/01/06 16:35:10  jwellner
 		no message

 		Revision 1.7  2006/01/06 16:17:20  cvs
 		van datum aanpassen


*/
//$AEPDF2=true;
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
$type='portefeuille';

$html = "<b>selecteer rapport</b><br><br><form name=\"selectForm\">";

// selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.
$query = "SELECT OIH, OIS, HSE, OIB, OIV, PERF, VOLK, VHO, TRANS, MUT, OIR, GRAFIEK,layout FROM Vermogensbeheerders LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

include_once("rapportFrontofficeClientSelectieLayout.php");

$html = "<b>selecteer rapport</b><br><br><form name=\"selectForm\">";

while (list($key, $value) = each($__appvar["Rapporten"]))
{
	if($rdata[$key] > 0)
		$checked = "checked";
	else
		$checked = "";
	$html .= "<input type=\"checkbox\" value=\"".$key."\" name=\"rapport_type\" ".$checked." onClick=\"parent.frames['content'].selectTab()\" >  <label for=\"".$key."\" title=\"".$value."\">".$key." </label><br>";
}
$html .= "</form>";


$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem($html,"");
//$_SESSION[submenu]->addItem('Brief opmaak','kwartaalBriefEdit.php');
$_SESSION['submenu']->addItem('<br>','');
$_SESSION['submenu']->addItem('ATT opmaak','kwartaalBriefEdit.php?brief=ATTopmaak&titel=ATTtitel');
$_SESSION['submenu']->addItem('PERF opmaak','kwartaalBriefEdit.php?brief=PERFopmaak&titel=PERFtitel');
$_SESSION['submenu']->addItem('eMail opmaak','kwartaalBriefEdit.php?brief=eMailopmaak&titel=eMailtitel');
$_SESSION[submenu]->onLoad = " onLoad=\"parent.frames['content'].selectTab()\" ";

$_SESSION[NAV] = "";

$content[javascript] .= "
";

$content[calendarinclude] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content[calendar] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);

// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();
?>
<script type="text/javascript">
function setRapportTypes()
{
	document.selectForm.rapport_types.value = "";
	var tel =0;
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			document.selectForm.rapport_types.value = document.selectForm.rapport_types.value + '|' + parent.frames['submenu'].document.selectForm.rapport_type[i].value;
 			tel++;
 		}
 	}
}
<?
if($rapportSelectie[$rdata['layout'].'_b'])
{
  echo $rapportSelectie[$rdata['layout'].'_b'];
}
else
{
  echo 'function selectTab() {}';
}
?>

function print()
{
	document.selectForm.target = "generateFrame";
 	setRapportTypes();
	document.selectForm.save.value="0";
	document.selectForm.exportRap.value="0";
	document.selectForm.submit();
}


function saveasfile()
{
	document.selectForm.target = "generateFrame";
	setRapportTypes()
	document.selectForm.save.value="1";
	document.selectForm.exportRap.value="0";
	document.selectForm.submit();
}

function exportData()
{
	document.selectForm.target = "";
	if(document.selectForm.inclFactuur.checked == true)
	{
	document.selectForm.inclFactuur.value="1";
	}
	document.selectForm.type.value="kwartaal";
	document.selectForm.exportRap.value="1";
	document.selectForm.submit();
}

function eMail()
{
  var answer=confirm("Kwartaal rapportages per eMail versturen?")
  if (answer)
  {
  	document.selectForm.target = "";
  	if(document.selectForm.inclFactuur.checked == true)
    	document.selectForm.inclFactuur.value="1";
  	document.selectForm.type.value="eMailKwartaal";
  	document.selectForm.exportRap.value="2";
  	document.selectForm.submit();
  }
}


function eDossier()
{
  var answer=confirm("Kwartaal rapportages aan digitaal dosier toevoegen?")
  if (answer)
  {
  	document.selectForm.target = "";
  	if(document.selectForm.inclFactuur.checked == true)
    	document.selectForm.inclFactuur.value="1";
  	document.selectForm.type.value="eDossierKwartaal";
  	document.selectForm.exportRap.value="2";
  	document.selectForm.submit();
  }
}
</script>


<br><br>
<div class="tabbuttonRow">
	<input type="button" class="tabbuttonInActive" 	onclick="javascript:document.location = 'rapportBackofficeClientSelectie.php';"	id="tabbutton0" value="Clienten">
	<input type="button" class="tabbuttonInActive"  onclick="javascript:document.location = 'rapportBackofficeMaandSelectie.php';" id="tabbutton1" value="Maandrapportage">
	<input type="button" class="tabbuttonActive"  id="tabbutton2" value="Kwartaalrapportage">
</div>
<br>

<form action="rapportBackofficeClientAfdrukken.php" method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="kwartaalrapportage" value="1" />
<input type="hidden" name="exportRap" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="inclFactuur" value="" />

<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>

<table border="0">
	<tr>
	<td width="540" valign="top">

	<div class="form">

<fieldset id="Selectie" >
<legend accesskey="S"><u>S</u>electie</legend>

<?
$DB = new DB();
if(checkAccess($type))
	$join = "";
else
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$DB->SQL("SELECT Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders ".$join." ORDER BY Vermogensbeheerders.Vermogensbeheerder");

$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$t++;
	if($t == 1)
		$selectA = "SELECTED";
	else
		$selectA = "";

	if($t == ($aantal))
		$selectB = "SELECTED";
	else
		$selectB = "";

	$vermogensbeheerderOptionsA .= "<option value=\"".$gb[Vermogensbeheerder]."\" ".$selectA.">".$gb[Vermogensbeheerder]."</option>\n";
	$vermogensbeheerderOptionsB .= "<option value=\"".$gb[Vermogensbeheerder]."\" ".$selectB.">".$gb[Vermogensbeheerder]."</option>\n";
}
?>

<div class="formblock">
<div class="formlinks"> <?=VT("Van vermogensbeheerder")?> </div>
<div class="formrechts">
<select name="vermogensbeheerderVan" style="width:200px">
<?=$vermogensbeheerderOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m vermogensbeheerder </div>
<div class="formrechts">
<select name="vermogensbeheerderTm" style="width:200px">
<?=$vermogensbeheerderOptionsB?>
</select>
</div>
</div>
<?


if(checkAccess($type))
	$join = "";
else
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";


$DB->SQL("SELECT Accountmanagers.Accountmanager FROM Accountmanagers ".$join." ORDER BY Accountmanager");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$t++;
	if($t == 1)
		$selectA = "SELECTED";
	else
		$selectA = "";

	if($t == ($aantal))
		$selectB = "SELECTED";
	else
		$selectB = "";

	$accountmanagerOptionsA .= "<option value=\"".$gb[Accountmanager]."\" ".$selectA.">".$gb[Accountmanager]."</option>\n";
	$accountmanagerOptionsB .= "<option value=\"".$gb[Accountmanager]."\" ".$selectB.">".$gb[Accountmanager]."</option>\n";
}
?>

<div class="formblock">
<div class="formlinks"> Van accountmanager </div>
<div class="formrechts">
<select name="accountmanagerVan" style="width:200px">
<?=$accountmanagerOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m accountmanager </div>
<div class="formrechts">
<select name="accountmanagerTm" style="width:200px">
<?=$accountmanagerOptionsB?>
</select>
</div>
</div>
<?

$DB = new DB();
	if(checkAccess($type))
  {
	  $join = "";
	  $beperktToegankelijk = '';
  }
  else
  {
  	$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	  $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }

$DB->SQL("SELECT Clienten.Client, Clienten.Naam FROM Clienten, Portefeuilles ".$join." WHERE Clienten.Client = Portefeuilles.Client AND Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$t++;
	if($t == 1)
		$selectA = "SELECTED";
	else
		$selectA = "";

	if($t == ($aantal))
		$selectB = "SELECTED";
	else
		$selectB = "";

	$clientOptionsA .= "<option value=\"".$gb[Client]."\" ".$selectA.">".$gb[Client]."</option>\n";
	$clientOptionsB .= "<option value=\"".$gb[Client]."\" ".$selectB.">".$gb[Client]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Van client </div>
<div class="formrechts">
<select name="clientVan" style="width:200px">
<?=$clientOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m client </div>
<div class="formrechts">
<select name="clientTm" style="width:200px">
<?=$clientOptionsB?>
</select>
</div>
</div>
<?
	if(checkAccess($type))
  {
	  $join = "";
	  $beperktToegankelijk = '';
  }
  else
  {
  	$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	  $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }

$DB->SQL("SELECT Portefeuille FROM Portefeuilles ".$join." WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Portefeuille");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$t++;
	if($t == 1)
		$selectA = "SELECTED";
	else
		$selectA = "";

	if($t == ($aantal))
		$selectB = "SELECTED";
	else
		$selectB = "";

	$portfeuilleOptionsA .= "<option value=\"".$gb[Portefeuille]."\" ".$selectA.">".$gb[Portefeuille]."</option>\n";
	$portfeuilleOptionsB .= "<option value=\"".$gb[Portefeuille]."\" ".$selectB.">".$gb[Portefeuille]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Van portefeuille </div>
<div class="formrechts">
<select name="portefeuilleVan" style="width:200px">
<?=$portfeuilleOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m portefeuille </div>
<div class="formrechts">
<select name="portefeuilleTm" style="width:200px">
<?=$portfeuilleOptionsB?>
</select>
</div>
</div>

<?
$DB->SQL("SELECT Depotbank FROM Depotbanken ORDER BY Depotbank");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$t++;
	if($t == 1)
		$selectA = "SELECTED";
	else
		$selectA = "";

	if($t == ($aantal))
		$selectB = "SELECTED";
	else
		$selectB = "";

	$depotbankOptionsA .= "<option value=\"".$gb[Depotbank]."\" ".$selectA.">".$gb[Depotbank]."</option>\n";
	$depotbankOptionsB .= "<option value=\"".$gb[Depotbank]."\" ".$selectB.">".$gb[Depotbank]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Van depotbank </div>
<div class="formrechts">
<select name="depotbankVan" style="width:200px">
<?=$depotbankOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m depotbank </div>
<div class="formrechts">
<select name="depotbankTm" style="width:200px">
<?=$depotbankOptionsB?>
</select>
</div>
</div>



<div class="formblock">
<div class="formlinks"> Van datum </div>
<div class="formrechts">
<?
$jr = substr($totdatum,0,4);
$kal = new DHTML_Calendar();
$inp = array ('name' =>"datumVan",'value' =>date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>


<div class="formblock">
<div class="formlinks"> T/m datum </div>
<div class="formrechts">
<?php
$kal = new DHTML_Calendar();
$inp = array ('name' =>"datumTm",'value' =>date("d-m-Y",db2jul($totdatum)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>

<!-- Variabele selecties -->
<?php
$DB = new DB();
if(checkAccess($type))
	$join = "";
else
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$DB->SQL("SELECT DISTINCT(Risicoklasse) AS Risicoklasse FROM Portefeuilles ".$join." ORDER BY Risicoklasse");
$DB->Query();
$aantal = $DB->records();
if($aantal >1)
{
	$t=0;
	while($gb = $DB->NextRecord())
	{
		$t++;
		if($t == 1)
			$selectA = "SELECTED";
		else
			$selectA = "";

		if($t == ($aantal))
			$selectB = "SELECTED";
		else
			$selectB = "";

		$risicoOptionsA .= "<option value=\"".$gb[Risicoklasse]."\" ".$selectA.">".$gb[Risicoklasse]."</option>\n";
		$risicoOptionsB .= "<option value=\"".$gb[Risicoklasse]."\" ".$selectB.">".$gb[Risicoklasse]."</option>\n";
	}
?>
<div class="formblock">
<div class="formlinks"> Van risicoklasse </div>
<div class="formrechts">
<select name="RisicoklasseVan" style="width:200px">
<?=$risicoOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m risicoklasse </div>
<div class="formrechts">
<select name="RisicoklasseTm" style="width:200px">
<?=$risicoOptionsB?>
</select>
</div>
</div>
<?
}

$DB->SQL("SELECT DISTINCT(AFMprofiel) AS AFMprofiel FROM Portefeuilles ".$join." ORDER BY AFMprofiel");
$DB->Query();
$aantal = $DB->records();
if($aantal >1)
{
	$t=0;
	while($gb = $DB->NextRecord())
	{
		$t++;
		if($t == 1)
			$selectA = "SELECTED";
		else
			$selectA = "";

		if($t == ($aantal))
			$selectB = "SELECTED";
		else
			$selectB = "";

		$AFMprofielOptionsA .= "<option value=\"".$gb[AFMprofiel]."\" ".$selectA.">".$gb[AFMprofiel]."</option>\n";
		$AFMprofielOptionsB .= "<option value=\"".$gb[AFMprofiel]."\" ".$selectB.">".$gb[AFMprofiel]."</option>\n";
	}
?>
<div class="formblock">
<div class="formlinks"> Van AFM profiel </div>
<div class="formrechts">
<select name="AFMprofielVan" style="width:200px">
<?=$AFMprofielOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m AFM profiel </div>
<div class="formrechts">
<select name="AFMprofielTm" style="width:200px">
<?=$AFMprofielOptionsB?>
</select>
</div>
</div>
<?
}

$DB->SQL("SELECT DISTINCT(SoortOvereenkomst) AS SoortOvereenkomst  FROM Portefeuilles ".$join." ORDER BY SoortOvereenkomst");
$DB->Query();
$aantal = $DB->records();
if($aantal >1)
{
	$t=0;
	while($gb = $DB->NextRecord())
	{
		$t++;
		if($t == 1)
			$selectA = "SELECTED";
		else
			$selectA = "";

		if($t == ($aantal))
			$selectB = "SELECTED";
		else
			$selectB = "";

		$SoortOvereenkomstOptionsA .= "<option value=\"".$gb[SoortOvereenkomst]."\" ".$selectA.">".$gb[SoortOvereenkomst]."</option>\n";
		$SoortOvereenkomstOptionsB .= "<option value=\"".$gb[SoortOvereenkomst]."\" ".$selectB.">".$gb[SoortOvereenkomst]."</option>\n";
	}
?>
<div class="formblock">
<div class="formlinks"> Van soort overeenkomst </div>
<div class="formrechts">
<select name="SoortOvereenkomstVan" style="width:200px">
<?=$SoortOvereenkomstOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m soort overeenkomst </div>
<div class="formrechts">
<select name="SoortOvereenkomstTm" style="width:200px">
<?=$SoortOvereenkomstOptionsB?>
</select>
</div>
</div>
<?
}

$DB->SQL("SELECT DISTINCT(Remisier ) AS Remisier   FROM Portefeuilles ".$join." ORDER BY Remisier ");
$DB->Query();
$aantal = $DB->records();
if($aantal >1)
{
	$t=0;
	while($gb = $DB->NextRecord())
	{
		$t++;
		if($t == 1)
			$selectA = "SELECTED";
		else
			$selectA = "";

		if($t == ($aantal))
			$selectB = "SELECTED";
		else
			$selectB = "";

		$RemisierOptionsA .= "<option value=\"".$gb[Remisier]."\" ".$selectA.">".$gb[Remisier]."</option>\n";
		$RemisierOptionsB .= "<option value=\"".$gb[Remisier]."\" ".$selectB.">".$gb[Remisier]."</option>\n";
	}
?>
<div class="formblock">
<div class="formlinks"> Van remisier </div>
<div class="formrechts">
<select name="RemisierVan" style="width:200px">
<?=$RemisierOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m remisier </div>
<div class="formrechts">
<select name="RemisierTm" style="width:200px">
<?=$RemisierOptionsB?>
</select>
</div>
</div>
<?
}
?>

<div class="formblock">
<div class="formlinks"> Factuur toevoegen </div>
<div class="formrechts">
<input type="checkbox" name="inclFactuur" value="1">
<input type="text" name="factuurnummer" size="4">
</div>
</div>
<!--
<div class="formblock">
<div class="formlinks"> Brief toevoegen </div>
<div class="formrechts">
<input type="checkbox" name="inclBrief" value="1">
</div>
</div>
-->
<div class="formblock">
<div class="formlinks"> Algemeen drempel percentage </div>
<div class="formrechts">
<input type="text" name="drempelPercentage" size="4">
</div>
</div>


</fieldset>

</div>

</td>
<td valign="top">
	<input type="button" onclick="javascript:print();" value=" Afdrukken " style="width:100px"><br><br>
	<input type="button" onclick="javascript:saveasfile();" value=" Opslaan " style="width:100px"><br><br>
	<input type="button" onclick="javascript:exportData();" value=" Kwartaal Export " style="width:120px"><br><br>
	<input type="button" onclick="javascript:eMail();" value=" Per eMail " style="width:100px"><br><br>
	<input type="button" onclick="javascript:eDossier();" value=" eDossier " style="width:100px"><br><br>
	<input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> Logo onderdrukken <br>
	<input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven" checked> Voorblad weergeven <br>
	<input type="checkbox" value="1" id="memoOnderdrukken" name="memoOnderdrukken" checked> Memo onderdrukken <br>
<?
if(isset($rapportSettings[$rdata['layout'].'_b']))
{

  echo $rapportSettings[$rdata['layout'].'_b'];
}
?>
</td>
</tr>
</table>

</form>
<?php
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>