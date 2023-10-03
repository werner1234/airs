<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/13 17:48:10 $
 		File Versie					: $Revision: 1.31 $

 		$Log: reportBuilder.php,v $
 		Revision 1.31  2019/04/13 17:48:10  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("reportBuilder_vars.php");


$loadedFromDb=false;
if(!empty($_GET['do']) && $_GET['setValue'] <> "cleanWizard")
{
	switch($_GET['do'])
	{
		case "queryOpenen" :
			session_start();
			$object = new Querydata();
			if ($_GET['airs'] == "true") $object->setTable("RapportBuilderQueryAirs");
			$object->getById($_GET['id']);
			$_SESSION['reportBuilder'] = unserialize($object->get("Data"));
			$_SESSION['reportBuilder']['naam'] = $object->get("Naam");
			session_write_close();
			$loadedFromDb=true;
   	// do query
			break;
		case "delete":
			$object = new Querydata();
			if ($_GET['airs'] == "true") $object->setTable("RapportBuilderQueryAirs");
			$object->getById($_GET['recordId']);
			$object->remove();
			$_GET['setValue']='standaardWizard';
			break;
	}
}
session_start();
$_SESSION["NAV"] = "";
switch($_GET['setValue'])
{
	case "step" :
		$_SESSION['reportBuilder']["step"] = $_GET["step"];
	  break;
	case "rapport" :
		$_SESSION['reportBuilder'] = array();
		$_SESSION['reportBuilder']['rapport'] = $_GET["rapport"];
		$_SESSION['reportBuilder']['datum'] = $_GET["datum"];
		$_SESSION['reportBuilder']['inactiefOpnemen'] = $_GET['inactiefOpnemen'];
		$_SESSION['reportBuilder']['incLiquiditeiten'] = $_GET['incLiquiditeiten'];
    $_SESSION['reportBuilder']['incConsolidaties'] = $_GET['incConsolidaties'];
		$_SESSION['reportBuilder']["step"] = 1;
		$standaardRapport = true;
		$totdatum = getLaatsteValutadatum();
		$_SESSION['reportBuilder']['datum'] = date("d-m-Y",db2jul($totdatum));
		if ($_GET["rapport"] == "Fondsoverzicht" )
		{
		  $_SESSION['reportBuilder']['standaardFondsen'] = 1;
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		}
		elseif ($_GET["rapport"] == "Managementoverzicht")
		{
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		  $begindatum=jul2form(mktime(1,1,1,1,1,date("Y",db2jul($totdatum))));
		  $_SESSION['reportBuilder']['datumVanaf'] = $begindatum;
		}
		else if ($_GET["rapport"] == "Geaggregeerd-portefeuille-overzicht")
		{
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		}
	  break;
	case "fields" :
		$_SESSION['reportBuilder']['fields'] = array();
		for($a=0; $a < count($_GET['selectedFields']); $a++)
		{
			$_SESSION['reportBuilder']['fields'][] = $_GET['selectedFields'][$a];
		}
		if(count($_GET['selectedFields']) < 1)
			$_SESSION['reportBuilder']['step'] = 1;
		else
			$_SESSION['reportBuilder']['step'] = 2;
	  break;
	case "where" :
		for($b=1; $b <=3; $b++)
		{
		  //aevertaal: gaat onderstaande goed?
		$_SESSION['reportBuilder'][where.$b]= array();
			for($a=0; $a < count($_GET["whereField".$b]); $a++)
			{
				if(empty($_GET["whereField".$b][$a]))
					break;
				$_SESSION['reportBuilder']["where".$b][$a]['field'] 		= $_GET["whereField".$b][$a];
				$_SESSION['reportBuilder']["where".$b][$a]['operator']  = $_GET["whereOperator".$b][$a];
				$_SESSION['reportBuilder']["where".$b][$a]['search'] 		= $_GET["whereSearch".$b][$a];
				$_SESSION['reportBuilder']["where".$b][$a]['andor'] 		= $_GET["whereAndOr".$b][$a];
			}
		}
		$_SESSION['reportBuilder']['step'] = 3;
	  break;
	case "orderby" :
		$_SESSION['reportBuilder']['orderby'] = array();
		for($a=0; $a < count($_GET["orderbyField"]); $a++)
		{
			if(empty($_GET['orderbyField'][$a]))
				break;
			$_SESSION['reportBuilder']['orderby'][$a]['field'] = $_GET['orderbyField'][$a];
			$_SESSION['reportBuilder']['orderby'][$a]['order'] = $_GET['orderbyOrder'][$a];
		}
		$_SESSION['reportBuilder']['step'] = 4;
	  break;
	case "groupby" :
		$_SESSION['reportBuilder']['groupby'] = array();
		for($a=0; $a < count($_GET['groupbyField']); $a++)
		{
			if(empty($_GET["groupbyField"][$a]))
				break;
			$_SESSION['reportBuilder']['groupby'][$a]['field'] 		= $_GET['groupbyField'][$a];
			$_SESSION['reportBuilder']['groupby'][$a]['actionType'] 	= $_GET['groupActionType'][$a];
			$_SESSION['reportBuilder']['groupby'][$a]['actionField'] 	= $_GET['groupActionField'][$a];
		}
		$_SESSION['reportBuilder']['step'] = 5;
	  break;
	case "raportdatum" :
	  $_SESSION['reportBuilder']['datum'] = $_GET['datum'];
	  $_SESSION['reportBuilder']['datumVanaf'] = $_GET['datumVanaf'];
	  $_SESSION['reportBuilder']['step'] = 6;
	  break;
	case "cleanWizard" :
		$_SESSION['reportBuilder'] = array();
		$_SESSION['reportBuilder']['step'] = 0;
		$_SESSION['reportBuilder']['standaardRapport'] = false;
		break;
	case "standaardWizard" :
		$_SESSION['reportBuilder'] = array();
		$_SESSION['reportBuilder']['step'] = 0;
		$_SESSION['reportBuilder']['standaardRapport'] = true;
	  break;
	case "queryOpslaan":
		 if ($_GET['naam'] <> '')
  		{
  		$dbi = new DB();
  		$object = new Querydata();
  		$query = "SELECT * FROM RapportBuilderQuery WHERE Naam = '".$_GET['naam']."'";
  		$dbi->SQL($query);
  		$alertStr = $_GET['naam'];
		if ($oldRec = $dbi->lookupRecord())
		{
	  	$object->set("id",$oldRec['id']);
  		$alertStr .= " ".vt("bestaand report overschreven en")." ";
		}

		$object->set("Naam",$_GET['naam']);
		$object->set("Gebruiker",$USR);
		$object->set("Omschrijving",vt("opgeslagen d.d.")." ".date('d.m.Y')." ".vt("om")." ".date('H:i'));
		$object->set("Data",serialize($_SESSION['reportBuilder']));
		$object->set("Type","reportBuilder");
		if($object->save())
		$alertStr .= " ".vt("opgeslagen");
    	}
    	else
     	{
     	$alertStr = vt("Geen raportnaam opgegeven!");
     	}
     	session_write_close();
		break;
	case "queryOpenen" :
	    $standaardRapport = true;
		$totdatum = getLaatsteValutadatum();

		$object = new Querydata();
		if ($_GET["airs"] == "true") $object->setTable("RapportBuilderQueryAirs");
		$object->getById($_GET["id"]);
		$_SESSION['reportBuilder'] = unserialize($object->get("Data"));
		$_SESSION['reportBuilder']['naam'] = $object->get("Naam");

		if ($standaardRapport == true && $_SESSION['reportBuilder']['rapport'] == "Fondsoverzicht" )
		{
		  $_SESSION['reportBuilder']['standaardFondsen'] = 1;
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		}
		if ($standaardRapport = true && $_SESSION['reportBuilder']['rapport'] == "Managementoverzicht")
		{
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		  $jaar = date("Y",db2jul($totdatum));
		  $begindatumJul = mktime(1,1,1,1,1,$jaar);
		  $begindatum=jul2form($begindatumJul);
		  $_SESSION['reportBuilder']['datumVanaf'] = $begindatum;
		}
		if ($standaardRapport = true && $_SESSION['reportBuilder']['rapport'] == "Geaggregeerd-portefeuille-overzicht")
		{
		  $_SESSION['reportBuilder']['standaardPortefeuilles'] = 1;
		}
		$_SESSION['reportBuilder']['datum'] = date("d-m-Y",db2jul($totdatum));
		$loadedFromDb=true;
		break;
}
session_write_close();
session_start();

