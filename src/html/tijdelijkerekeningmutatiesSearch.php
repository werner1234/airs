<?php
/* 	
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/20 08:43:36 $
 		File Versie					: $Revision: 1.9 $
 		
 		$Log: tijdelijkerekeningmutatiesSearch.php,v $
 		Revision 1.9  2020/05/20 08:43:36  cvs
 		call 8644
 		
 		Revision 1.8  2008/05/16 08:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2005/12/19 16:32:07  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2005/12/05 11:14:13  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2005/11/09 10:41:10  cvs
 		zoek en vervang datum in tijdelijke transaktietabel aangepast
 		
 		Revision 1.4  2005/11/01 11:20:08  jwellner
 		diverse aanpassingen
 		
 		Revision 1.3  2005/10/19 17:14:06  cvs
 		validatie vervang waardes
 		
 		Revision 1.2  2005/05/07 13:52:10  cvs
 		no message
 		
 		Revision 1.1  2005/05/07 12:17:45  cvs
 		
 		
 		
 		
*/

$errorArray[0]  = "Onbekende fout";
$errorArray[1]  = "Grootboekrekening bevat een ongeldige waarde";
$errorArray[2]  = "Fondskoers is geen getal of kleiner of gelijk aan 0";
$errorArray[3]  = "Ongeldige boekdatum opgegeven";
$errorArray[4]  = "Fonds bevat een ongeldige waarde";
$errorArray[5]  = "Valutakoers is geen getal of kleiner of gelijk aan 0";
$errorArray[6]  = "Transactietype bevat een ongeldige waarde";
$errorArray[99] = "Zoek en Vervang zijn verplichte velden";


$_velden=array("Omschrijving","Grootboekrekening","Fondskoers","Boekdatum","Fonds","Valutakoers","Transactietype");

// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");
session_start();
$_SESSION["submenu"] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);
//listarray($_GET);
$error = 0;
// valideren of geldige waardes opgegeven
$tempDB = new DB();
switch ($kolom)
{
  case "Grootboekrekening":
    $query = "SELECT * FROM Grootboekrekeningen WHERE Grootboekrekening = '$vervang' ";
    $tempDB->SQL($query);
    if (!$record = $tempDB->lookupRecord())
    {
      $error=1;    
    }        
    else 
    {  
      $vervang = $record["Grootboekrekening"];
    }  
    break;
  case "Fondskoers":
    if (!(is_numeric($vervang) AND $vervang > 0))
    {
      $error = 2;
    }
    break;  
  case "Valutakoers":
    if (!(is_numeric($vervang) AND $vervang > 0))
    {
      $error = 5;
    }
    break;  
  case "Boekdatum":  
    if(empty($vervang))
    {
	    $error = 3;
	  }
	  else 
	  {
		  $dd = explode($__appvar["date_seperator"],$vervang);
		  if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))   
		    $error = 3;
		  else 
		  {
		    $vervang = $dd[2]."-".$dd[1]."-".$dd[0]." 00:00:00";  
		    
		    $dd = explode("-",$zoek);
		    $zoek    = $dd[2]."-".$dd[1]."-".$dd[0];
		  }  
		  
	  }
    break;
  case "Fonds":
    $query = "SELECT * FROM Fondsen WHERE Fonds = '$vervang' ";
    $tempDB->SQL($query);
    if (!$record = $tempDB->lookupRecord())
    {
      $error=4;    
    }        
    else 
    {  
      $vervang = $record["Fonds"];
    }  
    break;
  case "Transactietype":
    $query = "SELECT * FROM Transactietypes WHERE Transactietype = '$vervang' ";
    $tempDB->SQL($query);
    if (!$record = $tempDB->lookupRecord())
    {
      $error=6;    
    }        
    else 
    {  
      $vervang = $record["Transactietype"];
    }  
    break;
}

if ($doIt == 1 and $zoek and $vervang and $error == 0 )
{

  // call 8644
  $db1 = new DB();

  $queryParts = explode("LIMIT", $_SESSION["trm_zv_query"]);
 // debug($queryParts[0]);
  $db1->executeQuery($queryParts[0]);
  $dataset = array();
  while ($rec = $db1->nextRecord())
  {
    $dataset[] = $rec["id"];
  }
  //debug($dataset, count($dataset));
  
	  $query = "
      UPDATE 
        TijdelijkeRekeningmutaties 
      SET 
        {$kolom} = '{$vervang}' 
      WHERE 
        {$kolom}  LIKE '%$zoek%' AND
        `id` IN (".implode(",",$dataset).")
        ";
//	  debug($query);
//	  echo "\n\n<!--\n ".$query."\n-->";
	 	$DB = new DB();
		$DB->SQL($query);
		if ($DB->Query())
		{
			echo "<br><br>Er zijn ".$DB->mutaties()." record(s)voor gebruiker ($USR) aangepast";
		}	
		else 
		{
			echo "<br><br>FOUT De wijzigingen zijn mislukt!";
		}
	
	  
	
	
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
  if ((!$zoek or !$vervang) and $doIt)
  {
    $error = 99;
  }  

  if ($error)
  {
    if (array_key_exists($error, $errorArray)) 
      $errorTxt = $errorArray[$error];
    else 
      $errorTxt = $errorArray[0];
?>
<table bgcolor="Maroon">
<tr>
  <td width="500" align="center"><font color="White">
    <?=$errorTxt?>
  </td>
</tr>
</table>  
<?  
  
      
  }

if ($selectie)
  $_sel = $selectie;
else 
  $_sel = "gehele tabel";  
?>
Zoeken en vervang gegevens in de tijdelijke rekening mutaties<br>
<br>
<form action="<?=$PHP_SELF?>">
<input type="hidden" name="doIt" value="1">
<input type="hidden" name="selectie" value="<?=$selectie?>">
<table>
<tr>
  <td width="250">
    Gekozen selectie
  </td>
  <td width="250">
  <b><?=$_sel?></b><br><br>

  </td>
</tr>
<tr>
  <td width="250">
    Welke rij moet bewerkt worden?
  </td>
  <td width="250">
  <select name="kolom">
    <?=SelectArray($kolom,$_velden)?>
    
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
    Vervang door 
  </td>
  <td width="250">
    <input type="text" name="vervang" value="<?=$vervang?>">
  </td>
</tr>
<tr>
  <td colspan="2" align="center">  
    <input type="submit" value=" Uitvoeren">
  </td>
</tr>
</table>
</form>  
<?
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>