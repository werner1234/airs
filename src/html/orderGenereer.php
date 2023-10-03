<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/06 18:12:07 $
 		File Versie					: $Revision: 1.13 $

 		$Log: orderGenereer.php,v $
 		Revision 1.13  2014/12/06 18:12:07  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/11/19 16:41:12  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2014/11/08 18:35:29  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/10/11 16:21:09  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2011/12/28 18:45:14  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2011/12/18 14:25:43  rvv
 		*** empty log message ***

 		Revision 1.7  2009/10/25 08:37:21  rvv
 		*** empty log message ***

 		Revision 1.6  2009/01/20 17:46:01  rvv
 		*** empty log message ***

 		Revision 1.5  2006/10/18 06:56:43  rvv
 		*** empty log message ***

 		Revision 1.3  2006/07/13 18:31:24  cvs
 		*** empty log message ***

 		Revision 1.2  2006/06/28 12:44:53  cvs
 		*** empty log message ***

 		Revision 1.1  2006/06/16 11:40:31  cvs

 		\


*/

include_once("wwwvars.php");
include_once("./orderControlleRekenClass.php");

$db = new DB();



function makeOrder($records,$action)
{
  global $db, $USR, $__appvar;

  $cfg=new AE_config();
  $newBatchId=$cfg->getData('lastOrderBatchId')+1;
  $cfg->addItem('lastOrderBatchId',$newBatchId);

  $query="SELECT Vermogensbeheerders.OrderStandaardTijdsSoort, Vermogensbeheerders.OrderStandaardTransactieType
   FROM Vermogensbeheerders
  Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' LIMIT 1";
  $db->SQL($query); 
  $db->Query();
  $tmp=$db->nextRecord();
  if($tmp['OrderStandaardTransactieType'] <> '')
    $transactieType=$tmp['OrderStandaardTransactieType'];
  else
    $transactieType='L';
    
  if($tmp['OrderStandaardTijdsSoort']=='GTC')
  {
    $tijdsSoort='GTC';
    $tijdsLimiet="''";
  }
  else
  {
    $tijdsSoort='DAT';
    $tijdsLimiet='NOW()';  
  }  
  
  $data = $records[0];
  $query  = "INSERT INTO Orders SET ";
  $query .= "  vermogensBeheerder = '".$__appvar['bedrijf']."'";
  $query .= ", fondsCode          = '".$data["ISINCode"]."' ";
  $query .= ", fonds              = '".mysql_escape_string($data["Fonds"])."' ";
  $query .= ", fondsOmschrijving  = '".mysql_escape_string($data["FondsOmschrijving"])."' ";
  $query .= ", OrderSoort         = '".$data["OrderSoort"]."' ";
  $query .= ", transactieType     = '$transactieType' ";
  $query .= ", batchId      = '".$newBatchId."'";
  $query .= ", tijdsLimiet        = $tijdsLimiet";
  $query .= ", tijdsSoort         = '$tijdsSoort' ";
  $query .= ", laatsteStatus      = 0";
  $query .= ", Depotbank          = '".$data["Depotbank"]."' ";
  $query .= ", status             = '".date("Ymd_Hi")."/".$data["add_user"]." - aangemaakt via mutatievoorstel fondsen\n'";
  $query .= ", add_user           = '".$data["add_user"]."' ";
  $query .= ", add_date           = NOW() ";

  $db->SQL($query);
  $db->Query();
  $orderIdent = $db->last_id();
  $orderid = $__appvar['bedrijf'].$orderIdent;
  $query  = "UPDATE Orders SET ";
  $query .= "  orderid ='".$orderid."' ";
  if ($action == "koop")
    $query .= ", transactieSoort = 'A' ";
  else
    $query .= ", transactieSoort = 'V' ";
  $db->SQL($query." WHERE id = ".$orderIdent);
  $db->Query();


  $queryf="SELECT Fonds as fonds, Valuta, Fondseenheid FROM Fondsen WHERE fonds='".$data['Fonds']."'";
  $db->SQL($queryf);
  $fonds = $db->lookupRecord();

  $queryf = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$data['Fonds']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($queryf);
  $fondsKoers = $db->lookupRecord();

  $queryf = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($queryf);
  $valutaKoers = $db->lookupRecord();



  $aantal = 0;
  for ($x=0;$x < count($records);$x++)
  {
    $regelRec = $records[$x];
    $aantal += abs($regelRec["Aantal"]);
    $order = new orderControlleBerekening();
    $db->SQL("SELECT * , if(Depotbank='".$data["Depotbank"]."',0,1) as volgorde  FROM Rekeningen 
    WHERE Portefeuille = '".$regelRec["Portefeuille"]."' AND Valuta = 'EUR' AND Deposito=0 AND Memoriaal = 0 AND Termijnrekening = 0 AND Inactief=0
    ORDER BY volgorde");
    $rekeningRec = $db->lookupRecord();
    
    $order->setdata($orderid,$regelRec["Portefeuille"],'EUR',$regelRec['Aantal'],true,$rekeningRec['Rekening']);
    $squery = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille = '".$regelRec["Portefeuille"]."'";
    $checks = $order->getchecks();
    $db->SQL($squery);
	  $vermogenbeheerder = $db->lookupRecord();
	  $vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
    $checks = unserialize($checks[$vermogenbeheerder]);
    $order->setchecks($checks);
    $resultaat = $order->check();

    $data["brutoBedragValuta"]=abs($regelRec["Aantal"])*$fonds['Fondseenheid']*$fondsKoers['koers'];
    $data["brutoBedrag"]=$data["brutoBedragValuta"]*$valutaKoers['koers'];


    $rekNr = ereg_replace("[^0-9]","",$rekeningRec['Rekening']);

    $ordQ  = "INSERT INTO OrderRegels SET ";
    $ordQ .= "  orderid      = '".$orderid."' ";
    $ordQ .= ", positie      = ".($x+1);
    $ordQ .= ", portefeuille = '".$regelRec["Portefeuille"]."'";
    $ordQ .= ", rekeningnr   = '".$rekNr."'";
    $ordQ .= ", brutoBedragValuta   = '". $data["brutoBedragValuta"]."'";
    $ordQ .= ", brutoBedrag   = '". $data["brutoBedrag"]."'";
    $ordQ .= ", valuta       = 'EUR'";
    $ordQ .= ", controle='".$order->checkmax()."'";
    $ordQ .= ", aantal       = '".abs($regelRec["Aantal"])."'";
    $ordQ .= ", client       = '".mysql_escape_string($regelRec["ClientNaam"])."'";
    $ordQ .= ", status       = 0";
    $ordQ .= ", add_user     = '".$data["add_user"]."' ";
    $ordQ .= ", add_date     = NOW() ";

    $db->SQL($ordQ);
    $db->Query();
  }


  $query .= ", aantal = ".abs($aantal);
  $query .= ", koersLimiet = '".$regelRec["AankoopWaarde"]."'";
  $query .= " WHERE id = ".$orderIdent;
  $db->SQL($query);
  $db->Query();
  return true;
}




$query = "SELECT * FROM tmpOrder WHERE tmpOrdernr='".$_GET["tmpOrdernr"]."' ORDER BY Fonds,Depotbank ";
$db->SQL($query);
$db->Query();
$orders=array();
while ($row = $db->nextRecord())
{
  $orders[$row['Fonds']][$row['Depotbank']][]=$row;
}
foreach ($orders as $fonds=>$depotBanken)
{
  foreach ($depotBanken as $depotBank=>$rows)
  {
    $koopArray = array();
    $verkoopArray = array();




    foreach ($rows as $row)  //while ($row = $db->nextRecord())
    {
      if ($row["Aantal"] > 0)
        $koopArray[] = $row;
      else
        $verkoopArray[] = $row;
    }

    if (count($koopArray)==1)
      $orderSoort='E';
    else
      $orderSoort='M';

    foreach ($koopArray as $index=>$data)
    {
      $data['OrderSoort']=$orderSoort;
      $koopArray[$index]=$data;
    }

    if (count($verkoopArray)==1)
      $orderSoort='E';
    else
      $orderSoort='M';

    foreach ($verkoopArray as $index=>$data)
    {
      $data['OrderSoort']=$orderSoort;
      $verkoopArray[$index]=$data;
    }

    $orderCreated[$depotBank] = false;
    if (count($koopArray) > 0)
    {
      $orderCreated[$depotBank] = makeOrder($koopArray,"koop");
      $orderCreated[$depotBank] = true;
    }
    if (count($verkoopArray) > 0)
    {
      $orderCreated[$depotBank] = makeOrder($verkoopArray,"verkoop");
      $orderCreated[$depotBank] = true;
    }
  }
}


foreach ($orderCreated as $depotBank=>$waarde)
{
  if ($waarde == false)
  {
    echo "Er waren geen gegevens om een order aan te maken voor depotbank $depotBank";
    exit;
  }
}

$query = "DELETE FROM tmpOrder WHERE tmpOrdernr='".$_GET["tmpOrdernr"]."' ";
$db->SQL($query);
$db->Query();

echo "<a href=\"ordersList.php\">ga naar de orderlijst</a>";

?>