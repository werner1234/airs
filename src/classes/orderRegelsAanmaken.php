<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/28 14:26:49 $
 		File Versie					: $Revision: 1.10 $

 		$Log: orderRegelsAanmaken.php,v $
 		Revision 1.10  2014/12/28 14:26:49  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/11/30 13:03:37  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/11/23 14:02:27  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/11/08 18:26:43  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/03/02 10:20:14  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/02/02 10:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/02/13 17:03:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/12/28 18:44:10  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/10/25 08:58:11  rvv
 		*** empty log message ***

 		Revision 1.1  2009/03/29 14:36:25  rvv
 		*** empty log message ***


*/

include_once("../html/orderControlleRekenClass.php");

class orderRegelsAanmaken
{
  function orderRegelsAanmaken()
  {
    global $USR;
    $this->USR=$USR;
    $this->db = new DB();
    $this->db2 = new DB();
    $this->counter=0;
    $this->log = array();
    $this->orderData = array();
    $this->portefeuilles = array();
    $this->fondsen = array();
    
    $query="SELECT max(Vermogensbeheerders.OrderuitvoerBewaarder) as OrderuitvoerBewaarder FROM
    Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
    $this->db ->SQL($query);
    $this->bewaarder=$this->db ->lookupRecord();

  }

  function verwijderId($id)
  {
     $query="DELETE FROM TijdelijkeOrderRegels WHERE add_user='".$this->USR."' AND id='$id'";
     $this->db->SQL($query);
     $this->db->Query();
     $this->counter++;
  }
  function verwijderFonds($fonds)
  {
     $query="DELETE FROM TijdelijkeOrderRegels WHERE add_user='".$this->USR."' AND fonds='$fonds'";
     $this->db->SQL($query);
     $this->db->Query();
     $this->counter++;
  }

  function verzamel($id)
  {
    $query="SELECT * FROM TijdelijkeOrderRegels WHERE add_user='".$this->USR."' AND id='$id'";
    $this->db->SQL($query);
    $orderData=$this->db->lookupRecord();
    
    if($this->bewaarder['OrderuitvoerBewaarder']==1)
    {
       $query="SELECT if(Rekeningmutaties.Bewaarder='','NB',Rekeningmutaties.Bewaarder) as depotbank
               FROM Rekeningen 
               INNER JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
               WHERE  YEAR(Rekeningmutaties.Boekdatum)='".date('Y')."' AND 
               Rekeningen.Portefeuille='".$orderData['portefeuille']."' AND
               Rekeningmutaties.Fonds='".mysql_real_escape_string($orderData['fonds'])."'
               GROUP BY Bewaarder";
        $this->db2->SQL($query);
        $portData=$this->db2->lookupRecord();  
        if($portData['depotbank']=='') 
          $portData['depotbank']='NB';
    }
    else
    {
      $query="SELECT depotbank FROM Portefeuilles WHERE portefeuille='".$orderData['portefeuille']."'";
      $this->db->SQL($query);
      $portData=$this->db->lookupRecord();
    }

    $orderData['depotbank']=$portData['depotbank'];
    if($orderData['kopen'] > 0)
      $type='aankoop';
    else
      $type='verkoop';

    $this->portefeuilles[$orderData['portefeuille']] = $orderData['portefeuille'];
    $this->fondsen[$orderData['fonds']]=$orderData['fonds'];
    $this->orderData[$orderData['fonds']][$portData['depotbank']][$type][]=$orderData;
  }

