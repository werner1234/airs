<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/01/04 13:14:14 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20141217_PREinstall.php,v $
 		Revision 1.1  2015/01/04 13:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Bedrijfsgegevens","laatsteDagelijkeUpdate",array("Type"=>"datetime","Null"=>false));

$rekeningVelden=array();
$rekeningVelden[]=array('Rekeningen','Rekening');
$rekeningVelden[]=array('TijdelijkeRapportage','Rekening');
$rekeningVelden[]=array('TijdelijkeRekeningmutaties','Rekening');
$rekeningVelden[]=array('Rekeningafschriften','Rekening');
$rekeningVelden[]=array('OrderRegels','rekeningnr');
$rekeningVelden[]=array('DepositoRentepercentages','Rekening');
$rekeningVelden[]=array('Rekeningmutaties','Rekening');
$rekeningVelden[]=array('VoorlopigeRekeningafschriften','Rekening');
$rekeningVelden[]=array('VoorlopigeRekeningmutaties','Rekening');
$rekeningVelden[]=array('tijdelijkeRecon','rekeningnummer');

$db=new DB();
$query="UPDATE Bedrijfsgegevens SET laatsteDagelijkeUpdate=now() WHERE laatsteDagelijkeUpdate='0000-00-00 00:00:00'";
$db->SQL($query);
$db->Query();


$tables=array('fondsOptieSymbolen'=>"CREATE TABLE `fondsOptieSymbolen` (
  `id` int(11) NOT NULL auto_increment,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `key` varchar(45) NOT NULL,
  `Fonds` varchar(25) NOT NULL,
  `aantal` varchar(5) NOT NULL default '',
  `Valuta` varchar(4) NOT NULL default '',
  `Beurs` varchar(4) NOT NULL default '',
  `optieVWD` varchar(80) NOT NULL default '',
  `optieAABCode` varchar(26) NOT NULL default '',
  `optieaabbeCode` varchar(26) NOT NULL default '',
  `optiestroeveCode` varchar(26) NOT NULL default '',
  `optiekasbankCode` varchar(26) NOT NULL default '',
  `optiebinckCode` varchar(26) NOT NULL default '',
  `optiesnsSecCode` varchar(26) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `Fonds` (`Fonds`)
)");

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


foreach($rekeningVelden as $index=>$velden)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '".$velden[0]."'") > 0)
  {
    $tabelVelden=array();
    $db->SQL('desc '.$velden[0]);
    $db->Query();
    while($tmp=$db->nextRecord())
    {
      $tabelVelden[]=$tmp['Field'];
    }
   
    $updateVeld='';
    foreach($tabelVelden as $mogelijkVeld)
	{
	  if(strtolower($mogelijkVeld)==strtolower($velden[1]))
	    $updateVeld=$mogelijkVeld;
	}
	
    if($updateVeld <> '')
    {
  	  echo "Going to change ".$velden[0].".".$updateVeld." to varchar(25)<br>\n";
      $tst->changeField($velden[0],$updateVeld,array("Type"=>"varchar(25)","Null"=>false));
    }
    else
      echo "Veld ".$velden[0].".".$velden[1]." niet gevonden.<br>\n";
  }
  else 
  {
    echo "Table ".$velden[0]." not found.<br>\n";	
  }
}


$portefeuilleVelden=array();
$portefeuilleVelden[]=array('Fondsen','Portefeuille');
$portefeuilleVelden[]=array('Portefeuilles','Portefeuille');
$portefeuilleVelden[]=array('CRM_naw','portefeuille');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille1');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille2');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille3');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille4');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille5');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille6');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille7');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille8');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille9');
$portefeuilleVelden[]=array('GeconsolideerdePortefeuilles','Portefeuille10');
$portefeuilleVelden[]=array('ZorgplichtPerPortefeuille','Portefeuille');
$portefeuilleVelden[]=array('HistorischePortefeuilleIndex','Portefeuille');
$portefeuilleVelden[]=array('BestandsvergoedingPerPortefeuille','portefeuille');
$portefeuilleVelden[]=array('StandaarddeviatiePerPortefeuille','Portefeuille');
$portefeuilleVelden[]=array('Portefeuilles','ModelPortefeuille');
$portefeuilleVelden[]=array('FactuurHistorie','portefeuille');
$portefeuilleVelden[]=array('IndexPerBeleggingscategorie','Portefeuille');
$portefeuilleVelden[]=array('orderkosten','portefeuille');
$portefeuilleVelden[]=array('ModelPortefeuilleFixed','Portefeuille');
$portefeuilleVelden[]=array('Rekeningen','Portefeuille');
$portefeuilleVelden[]=array('ModelPortefeuilles','Portefeuille');
$portefeuilleVelden[]=array('HistorischeSpecifiekeIndex','portefeuille');
$portefeuilleVelden[]=array('laatstePortefeuilleWaarde','portefeuille');
$portefeuilleVelden[]=array('PortaalQueue','portefeuille');
$portefeuilleVelden[]=array('TijdelijkeRapportage','portefeuille');
$portefeuilleVelden[]=array('TijdelijkeBulkOrders','portefeuille');
$portefeuilleVelden[]=array('HistorischePerformance','Portefeuille');
$portefeuilleVelden[]=array('Transactieoverzicht','Portefeuille');
$portefeuilleVelden[]=array('Zorgplichtcontrole','Portefeuille');
$portefeuilleVelden[]=array('Beleggingsplan','Portefeuille');
$portefeuilleVelden[]=array('PositieLijst','portefeuille');
$portefeuilleVelden[]=array('TijdelijkePositieLijst','portefeuille');
$portefeuilleVelden[]=array('EigendomPerPortefeuille','Portefeuille');
$portefeuilleVelden[]=array('NormwegingPerBeleggingscategorie','Portefeuille');
$portefeuilleVelden[]=array('OrderRegels','portefeuille');
$portefeuilleVelden[]=array('TijdelijkeOrderRegels','portefeuille');
$portefeuilleVelden[]=array('tmpOrder','portefeuille');
$portefeuilleVelden[]=array('tijdelijkeRecon','portefeuille');

foreach($portefeuilleVelden as $index=>$velden)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '".$velden[0]."'") > 0)
  {
    $tabelVelden=array();
    $db->SQL('desc '.$velden[0]);
    $db->Query();
    while($tmp=$db->nextRecord())
    {
      $tabelVelden[]=$tmp['Field'];
    }

    $updateVeld='';
    foreach($tabelVelden as $mogelijkVeld)
	{
	  if(strtolower($mogelijkVeld)==strtolower($velden[1]))
	    $updateVeld=$mogelijkVeld;
	}
	
    if($updateVeld <> '')
    {
      echo "Going to change ".$velden[0].".".$updateVeld." to varchar(24)<br>\n";
      $tst->changeField($velden[0],$updateVeld,array("Type"=>"varchar(24)","Null"=>false));
    }
    else
      echo "Veld ".$velden[0].".".$velden[1]." niet gevonden.<br>\n";
  }
  else 
  {
    echo "Table ".$velden[0]." not found.<br>\n";	
  }
}


?>