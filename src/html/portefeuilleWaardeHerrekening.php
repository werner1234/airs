<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/06/23 08:48:22 $
 		File Versie					: $Revision: 1.8 $

 		$Log: portefeuilleWaardeHerrekening.php,v $
 		Revision 1.8  2018/06/23 08:48:22  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/05/26 17:21:28  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/11/05 17:49:52  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/11/05 11:36:15  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/11/05 10:02:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/31 18:53:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/07/31 15:44:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/11/25 13:15:50  rvv
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


$kal2 = new DHTML_Calendar();
$content['calendar'] = $kal2->get_load_files_code();
echo template($__appvar["templateContentHeader"],$content);
if($_POST['posted'])
{
	$start = getmicrotime();
	//$selectData['datumVan'] 							= form2jul($_POST['datumVan']);
	//$selectData['datumTm'] 								= form2jul($_POST['datumTm']);
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
  		// selectie scherm.
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
	  if($_POST['alternatievedatum']==1)
		{
			$rapportageDatum=date('Y-m-d',form2jul($_POST['datum_tot']));
			$extraquery .= " Portefeuilles.StartDatum < '" . $rapportageDatum ."' AND";
			$einddatum ="'" . $rapportageDatum . "'";
			$cleanQueries=array("TRUNCATE table tempLaatstePortefeuilleWaarde","TRUNCATE table tempLaatsteFondsWaarden");
		}
	  else
	  {
		  $einddatum="NOW()";
			$cleanQueries=array();
	  }
    $DBs = new DB();
    foreach($cleanQueries as $query)
    {
      $DBs->SQL($query);
      $DBs->Query();
    }
//	listarray($_POST);

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE Portefeuilles.Einddatum  >=  $einddatum AND ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";
//echo $query;echo '';exit;
		$DBs->SQL($query);
		$DBs->Query();
	  $portefeuilles=array();
    while($data=$DBs->nextRecord())
    {
      $portefeuilles[$data['Portefeuille']]=$data['Portefeuille'];
    }


	// maak progressbar
	$prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();
	$prb->moveStep(0);
	$pro_step = 0;
	$pro_multiplier = 100 / count($portefeuilles);
	$rapportageDatum=false;
	if($_POST['alternatievedatum']==1)
	{
		$rapportageDatum=date('Y-m-d',form2jul($_POST['datum_tot']));
	}
  foreach($portefeuilles as $portefeuille)
  {
    portefeuilleWaardeHerrekening($portefeuille,$rapportageDatum);
  	$pro_step += $pro_multiplier;
  	$prb->moveStep($pro_step);
  }
	bepaalSignaleringen($__appvar["bedrijf"]);
 	$prb->hide();
	exit;
}
else
{
	// selecteer laatst bekende valutadatum
	$totdatum = getLaatsteValutadatum();
 // $jr = substr($totdatum,0,4);

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

$html .= "<b>Selectie methode</b><br><br><form name=\"selectForm\"> <table>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" id=\"selectieall\" value=\"alles\"        $selectieAlles        onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=alles'\"></td><td style='font-size: 12px;'><label for=\"selectieall\" title=\"multiselectie\"> " . vt('multiselectie') . "</label></td></tr>";
$html .= "<tr><td><input type=\"radio\" name=\"selectie\" id=\"selectieport\" value=\"portefeuille\" $selectiePortefeuille onClick=\"parent.frames['content'].location = '$PHP_SELF?selectie=portefeuille'\"></td><td style='font-size: 12px;'>  <label for=\"selectieport\" title=\"enkelvoudige\"> " . vt('enkelvoudige selectie') . " </label> </td></tr>";
$html .= '</table></form>';

	$_SESSION[NAV] = "";
	$_SESSION[submenu] = New Submenu();
  $_SESSION[submenu]->addItem($html,"");
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
<b><?= vt('Herberekening Portefeuille waarde'); ?></b>
<br>
<br>
<table border="0">
<tr>
	<td width="540">

<fieldset id="Periode" >
<legend accesskey="R"><?= vt('Periode'); ?></legend>


<div class="formblock">
<div class="formlinks"> <?= vt('T/m datum'); ?> </div>
<div id="fixedDateDiv" class="formrechts">
<?php
echo date("d-m-Y",db2jul($totdatum));
?>
</div>
<div id="customDateDiv" class="formrechts" style="display: none">
		<?php
		$inp2 = array ('name' =>"datum_tot",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");
		echo $kal2->make_input_field("",$inp2,"")
		?>
</div>
</div>



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
<div class="formlinks"> <?= vt('Van vermogensbeheerder'); ?> </div>
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

	$accountmanagerOptionsA .= "<option value=\"".$gb[Accountmanager]."\" ".$selectA.">".$gb[Accountmanager]."</option>\n";
	$accountmanagerOptionsB .= "<option value=\"".$gb[Accountmanager]."\" ".$selectB.">".$gb[Accountmanager]."</option>\n";
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


$DB->SQL("SELECT Clienten.Client, Clienten.Naam FROM Clienten, Portefeuilles ".$join." WHERE Clienten.Client = Portefeuilles.Client ORDER BY Client");
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



$DB->SQL("SELECT Portefeuille FROM Portefeuilles ".$join." ORDER BY Portefeuille");
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

	$depotbankOptionsA .= "<option value=\"".$gb[Depotbank]."\" ".$selectA.">".$gb[Depotbank]."</option>\n";
	$depotbankOptions .= "<option value=\"".$gb[Depotbank]."\">".$gb[Depotbank]."</option>\n";
	$depotbankOptionsB .= "<option value=\"".$gb[Depotbank]."\" ".$selectB.">".$gb[Depotbank]."</option>\n";
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
	<input type="checkbox" name="alternatievedatum"		onclick="javascript:if(this.checked==true){$('#customDateDiv').show(); $('#fixedDateDiv').hide(); }else{$('#customDateDiv').hide(); $('#fixedDateDiv').show();   };" 	value="1" >
	<?= vt('Gebruik temp tabel'); ?><br><br>
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