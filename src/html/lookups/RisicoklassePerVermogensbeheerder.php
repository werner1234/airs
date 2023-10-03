<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/08/06 16:35:32 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: RisicoklassePerVermogensbeheerder.php,v $
 		Revision 1.1  2010/08/06 16:35:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***
 		
 	
*/

$theQuery = "SELECT Risicoklasse FROM Risicoklassen WHERE Vermogensbeheerder = '$search'";
$velden = array("Risicoklasse");

?>