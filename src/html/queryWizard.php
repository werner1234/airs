<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/12/20 06:44:32 $
 		File Versie					: $Revision: 1.12 $

 		$Log: queryWizard.php,v $
 		Revision 1.12  2017/12/20 06:44:32  rvv
 		*** empty log message ***


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("reportBuilder_vars.php");

session_start();

if (GetModuleAccess("CRM"))
{
  if ($_GET['type'] == 'CRM')
    $_SESSION['queryWizard']['CRM'] = true;  

  if($_GET['type'] == 'all')
     $_SESSION['queryWizard']['CRM'] = false; 

  if ($_SESSION['queryWizard']['CRM'] == true)
    $queryWizardObjects = array();

  $queryWizardObjects['Naw']='Relaties';
  $queryWizardObjects['CRM_naw_kontaktpersoon']='Contactpersonen';
  $queryWizardObjects['CRM_naw_cf']='Relaties extra';
}



$_POST = array_merge($_POST,$_GET);

if(!empty($_POST['do']) && $_POST["setValue"] <> "cleanWizard")
{
	switch($_POST['do'])
	{
		case "screen" :
			header("Location: queryWizardPrint.php?type=screen");
			exit;
		break;
		case "queryOpslaan" :
			session_start();
			$object = new Querydata();
			$object->set("Naam",$_POST['naam']);
			$object->set("Gebruiker",$USR);
			$object->set("Data",serialize($_SESSION['queryWizard']));
			$object->set("Type","queryWizard");
			if($object->save())
				$querySaved = true;
			session_write_close();
		// do query
		break;
		case "queryOpenen" :
			session_start();
			$object = new Querydata();
			$object->getById($_POST["id"]);
			$_SESSION['queryWizard'] = unserialize($object->get("Data"));

			session_write_close();
		// do query
		break;
	}
}


$_SESSION["NAV"] = "";

switch($_POST["setValue"])
{
	case "step" :
		$_SESSION["queryWizard"]["step"] = $_POST["step"];
	  break;
	case "object" :
		$_SESSION["queryWizard"] = array();
		$_SESSION["queryWizard"]["object"] = $_POST["object"];
		$_SESSION["queryWizard"]["step"] = 1;
	  break;
	case "fields" :
		$_SESSION["queryWizard"]["fields"] = array();
		for($a=0; $a < count($_POST["selectedFields"]); $a++)
		{
			$_SESSION["queryWizard"]["fields"][] = $_POST["selectedFields"][$a];
		}
		if(count($_POST["selectedFields"]) < 1)
			$_SESSION["queryWizard"]["step"] = 1;
		else
			$_SESSION["queryWizard"]["step"] = 2;
	  break;
	case "where" :
		$_SESSION["queryWizard"]["where"] = array();
		for($a=0; $a < count($_POST["whereField"]); $a++)
		{
			if(empty($_POST["whereField"][$a]))
				break;
			$_SESSION["queryWizard"]["where"][$a]["field"]    = $_POST["whereField"][$a];
			$_SESSION["queryWizard"]["where"][$a]["operator"] = $_POST["whereOperator"][$a];
			$_SESSION["queryWizard"]["where"][$a]["search"]   = $_POST["whereSearch"][$a];
			$_SESSION["queryWizard"]["where"][$a]["andor"]    = $_POST["whereAndOr"][$a];
		}
		$_SESSION["queryWizard"]["step"] = 3;
	  break;
	case "orderby" :
		$_SESSION["queryWizard"]["orderby"] = array();
		for($a=0; $a < count($_POST["orderbyField"]); $a++)
		{
			if(empty($_POST["orderbyField"][$a]))
				break;
			$_SESSION["queryWizard"]["orderby"][$a]["field"] = $_POST["orderbyField"][$a];
			$_SESSION["queryWizard"]["orderby"][$a]["order"] = $_POST["orderbyOrder"][$a];
		}
		$_SESSION["queryWizard"]["step"] = 4;
	  break;
	case "groupby" :
		$_SESSION["queryWizard"]["groupby"] = array();
		for($a=0; $a < count($_POST["groupbyField"]); $a++)
		{
			if(empty($_POST["groupbyField"][$a]))
				break;
			$_SESSION["queryWizard"]["groupby"][$a]["field"] = $_POST["groupbyField"][$a];
			$_SESSION["queryWizard"]["groupby"][$a]["actionType"] = $_POST["groupActionType"][$a];
			$_SESSION["queryWizard"]["groupby"][$a]["actionField"] = $_POST["groupActionField"][$a];
		}
		$_SESSION["queryWizard"]["step"] = 5;
	  break;
	case "cleanWizard" :
		//$_SESSION[queryWizard] = array();
		$_SESSION["queryWizard"]["step"] = 0;
	  break;
}

