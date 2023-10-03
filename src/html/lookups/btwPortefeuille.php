<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/12/13 17:27:45 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: btwPortefeuille.php,v $
 		Revision 1.1  2009/12/13 17:27:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 12:00:33  rvv
 		*** empty log message ***
 		
 	
*/

$theQuery = "SELECT BeheerfeeBTW as btw FROM Portefeuilles WHERE Portefeuille = '$search'";

$velden = array("btw");
?>