<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/09/02 09:33:51 $
 		File Versie					: $Revision: 1.1 $

 		$Log: crmLookups.php,v $
 		Revision 1.1  2012/09/02 09:33:51  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/12/05 09:53:22  rvv
 		*** empty log message ***

*/

$veld=array(
'Fondsen'=>'SELECT Fonds,Omschrijving FROM Fondsen WHERE EindDatum > NOW() OR EindDatum = \'0000-00-00\' ORDER BY Fonds',
'Portefeuilles'=>'SELECT Portefeuille,Portefeuille FROM Portefeuilles ORDER BY Portefeuille',
'Regios'=>'Regio',
'AttributieCategorien'=>'AttributieCategorie');

if(count($searchParts) > 1)
{
  $theQuery = "SELECT waarde FROM KeuzePerVermogensbeheerder WHERE vermogensbeheerder='".$searchParts[0]."' AND categorie='".$searchParts[1]."' ";
  $velden = array('waarde');
}
else
{
  $theQuery = "SELECT ".$veld[$search]." FROM $search ";
  $velden = array($veld[$search]);
}
?>