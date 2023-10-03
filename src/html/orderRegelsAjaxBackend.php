<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/02/21 17:20:10 $
 		File Versie					: $Revision: 1.11 $

 		$Log: orderRegelsAjaxBackend.php,v $
 		Revision 1.11  2016/02/21 17:20:10  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/11/30 13:12:00  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/07/31 15:44:40  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/04/08 08:10:43  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/02/01 19:16:58  rvv
 		*** empty log message ***

 		Revision 1.6  2012/01/28 16:13:06  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/23 17:24:57  rvv
 		*** empty log message ***

 		Revision 1.4  2009/10/07 10:07:10  rvv
 		*** empty log message ***

 		Revision 1.3  2009/10/07 10:01:11  rvv
 		*** empty log message ***

 		Revision 1.2  2009/09/12 11:16:30  rvv
 		*** empty log message ***

 		Revision 1.1  2006/06/08 14:47:14  cvs
 		*** empty log message ***


*/
include_once("wwwvars.php");

$DB = new DB();




if(!empty($_GET['q']))
{
  if ($_GET['fld']  == "valuta")
    echo getValuta();
  elseif($_GET['fld']  == "koersen")
    echo getKoersen();
  elseif($_GET['fld']  == "Depotbank")
    echo getBewaarder();
  else
    echo getRekeningen();
}

function getBewaarder()
{  
  global $_GET,$DB;
  $txt = "";
  $search = trim($_GET['q']);
  $extra = trim($_GET['extra']);
      $query="SELECT SUM(Rekeningmutaties.Aantal) as aantal,max(bewaarder) as Depotbank FROM Rekeningmutaties Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$search."' AND Rekeningmutaties.Fonds='".$extra."'
AND Rekeningmutaties.Grootboekrekening='FONDS' AND YEAR(Rekeningmutaties.Boekdatum)='".substr(getLaatsteValutadatum(),0,4)."' GROUP  by Rekeningmutaties.Fonds";
  $DB->SQL($query);
  $DB->Query();
  $aantal=$DB->nextRecord();
  $bewaarder=$aantal['Depotbank'];
  return "$bewaarder#$bewaarder~";
}

function getRekeningen()
{
  global $_GET,$DB;
  $txt = "";
  $search = trim($_GET['q']);

  $query = "
  SELECT
    Rekeningen.id,
    Rekeningen.Rekening,
    Rekeningen.Portefeuille,
    Rekeningen.Valuta,
    Rekeningen.Memoriaal,
    Rekeningen.Tenaamstelling,
    Rekeningen.Termijnrekening,
    IF(LEFT(Rekeningen.Rekening,CHAR_LENGTH(Rekeningen.Portefeuille))=Rekeningen.Portefeuille,0,1) as volgorde
  FROM
    Rekeningen
    JOIN Valutas on Rekeningen.Valuta=Valutas.Valuta
  WHERE
    Rekeningen.Memoriaal = 0 AND Rekeningen.Inactief=0 AND Rekeningen.Termijnrekening = 0 AND Rekeningen.Deposito = 0 AND Rekeningen.Portefeuille = '".$search."' 
  ORDER BY 
    volgorde, Valutas.Afdrukvolgorde, Rekeningen.Rekening";
  $DB->SQL($query);
  $DB->Query();
  $tmpRec = "";
  while ($rec = $DB->nextRecord())
  {
    $recNu = ereg_replace("[^0-9-]","",$rec['Rekening']);
    $valuta = ereg_replace("[^A-Z]","",$rec['Rekening']);
    
    $reVal = '/^(.*)([A-Z]{3})$/m';
    preg_match_all($reVal, $rec['Rekening'], $matches);
    if(isset($matches[1][0]))
      $recNu=$matches[1][0];
    if(isset($matches[2][0]))
      $valuta=$matches[2][0];
    
    $valRek[] = $valuta;
    if ($recNu <> $tmpRec)
    {
      $txt .= $recNu.'#'.$recNu."~";
      $tmpRec = $recNu;
    }
  }

  return substr($txt,0,-1);
}


function getKoersen()
{
  global $_GET,$DB;
  include_once("rapport/rapportRekenClass.php");
  $txt = "";
  $search = trim($_GET['q']);
  $items=explode("|",$search);
  $items[0] = formdate2db($items[0]);
  $query = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Datum <=   '".$items[0]."' AND Fonds = '".$items[1]."' ORDER BY Datum DESC LIMIT 1";
  $DB->SQL($query);
  $DB->Query();
  $rec = $DB->lookupRecord();
  $recFonds = $rec['koers'];
  $query = "SELECT Fonds as fonds, Rentedatum as rentedatum, EersteRentedatum as eersteRentedatum, Renteperiode as renteperiode, Valuta FROM Fondsen WHERE Fonds='".$items[1]."'";
  $DB->SQL($query);
  $DB->Query();
  $fondsData = $DB->lookupRecord();
  $fondsData['totaalAantal']=$items[2];
  $waarde=round(renteOverPeriode($fondsData,$items[0]),2);
  $acties = array('A'=>-1,'AO'=>-1,'AS'=>-1,'V'=>1,'VO'=>1,'VS'=>1,'I'=>-1);
  $waarde = $acties[$items[3]] * $waarde;
  $query = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Datum <=   '".$items[0]."' AND Valuta = '".$fondsData['Valuta']."' ORDER BY Datum DESC LIMIT 1";
  $DB->SQL($query);
  $DB->Query();
  $rec = $DB->lookupRecord();
  $recValuta = $rec['koers'];

  //logIt($recFonds.'#'.$recValuta.'#'.$waarde."->".$items[0]."->".$items[1]."->".$items[2]."->".$items[3]);
  return $txt .= $recFonds.'#'.$recValuta.'#'.$waarde;
}

function getValuta()
{
  global $_GET,$DB;
  $query = "SELECT * FROM Valutas WHERE TermijnValuta = 0 ORDER BY Afdrukvolgorde";//";
  $DB->SQL($query);
  $DB->Query();
  $vals=array();
  $valRek=array();
  while ($rec = $DB->nextRecord())
  {
    $vals[] = $rec["Valuta"];
  }

  $search = trim($_GET['q']);

  $query = "
  SELECT
    id,
    Rekening,
    Portefeuille,
    Valuta,
    Memoriaal,
    Tenaamstelling,
    Termijnrekening
  FROM
    Rekeningen
  WHERE
    Memoriaal = 0 AND Inactief=0 AND Termijnrekening = 0 AND Rekening LIKE '".$search."%'";
  $DB->SQL($query);
  $DB->Query();
  while ($rec = $DB->nextRecord())
  {
    $valuta = ereg_replace("[^A-Z]","",$rec['Rekening']);
  
    $reVal = '/^(.*)([A-Z]{3})$/m';
    preg_match_all($reVal, $rec['Rekening'], $matches);
    if(isset($matches[2][0]))
      $valuta=$matches[2][0];
  
    
    $valRek[] = $valuta;
  }
  
  $valList0='';
  $valList1='';
  for ($x=0;$x <count($vals);$x++)
  {
    if (in_array($vals[$x],$valRek))
      $valList0 .= $vals[$x]."#".$vals[$x]."~";
    else
      $valList1 .= $vals[$x]."*#".$vals[$x]."*~";

  }

  return substr($valList0.$valList1,0,-1);


}
?>