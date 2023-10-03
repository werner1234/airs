<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/30 13:12:00 $
 		File Versie					: $Revision: 1.10 $

 		$Log: ordersAjaxBackend.php,v $
 		Revision 1.10  2014/11/30 13:12:00  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/01/09 17:06:28  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/12/05 16:43:42  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/12/02 11:04:26  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/10/07 14:54:38  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/01/22 13:44:07  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/11/03 19:26:01  rvv
 		*** empty log message ***

 		Revision 1.3  2011/10/30 13:31:24  rvv
 		*** empty log message ***

 		Revision 1.2  2011/07/23 17:24:57  rvv
 		*** empty log message ***

 		Revision 1.1  2006/06/08 14:47:14  cvs
 		*** empty log message ***


*/
include_once("wwwvars.php");

$DB = new DB();
$DB2 = new DB();

if(!empty($_GET['q']))
{
  $txt = "";
  $search = trim($_GET['q']);
  $searchParts=explode("|",$search);

 $searchParts[0]=urldecode($searchParts[0]);
 $query = "
  SELECT Valuta, ISINCode,Fonds,Fondseenheid FROM Fondsen WHERE Fondsen.Fonds = '".$searchParts[0]."' LIMIT 1";
  $DB->SQL($query);
  $DB->Query();
  while ($rec = $DB->nextRecord())
  {
    $q2="SELECT Fondskoersen.Koers,Fondskoersen.Datum FROM Fondskoersen WHERE Fondskoersen.Fonds = '".$rec['Fonds']."' ORDER BY Fondskoersen.Datum DESC";
    $DB2->SQL($q2);
    $DB2->Query();
    $koers=$DB2->nextRecord();
    
    $q2="SELECT Valutakoersen.Koers FROM Valutakoersen WHERE Valutakoersen.Valuta='".$rec['Valuta']."' ORDER BY Valutakoersen.Datum desc limit 1 ";
    $DB2->SQL($q2);
    $DB2->Query();
    $valutakoers=$DB2->nextRecord(); 

    $query="SELECT SUM(Rekeningmutaties.Aantal) as aantal,max(bewaarder) as Depotbank FROM Rekeningmutaties Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$searchParts[1]."' AND Rekeningmutaties.Fonds='".$rec['Fonds']."'
AND Rekeningmutaties.Grootboekrekening='FONDS' AND YEAR(Rekeningmutaties.Boekdatum)='".substr(getLaatsteValutadatum(),0,4)."' GROUP  by Rekeningmutaties.Fonds";
    $DB2->SQL($query);
    //logIt($query);
    $DB2->Query();
    $aantal=$DB2->nextRecord();

    if($aantal['aantal']=='')
      $aantal['aantal']=0;
      
    $q2="SELECT Depotbank,OrderuitvoerBewaarder FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
    WHERE Portefeuille='".$searchParts[1]."'";
    $DB2->SQL($q2);
    $DB2->Query();
    $depotbank=$DB2->nextRecord();     
    if($depotbank['OrderuitvoerBewaarder']==1)
    {
      if($aantal['Depotbank']=='')
        $bewaarder='NB';
      else
        $bewaarder=$aantal['Depotbank'];  
    }
    else
      $bewaarder=$depotbank['Depotbank'];  
        
  	$txt .= $rec['Fonds'].'#'.dbdate2form($koers['Datum']).'#'.number_format($koers['Koers'],3,',','.').'#'.$rec['Valuta']."#".$koers['Koers']."#".$aantal['aantal']."#".$valutakoers['Koers']."#".$rec['Fondseenheid']."#".$bewaarder."~";
  }
  echo substr($txt,0,-1);

}
?>