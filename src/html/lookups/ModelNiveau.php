<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/03/06 17:01:39 $
 		File Versie					: $Revision: 1.1 $

 		$Log: ModelNiveau.php,v $
 		Revision 1.1  2013/03/06 17:01:39  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/03/06 11:19:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/11 11:57:16  rvv
 		*** empty log message ***

 		Revision 1.2  2010/02/17 11:28:50  rvv
 		*** empty log message ***

 		Revision 1.1  2010/02/10 17:55:37  rvv
 		*** empty log message ***

 		Revision 1.1  2009/12/23 14:59:25  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/25 12:00:33  rvv
 		*** empty log message ***


*/
$level=strtolower($_GET['query']);


if($level == 'beleggingscategorie' || $level == 'cat')
  $theQuery='SELECT Beleggingscategorien.Beleggingscategorie as sleutel, Beleggingscategorien.Omschrijving as waarde
  FROM Beleggingscategorien
  LEFT Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
  WHERE CategorienPerHoofdcategorie.Hoofdcategorie is null ORDER BY sleutel';
elseif($level == 'beleggingssector' || $level=='sec')
  $theQuery='SELECT Beleggingssectoren.Beleggingssector as sleutel, Beleggingssectoren.Omschrijving as waarde FROM Beleggingssectoren ORDER BY sleutel';
elseif($level == 'regio' || $level=='regio')
  $theQuery='SELECT Regios.Regio as sleutel, Regios.Omschrijving as waarde FROM Regios ORDER BY sleutel';
elseif($level == 'valutas')
  $theQuery='SELECT Valutas.Valuta as sleutel, Valutas.Omschrijving as waarde FROM Valutas ORDER BY sleutel';
elseif($searchParts[0] == 'fondsen')
  $theQuery="SELECT Fondsen.Fonds as sleutel, Fondsen.Omschrijving as waarde FROM Fondsen WHERE OptieBovenliggendFonds='".$searchParts[1]."' ORDER BY waarde";
else
 $theQuery="SELECT '' as sleutel ,'' as  waarde";

//logIt($_GET['query']." ".$theQuery);

$velden = array("sleutel","waarde");
?>