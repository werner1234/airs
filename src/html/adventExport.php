<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/03/12 11:18:50 $
 		File Versie					: $Revision: 1.3 $

 		$Log: adventExport.php,v $
 		Revision 1.3  2014/03/12 11:18:50  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2013/12/11 10:06:26  cvs
 		*** empty log message ***

 		Revision 1.1  2013/11/15 10:22:21  cvs
 		aanpassing tbv Adventexport

*/

include_once("wwwvars.php");

$content = array();


echo template($__appvar["templateContentHeader"],$content);
?>
<br />
<h2>Advent exportmenu</h2>
<br />
<div style="width: 220px; ">
<ul>
  <li><a href="adventExportFile.php?type=validatePre">Valideer stamgegevens</a></li>
  <li><a href="adventExportFile.php?type=validatePost">Valideer niet verwerkt</a></li>
  <hr />
  <li><a href="adventExportFile.php?type=transBeheer">Transactie bestanden</a></li>
  <li><a href="adventExportFile.php?type=cash">Overige mutaties</a></li>
  <hr />
  <li><a href="adventExportFile.php?type=alle">Alle mutaties/transacties</a></li>


</ul>
</div>

<?
echo template($__appvar["templateContentFooter"],$content);
?>