<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/07/25 16:00:02 $
 		File Versie					: $Revision: 1.1 $

 		$Log: BeleggingscategorienPerPortefeuille.php,v $
 		Revision 1.1  2012/07/25 16:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***


*/

//$theQuery = "SELECT Beleggingscategorie FROM CategorienPerVermogensbeheerder WHERE Vermogensbeheerder = '$search'";
$theQuery = "SELECT waarde FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON Portefeuilles.Vermogensbeheerder=KeuzePerVermogensbeheerder.Vermogensbeheerder
WHERE KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien' AND Portefeuilles.Portefeuille = '$search'";

//$velden = array("Beleggingscategorie");
$velden = array("waarde");

?>