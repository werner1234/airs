<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/27 15:19:54 $
 		File Versie					: $Revision: 1.20 $

 		$Log: queueExportQueryKoers.php,v $
 		Revision 1.20  2020/05/27 15:19:54  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2020/05/23 16:36:21  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/02/12 07:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/02/11 13:24:12  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/06/07 16:25:19  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/01/11 17:18:52  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/01/04 17:14:07  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/01/04 16:34:50  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/12/30 20:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/12/20 13:16:33  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/01/18 18:53:40  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2011/07/30 16:37:29  rvv
 		*** empty log message ***

 		Revision 1.7  2011/07/27 16:26:05  rvv
 		*** empty log message ***

 		Revision 1.6  2011/07/23 17:24:57  rvv
 		*** empty log message ***

 		Revision 1.5  2010/09/22 19:04:53  rvv
 		*** empty log message ***

 		Revision 1.4  2010/06/12 09:51:44  rvv
 		*** empty log message ***

 		Revision 1.3  2010/06/06 14:10:23  rvv
 		*** empty log message ***

 		Revision 1.2  2010/05/13 09:16:29  rvv
 		*** empty log message ***

 		Revision 1.1  2010/05/09 19:19:21  rvv
 		*** empty log message ***

 		Revision 1.28  2010/04/25 12:32:21  rvv
 		*** empty log message ***


*/

  $DB=new DB();
  $query = "SELECT DISTINCT(Valutas.Valuta)
  FROM
    Valutas
    JOIN Fondsen ON Valutas.Valuta = Fondsen.Valuta
    JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = Fondsen.Fonds AND FondsenPerBedrijf.Bedrijf =  '".$Bedrijf."'
  WHERE
	  FondsenPerBedrijf.change_date >= '$lastUpdate'";

	$DB->SQL($query);
	$DB->Query();
	$ValArray = array();
	while($Valdata = $DB->NextRecord())
		$ValArray[$Valdata['Valuta']] = $Valdata['Valuta'];
	$valutaQuery = " IN('".implode("','",$ValArray)."')";





$exportQuery=array();

if($koersExport['koersExport']==2)
{
	$query="SELECT LaatsteUpdate FROM Bedrijfsgegevens WHERE Bedrijf = '".$Bedrijf."' ";
	$DB->SQL($query);
	$DB->Query();
	$laatsteUpdate = $DB->NextRecord();

	$query="SELECT recordId FROM trackAndTrace WHERE trackAndTrace.tabel='Fondsen' AND  trackAndTrace.veld='Fonds' AND add_date >= '".$laatsteUpdate['LaatsteUpdate']."'";
	$DB->SQL($query);
	$DB->Query();
	$fondsIdArray = array();
	while($fondsData = $DB->NextRecord())
		$fondsIdArray[]=$fondsData['recordId'];
	$FondsIdQuery = " IN('".implode("','",$fondsIdArray)."')";

	$exportQuery['Fondsen'] = "SELECT {Fondsen} ".
		" FROM (Fondsen, tmpFondsenPerBedrijf) ".
		" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
		" WHERE  ".
		" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
		" tmpFondsenPerBedrijf.Fonds = Fondsen.Fonds AND ".
		" (FondsenPerBedrijf.Bedrijf IS NULL OR Fondsen.change_date >= '{lastUpdate}')  AND (Fondsen.add_date > now()-INTERVAL 2 DAY OR Fondsen.id $FondsIdQuery OR FondsenPerBedrijf.Bedrijf IS NULL)";
}
else
{
	$exportQuery['Fondsen'] = "SELECT {Fondsen} ".
		" FROM (Fondsen, tmpFondsenPerBedrijf) ".
		" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
		" WHERE  ".
		" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
		" tmpFondsenPerBedrijf.Fonds = Fondsen.Fonds AND ".
		" (FondsenPerBedrijf.Bedrijf IS NULL OR Fondsen.change_date >= '{lastUpdate}')  ";
}

if($koersExport['fondskostenDoorkijkExport'] ==1)
{
	$exportQuery['doorkijk_categorieWegingenPerFonds'] = "SELECT {doorkijk_categorieWegingenPerFonds} FROM doorkijk_categorieWegingenPerFonds
JOIN tmpFondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds = doorkijk_categorieWegingenPerFonds.Fonds 
LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' 
WHERE  tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND  
(FondsenPerBedrijf.Bedrijf IS NULL OR doorkijk_categorieWegingenPerFonds.change_date >= '{lastUpdate}') {add_date_filter}";

	$exportQuery['fondskosten'] = "SELECT {fondskosten}  FROM (fondskosten, tmpFondsenPerBedrijf) 
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' 
	 WHERE 
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND 
	 tmpFondsenPerBedrijf.Fonds = fondskosten.Fonds AND 
	 (FondsenPerBedrijf.Bedrijf IS NULL OR fondskosten.change_date >= '{lastUpdate}') {add_date_filter}";
  
  $exportQuery['FondsenEMTdata'] = "SELECT {FondsenEMTdata}  FROM (FondsenEMTdata, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = FondsenEMTdata.Fonds AND
	 (FondsenPerBedrijf.Bedrijf IS NULL OR FondsenEMTdata.change_date >= '{lastUpdate}') {add_date_filter}";
  
  $exportQuery['FondsenFundInformatie'] = "SELECT {FondsenFundInformatie}  FROM (FondsenFundInformatie, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = FondsenFundInformatie.fonds AND
	 (FondsenPerBedrijf.Bedrijf IS NULL OR FondsenFundInformatie.change_date >= '{lastUpdate}') {add_date_filter}";
  
}


$exportQuery['Fondskoersen'] = "SELECT {Fondskoersen}
 FROM Fondskoersen use INDEX ( FondsDatum )
 WHERE
  (Fondskoersen.change_date >= '{lastUpdate}' AND Fondskoersen.Fonds {fondsenQuery} AND Fondskoersen.add_date > now()-INTERVAL 7 DAY)  OR Fondskoersen.Fonds {newFondsenQuery} ";

$exportQuery['Rentepercentages'] = "SELECT {Rentepercentages}
	 FROM (Rentepercentages, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = Rentepercentages.Fonds AND
	 {jaarInQuery}
	 (FondsenPerBedrijf.Bedrijf IS NULL OR Rentepercentages.change_date >= '{lastUpdate}')";

$exportQuery['Valutakoersen'] = "SELECT {Valutakoersen} ".
	" FROM Valutakoersen
	  JOIN Fondsen ON Valutakoersen.Valuta = Fondsen.Valuta ".
	" WHERE Fondsen.Fonds {fondsenQuery} AND ".
	" (Valutakoersen.add_date >= '{lastUpdate}' OR (Valutakoersen.change_date >= '{lastUpdate}' AND Valutakoersen.add_date > NOW()-INTERVAL 7 DAY) ) ";

$exportQuery['Valutas'] = "SELECT {Valutas} FROM Valutas WHERE Valutas.Valuta $valutaQuery";

$exportQuery['updateInformatie'] = "SELECT {updateInformatie} FROM updateInformatie WHERE  updateInformatie.publiceer = 1 AND updateInformatie.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['handleidingenAIRS'] = "SELECT {handleidingenAIRS} FROM handleidingenAIRS WHERE  handleidingenAIRS.publiceer = 1 AND handleidingenAIRS.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['fondsOptieSymbolen'] = "SELECT {fondsOptieSymbolen} FROM fondsOptieSymbolen WHERE  fondsOptieSymbolen.change_date >= '{lastUpdate}' {add_date_filter}";

?>