session_write_close();
session_start();

$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem(vt("Nieuwe Query"),"queryWizard.php?setValue=cleanWizard");
$_SESSION["submenu"]->addItem("<br>","");
$_SESSION["submenu"]->addItem(vt("Query openen"),"querydataList.php?Gebruiker=".$USR."&type=queryWizard");


$content = array();

echo template($__appvar["templateContentHeader"],$content);
include_once("javascript/queryWizard.js");
?>

<?
  for($a=0 ; $a < count($_SESSION["queryWizard"]["where"]);$a++)
  {
	  $where .= " ".$_SESSION["queryWizard"]["where"][$a]["field"]." ".
	                $_SESSION["queryWizard"]["where"][$a]["operator"]." '".
	                $_SESSION["queryWizard"]["where"][$a]["search"]."' ".
	                $_SESSION["queryWizard"]["where"][$a]["andor"]."<BR>";
	  $end =  $_SESSION["queryWizard"]["where"][$a]["andor"];
  }

  $where = substr($where,0,-1 * (strlen($end)+4))." ";  // laatste operator ervan afknippen
  for($a=0 ;$a < count($_SESSION["queryWizard"]["orderby"]);$a++)
  {
	  $order .= $_SESSION["queryWizard"]["orderby"][$a]["field"]." ".$_SESSION["queryWizard"]["orderby"][$a]["order"]."<br>";
  }

  for($a=0 ; $a < count($_SESSION["queryWizard"]["groupby"]);$a++)
  {
	  $groep .= $_SESSION["queryWizard"]["groupby"][$a]["field"]."<b> ".vt("aktie")."</b> ".$_SESSION["queryWizard"]["groupby"][$a]["actionType"]."<b> ".vt("op veld")."</b> ".$_SESSION["queryWizard"]["groupby"][$a]["actionField"]." <br>";
  }

?>

<br>
<table border="0" style="border: 1px solid Gray">
<tr>
	<td colspan="2"><b><?=vt("Query opbouw")?></b></td>
</tr>
<tr>
	<td align="right" width="100"  valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=0"><u><?=vt("tabel")?></u></a> : </td>
	<td><?=$_SESSION["queryWizard"]["object"]?></td>
</tr>
<tr>
	<td align="right" valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=1"><u><?=vt("velden")?></u></a> : </td>
	<td><?=implode("<br>",$_SESSION["queryWizard"]["fields"])?></td>
</tr>
<tr>
	<td align="right" valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=2"><u><?=vt("selectie")?></u></a> : </td>
	<td><?=$where?></td>
</tr>
<tr>
	<td align="right" valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=3"><u><?=vt("sortering")?></u></a> : </td>
	<td><?=$order?></td>
</tr>
<tr>
	<td align="right" valign="top"><a href="<?=$PHP_SELF?>?setValue=step&step=4"><u><?=vt("groepeer")?></u></a> : </td>
	<td><?=$groep?></td>
</tr>
</table>
<br>
<form action="queryWizard.php" name="wizard" method="POST">

<?php

/**
 * STAP 1.
 */