if( $_GET['do'] == "screen")
{
	header("Location: reportBuilderPrint.php?type=screen");
	exit;
}

$soortReport = $_SESSION['reportBuilder']["rapport"];   // welk rapport gebruiken we

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem(vt("Nieuwe Rapport"),"reportBuilder.php?setValue=cleanWizard");
$_SESSION["submenu"]->addItem("<br>","");
$_SESSION["submenu"]->addItem(vt("Standaard Rapport"),"reportBuilder.php?setValue=standaardWizard");
$_SESSION["submenu"]->addItem("<br>","");
$_SESSION["submenu"]->addItem(vt("Rapportages muteren"),"querydataList.php?Gebruiker=".$USR."&type=reportBuilder");

$db = new DB();
$query = "SELECT Naam,id FROM RapportBuilderQuery ORDER BY Naam"; //LIMIT 10
$db->SQL($query);
$db->Query();
if ($db->records() > 0)
{
  $_SESSION["submenu"]->addItem("<br><hr>".vt("eigen rapportages")."<br>","");
  $rapJavaSelect .= "\n<SCRIPT LANGUAGE = \"JavaScript\"  TYPE=\"text/javascript\">\n";
  $rapJavaSelect .= "function OpenRap()\n";
  $rapJavaSelect .= "{\nvar item = document.rapmenu.raportages.selectedIndex;\n";
  $rapJavaSelect .= "id = document.rapmenu.raportages.options[item].value;\n";
  $rapJavaSelect .= "id = document.rapmenu.raportages.options[item].value;\n";
  $rapJavaSelect .= "parent.content.location.href=\"reportBuilder.php?setValue=queryOpenen&id=\"+(id);\n}\n";
  $rapJavaSelect .= "</SCRIPT>\n";

  $rapMenu .= "\n <form action=\"reportBuilder.php\" method=\"post\" name=\"rapmenu\"> \n";
  $rapMenu .= "<select name=\"raportages\" size=\"10\" style=\"width:120px; font-size: 10px;\" onChange=\"OpenRap()\"> \n";

  while ($rapItems = $db->nextRecord())
  {
	$rapMenu .= "<option value=\"".$rapItems["id"]."\">".$rapItems["Naam"]."</option>\n";
  }
  $rapMenu  .= "</select>";
  $_SESSION["submenu"]->addItem("$rapJavaSelect $rapMenu","");
}

