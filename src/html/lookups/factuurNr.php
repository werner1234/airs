<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/15 18:26:28 $
 		File Versie					: $Revision: 1.4 $

 		$Log: factuurNr.php,v $
 		Revision 1.4  2020/02/15 18:26:28  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/04/17 14:59:04  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/08/11 15:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/12/23 14:59:25  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/25 12:00:33  rvv
 		*** empty log message ***


*/

/*
$theQuery = "SELECT
FactuurHistorie.factuurNr+1 as factuurNr
FROM
FactuurHistorie
Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille
WHERE
Portefeuilles.Depotbank = (SELECT Depotbank FROM Portefeuilles WHERE Portefeuille='".$searchParts[0]."')
AND YEAR(FactuurHistorie.factuurDatum) = YEAR(STR_TO_DATE('".$searchParts[1]."','%d-%m-%Y'))
ORDER BY FactuurHistorie.factuurNr desc limit 1 ";
*/
/*
$theQuery = "
SELECT if((SELECT Depotbank FROM Portefeuilles WHERE Portefeuille='".$searchParts[0]."')='AAB',
  (SELECT
  FactuurHistorie.factuurNr+1 as factuurNr
  FROM
  FactuurHistorie
  Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille
  WHERE
  Portefeuilles.Depotbank ='AAB' AND YEAR(FactuurHistorie.factuurDatum) = YEAR(STR_TO_DATE('".$searchParts[1]."','%d-%m-%Y'))
  ORDER BY FactuurHistorie.factuurNr desc limit 1
)
,
( SELECT
  FactuurHistorie.factuurNr+1 as factuurNr
  FROM
  FactuurHistorie
  Inner Join Portefeuilles ON FactuurHistorie.portefeuille = Portefeuilles.Portefeuille
  WHERE
  Portefeuilles.Depotbank <> 'AAB' AND YEAR(FactuurHistorie.factuurDatum) = YEAR(STR_TO_DATE('".$searchParts[1]."','%d-%m-%Y'))
  ORDER BY FactuurHistorie.factuurNr desc limit 1
)
) as factuurNr
";
*/
$theQuery = "SELECT factuurNr FROM ( (SELECT FactuurHistorie.factuurNr + 1 AS factuurNr	FROM FactuurHistorie WHERE YEAR(FactuurHistorie.factuurDatum)=YEAR(STR_TO_DATE('".$searchParts[1]."','%d-%m-%Y'))	ORDER BY FactuurHistorie.factuurNr DESC	LIMIT 1)
      UNION (SELECT 1 as factuurNr) ) a
ORDER BY factuurNr desc limit 1";

$velden = array("factuurNr");
?>