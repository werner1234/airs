<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/09/11 13:43:30 $
 		File Versie					: $Revision: 1.3 $

 		$Log: aeFilehistory.php,v $
 		Revision 1.3  2007/09/11 13:43:30  cvs
 		helpfuncties uitbreiden
 		
 		Revision 1.2  2006/08/12 12:12:37  cvs
 		datum niet goed op windows servers
 		
 		Revision 1.1  2006/08/11 13:31:58  cvs
 		*** empty log message ***


*/

$extArray = array("php","html","js");

if ($_GET["ext"])
{
  $extArray = explode("|",strtolower($_GET["ext"]));
}


include_once("../config/helperFunctions.php");

$filelist =searchdir("./../",3);
?>
<table cellpadding="4" cellspacing="0" border="1">
<tr>
  <td bgcolor="silver">&nbsp;&nbsp;bestand&nbsp;</td>
  <td bgcolor="silver">&nbsp;&nbsp;gewijzigd&nbsp;</td>
  <td bgcolor="silver">&nbsp;&nbsp;grootte&nbsp;</td>
  <td bgcolor="silver">&nbsp;&nbsp;versie&nbsp;</td>
  <td bgcolor="silver">&nbsp;&nbsp;MD5&nbsp;</td>
</tr>
<?
while (list($key, $val) = each($filelist))
{
  if (validExt($val))
  {
    

  echo "<tr>
           <td>&nbsp;".substr($val,2)." &nbsp;&nbsp;</td>
           <td align=right>&nbsp;".date("d-m-Y H:i", filemtime($val))."&nbsp;</td>
           <td align=right>".ceil(filesize($val)/1024)." Kb&nbsp;</td>
           <td align=right>". getVersie($val) ." &nbsp;</td>
           <td align=right>". md5_file($val) ." &nbsp;</td>
           
        </tr>";
  }
}



?>
</table>