$content = array();
$content["jsincludes"] 			   .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$content["calendarinclude"] 			= "<script language=JavaScript src=\"javascript/algemeen.js\"  type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content["calendar"] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
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

function submitForm(action)
{
	if(document.wizard['inFields[]'])
	{
		var inFields  			= document.wizard['inFields[]'];
		var selectedFields 	= document.wizard['selectedFields[]'];

		for(j=0; j < inFields.options.length; j++)
		{
	 		inFields.options[j].selected = true;
		}

		for(j=0; j < selectedFields.options.length; j++)
		{
 			selectedFields.options[j].selected = true;
		}
	}

	if(document.wizard['do'])
	{
		if(document.wizard['do'].value == 'screen' )
		{
			document.wizard.target = "generateFrame";
		}
	}

	if (action =='execute')
	{
   	  document.wizard['do'].value = 'screen';
	  document.wizard.target = "generateFrame";
	}

	if (action =='nextStep')
	{
	  document.wizard['do'].value = '';
      document.wizard.target = "content";
	}
	if (action =='delete')
	{
		document.wizard['do'].value = 'delete';
		document.wizard.target = "content";
	}

	document.wizard.submit();
}
</script>
<?
for($b=1; $b <=3; $b++)
{

	for($a=0;$a < count($_SESSION['reportBuilder']["where".$b]);$a++)
	{
		$where .= $_SESSION['reportBuilder']["where".$b][$a]['field']." ".
		          $_SESSION['reportBuilder']["where".$b][$a]['operator']." '".
		          $_SESSION['reportBuilder']["where".$b][$a]['search']."' ".
		          $_SESSION['reportBuilder']["where".$b][$a]['andor']."<br>";
    	$end =    $_SESSION['reportBuilder']["where".$b][$a]['andor'];
	}
}
$where = substr($where,0,(-1 * (strlen($end)+4)))." ";
for($a=0;$a<count($_SESSION['reportBuilder']['orderby']);$a++)
{
	$order .= $_SESSION['reportBuilder']['orderby'][$a]['field']." ".$_SESSION['reportBuilder']['orderby'][$a]['order']." <br>";
}
for($a=0;$a<count($_SESSION['reportBuilder']['groupby']);$a++)
{
	$groep .= " ".vt("op")." ".$_SESSION['reportBuilder']['groupby'][$a]['field'].
	          " <b> ".vt("aktie")."</b> ".$_SESSION['reportBuilder']['groupby'][$a]['actionType'].
	          " <b> ".vt("op veld")."</b> ".$_SESSION['reportBuilder']['groupby'][$a]['actionField']." <br>";
}

$rapdat = $_SESSION['reportBuilder']['datum'];
$vanafDat =	$_SESSION['reportBuilder']['datumVanaf'];
?>
<br>
<table border="0" style="border: 1px solid Gray">
<tr>
	<td colspan="2"><b><?=vt("Rapport opbouw")?></b>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;<?=$_SESSION['reportBuilder']['naam'] ?></td>