if(!$_SESSION["queryWizard"]["step"] || $_SESSION["queryWizard"]["step"] == 0)
{
	// stap 1 tabel selectie
?>
	<b><?=vt("Stap 1: tabel selectie")?></b><br><br>
	<input type="hidden" name="setValue" value="object" />
  <select name="object">

<?
/*
  sort($queryObjects);
  for($a = 0; $a < count($queryObjects); $a++)
  {
	  $sstring = "";
	  if($_SESSION[queryWizard][object] == $queryObjects[$a])
		  $sstring = "selected";
	  echo "\n   <option value=\"".$queryObjects[$a]."\" ".$sstring.">".$queryObjects[$a]."</option>\n";
  }
*/

?>
<?
    //  $options = getOptions($queryObjects,$_SESSION[queryWizard][object],true,false );
      $options = getOptions1($queryWizardObjects,$_SESSION["queryWizard"]["object"] );
      
		  echo $options
?>

    </select>
<?
}

/**
 * STAP 2.
 */
else if($_SESSION["queryWizard"]["step"] == 1)
{
	//print_r($selectedFields);
	// stap 2 veld selectie
	echo "<b>".vt("Stap 2: veld selectie")."</b><br><br>";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"fields\" />";

	// maak object aan en loop over list fields.
	if(!empty($_SESSION["queryWizard"]["object"]))
	{
		$object = new $_SESSION["queryWizard"]["object"]();
		reset($object->data['fields']);
		asort($object->data['fields']);

		while (list($key, $value) = each($object->data['fields']))
		{
			if($object->data['fields'][$key]['list_visible'] == true)
			{
			  if (!in_array($key,$_SESSION["queryWizard"]["fields"]))
				  $options .= "\n  <option value=\"".$key."\">".$object->data['fields'][$key]['description']."</option>";
			}
		}
		$sFields = $_SESSION["queryWizard"]["fields"];
		for ($x=0;$x < count($sFields);$x++)
		{
		  $optionsT .= "\n  <option value=\"".$sFields[$x]."\">".$object->data['fields'][$sFields[$x]]['description']."</option>";
		}
	}
?>
<table border="0">
<tr>
  <td>
	 <select name="inFields[]" multiple size="8" style="width : 200px">
		  <?=$options?>
	 </select>
  </td>
  <td valign="top" width="30">
    <br><br>
    <a href="javascript:moveItem(document.wizard['inFields[]'],document.wizard['selectedFields[]']);"><img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle"></a>
    <br><br>
    <a href="javascript:moveItem(document.wizard['selectedFields[]'],document.wizard['inFields[]']);"><img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle"></a>
  </td>
  <td>
    <select name="selectedFields[]" multiple size="8" style="width : 200px">
      <?=$optionsT?>
	  </select>
  </td>
  <td>
    <a href="javascript:moveOptionUp(document.wizard['selectedFields[]'])"><img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle"></a> <br><br>
    <a href="javascript:moveOptionDown(document.wizard['selectedFields[]'])"><img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle"></a>
  </td>
</tr>
</table>
<?
}
/**
 * STAP 3.
 */
