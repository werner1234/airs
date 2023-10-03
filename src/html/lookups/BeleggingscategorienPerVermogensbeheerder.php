<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/04/17 14:58:56 $
 		File Versie					: $Revision: 1.2 $

 		$Log: BeleggingscategorienPerVermogensbeheerder.php,v $
 		Revision 1.2  2013/04/17 14:58:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***


*/

//$theQuery = "SELECT Beleggingscategorie FROM CategorienPerVermogensbeheerder WHERE Vermogensbeheerder = '$search'";
$theQuery = "SELECT waarde FROM KeuzePerVermogensbeheerder WHERE categorie = 'Beleggingscategorien' AND Vermogensbeheerder = '$search'";

//$velden = array("Beleggingscategorie");
$velden = array("waarde");

?>