</tr>
<tr>
	<?php
	if($loadedFromDb==true)
	{
		$reportLink='rapport';
		if($_SESSION['reportBuilder']['inactiefOpnemen']==1)
			$inactiefInfo=' (inclusief inactieve portefeuilles)';
		else
			$inactiefInfo.=' (exclusief inactieve portefeuilles)';

		if($_SESSION['reportBuilder']['incLiquiditeiten']==1)
			$inactiefInfo.=' (inclusief Liquiditeiten)';
    if($_SESSION['reportBuilder']['incConsolidaties']==1)
      $inactiefInfo.=' (inclusief Consolidaties)';
	}
	else
	{
		$reportLink = '<a href="' . $PHP_SELF . '?setValue=step&step=0"><u>'.vt("rapport").'</u></a>';
		$inactiefInfo='';
	}

	?>
	<td align="right" width="100"  valign="top"><?=$reportLink?>: </td>
	<td><?=$_SESSION['reportBuilder']['rapport']?> <?=$inactiefInfo?></td>
</tr>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=1"><u><?=vt("velden")?></u></a> : </td>
	<td><?=implode(", ",$_SESSION['reportBuilder'][fields])?></td>
</tr>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=2"><u><?=vt("selectie")?></u></a> : </td>
	<td><?=$where?></td>
</tr>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=3"><u><?=vt("sortering")?></u></a> : </td>
	<td><?=$order?></td>
</tr>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=4"><u><?=vt("groep")?></u></a> : </td>
	<td><?=$groep?></td>
</tr>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=5"><u><?=vt("datum")?></u></a> : </td>
	<td><?=$rapdat?></td>
</tr>
<?
if ($_SESSION['reportBuilder']['rapport'] == "Managementoverzicht")
{
?>
<tr>
	<td align="right"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=5"><u><?=vt("vanaf datum")?></u></a> : </td>
	<td><?=$vanafDat?></td>
</tr>
<?
}
?>
</table>
<br>

<form action="reportBuilder.php" name="wizard" method="GET">

<?php
if ($_SESSION['reportBuilder']['standaardRapport'] == false )	echo "<input type=\"hidden\" name=\"do\">"; //rvv

