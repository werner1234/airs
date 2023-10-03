<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/13 12:34:57 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20141203_PREinstall.php,v $
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","check_Beurs",array("Type"=>"tinyint","Null"=>false));
$tst->changeField("Vermogensbeheerders","check_BB_Landcodes",array("Type"=>"tinyint","Null"=>false));
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

$db=new DB();
foreach($portefeuilleVelden as $index=>$velden)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '".$velden[0]."'") > 0)
  {
  	echo "Going to change ".$velden[0].".".$velden[1]." to varchar(24)<br>\n";
    $tst->changeField($velden[0],$velden[1],array("Type"=>"varchar(24)","Null"=>false));
  }
  else 
  {
    echo "Table ".$velden[0]." not found.<br>\n";	
  }
}

if(file_exists("../classes/records/FondsParticipatieVerloop.php"))
{
    unlink("../classes/records/FondsParticipatieVerloop.php");
}

?>