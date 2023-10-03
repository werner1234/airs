<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/26 16:47:37 $
 		File Versie					: $Revision: 1.7 $

 		$Log: FondsAantal.php,v $
 		Revision 1.7  2014/11/26 16:47:37  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/02/22 18:53:43  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2010/08/11 15:57:01  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2010/08/06 16:35:32  rvv
 		*** empty log message ***

 		Revision 1.3  2010/08/04 15:42:20  rvv
 		*** empty log message ***

 		Revision 1.2  2010/06/13 16:30:44  rvv
 		*** empty log message ***

 		Revision 1.1  2010/06/13 15:56:25  rvv
 		*** empty log message ***


*/

$date=explode("-",$searchParts[2]);
$date=mktime(0,0,0,$date[1],$date[0],$date[2]);

$theQuery='SELECT 

(SELECT ifnull(SUM(Aantal),0) as aantal FROM Rekeningmutaties
WHERE
Rekening IN (SELECT Rekening  FROM Rekeningen WHERE Portefeuille IN (SELECT  Portefeuille FROM Rekeningen WHERE Rekening=\''.$searchParts[0].'\') )
AND Fonds=\''.$searchParts[1].'\' AND Boekdatum >= \''.date('Y',$date).'-01-01\' AND Boekdatum < \''.date('Y-m-d',$date).'\') as aantal,

(SELECT Koers FROM Fondskoersen WHERE Fonds=\''.$searchParts[1].'\' AND Datum <= \''.date('Y-m-d',$date).'\' ORDER BY Datum desc limit 1) as Koers

';

//logIt($theQuery);

$velden = array("aantal","Koers");
?>