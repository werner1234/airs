<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/07 16:08:09 $
 		File Versie					: $Revision: 1.6 $
 		
 		$Log: BedrijfPortefuilles.php,v $
 		Revision 1.6  2019/08/07 16:08:09  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/11/17 17:35:44  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/09/01 16:44:45  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/03/18 08:29:44  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/08/27 16:44:14  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/07/19 14:28:59  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/08/06 16:35:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/03/25 17:41:59  rvv
 		*** empty log message ***
 		
 	
*/

$tmp=explode("|",$search);
if(count($tmp)==2)
{
  if($tmp[1]=='consolidatie')
  {
    $theQuery = "(select '' as Portefeuille) 
UNION
(SELECT
Portefeuilles.Portefeuille
FROM
Portefeuilles WHERE Portefeuilles.consolidatie=1 AND vermogensbeheerder='" . $tmp[0] . "'
AND Portefeuilles.Einddatum > NOW() )
 ORDER BY Portefeuille";
    $velden = array("Portefeuille");
  }
  else
  {
    $theQuery = "(select '' as Portefeuille,'---' as Omschrijving) 
UNION
(SELECT
Portefeuilles.Portefeuille, if(Portefeuilles.consolidatie=1,concat(Portefeuilles.Portefeuille,' (CON)'),Portefeuilles.Portefeuille) as Omschrijving
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Bedrijf IN(SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='" . $tmp[0] . "')
AND Portefeuilles.Einddatum > NOW() AND Portefeuilles.Startdatum > '0000-00-00')  ORDER BY Portefeuille";
/*
    UNION
(SELECT
GeconsolideerdePortefeuilles.VirtuelePortefeuille as Portefeuille, concat(GeconsolideerdePortefeuilles.VirtuelePortefeuille,\" (CON) \") as Omschrijving
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN GeconsolideerdePortefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = GeconsolideerdePortefeuilles.Vermogensbeheerder
WHERE Bedrijf IN(SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='" . $tmp[0] . "')
AND GeconsolideerdePortefeuilles.Einddatum > NOW()) ORDER BY Portefeuille";
*/
    $velden = array("Portefeuille","Omschrijving");
  }

}
else
{
  $theQuery = "(select '---' as Portefeuille) 
UNION
(SELECT
Portefeuilles.Portefeuille
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Portefeuilles.consolidatie=0
WHERE Bedrijf IN(SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='$search')
AND Portefeuilles.Einddatum > NOW() AND Portefeuilles.Startdatum > '0000-00-00') ORDER BY Portefeuille ";
  $velden = array("Portefeuille");
}


?>