if(!$_SESSION['reportBuilder']['step'] || $_SESSION['reportBuilder']["step"] == 0)
{
	if ( $_SESSION['reportBuilder']['standaardRapport'] == false)
	{
	// stap 1 tabel selectie
	echo "<b>".vt("Stap 1: rapport soort")."</b><br><br>";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"rapport\" />";
	?>
	<select name="rapport">
	<?php
	sort($rbuilder);

  	for($a = 0; $a < count($rbuilder); $a++)
  		{
    	$sstring = "";
	  	if($_SESSION['reportBuilder']['rapport'] == $rbuilder[$a]) $sstring = "selected";
	  	echo "<option value=\"".$rbuilder[$a]."\" ".$sstring.">".vt($rbuilder[$a])."</option>\n";
  		}

	}
	else
	{
	// stap standaard rapport keuze
	echo "<input type=\"hidden\" name=\"do\" />";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"queryOpenen\">";
	echo "<input type=\"hidden\" name=\"airs\" value=\"true\" />";
	echo "<b>".vt("Kies een rapport soort")."</b><br><br>";
	?>
	<select name="id">
	<?php
  	$DB = new DB();
  	$DB->SQL("SELECT id, naam FROM RapportBuilderQueryAirs WHERE type = 'standaard' ORDER BY id ");
  	$DB->Query();
  	$aantal = $DB->records();
  	$t=0;
	while($queryData = $DB->NextRecord())
	{
	  echo "<option name=\"id\" value=\"".$queryData['id']."\">".$queryData['naam']."</option>\n";
	}
 }
?>
</select>
<br><br>
<input type="checkbox" name="inactiefOpnemen" value="1" <?if($_SESSION['reportBuilder']['inactiefOpnemen'] == 1)echo "CHECKED";?>> <?=vt("Inactieve portefeuilles opnemen")?>.
<input type="checkbox" name="incLiquiditeiten" value="1" <?if($_SESSION['reportBuilder']['incLiquiditeiten'] == 1)echo "CHECKED";?>> <?=vt("Inclusief Liquiditeiten")?>.
 <input type="checkbox" name="incConsolidaties" value="1" <?if($_SESSION['reportBuilder']['incConsolidaties'] == 1)echo "CHECKED";?>> <?=vt("Inclusief Consolidaties")?>.

<br>
<?php
}
else if($_SESSION['reportBuilder']["step"] == 1)
{

	// maak object aan en loop over list fields.
natcasesort($rselection[$soortReport]);
foreach ($rselection[$soortReport] as $value)
 $tmp[]=$value;
$rselection[$soortReport] = $tmp;

  	$selectedFields = $_SESSION['reportBuilder']['fields']; //welke velden waren geselecteerd
	for($a = 0; $a < count($rselection[$soortReport]); $a++)
	{
	  if (!in_array($rselection[$soortReport][$a],$selectedFields))
		  $options .= "<option value=\"".$rselection[$soortReport][$a]."\">".$rselection[$soortReport][$a]."</option>";
	}
	for ($x=0;$x < count($selectedFields);$x++)
	{
	  $optionsT .= "\n  <option value=\"".$selectedFields[$x]."\">".$selectedFields[$x]."</option>";
	}
?>
  <b><?=vt("Stap 2: veld selectie")?></b><br><br>
  <input type="hidden" name="setValue" value="fields" />

<table border="0">
<tr>
  <td>
	  <select name="inFields[]" multiple size="8" style="width : 200px">
		  <?=$options?>
	  </select>
  </td>
  <td width="30">
	  <a href="javascript:moveItem(document.wizard['inFields[]'],document.wizard['selectedFields[]']);">
		  <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="<?=vt("toevoegen")?> align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveItem(document.wizard['selectedFields[]'],document.wizard['inFields[]']);">
		  <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="<?=vt("verwijderen")?> align="absmiddle">
	  </a>
  </td>
  <td>
	  <select name="selectedFields[]" multiple size="8" style="width : 200px">
      <?=$optionsT?>
	  </select>
  </td>
  <td>
	  <a href="javascript:moveOptionUp(document.wizard['selectedFields[]'])">
		  <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveOptionDown(document.wizard['selectedFields[]'])">
		  <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle">
	  </a>
  </td>
</tr>
</table>
<? //echo "-r- ". serialize($_SESSION['reportBuilder']) ." -r-"; //Om een standaard rapport te maken
}
else if($_SESSION['reportBuilder']['step'] == 2)
{
	sort($rselection[$soortReport]); //echo $rselection; echo $soortReport; print_r($rselection[$soortReport]);
	// opbouwen uit 3 losse selectie mogelijkheden!!! ivm met combinaties die anders niet mogelijk zijn.
?>
  <b><?=vt("Stap 3: selectie")?></b><br><br>
  <input type="hidden" name="setValue" value="where" />
  <?=vt("Selectie 1")?>
  <table border="0">
  <tr>
<?
  $options = "<option value=\"\">--</option>";
  for($a = 0; $a < count($rselection[$soortReport]); $a++)
  {
  	if($rselection[$soortReport."Select"][$rselection[$soortReport][$a]] == 1)
	  {
	    $selectionArray[] = $rselection[$soortReport][$a];
	  }
  }
for($a=0;$a <2; $a++)
{
?>
    <td>
      <?=($a+1)?>:
    </td>
    <td>
	    <select name="whereField1[<?=$a?>]" style="width : 200px">
<?
if ( $_SESSION['reportBuilder']['standaardFondsen'] == 1 && ($a == 0 || $a == 1) ) //Wanneer we een fonds moeten selecteren fondsen ophalen.
	{
	?><option value="Fonds" selected> <?=vt("Fonds")?> </option>
      </select>
    </td>
    <td valign="top" width="30">
	    <select name="whereOperator1[<?=$a?>]" style="width : 50px">
	<?=getOptions($operatorArray,$_SESSION['reportBuilder']["where1"][$a]["operator"],false );?>
    	</select>
    </td>
    <td>
	<?
	$alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
	$DB = new DB();
	$DB->SQL("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Fonds");
	$DB->Query();
	$aantal = $DB->records();
	$t=0;
	while($gb = $DB->NextRecord())
	{
	  $selectionArray['fondsen'][$gb['Fonds']]=$gb['Fonds'].' ('.$gb['Omschrijving'].')';
	}
//	ksort($selectionArray['fondsen']);
	echo "<select name=\"whereSearch1[$a]\" style=\"width:165px\">\n";
	echo getOptions1($selectionArray['fondsen'],$_SESSION['reportBuilder']["where1"][$a]['search']);
	?>
	</select>
    <td>
	    <select name="whereAndOr1[<?=$a?>]" style="width : 50px">
	    <option value="AND" selected> <?=vt("EN")?> </option>
	<?
	}
else //Anders vrij veld tonen.
	{
	echo getOptions($selectionArray,$_SESSION['reportBuilder']["where1"][$a]['field'],true,false );
	?>
    </select>
    </td>
    <td valign="top" width="30">
	    <select name="whereOperator1[<?=$a?>]" style="width : 50px">
		<?=getOptions($operatorArray,$_SESSION['reportBuilder']["where1"][$a]["operator"],false );?>
    	</select>
    </td>
    <td>
	<input name="whereSearch1[<?=$a?>]" value="<?=$_SESSION['reportBuilder']["where1"][$a]["search"]?>" size="20"> </td>
    <td>
	    <select name="whereAndOr1[<?=$a?>]" style="width : 50px">
	<?
	if ($a != 1) //Laatste veld geen and/or
	  echo getOptions($andOrArray,$_SESSION['reportBuilder']["where1"][$a]["andor"],false );
	else
	  echo "<option value=\"\" selected> - </option> ";
	}
	?>
     </select>
    </td>
  </tr>
<?
}
?>
  </table>
  <?=vt("(EN) Selectie 2")?>
  <table border="0">
  <tr>
<?
  $options = "<option value=\"\">--</option>";
  for($a = 0; $a < count($rselection[$_SESSION['reportBuilder']['rapport']]); $a++)
  {
 	  if($rselection[$soortReport."Select"][$rselection[$soortReport][$a]] == 1)
	  {
	    $selectionArray[] = $rselection[$soortReport][$a];
	  }
  }
  for($a=0;$a <4; $a++)
  {
?>
    <td>
      <?=($a+1)?>:
    </td>
    <td>
	    <select name="whereField2[<?=$a?>]" style="width : 200px">
<?
	if ( $_SESSION['reportBuilder']['standaardPortefeuilles'] == 1 && ($a ==0 || $a ==1) )
	{
	  echo "<option value=\"Portefeuille\" selected> ".vt("Portefeuille")." </option> ";
	  ?>
    	</select>
      </td>
      <td valign="top" width="30">
	    <select name="whereOperator2[<?=$a?>]" style="width : 50px">
	  <?
 		if 		($a == 0)
		  echo "<option value=\">=\" selected> >= </option> ";
		elseif 	($a == 1)
		  echo "<option value=\"<=\" selected> <= </option> ";
	  ?>
    	</select>
    </td>
    <td>
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

  	if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
      $internDepotToegang="OR Portefeuilles.interndepot=1";

	  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
    else
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }
	  if($_SESSION['reportBuilder']['inactiefOpnemen'] == 1)
	    $extraquery=' WHERE 1 ';
	  else
	    $extraquery  = "WHERE Portefeuilles.Einddatum > NOW() ";

	  $DB->SQL("SELECT Portefeuille FROM Portefeuilles  $join $extraquery $beperktToegankelijk ORDER BY Portefeuille");
	  $DB->Query();
	  $aantal = $DB->records();
	  $t=0;
	  while($gb = $DB->NextRecord())
	  	{
	    $selectionArray['Portefeuilles'][$gb['Portefeuille']]= $gb['Portefeuille'];
	  	}
	  ?><select name="whereSearch2[<?=$a?>]" style="width:165px"><?
		$selectedPortefeuille = $_SESSION['reportBuilder']["where2"][$a]['search'];
		if ($selectedPortefeuille == "" && $a == 0)
	  	  echo getOptions1($selectionArray['Portefeuilles'],$selectedPortefeuille);
		elseif ($selectedPortefeuille == "" && $a == 1)
	  	  echo getOptions1($selectionArray['Portefeuilles'],array_pop($selectionArray['Portefeuilles']));
		elseif ($selectedPortefeuille != "" )
	  	  echo getOptions1($selectionArray['Portefeuilles'],$selectedPortefeuille);
	  ?>
	</select>
 	</td>
      <td>
	    <select name="whereAndOr2[<?=$a?>]" style="width : 50px">
         <option value="AND" selected> <?=vt("EN")?> </option>
    	</select>
      </td>
  	</tr>
	<?
	}
	elseif ( $_SESSION['reportBuilder']['standaardPortefeuilles'] == 1 && ($a ==2 || $a ==3) )
	{
	  echo "<option value=\"Client\" selected> ".vt("Client")." </option> ";
	  ?>
    	</select>
      </td>
      <td valign="top" width="30">
	    <select name="whereOperator2[<?=$a?>]" style="width : 50px">
	  <?
 		if 		($a == 2)
		  echo "<option value=\">=\" selected> >= </option> ";
		elseif 	($a == 3)
		  echo "<option value=\"<=\" selected> <= </option> ";
	  ?>
    	</select>
    </td>
    <td>
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

	  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') ";
    else
	    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }
	  if($_SESSION['reportBuilder']['inactiefOpnemen'] == 1)
	    $extraquery=' WHERE 1 ';
	  else
	    $extraquery  = "WHERE Portefeuilles.Einddatum > NOW() ";

	  $DB->SQL("SELECT Client FROM Portefeuilles  $join $extraquery $beperktToegankelijk ORDER BY Client");
	  $DB->Query();
	  $aantal = $DB->records();
	  $t=0;
	  while($gb = $DB->NextRecord())
	  	{
	    $selectionArray['Client'][$gb['Client']]= $gb['Client'];
	  	}
	  ?><select name="whereSearch2[<?=$a?>]" style="width:165px"><?
		$selectedClient = $_SESSION['reportBuilder']["where2"][$a]['search'];

		if ($selectedClient == "" && $a == 2)
	  	  echo getOptions1($selectionArray['Client'],$selectedClient);
		elseif ($selectedClient == "" && $a == 3)
	  	  echo getOptions1($selectionArray['Client'],array_pop($selectionArray['Client']));
		elseif ($selectedClient != "" )
	  	  echo getOptions1($selectionArray['Client'],$selectedClient);
	  ?>
	</select>
 	</td>
      <td>
	    <select name="whereAndOr2[<?=$a?>]" style="width : 50px">
         <option value="AND" selected> <?=vt("EN")?> </option>
    	</select>
      </td>
  	</tr>
	<?
	}
	else
	{
	  echo getOptions($selectionArray,$_SESSION['reportBuilder']["where2"][$a]["field"],true,false );
	  ?>
    	</select>
      </td>
      <td valign="top" width="30">
	    <select name="whereOperator2[<?=$a?>]" style="width : 50px">
      <?=getOptions($operatorArray,$_SESSION['reportBuilder']["where2"][$a]["operator"],false );?>
    	</select>
    </td>
    <td>
    <input name="whereSearch2[<?=$a?>]" value="<?=$_SESSION['reportBuilder']["where2"][$a]["search"]?>" size="20">
	</td>
    <td>
	    <select name="whereAndOr2[<?=$a?>]" style="width : 50px">
       <?
        if ($a != 2)
	      echo getOptions($andOrArray,$_SESSION['reportBuilder']["where2"][$a]["andor"],false );
	    else
	      echo "<option value=\"\" selected> - </option> ";
	   ?>
    	</select>
    </td>
  </tr>
	<?
	}
}
?>
  </table>
  <?=vt("(EN) Selectie 3")?>
  <table border="0">
