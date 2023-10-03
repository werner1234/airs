<?php
/* 	
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/06/22 11:47:03 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: tijdelijkePositieDrop.php,v $
 		Revision 1.1  2011/06/22 11:47:03  cvs
 		*** empty log message ***
 		

 		
*/
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;

// include wwwvars
include_once("wwwvars.php");
session_start();
$_SESSION[submenu] = "";
//clear navigatie
$_SESSION[NAV] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);
if ($doIt == 1)
{
	if ($action == "ja")
	{
		$_txt = "De gegevens zijn verwijderd voor gebruiker ($USR).";
		$DB = new DB();
		$DB->SQL("DELETE FROM TijdelijkePositieLijst WHERE add_user = '$USR' ");
		$DB->Query();
	}
	else 
	{
		$_txt = "De verwijderaktie is geanuleerd!";
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

?>
<br>
Weet u zeker dat u de lijst wilt leegmaken?
<form action="<?=$PHP_SELF?>">
  <input type="hidden" name="doIt" value="1">
  <select name="action">
    <option value="nee">Niet leegmaken</option>
    <option value="ja">Lijst leegmaken</option>
  </select>
  <input type="submit" value=" Uitvoeren">
</form>  
<?
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>