  function verzamelFonds($fonds)
  {
    $query="SELECT * FROM TijdelijkeOrderRegels WHERE add_user='".$this->USR."' AND fonds='".mysql_real_escape_string($fonds)."'";
    $this->db->SQL($query);
    $this->db->Query();
    while($orderData = $this->db->NextRecord())
    {
      if($this->bewaarder['OrderuitvoerBewaarder']==1)
      {
        $query="SELECT if(Rekeningmutaties.Bewaarder='','NB',Rekeningmutaties.Bewaarder) as depotbank
                FROM Rekeningen 
                INNER JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
                WHERE  YEAR(Rekeningmutaties.Boekdatum)='".date('Y')."' AND 
                Rekeningen.Portefeuille='".$orderData['portefeuille']."' AND
                Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds)."'
                GROUP BY Bewaarder";
        $this->db2->SQL($query);
        $portData=$this->db2->lookupRecord();  
        if($portData['depotbank']=='') 
          $portData['depotbank']='NB';
      }
      else
      {
        $query="SELECT depotbank FROM Portefeuilles WHERE portefeuille='".$orderData['portefeuille']."'";
        $this->db2->SQL($query);
        $portData=$this->db2->lookupRecord();
      }
      $orderData['depotbank']=$portData['depotbank'];

      if($orderData['kopen'] > 0)
        $type='aankoop';
      else
        $type='verkoop';
        
      $this->portefeuilles[$orderData['portefeuille']] = $orderData['portefeuille'];
      $this->fondsen[$orderData['fonds']]=$orderData['fonds'];
      $this->orderData[$orderData['fonds']][$portData['depotbank']][$type][]=$orderData;
    }
  }

  function makeOrders()
  {
    foreach ($this->orderData as $fonds=>$depotbanken)
    {
      foreach ($depotbanken as $depotbank=>$typen)
      {
        foreach ($typen as $type=>$orderData)
          $this->makeOrder($type,$orderData);
      }
    }
  }


  function makeOrder($type,$data)
  {
  global $db, $USR, $__appvar;

  $query="SELECT * FROM Fondsen WHERE Fonds='".$data[0]['fonds']."'";
  $this->db->SQL($query);
  $fonds=$this->db->lookupRecord();
  
  $query="SELECT OrderStandaardTijdsSoort,OrderStandaardTransactieType FROM Vermogensbeheerders WHERE vermogensBeheerder = '".$__appvar['bedrijf']."'";
  $this->db->SQL($query);
  $vermogensbeheerder=$this->db->lookupRecord();
  if($vermogensbeheerder['OrderStandaardTijdsSoort']=='GTC')
  {
    $tijdsSoort='GTC';
    $tijdsLimiet="''";
  }
  else
  {
    $tijdsSoort='DAT';
    $tijdsLimiet='NOW()';  
  }
  if($vermogensbeheerder['OrderStandaardTransactieType'] <> '')
    $transactieType=$vermogensbeheerder['OrderStandaardTransactieType'];
  else
    $transactieType='L';
    
  $query  = "INSERT INTO Orders SET ";
  $query .= "  vermogensBeheerder = '".$__appvar['bedrijf']."'";
  $query .= ", fondsCode          = '".$fonds["ISINCode"]."' ";
  $query .= ", fonds              = '".mysql_escape_string($fonds["Fonds"])."' ";
  $query .= ", fondsOmschrijving  = '".mysql_escape_string($fonds["Omschrijving"])."' ";
  $query .= ", transactieType     = '$transactieType' ";
  $query .= ", tijdsLimiet        = $tijdsLimiet";
  $query .= ", tijdsSoort         = '$tijdsSoort' ";
  $query .= ", laatsteStatus      = 0";
  $query .= ", Depotbank           = '".$data[0]["depotbank"]."' ";
  $query .= ", status             = '".date("Ymd_Hi")."/".$data[0]["add_user"]." - aangemaakt via modelrapport.\n'";
  $query .= ", add_user           = '".$data[0]["add_user"]."' ";
  $query .= ", add_date           = NOW() ";

  $this->db->SQL($query);
  $this->db->Query();
  $orderIdent = $this->db->last_id();
  $orderid = $__appvar['bedrijf'].$orderIdent;

  $this->db->SQL("UPDATE Orders SET orderid ='".$orderid."'  WHERE id = '".$orderIdent."' ");
  $this->db->Query();

  $aantalTotaal=0;
  $x=1;
  foreach ($data as $orderRegel)
  {
    $query="SELECT * FROM Portefeuilles WHERE Portefeuille='".$orderRegel['portefeuille']."'";
    $this->db->SQL($query);
    $portefeuille=$this->db->lookupRecord();

    $query="SELECT * FROM Clienten WHERE Client='".$portefeuille['Client']."'";
    $this->db->SQL($query);
    $client=$this->db->lookupRecord();

    $this->db->SQL("SELECT * FROM Rekeningen WHERE Portefeuille = '".$orderRegel["portefeuille"]."' AND Valuta = 'EUR' AND Deposito=0 AND Memoriaal = 0 AND Termijnrekening = 0 and Inactief=0 ");
    $rekeningRec = $this->db->lookupRecord();
    $rekNr = ereg_replace("[^0-9]","",$rekeningRec['Rekening']);

    $aantal = round($orderRegel['kopen']-$orderRegel['verkopen'],0);
    $aantalTotaal += $aantal;

    $queryf = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$fonds['Fonds']."' ORDER BY Datum DESC LIMIT 1";
    $db->SQL($queryf);
    $fondsKoers = $db->lookupRecord();

    $queryf = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
    $db->SQL($queryf);
    $valutaKoers = $db->lookupRecord();

    $order = new orderControlleBerekening();
    $order->setdata($orderid,$orderRegel["portefeuille"],'EUR',abs($aantal),true,$rekeningRec['Rekening']);
    $squery = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille = '".$orderRegel["portefeuille"]."'";
    $checks = $order->getchecks();
    $db->SQL($squery);
	  $vermogenbeheerder = $db->lookupRecord();
	  $vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
    $checks = unserialize($checks[$vermogenbeheerder]);
    $order->setchecks($checks);
    $resultaat = $order->check();

    //echo abs($aantal)."*".$fonds['Fondseenheid']."*".$fondsKoers['koers']."<br>\n";
    $tmp=array();
    $tmp["brutoBedragValuta"]=abs($aantal)*$fonds['Fondseenheid']*$fondsKoers['koers'];
    $tmp["brutoBedrag"]=$tmp["brutoBedragValuta"]*$valutaKoers['koers'];

    $ordQ  = "INSERT INTO OrderRegels SET ";
    $ordQ .= "  orderid      = '".$orderid."' ";
    $ordQ .= ", positie      = '".$x."' ";
    $ordQ .= ", portefeuille = '".$orderRegel["portefeuille"]."'";
    $ordQ .= ", rekeningnr   = '".$rekNr."'";
    $ordQ .= ", brutoBedragValuta   = '". $tmp["brutoBedragValuta"]."'";
    $ordQ .= ", brutoBedrag   = '". $tmp["brutoBedrag"]."'";
    $ordQ .= ", valuta       = 'EUR'";
    $ordQ .= ", controle     = '".$order->checkmax()."'";
    $ordQ .= ", aantal       = '".abs($aantal)."'";
    $ordQ .= ", client       = '".mysql_escape_string($client["Client"])."'";
    $ordQ .= ", status       = 0";
    $ordQ .= ", add_user     = '".$orderRegel["add_user"]."' ";
    $ordQ .= ", add_date     = NOW() ";

    $this->db->SQL($ordQ);
    $this->db->Query();
    $this->verwijderId($orderRegel['id']);
    $x++;
  }
  if(count($data) > 1)
    $orderSoort='M';
  else
  {
    if(count($this->portefeuilles)==1 && count($this->fondsen) > 1)
      $orderSoort='C';
    else  
      $orderSoort='E';
  }
  
  if($this->lastPortefeuille==$orderRegel["portefeuille"] && $orderSoort=='C')
  {
    $newBatchId=$this->lastBatchId;
  }
  else
  {
    $cfg=new AE_config();
    $newBatchId=$cfg->getData('lastOrderBatchId')+1;
    $cfg->addItem('lastOrderBatchId',$newBatchId);
  }  

  $query  = "UPDATE Orders SET orderid ='".$orderid."', OrderSoort='$orderSoort', BatchId='$newBatchId'  ";
  if ($type == 'aankoop')
    $query .= ", transactieSoort = 'A' ";
  else
    $query .= ", transactieSoort = 'V' ";
  $query .= ", aantal = '".abs($aantalTotaal)."'";
  $query .= ", koersLimiet = '".$orderRegel["koers"]."'";
  $query .= " WHERE id = '".$orderIdent."'";
  $this->db->SQL($query);
  $this->db->Query();
  $this->lastPortefeuille=$orderRegel["portefeuille"];
  $this->lastBatchId=$newBatchId;
  
  return true;
  }

}
?>