<?
for($a = 0; $a < count($rselection[$_SESSION['reportBuilder']["rapport"]]); $a++)
{
	if($rselection[$soortReport."Select"][$rselection[$soortReport][$a]] == 2)
	{
	  $selectionArray[] = $rselection[$soortReport][$a];
	}
}
for($a=0;$a <3; $a++)
{
?>
  <tr>
    <td>
      <?=($a+1)?>:
    </td>
    <td>
	    <select name="whereField3[<?=$a?>]" style="width : 200px">
        <?=getOptions($selectionArray,$_SESSION['reportBuilder']["where3"][$a]['field'],true,false );?>
	    </select>
    </td>
    <td valign="top" width="30">
	    <select name="whereOperator3[<?=$a?>]" style="width : 50px">
        <?=getOptions($operatorArray,$_SESSION['reportBuilder']["where3"][$a]["operator"],false );?>
	    </select>
    </td>
    <td>
	    <input name="whereSearch3[<?=$a?>]" value="<?=$_SESSION['reportBuilder']["where3"][$a]["search"]?>" size="20">
    </td>
    <td>
    	<select name="whereAndOr3[<?=$a?>]" style="width : 50px">
        <?
        if ($a != 2)
	      echo getOptions($andOrArray,$_SESSION['reportBuilder']["where3"][$a]["andor"],false );
	    else
	      echo "<option value=\"\" selected> - </option> ";
        ?>
	    </select>
    </td>
  </tr>
<?
}
?>
  </table>
