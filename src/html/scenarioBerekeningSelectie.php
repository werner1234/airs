<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/10/13 17:16:37 $
 		File Versie					: $Revision: 1.2 $

 		$Log: scenarioBerekeningSelectie.php,v $
 		Revision 1.2  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/09 17:03:38  rvv
 		*** empty log message ***
 		
 	

*/


include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

if(strtolower($__appvar["indexUser"])==strtolower($USR))
  $indexSuperUser=true;
else
  $indexSuperUser=false;


$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
flush();
if($_POST['posted'])
{

	$start = getmicrotime();

	if(!empty($_POST['datumTm']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datumTm']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>" . vt('Fout: ongeldige datum opgegeven!') . "</b>";
			exit;
		}
	}
	else
	{
		echo "<b>" . vt('Fout: geen datum opgegeven!') . "</b>";
		exit;
	}

	$selectData['datumVan'] 							= form2jul($_POST['datumVan']);
	$selectData['datumTm'] 								= form2jul($_POST['datumTm']);
	$selectData['vermogensbeheerderVan'] 	= $_POST['vermogensbeheerderVan'];
	$selectData['vermogensbeheerderTm']  	= $_POST['vermogensbeheerderTm'];
	$selectData['accountmanagerVan'] 			= $_POST['accountmanagerVan'];
	$selectData['accountmanagerTm'] 			= $_POST['accountmanagerTm'];
	$selectData['portefeuilleVan'] 				= $_POST['portefeuilleVan'];
	$selectData['portefeuilleTm'] 				= $_POST['portefeuilleTm'];
	$selectData['depotbankVan'] 					= $_POST['depotbankVan'];
	$selectData['depotbankTm'] 						= $_POST['depotbankTm'];
	$selectData['clientVan']  					= $_POST['clientVan'];
	$selectData['clientTm'] 						= $_POST['clientTm'];
	$selectData['selectedPortefeuilles'] = $_POST['selectedFields'];
	$selectData['aanvullen']             = $_POST['aanvullen'];
	$selectData['debug']             = $_POST['debug'];
  $selectData['metConsolidatie']   ='0';

	// maak progressbar
	$prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();

	if($_POST['dataVerwijderen'] == 'true')
	{
	  if($selectData['datumVan'])
			$extraquery .= "HistorischePortefeuilleIndex.Datum >= '".jul2sql($selectData['datumVan'])."' AND ";
		if($selectData['datumTm'])
			$extraquery .= "HistorischePortefeuilleIndex.Datum <= '".jul2sql($selectData['datumTm'])."' AND ";

	  if($selectData['portefeuilleTm'])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$selectData['portefeuilleVan']."' AND Portefeuilles.Portefeuille <= '".$selectData['portefeuilleTm']."') AND";
		if($selectData['vermogensbeheerderTm'])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$selectData['vermogensbeheerderVan']."' AND Portefeuilles.Vermogensbeheerder <= '".$selectData['vermogensbeheerderTm']."') AND ";
		if($selectData['accountmanagerTm'])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$selectData['accountmanagerVan']."' AND Portefeuilles.Accountmanager <= '".$selectData['accountmanagerTm']."') AND ";
		if($selectData['depotbankTm'])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$selectData['depotbankVan']."' AND Portefeuilles.Depotbank <= '".$selectData['depotbankTm']."') AND ";
		if($selectData['AFMprofielTm'])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$selectData['AFMprofielVan']."' AND Portefeuilles.AFMprofiel <= '".$selectData['AFMprofielTm']."') AND ";
		if($selectData['RisicoklasseTm'])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$selectData['RisicoklasseVan']."' AND Portefeuilles.Risicoklasse <= '".$selectData['RisicoklasseTm']."') AND ";
		if($selectData['SoortOvereenkomstTm'])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$selectData['SoortOvereenkomstVan']."' AND Portefeuilles.SoortOvereenkomst <= '".$selectData['SoortOvereenkomstTm']."') AND ";
		if($selectData['RemisierTm'])
			$extraquery .= " (Portefeuilles.Remisier >= '".$selectData['RemisierVan']."' AND Portefeuilles.Remisier <= '".$selectData['RemisierTm']."') AND ";
		if($selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$selectData['clientVan']."' AND Portefeuilles.Client <= '".$selectData['clientTm']."') AND ";
		if (count($selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}

		if(!checkAccess())
		{
		  echo vt("Geen rechten om records te verwijderen!");
		  exit;
		}

		$query = " DELETE HistorischePortefeuilleIndex ".
						 " FROM (Portefeuilles) JOIN HistorischePortefeuilleIndex ON HistorischePortefeuilleIndex.Portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.consolidatie=0 AND ".$extraquery." 1 ";

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
	$herberekening->BerekenScenarios();
	exit;
}
else
{
	// selecteer laatst bekende valutadatum
	$totdatum = getLaatsteValutadatum();
  $jr = substr($totdatum,0,4);

  session_start();
	if ($_GET['selectie'] == 'alles')
	  $_SESSION['selectieMethode'] = 'alles';
	elseif ($_GET['selectie'] == 'portefeuille')
	  $_SESSION['selectieMethode'] = 'portefeuille';

	if($_SESSION['selectieMethode'] == 'portefeuille')
	{
	  $selectieAlles = '';
	  $selectiePortefeuille = 'checked';
	}
	else
	{
	  $selectieAlles = 'checked';
	  $selectiePortefeuille = '';
	}

$html .= "<b>" . vt('Selectie methode') . "</b><br><br><form name=\"selectForm\"> <table>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" id=\"selectieall\" value=\"alles\"        $selectieAlles        onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=alles'\"></td><td style='font-size: 12px;'><label for=\"selectieall\" title=\"multiselectie\"> " . vt('multiselectie') . "</label></td></tr>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" id=\"selectieport\" value=\"portefeuille\" $selectiePortefeuille onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=portefeuille'\"></td><td style='font-size: 12px;'>  <label for=\"selectieport\" title=\"enkelvoudige\"> " . vt('enkelvoudige selectie') . " </label> </td></tr>";
$html .= '</table></form>';

	$_SESSION['NAV'] = "";
	$_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($html,"");
	session_write_close();
	?>
	<script type="text/javascript">


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

<br>
<b><?= vt('Vastleggen scenario berekening'); ?></b>
<br>
<br>
<table border="0">
<tr>
	<td width="540">

<fieldset id="Periode" >
<legend accesskey="R"><?= vt('Periode'); ?></legend>

<?php
$kal = new DHTML_Calendar();
?>

<!--
<div class="formblock">
<div class="formlinks"> Van datum </div>
<div class="formrechts">
<?
$inp = array ('name' =>"datumVan",'value'=>date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>
-->

<div class="formblock">
<div class="formlinks"> <?= vt('T/m datum'); ?></div>
<div class="formrechts">
<?php

$inp = array ('name' =>"datumTm",'value' =>date("d-m-Y",db2jul($totdatum)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>

<!--
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
-->

</fieldset>
<br />
<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
<div id="PortefueilleSelectie" style="">

<fieldset id="Selectie" >
<legend accesskey="S"><?= vt('Selectie'); ?></legend>
<?
// portefeuille selectie
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<script language="Javascript">
function moveItem(from,to){
	var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++) {
 		if(from.options[i].selected)
 		{
			var blnInList = false;
			for(j=0; j < to.options.length; j++)
			{
 				if(to.options[j].value == from.options[i].value)
				{
 					//alert("already in list");
 					blnInList = true;
 					break;
 				}
			}
			if(!blnInList)
 			{
				to.options.length++;
				to.options[to.options.length-1].text = from.options[i].text;
				to.options[to.options.length-1].value = from.options[i].value;
			}
 		}
		else
		{
			tmp_text.length++;
			tmp_value.length++;
			tmp_text[tmp_text.length-1] = from.options[i].text;
			tmp_value[tmp_text.length-1] = from.options[i].value;

		}
 	}
 	from.options.length = 0;
 	for(var i=0; i < tmp_text.length; i++) {
 		from.options.length++;
		from.options[from.options.length-1].text = tmp_text[i];
		from.options[from.options.length-1].value = tmp_value[i];
 	}
 	from.selectedIndex = -1;
}
</script>
<table cellspacing="0" border = 1>

<?
$DB = new DB();
if(checkAccess($type))
	$join = "";
else
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() ORDER BY Client ";

$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
$portfeuilleOptions .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Portefeuille']. " - ".$gb['Client']. "</option>\n";
}
$portfeuilleOptions2 .= "";
?>

<br><br>
  <input type="hidden" name="setValue" value="fields" />

<table border="0">
<tr>
  <td>
	  <select name="inFields[]" multiple size="16" style="width : 200px; margin-left: 13px; ">
		  <?=$portfeuilleOptions?>
	  </select>
  </td>
  <td width="70" >
	  <a href="javascript:moveItem(document.selectForm['inFields[]'],document.selectForm['selectedFields[]']);">
		  <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveItem(document.selectForm['selectedFields[]'],document.selectForm['inFields[]']);">
		  <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
	  </a>
  </td>
  <td>
	  <select name="selectedFields[]" multiple size="16" style="width : 200px">
      <?=$portfeuilleOptions2?>
	  </select>
  </td>
  <td width="70" >
	  <a href="javascript:moveOptionUp(document.selectForm['selectedFields[]'])">
		  <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveOptionDown(document.selectForm['selectedFields[]'])">
		  <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle">
	  </a>
  </td>

</tr>

</table>
<br><br>
<?
}
// end portefeuille selectie
else
{

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
  {
		$selectA = "SELECTED";
    $selectB = "SELECTED";
  }
	else
  {
		$selectA = "";
    $selectB = "";
  }
	//if($t == ($aantal))
	//	$selectB = "SELECTED";
  //else
	//	$selectB = "";

	$vermogensbeheerderOptionsA .= "<option value=\"".$gb['Vermogensbeheerder']."\" ".$selectA.">".$gb['Vermogensbeheerder']."</option>\n";
	$vermogensbeheerderOptionsB .= "<option value=\"".$gb['Vermogensbeheerder']."\" ".$selectB.">".$gb['Vermogensbeheerder']."</option>\n";
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
<div class="formlinks"> <?= vt('T/m vermogensbeheerder'); ?> </div>
<div class="formrechts">
<select name="vermogensbeheerderTm" style="width:200px">
<?=$vermogensbeheerderOptionsB?>
</select>
</div>
</div>
<?

//Vermogensbeheerders

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

	$accountmanagerOptionsA .= "<option value=\"".$gb['Accountmanager']."\" ".$selectA.">".$gb['Accountmanager']."</option>\n";
	$accountmanagerOptionsB .= "<option value=\"".$gb['Accountmanager']."\" ".$selectB.">".$gb['Accountmanager']."</option>\n";
}
?>

<div class="formblock">
<div class="formlinks"> <?= vt('Van accountmanager'); ?> </div>
<div class="formrechts">
<select name="accountmanagerVan" style="width:200px">
<?=$accountmanagerOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('T/m accountmanager'); ?> </div>
<div class="formrechts">
<select name="accountmanagerTm" style="width:200px">
<?=$accountmanagerOptionsB?>
</select>
</div>
</div>
<?

if(checkAccess($type))
	$join = "";
else
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";


$DB->SQL("SELECT Clienten.Client, Clienten.Naam FROM Clienten, Portefeuilles ".$join." WHERE Clienten.Client = Portefeuilles.Client AND Portefeuilles.consolidatie=0 AND Portefeuilles.Einddatum  >=  NOW() ORDER BY Client");
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

	$clientOptionsA .= "<option value=\"".$gb['Client']."\" ".$selectA.">".$gb['Client']."</option>\n";
	$clientOptionsB .= "<option value=\"".$gb['Client']."\" ".$selectB.">".$gb['Client']."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> <?= vt('Van client'); ?> </div>
<div class="formrechts">
<select name="clientVan" style="width:200px">
<?=$clientOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('T/m client'); ?> </div>
<div class="formrechts">
<select name="clientTm" style="width:200px">
<?=$clientOptionsB?>
</select>
</div>
</div>
<?



$DB->SQL("SELECT Portefeuille FROM Portefeuilles ".$join." WHERE Portefeuilles.Einddatum  >=  NOW() AND Portefeuilles.consolidatie=0 ORDER BY Portefeuille");
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

	$portfeuilleOptionsA .= "<option value=\"".$gb['Portefeuille']."\" ".$selectA.">".$gb['Portefeuille']."</option>\n";
	$portfeuilleOptionsB .= "<option value=\"".$gb['Portefeuille']."\" ".$selectB.">".$gb['Portefeuille']."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> <?= vt('Van portefeuille'); ?> </div>
<div class="formrechts">
<select name="portefeuilleVan" style="width:200px">
<?=$portfeuilleOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('T/m portefeuille'); ?> </div>
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

	$depotbankOptionsA .= "<option value=\"".$gb['Depotbank']."\" ".$selectA.">".$gb['Depotbank']."</option>\n";
	$depotbankOptions .= "<option value=\"".$gb['Depotbank']."\">".$gb['Depotbank']."</option>\n";
	$depotbankOptionsB .= "<option value=\"".$gb['Depotbank']."\" ".$selectB.">".$gb['Depotbank']."</option>\n";
}
?>

<div class="formblock">
<div class="formlinks"> <?= vt('Van depotbank'); ?> </div>
<div class="formrechts">
<select name="depotbankVan" style="width:200px">
<?=$depotbankOptionsA?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('T/m depotbank'); ?> </div>
<div class="formrechts">
<select name="depotbankTm" style="width:200px">
<?=$depotbankOptionsB?>
</select>
</div>
</div>

<!-- Variabele selecties -->
<?php


}
?>

</fieldset>

</div>

</td>
<td valign="top" width="400">
<br />
<input type="button" onclick="javascript:bereken();" 				value=" Berekenen " style="width:100px"><br><br>
<?
//if($indexSuperUser)
  //echo '<input type="button" onclick="javascript:verwijderen();" 				value=" Verwijderen " style="width:100px"><br><br>';
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