else if($_SESSION["queryWizard"]["step"] == 2)
{
	echo "<b>".vt("Stap 3: selectie")."</b><br><br>";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"where\" />";

	if(!empty($_SESSION["queryWizard"]["object"]))
	{
		$object = new $_SESSION["queryWizard"]["object"]();
	}

?>
<table border="0">
<tr>
<?
for($a=0;$a <3; $a++)
{
?>
  <td>
  <?=($a+1)?>:
  </td>
  <td>
	  <select name="whereField[<?=$a?>]" style="width : 200px">
<?
      $options = getOptions($object->data['fields'],$_SESSION["queryWizard"]["where"][$a]["field"] );
		  echo $options
?>
	  </select>
  </td>
  <td valign="top" width="30">
	  <select name="whereOperator[<?=$a?>]" style="width : 50px">
<?
      $options = getOptions($operatorArray,$_SESSION["queryWizard"]["where"][$a]["operator"],false );
		  echo $options
?>

	  </select>
  </td>
  <td>
  	<input name="whereSearch[<?=$a?>]" value="<?=$_SESSION["queryWizard"]["where"][$a]["search"]?>" size="20">
  </td>
  <td>
	  <select name="whereAndOr[<?=$a?>]" style="width : 50px">
<?
      $options = getOptions($andOrArray,$_SESSION["queryWizard"]["where"][$a]["andor"],false );
		  echo $options
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
/**
 * STAP 4.
 */
else if($_SESSION["queryWizard"]["step"] == 3)
{
?>
	<b><?=vt("Stap 4: sorteren")?></b><br><br>
	<input type="hidden" name="setValue" value="orderby" />

<br><br>
<table border="0">
<?
  $object = new $_SESSION["queryWizard"]["object"]();
	for($a=0;$a <3; $a++)
	{
?>
<td>
<?=($a+1)?>:
</td>
<td>
	<select name="orderbyField[<?=$a?>]" style="width : 200px">
<?
     $options = getOptions($object->data['fields'],$_SESSION["queryWizard"]["orderby"][$a]["field"] );
     echo $options
?>
	</select>
</td>
<td>
	<select name="orderbyOrder[<?=$a?>]" style="width : 200px">
<?
      $options = getOptions($orderByArray,$_SESSION["queryWizard"]["orderby"][$a]["order"],false );
		  echo $options
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
/**
 * STAP 5.
 */
else if($_SESSION["queryWizard"]["step"] == 4)
{
	echo "<b>".vt("Stap 5: grouperen")."</b><br><br>";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"groupby\" />";

	if(!empty($_SESSION["queryWizard"]["object"]))
	{
		$object = new $_SESSION["queryWizard"]["object"]();
		reset($object->data['fields']);

		$options = "<option value=\"\">--</option>";
		for($a=0; $a < count($_SESSION["queryWizard"]["fields"]);$a++)
		{
			$field = $_SESSION["queryWizard"]["fields"][$a];
			$options .= "<option value=\"".$field."\">".$object->data['fields'][$field]['description']."</option>";
		}
	}

?>
<br><br>
<table border="0">
<tr>
	<td></td>
	<td><?=vt("Groeperen")?></td>
	<td><?=vt("Actie op veld")?></td>
	<td><?=vt("Actie")?></td>
</tr>
<tr>
<tr>
<?
	for($a=0;$a <1; $a++)
	{
?>
<td>
<?=($a+1)?>:
</td>
<td>
	<select name="groupbyField[<?=$a?>]" style="width : 200px">
<?
     $options = getOptions($object->data['fields'],$_SESSION["queryWizard"]["groupby"][$a]["field"] );
     echo $options
?>
  </select>
</td>
<td>
	<select name="groupActionField[<?=$a?>]" style="width : 200px">
<?
     $options = getOptions($object->data['fields'],$_SESSION["queryWizard"]["groupby"][$a]["actionField"] );
     echo $options
?>

	</select>
</td>
<td>
	<select name="groupActionType[<?=$a?>]" style="width : 200px">
<?
      $options = getOptions($groupActionArray,$_SESSION["queryWizard"]["groupby"][$a]["actionType"],false );
		  echo $options
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
elseif($_SESSION["queryWizard"]["step"] > 4 && !$_POST['do'])
{
	echo "<b>".vt("Stap 5: afdrukken")."</b><br><br>";
	echo "<input type=\"hidden\" name=\"setValue\" value=\"\" /><br><br>";

	// query opslaan
	?>
	<input type="radio" name="do" value="screen" checked> <?=vt("Resultaat afdrukken")?>	<br>
	<input type="radio" name="do" value="queryOpslaan"> <?=vt("Query opslaan als")?> : <input type="text" name="naam" value="" size="20"><br>
	<?
	// query afdrukken
}

if($querySaved)
{
	echo vt("Query opgeslagen als")." ".$naam;
}
?>
<br><br><input type="button" value=" >> <?=vt("volgende")?> " onClick="submitForm();">

</form>
</body>
<?
session_write_close();
echo template($__appvar["templateRefreshFooter"],$content);