<?
}
else if($_SESSION['reportBuilder']['step'] == 3)
{
	for($a=0; $a < count($_SESSION['reportBuilder']['fields']);$a++)
	{
		$fieldArray[] = $_SESSION['reportBuilder']['fields'][$a];
	}
?>
	<b><?=vt("Stap 4: sortering")?></b><br><br>
	<input type="hidden" name="setValue" value="orderby" />
  <br><br>
  <table border="0">
<?
	for($a=0;$a <3; $a++)
	{
?>
    <tr>
      <td>
        <?=($a+1)?>:
      </td>
      <td>
	      <select name="orderbyField[<?=$a?>]" style="width : 200px">
          <?=getOptions($fieldArray,$_SESSION['reportBuilder']["orderby"][$a]["field"],true,false);?>
      	</select>
      </td>
      <td>
	      <select name="orderbyOrder[<?=$a?>]" style="width : 200px">
          <?=getOptions($orderByArray,$_SESSION['reportBuilder']["orderby"][$a]["order"],false );?>
      	</select>
      </td>
    </tr>
<?
	}
?>
  </table>
<?
}
else if($_SESSION['reportBuilder']['step'] == 4)
{
	for($a=0; $a < count($_SESSION['reportBuilder']['fields']);$a++)
	{
		$fieldArray[] = $_SESSION['reportBuilder']['fields'][$a];
	}
  $a = 0;
?>
  <b><?=vt("Stap 5: groeperen")?></b><br><br>
  <input type="hidden" name="setValue" value="groupby" />
  <br><br>
  <table border="0">
  <tr>
	  <td></td>
	  <td><?=vt("Groeperen")?></td>
	  <td><?=vt("Actie op veld")?></td>
    <td><?=vt("Actie")?></td>
  </tr>
		<?
    for($a=0;$a<3;$a++)
		{
			?>
			<tr>
				<td>
					<?=($a + 1)?>:
				</td>
				<td>
					<select name="groupbyField[<?=$a?>]" style="width : 200px">
						<?=getOptions($fieldArray, $_SESSION['reportBuilder']["groupby"][$a]["field"], true, false);?>
					</select>
				</td>
				<td>
					<select name="groupActionField[<?=$a?>]" style="width : 200px">
						<?=getOptions($fieldArray, $_SESSION['reportBuilder']["groupby"][$a]["actionField"], true, false);?>
					</select>
				</td>
				<td>
					<select name="groupActionType[<?=$a?>]" style="width : 200px">
						<?=getOptions($groupActionArray, $_SESSION['reportBuilder']["groupby"][$a]["actionType"], false);?>
					</select>
				</td>
			</tr>
			<?
		}
		?>
	</table>
<?
}
else if($_SESSION['reportBuilder']['step'] == 5)
{
  $date = getLaatsteValutadatum();
  $kal = new DHTML_Calendar();
  $inp = array ('name' =>"datum",'value'=>$_SESSION['reportBuilder']['datum'],'size'  => "11");
?>
  	<b><?=vt("Stap 6: Raportagedatum")?></b>
  	<input type="hidden" name="setValue" value="raportdatum" />
	<table border="0">
	<tr><td> <?=vt("Rapportagedatum")?> :</td><td> <?=$kal->make_input_field("",$inp,"");?></td></tr>
<?
if ($standaardRapport = true && $_SESSION['reportBuilder']['rapport'] == "Managementoverzicht")
{
	$inp["name"]="datumVanaf";
	$inp["value"]=$_SESSION['reportBuilder']['datumVanaf'];
?>	<tr><td><?=vt("Vanafdatum")?> :</td><td><?=$kal->make_input_field("",$inp,"v");?></td></tr><?
}
	echo "</table>";
}
elseif($_SESSION['reportBuilder']['step'] > 5 && !$_GET['do'])
{
	// query opslaan
?>
	<b><?=vt("Stap 7: afdrukken")?></b><br><br>
	<input type="hidden" name="setValue" value="queryOpslaan" /><br>
	<br>
	<?=vt("Deze rapportage opslaan als")?> : <input type="text" name="naam" value="" size="20"><br>
<?
	// query afdrukken
}
if($alertStr <> '')
{
  echo "<script>alert('$alertStr');</script>";
}
?>
  <br>
  <br>
  <input type="button" value=" >> <?=vt("volgende")?> " onClick="submitForm('nextStep');">
<?
if ($_SESSION['reportBuilder']["standaardRapport"] == false )
  echo "  <input type=\"button\" value=\" >> ".vt("nu uitvoeren")." \" onClick=\"submitForm('execute');\"> ";
if($loadedFromDb)
{
	echo "  <input type=\"hidden\" name=\"recordId\" value=\"".$_GET['id']."\"> ";
	echo "  <input type=\"button\" value=\" >> ".vt("rapport verwijderen")." \" onClick=\"submitForm('delete');\"> ";
}
?>



</form>
<?echo progressFrame();?>
</body>
<?
session_write_close();
echo template($__appvar["templateRefreshFooter"],$content);
