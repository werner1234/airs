<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:41:13 $
 		File Versie					: $Revision: 1.3 $

 		$Log: getFondsRente.php,v $
 		Revision 1.3  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.2  2017/02/08 14:34:59  rm
 		no message
 		
 		Revision 1.1  2017/02/08 10:10:50  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/09/30 06:36:23  cvs
 		call 4848: derde bestand Kasbankl
 		
 		Revision 1.4  2016/09/02 13:39:33  cvs
 		no message
 		



*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../rapport/rapportRekenClass.php");
require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["fonds"])) < 2)
{
  exit;
}

$returnData = array (
  'fonds'               => '',
  'rentedatum'          => '',
  'eersteRentedatum'    => '',
  'renteperiode'        => '',
  'Rente30_360'         => '',
  'fondsSoort'          => '',
  'totaalAantal'        => '',
  'renteVanaf'          => '',
  'renteDagen'          => '',
  'rentebedrag'         => '',
  'settlementDatum'     => ''
);



$data = $_GET;

$db = new DB();
$query = "SELECT  fonds,rentedatum,eersteRentedatum,renteperiode,Rente30_360,fondsSoort FROM Fondsen WHERE Fonds ='".mysql_real_escape_string($data['fonds'])."'";
$db->executeQuery($query);
$fondsdata = $db->nextRecord();
$fondsdata['totaalAantal']=$data['aantal'];
if($fondsdata['Rente30_360']==1)
  $methode=2;
else
  $methode=1;


$settlementDatum=date("Y-m-d",form2jul($data['datum'])+(48*3600));

if($fondsdata['fondsSoort']=='OBL')
{
  $rente = renteOverPeriode($fondsdata, $settlementDatum, false, $methode, true);
}
else
  $rente=array('rentebedrag'=>0,'Geen Obligatie?',$data['fonds'],$settlementDatum);

$rente['rentebedrag'] = number_format($rente['rentebedrag'], 2, ',', '.');
$rente['settlementDatum'] = $settlementDatum;


foreach ( $returnData as $key => $value ) {
  if ( isset ($rente[$key]) ) {
    $returnData[$key] = $rente[$key];
  } else {
    $returnData[$key] = false;
  }
}


echo json_encode($returnData);

?>