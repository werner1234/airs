<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/03/19 16:34:15 $
 		File Versie					: $Revision: 1.2 $

 		$Log: CRMSearchReplace.php,v $
 		Revision 1.2  2014/03/19 16:34:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/06 14:10:12  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2008/05/16 08:04:51  rvv
 		*** empty log message ***

*/
include_once("wwwvars.php");

function getOptions($theArray=array(),$selection="")
{
  foreach ($theArray as $key=>$val)
  {
  	$sstring = "";
  	if($selection == $key) $sstring = "selected";
  	$options .= "<option value=\"".$key."\" $sstring>".$val."</option>\n";
  }
  return $options;
}

$naw=new Naw();
foreach ($naw->data['fields'] as $field=>$data)
  $_velden[$field]=$data['description'];



// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
$_SESSION['submenu'] = "";


$content = array();
echo template($__appvar["templateContentHeader"],$content);
$error = 0;
// valideren of geldige waardes opgegeven
$tempDB = new DB();


$DB = new DB();

if($_SESSION['lastListQuery'])
{
  $q=explode("FROM",$_SESSION['lastListQuery']);
  $qEnd=$q[1];
  $q=explode("WHERE",$qEnd);
  $qFrom=$q[0];
  $qWhere=$q[1];
  $qWhereParts=explode("LIMIT",$qWhere);
  $qWhere=$qWhereParts[0];

  $_sel=$DB->QRecords($_SESSION['lastListQuery'])." records.";
}

$kolom=$_GET['kolom'];
$vervang=$_GET['vervang'];
$zoek=$_GET['zoek'];

if($_GET['set']=='woord')
  $set="REPLACE(CRM_naw.$kolom, '$zoek','$vervang')";
else
  $set="$kolom = '$vervang'";



if ($doIt == 1 )
{
	  $query = "UPDATE $qFrom SET CRM_naw.change_date=now(), CRM_naw.change_user='$USR', CRM_naw.$kolom=$set  WHERE $qWhere  ";
		$DB->SQL($query);
		if ($DB->Query())
			echo "<br><br>Er zijn ".$DB->mutaties()." record(s) aangepast.";
		else
			echo "<br><br>FOUT De wijzigingen zijn mislukt!";
?>
<br>
<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?=$_txt?>
<br>
<br>
<?
}
else
{
  $step='doIt';

if ($_GET['show'] == 1 )
{
  $accept='Weet u zeker dat u deze actie wilt uitvoeren? <input type="checkbox" name="doIt" value="1"> ';
}
?>
Zoeken en vervang gegevens in de CRM_NAW tabel<br>
<br>
<form action="<?=$PHP_SELF?>">
<input type="hidden" name="show" value="1">
<table>
<tr>
  <td width="250">
    Gekozen selectie
  </td>
  <td width="250">
  <b><?=$_sel?></b><br><br>

  </td>
</tr>
  <td width="250">
    Welk veld moet bewerkt worden?
  </td>
  <td width="250">
  <select name="kolom">
    <?=getOptions($_velden,$_GET['kolom'])?>

  </select>
  </td>
</tr>
<tr>
  <td>
    Zoeken naar
  </td>
  <td width="250">
    <input type="text" name="zoek" value="<?=$zoek?>">
  </td>
</tr>
<tr>
  <td>
  <?
if($_GET['set']=='veld')
  $veld='checked';
else
  $woord='checked';
  ?>
    Vervang (<input type="radio" name="set" value="woord" <?=$woord?>> woord  <input type="radio" name="set" value="veld" <?=$veld?>> veld ) door
  </td>
  <td width="250">
    <input type="text" name="vervang" value="<?=$vervang?>">
  </td>
</tr>
<tr>
  <td colspan="2" align="center">

    <?=$accept?> <input type="submit" value=" Uitvoeren">
  </td>
</tr>
</table>
</form>
<?
}

if ($_GET['show'] == 1  )
{
if($_GET['set']=='veld')
  $set="'$vervang'";

	  $query = "SELECT CRM_naw.$kolom as oudeWaarde, $set as nieuweWaarde FROM $qFrom WHERE $qWhere";
	  $DB->SQL($query);
		if ($DB->Query())
		{
		  if ($_GET['doIt'] == 1  )
		    echo "<table><tr><td><b>Nieuwe waarde</b></td></tr>";
		  else
  		  echo "<table><tr><td><b>Oude waarde</b></td><td><b>Nieuwe waarde</b></td></tr>";
		  while($data=$DB->nextRecord())
		  {
		    if ($_GET['doIt'] == 1  )
		      echo "<tr><td>".$data['oudeWaarde']."&nbsp;</td></tr>";
		    else
		       echo "<tr><td>".$data['oudeWaarde']."&nbsp;</td><td>".$data['nieuweWaarde']."&nbsp;</td></tr>";
		  }
		  echo "</table>";
		}
		echo $query;
		?>
<br>
<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?=$_txt?>
<br>
<br>
<?
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>