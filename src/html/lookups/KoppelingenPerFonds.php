<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/07/22 08:46:35 $
 		File Versie					: $Revision: 1.10 $

 		$Log: KoppelingenPerFonds.php,v $
 		Revision 1.10  2018/07/22 08:46:35  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/07/22 05:46:17  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/11/30 07:27:57  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/03/06 17:01:39  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/05/12 15:14:56  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/04/30 08:37:56  rvv
 		*** empty log message ***

 		Revision 1.4  2011/12/18 14:28:50  rvv
 		*** empty log message ***

 		Revision 1.3  2010/08/04 15:42:20  rvv
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


$theQuery='SELECT
(
SELECT
concat(
IF(BeleggingssectorPerFonds.Beleggingssector IS NOT NULL, CONCAT(BeleggingssectorPerFonds.Beleggingssector,"~", BeleggingssectorPerFonds.id)  , ""),"|",
IF(BeleggingssectorPerFonds.Regio IS NOT NULL, CONCAT(BeleggingssectorPerFonds.Regio,"~",BeleggingssectorPerFonds.id), ""),"|",
IF(BeleggingssectorPerFonds.AttributieCategorie IS NOT NULL, CONCAT(BeleggingssectorPerFonds.AttributieCategorie,"~",BeleggingssectorPerFonds.id), ""),"|",
IF(BeleggingssectorPerFonds.DuurzaamCategorie IS NOT NULL, CONCAT(BeleggingssectorPerFonds.DuurzaamCategorie,"~",BeleggingssectorPerFonds.id), "")) as BeleggingssectorPerFonds
FROM
BeleggingssectorPerFonds
WHERE BeleggingssectorPerFonds.Fonds=\''.$searchParts[1].'\' AND BeleggingssectorPerFonds.Vermogensbeheerder=\''.$searchParts[0].'\'
ORDER BY BeleggingssectorPerFonds.Vanaf DESC limit 1
) as Beleggingssectoren,
(
SELECT
concat(
IF(BeleggingscategoriePerFonds.Beleggingscategorie IS NOT NULL,CONCAT(BeleggingscategoriePerFonds.Beleggingscategorie,"~",BeleggingscategoriePerFonds.id),""),"|",
IF(BeleggingscategoriePerFonds.RisicoPercentageFonds IS NOT NULL,CONCAT(BeleggingscategoriePerFonds.RisicoPercentageFonds,"~",BeleggingscategoriePerFonds.id),""),"|",
IF(BeleggingscategoriePerFonds.afmCategorie IS NOT NULL,CONCAT(BeleggingscategoriePerFonds.afmCategorie,"~",BeleggingscategoriePerFonds.id),""),"|",
IF(BeleggingscategoriePerFonds.duurzaamheid IS NOT NULL,CONCAT(BeleggingscategoriePerFonds.duurzaamheid,"~",BeleggingscategoriePerFonds.id),"")
) as BeleggingscategoriePerFonds
FROM
BeleggingscategoriePerFonds
WHERE BeleggingscategoriePerFonds.Fonds=\''.$searchParts[1].'\' AND BeleggingscategoriePerFonds.Vermogensbeheerder=\''.$searchParts[0].'\'
ORDER BY BeleggingscategoriePerFonds.Vanaf DESC limit 1
) as BeleggingscategoriePerFonds,
(
SELECT
CONCAT(
IF(ZorgplichtPerFonds.Zorgplicht IS NOT NULL,CONCAT(ZorgplichtPerFonds.Zorgplicht,"~",ZorgplichtPerFonds.id),""),"|",
IF(Zorgplichtcategorien.Omschrijving IS NOT NULL,CONCAT(Zorgplichtcategorien.Omschrijving," ",if(count(ZorgplichtPerFonds.Percentage)>1,"(Meerdere records gevonden.)",""),"~",ZorgplichtPerFonds.id),""),"|",
IF(ZorgplichtPerFonds.Percentage IS NOT NULL,CONCAT(ZorgplichtPerFonds.Percentage,"~",ZorgplichtPerFonds.id),"")) as ZorgplichtPerFonds
FROM
ZorgplichtPerFonds
LEFT Join Zorgplichtcategorien ON ZorgplichtPerFonds.Vermogensbeheerder = Zorgplichtcategorien.Vermogensbeheerder AND ZorgplichtPerFonds.Zorgplicht = Zorgplichtcategorien.Zorgplicht
WHERE ZorgplichtPerFonds.Fonds=\''.$searchParts[1].'\' AND ZorgplichtPerFonds.Vermogensbeheerder=\''.$searchParts[0].'\' 
GROUP BY ZorgplichtPerFonds.Fonds limit 1
) as Zorgplichtcategorien,
(
SELECT concat(
Vermogensbeheerders.check_categorie,"|",
Vermogensbeheerders.check_sector,"|",
Vermogensbeheerders.check_zorgplichtFonds,"|",
Vermogensbeheerders.check_sectorRegio,"|",
Vermogensbeheerders.check_sectorAttributie,"|",
Vermogensbeheerders.check_afmCategorie,"|",
Vermogensbeheerders.check_duurzaamheid,"|",
Vermogensbeheerders.check_duurzaamCategorie)
FROM
Vermogensbeheerders
WHERE
Vermogensbeheerder=\''.$searchParts[0].'\'
) as checks
';

logIt($theQuery);

$velden = array("Beleggingssectoren","BeleggingscategoriePerFonds","Zorgplichtcategorien","checks");
?>