<?php
/* 	
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/05/16 08:04:51 $
 		File Versie					: $Revision: 1.6 $
 		
 		$Log: tijdelijkerekeningmutatiesDrop.php,v $
 		Revision 1.6  2008/05/16 08:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2005/11/01 11:20:08  jwellner
 		diverse aanpassingen
 		
 		Revision 1.4  2005/05/17 07:55:07  jwellner
 		no message
 		
 		Revision 1.2  2005/05/06 16:51:02  cvs
 		einde dag
 		
 		
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
		$DB->SQL("DELETE FROM TijdelijkeRekeningmutaties WHERE change_user = '$USR' ");
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