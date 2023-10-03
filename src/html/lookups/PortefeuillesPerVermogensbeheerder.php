<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/09 12:28:49 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PortefeuillesPerVermogensbeheerder.php,v $
 		Revision 1.4  2019/01/09 12:28:49  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/12/10 06:32:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/08/27 16:44:14  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/04/29 10:37:29  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/12/18 14:28:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/12/05 09:53:22  rvv
 		*** empty log message ***

 		Revision 1.1  2010/12/01 19:25:25  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***


*/

$veld=array('Portefeuilles'=>'Portefeuille','Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio','AttributieCategorien'=>'AttributieCategorie','afmCategorien'=>'afmCategorie');

if(count($searchParts) > 1)
{
    if($searchParts[1]=='portefeuillesgeconsolideerd')
    {
      $theQuery = "SELECT Portefeuille FROM Portefeuilles WHERE vermogensbeheerder=(SELECT vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . mysql_real_escape_string($searchParts[0]) . "') AND consolidatie=0
      AND (Portefeuille NOT IN(SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='" . mysql_real_escape_string($searchParts[0]) . "') OR Portefeuille='" . mysql_real_escape_string($searchParts[2]) . "') ORDER BY Portefeuille ";
    }
    elseif ($searchParts[1] == 'indexPerBeleggingscategorie')
    {
      $theQuery = "SELECT Portefeuille FROM Portefeuilles WHERE vermogensbeheerder='" . $searchParts[0] . "' AND Einddatum > NOW() AND consolidatie<2 ORDER BY Portefeuille";
    }
    else
    {
      $theQuery = "SELECT Portefeuille FROM Portefeuilles WHERE vermogensbeheerder='" . $searchParts[0] . "'" . $searchParts[1];
    }
    $velden = array('Portefeuille');
//  logIt($theQuery);
}
else
{
  $theQuery = "SELECT ".$veld[$search]." FROM $search ";
  $velden = array($veld[$search]);
}
?>