<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.16 $

 		$Log: ordersPrint.php,v $
 		Revision 1.16  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.15  2017/06/24 16:33:47  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/05/20 18:14:56  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/05/03 14:33:36  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/04/08 18:20:37  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/03/29 16:26:13  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/03/20 06:57:10  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/03/18 20:28:27  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/03/12 08:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/08/31 16:05:52  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/08/25 14:34:54  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/07/20 16:07:33  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/07/16 15:18:12  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/07/06 16:05:51  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/06/05 12:18:55  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/12/13 17:57:40  rvv
 		*** empty log message ***
 		
 	

*/


include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");
require_once("../classes/AE_cls_xls.php");
include_once("../config/ordersVars.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/rapportRekenClass.php");
include_once("../classes/AE_cls_FIXtransport.php");

class orderPrint
{
  function orderPrint($orderId)
  {
    global $USR;
    $this->fix = new AE_FIXtransport();
    $this->db=new DB();
    $query="SELECT id,fonds,beurs,ISINCode,fondsOmschrijving,transactieType,transactieSoort,tijdsLimiet,tijdsSoort,koersLimiet,fondseenheid,fondsValuta,
    orderStatus,memo,depotbank,batchId,orderSoort,giraleOrder,fixOrder,fixVerzenddatum,fixAnnuleerdatum,add_date,add_user,change_date,change_user
    FROM OrdersV2 WHERE id='$orderId'";
    $this->db->SQL($query);
    $this->orderRecord=$this->db->lookupRecord();

    $query = "SELECT OrderLoggingOpNota FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
    $this->db->SQL($query);
    $this->beheerderRec = $this->db->lookupRecord();

    $this->orders=array();
    if($this->orderRecord['orderSoort']=='E' || $this->orderRecord['orderSoort']=='M' || $this->orderRecord['orderSoort']=='N' || $this->orderRecord['orderSoort']=='O')
    {
      $this->orders[$orderId]['order']=$this->orderRecord;
      $this->orders[$orderId]['uitvoeringen']=$this->getUitvoeringen($orderId);
      $this->getFondsInfo($orderId,$this->orderRecord['fonds']);
      $this->orderRecordRegels=$this->getOrderRegels($orderId);
    }
    elseif($this->orderRecord['orderSoort']=='C')
    {
      $this->getOrders($this->orderRecord['batchId']);
    }

  }


  function getOrderRegels($orderId)
  {
    $query="SELECT id,positie,portefeuille,client,rekening,aantal,bedrag,orderregelStatus FROM OrderRegelsV2 WHERE orderid='$orderId' ORDER BY positie";
    $this->db->SQL($query); 
    $this->db->Query();
    $this->orders[$orderId]['orderregels']=array();
    while($data=$this->db->nextRecord())
    {
      $crmNaam = getCrmNaam($data['portefeuille'],true);
      if ($crmNaam['naam'] <> '')
      {
        $data["client"] = $crmNaam['naam'];
      }
      $this->orders[$orderId]['orderregels'][$data['id']]=$data;
      $this->orders[$orderId]['order']['aantal']+=$data['aantal'];
      $this->orders[$orderId]['order']['bedrag']+=$data['bedrag'];
      if(!isset($eersteOrderRegel))
        $eersteOrderRegel=$data;
    }
    if(isset($eersteOrderRegel))
      return $eersteOrderRegel;
  }

  function getOrders($batchId)
  {
    $query="SELECT id,fonds,beurs,ISINCode,fondsOmschrijving,transactieType,transactieSoort,tijdsLimiet,tijdsSoort,koersLimiet,fondseenheid,fondsValuta,
    orderStatus,memo,depotbank,batchId,orderSoort,giraleOrder,fixOrder,fixVerzenddatum,fixAnnuleerdatum,add_date,add_user,change_date,change_user
    FROM OrdersV2 WHERE batchId='$batchId' ORDER BY orderStatus,id ";
    $this->db->SQL($query);
    $this->db->Query();
    while($data=$this->db->nextRecord())
    {
      $this->orders[$data['id']]['order']=$data;
      $this->orders[$data['id']]['uitvoeringen']=$this->getUitvoeringen($data['id']);
    }
    foreach($this->orders as $orderId=>$orderData)
    {
      $this->orderRecordRegels=$this->getOrderRegels($orderId);
      $this->getFondsInfo($orderId,$orderData['order']['fonds']);
    }
  }

  function getUitvoeringen($orderId)
  {
    $db=new DB();
    $query = "SELECT * FROM OrderUitvoeringV2 WHERE orderid='".$orderId."'  ";
    $db->SQL($query);
    $db->Query();
    $uitvoering=array();
    while($data=$db->nextRecord())
    {
      $data['valutaKoers']=getValutaKoers($this->orders[$orderId]['order']['fondsValuta'],$data['uitvoeringsDatum']);
      $uitvoering['Waarde'] +=$data['uitvoeringsAantal']*$data['uitvoeringsPrijs'];
      $uitvoering['WaardeEur'] += $data['uitvoeringsAantal']*$data['uitvoeringsPrijs']*$data['valutaKoers'];
      $uitvoering['Aantal'] +=$data['uitvoeringsAantal'];
      $uitvoering[]=$data;
    }
    if($uitvoering['Aantal'] <> 0)
    {
      $uitvoering['gemiddeldePrijsValuta']=$uitvoering['Waarde']/$uitvoering['Aantal'];
      $uitvoering['gemiddeldePrijsEur']=$uitvoering['WaardeEur']/$uitvoering['Aantal'];
    }
    else
    {
      $uitvoering['gemiddeldePrijsValuta']=0;
      $uitvoering['gemiddeldePrijsEur']=0;
    }
    return $uitvoering;
  }
  function getFondsInfo($orderId,$fonds)
  {
    $query="SELECT Fondseenheid,Omschrijving,ISINCode,Valuta,Lossingsdatum FROM Fondsen WHERE Fonds='$fonds'";
    $this->db->SQL($query);
    $this->orders[$orderId]['fonds']=$this->db->lookupRecord();
    
    $query = "SELECT Fonds,Datum,Koers FROM Fondskoersen WHERE fonds='".$fonds."' Order by datum desc limit 1 ";
    $this->db->SQL($query);
    $this->orders[$orderId]['fondskoers'] = $this->db->lookupRecord();

    $query = "SELECT Valuta,Datum,Koers FROM Valutakoersen WHERE Valuta='".$this->orders[$orderId]['order']['fondsValuta']."' Order by datum desc limit 1 ";
    $this->db->SQL($query);
    $this->orders[$orderId]['valutakoers'] = $this->db->lookupRecord();
  }
  
  function pdfUitvoer()
  {
    $this->pdf->Output();
  }
  
  function XlsUitvoer()
  {  
    //$this->pdf->OutputXLS('order.xls','S');//,"F"
    $this->xls->setData($this->xlsData);
    $this->xls->OutputXls();
  }
  
  function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
 	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];

	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	   return number_format($waarde,$dec,",",".");
	  }
	}

  function addOrderregelHeader()
  {

  }
  
  function createPrint()
  {
    global $__ORDERvar,$__appvar,$USR;

    //$this->beheerderRec['OrderLoggingOpNota']=1;
    //if($this->beheerderRec['OrderLoggingOpNota']==1)
    //  $this->pdf=new PDFRapport('L','mm');
    //else
    if($this->orderRecord['orderSoort']=='C')
      $this->pdf=new PDFRapport('L','mm');
    else
      $this->pdf=new PDFRapport('P','mm');

    $this->xls = new AE_xls();

    $this->xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $this->xls->excelOpmaak['kopl']=array('setAlign'=>'left','setBgColor'=>'22');
    $this->xls->excelOpmaak['kopr']=array('setAlign'=>'left');


    loadLayoutSettings($this->pdf, $this->orderRecordRegels['portefeuille']);
    if($this->orderRecord['orderSoort']=='C')
      $this->pdf->rapport_type='ORDERL';
    else
      $this->pdf->rapport_type='ORDERP';
    $this->pdf->AddPage();
    $this->pdf->SetFont('arial','',9); 
    if(in_array($this->orderRecord['orderSoort'],array('E','M','N','O')))
    {
      $this->xls->setColumn[] = array(0,1,10);
      $this->xls->setColumn[] = array(2,2,15);
      $this->xls->setColumn[] = array(3,3,40);
      $this->xls->setColumn[] = array(4,4,20);
      $this->pdf->SetWidths(array(50,100));
      $this->pdf->Row(array('Order kenmerk',$__appvar['bedrijf'].$this->orderRecord['id'])); 
      $this->pdf->Row(array('Fonds',$this->orderRecord['fondsOmschrijving'].' (Laatste koers '.$this->orders[$this->orderRecord['id']]['fondskoers']['Koers'].')'));
      $this->pdf->Row(array('ISIN',$this->orderRecord['ISINCode']));
      $this->xlsData[] = array(array('Order kenmerk','kopl'),'',array($__appvar['bedrijf'].$this->orderRecord['id'],'kopr'));
      $this->xlsData[] = array(array('Fonds','kopl'),'',array($this->orderRecord['fondsOmschrijving'].' (Laatste koers '.$this->orders[$this->orderRecord['id']]['fondskoers']['Koers'].')','kopr'));
      $this->xlsData[] = array(array('ISIN','kopl'),'',array($this->orderRecord['ISINCode'],'kopr'));

      if($this->orderRecord['koersLimiet']<>0)
      {
        $this->pdf->Row(array('Koerslimiet',$this->orderRecord['koersLimiet']));
        $this->xlsData[] = array(array('Koerslimiet','kopl'),'',array($this->orderRecord['koersLimiet'],'kopr'));
      }
      else
      {
       // $this->pdf->Row(array('Laatste koers', $this->orders[$this->orderRecord['id']]['fondskoers']['Koers']));
       // $this->xlsData[] = array(array('Laatste koers','kopl'),'',array($this->orders[$this->orderRecord['id']]['fondskoers']['Koers'],'kopr'));
      }


      if ($this->orderRecord["tijdsSoort"] == "DAT")
        $looptijd = "  (".$this->orderRecord["tijdsLimiet"].")";

      if($this->orderRecord['orderSoort']=='N'||$this->orderRecord['orderSoort']=='O')
        $this->pdf->Row(array('Bedrag',$this->formatGetal($this->orders[$this->orderRecord['id']]['order']['bedrag'],2)));
      else
        $this->pdf->Row(array('Aantal',$this->formatAantal($this->orders[$this->orderRecord['id']]['order']['aantal'],0,true)));
      $this->pdf->Row(array('Fondsvaluta',$this->orders[$this->orderRecord['id']]['fonds']['Valuta'])); 
      $this->pdf->Row(array('TransactieType',$__ORDERvar["transactieType"][$this->orderRecord["transactieType"]]));
      $this->pdf->Row(array('TransactieSoort',$__ORDERvar["transactieSoort"][$this->orderRecord["transactieSoort"]])); 
      $this->pdf->Row(array('Looptijd',$__ORDERvar["tijdsSoort"][$this->orderRecord["tijdsSoort"]].$looptijd));

      if($this->orderRecord['orderSoort']=='N'||$this->orderRecord['orderSoort']=='O')
        $this->xlsData[] = array(array('Bedrag','kopl'),'',array(round($this->orders[$this->orderRecord['id']]['order']['bedrag'],2),'kopr'));
      else
        $this->xlsData[] = array(array('Aantal','kopl'),'',array(round($this->orders[$this->orderRecord['id']]['order']['aantal'],6),'kopr'));
      $this->xlsData[] = array(array('Fondsvaluta','kopl'),'',array($this->orders[$this->orderRecord['id']]['fonds']['Valuta'],'kopr'));
      $this->xlsData[] = array(array('TransactieType','kopl'),'',array($__ORDERvar["transactieType"][$this->orderRecord["transactieType"]],'kopr'));
      $this->xlsData[] = array(array('TransactieSoort','kopl'),'',array($__ORDERvar["transactieSoort"][$this->orderRecord["transactieSoort"]],'kopr'));
      $this->xlsData[] = array(array('Looptijd','kopl'),'',array($__ORDERvar["tijdsSoort"][$this->orderRecord["tijdsSoort"]].$looptijd,'kopr'));
      $this->xlsData[] = array(array('Depotbank','kopl'),'',array($this->orders[$this->orderRecord['id']]['order']['depotbank'],'kopr'));

 
      for ($x=0;$x < count($this->xlsData);$x++)
      {
       $this->xls->mergeCells[] = array($x,0,$x,1);
       $this->xls->mergeCells[] = array($x,2,$x,3);
      }
      $this->pdf->ln(10);
      $this->xlsData[] = array();
      $this->pdf->SetAligns(array('R','R','L','L','L','R','R'));
      $this->pdf->SetWidths(array(10,25,30,40,35,25,25));
      $this->pdf->CellBorders=array('U','U','U','U','U','U','U');
      //$this->beheerderRec['OrderLoggingOpNota']=1;

      if($this->orderRecord['orderSoort']=='N'||$this->orderRecord['orderSoort']=='O')
        $aantalTxt='Bedrag';
      else
        $aantalTxt='Aantal';

      if($this->beheerderRec['OrderLoggingOpNota']==1)
      {

        if( $this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsValuta'] == 0)
          $geschat="Geschat orderbedrag";
        else
          $geschat='Orderbedrag';

        if(strtolower($this->orderRecord['fondsvaluta']) <> 'eur')
          $geschatEur="Geschat orderbedrag";
        else
          $geschatEur=$geschat;

        if( $this->orders[$this->orderRecord['id']]['fonds']['Lossingsdatum'] <> '0000-00-00')
          $renteOpmerking="Geschatte orderbedrag exclusief opgelopen rente.";
        else
          $renteOpmerking='';

        $this->pdf->row(array("Pos", $aantalTxt, "Portefeuille", "Client", "Rekeningnr", $geschatEur . " eur", $geschat . ' valuta'));
        $this->xlsData[] = array(array('Pos', 'header'), array($aantalTxt, 'header'), array('Portefeuille', 'header'), array('Client', 'header'), array('Rekening', 'header'), array($geschatEur . " eur", 'header'), array($geschat . ' valuta', 'header'));

      }
      else
      {
        $this->pdf->row(array('Pos', $aantalTxt, 'Portefeuille', 'Client', 'Rekening'));
        $this->xlsData[] = array(array('Pos', 'header'), array($aantalTxt, 'header'), array('Portefeuille', 'header'), array('Client', 'header'), array('Rekening', 'header'));
      }
      $this->pdf->CellBorders=array();
      foreach($this->orders[$this->orderRecord['id']]['orderregels'] as $orderregelId=>$orderregel)
      {
       // listarray($this->orderRecord);
       // listarray( $this->orders[$this->orderRecord['id']]);
        if($this->orderRecord['orderSoort']=='N'||$this->orderRecord['orderSoort']=='O')
        {
          $aantal = $orderregel['bedrag'];
          $aantalPdf= $this->formatGetal($aantal,2);
        }
        else
        {
          $aantal = $orderregel['aantal'];
          $aantalPdf=$this->formatAantal($aantal,0,true);
        }
        $orderregel['portefeuille']=$this->fix->getDepotbankPortefeuille($orderregel['rekening'],$orderregel['portefeuille'],1);
        if($this->beheerderRec['OrderLoggingOpNota']==1 && ($this->orderRecord['orderSoort']!='N'||$this->orderRecord['orderSoort']!='O'))
        {
          if ($this->orders[$this->orderRecord['id']]['fonds']['Fondseenheid'] <> 0)
            $eenheid = $this->orders[$this->orderRecord['id']]['fonds']['Fondseenheid'];
          else
            $eenheid = 1;

          if($this->orders[$this->orderRecord['id']]['order']['koersLimiet'] <> 0)
            $koers=$this->orders[$this->orderRecord['id']]['order']['koersLimiet'];
          else
            $koers=$this->orders[$this->orderRecord['id']]['fondskoers']['Koers'];

          if ($this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsEur'] <> 0)
            $bedrag = $orderregel["aantal"] * $this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsEur'] * $eenheid;
          else
            $bedrag = $orderregel["aantal"] * $eenheid * $koers * $this->orders[$this->orderRecord['id']]['valutakoers']['Koers'];


          if ($this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsValuta'] <> 0)
            $bedragValuta = $orderregel["aantal"] * $this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsValuta'] * $eenheid;
          else
            $bedragValuta = $orderregel["aantal"] * $eenheid * $koers;

          $this->pdf->Row(array($orderregel['positie'], $aantalPdf, $orderregel['portefeuille'], $orderregel['client'], $orderregel['rekening'],number_format($bedrag, 2, ",", "."),number_format($bedragValuta, 2, ",", ".")));
          $this->xlsData[] = array($orderregel['positie'], $aantal, $orderregel['portefeuille'], $orderregel['client'], $orderregel['rekening'],round($bedrag, 2),round($bedragValuta, 2));
        }
        else
        {
          $this->pdf->Row(array($orderregel['positie'], $aantalPdf, $orderregel['portefeuille'], $orderregel['client'], $orderregel['rekening']));
          $this->xlsData[] = array($orderregel['positie'], $aantal, $orderregel['portefeuille'], $orderregel['client'], $orderregel['rekening']);
        }
      }
      $this->xlsData[]=array();
      $this->xlsData[]=array("Printinformatie:",'',$USR,date('d-m-Y'),date("H:i"),$USR);

      if($this->beheerderRec['OrderLoggingOpNota']==1)
      {
        if($this->pdf->getY()<140)
          $this->pdf->SetY(140);
        else
          $this->pdf->ln(4);
        $this->pdf->MultiCell(200,4,$renteOpmerking);

        if($this->orderRecord['memo'] <> '')
          $this->pdf->MultiCell(200,4,$this->orderRecord['memo']);

        $this->pdf->Row(array(''));
        $this->pdf->setwidths(array(60,120,50));
        $this->pdf->setaligns(array("L","L","L","L"));

        $orderLogs = new orderLogs();
        $logData = $orderLogs->getForOrder($this->orderRecord['id']);
        $logData=array_reverse($logData);
        foreach ($logData as $log)
        {
          $this->pdf->row(array(date('d-m-Y H:i:s', db2jul($log['change_date'])). $log['timeOffset'] . '/' . $log['add_user'],(($log['fixOrderId'] != 0)?$log['fixOrderId']:"") . ' ' . $log['message']));
          $this->xlsData[] = array(date('d-m-Y H:i', db2jul($log['change_date'])). $log['timeOffset'] . '/' . $log['add_user'],(($log['fixOrderId'] != 0)?$log['fixOrderId']:"") . ' ' . $log['message']);
        }
      }

    }
    else
    {
       $this->orderRecordRegels['portefeuille']=$this->fix->getDepotbankPortefeuille($this->orderRecordRegels['rekening'],$this->orderRecordRegels['portefeuille'],1);
       $this->pdf->SetWidths(array(50,100));
       $this->pdf->Row(array('Portefeuille',$this->orderRecordRegels['portefeuille'])); 
       $this->pdf->Row(array('Client',$this->orderRecordRegels['client'])); 
       
       $this->xls->setColumn[] = array(0,1,10);
       $this->xls->setColumn[] = array(2,2,40);
       $this->xls->setColumn[] = array(3,3,15);
       $this->xls->setColumn[] = array(4,5,10);
       $this->xls->setColumn[] = array(6,7,15);
    
       $this->xlsData[] = array(array('Portefeuille','kopl'),'',array($this->orderRecordRegels['portefeuille'],'kopr'));
       $this->xlsData[] = array(array('Client','kopl'),'',array($this->orderRecordRegels['client'],'kopr'));

       for ($x=0;$x < count($this->xlsData);$x++)
       {
        $this->xls->mergeCells[] = array($x,0,$x,1);
        $this->xls->mergeCells[] = array($x,2,$x,3);
       }
       $this->pdf->ln(10);    
            
       $this->pdf->SetWidths(array(10,20,58,30,14,20,20,30,22,22));
       $this->pdf->SetAligns(array("R","L","L","R","L","R","R","L","R","L"));
       $this->pdf->CellBorders=array('U','U','U','U','U','U','U','U','U','U');
       $this->xlsData[]=array();
       $aantalVeld=($this->orderRecord['orderSoort']=='N'||$this->orderRecord['orderSoort']=='O'?'Bedrag':'Aantal');
       $this->pdf->Row(array("Pos",'Kenmerk',"Fonds omschrijving","ISIN-code","Valuta",$aantalVeld,"Transactiesoort","Transactietype","Limietkoers","tijdsLimiet"));
       $this->xlsData[]=array(array('Pos','header'),array('Kenmerk','header'),array('Fonds omschrijving','header'),array('ISIN-code','header'),
                              array('Valuta','header'),array($aantalVeld,'header'),array('Transactiesoort','header'),array('Transactietype','header'),
                              array('Limietkoers','header'),array('tijdsLimiet','header'));

       $this->pdf->CellBorders=array();
       $this->pdf->ln(1);
       foreach ($this->orders as $orderId=>$orderData)
       {
         if(!isset($laatsteOrderStatus) || $orderData['order']['orderStatus'] <> $laatsteOrderStatus)
         {
           if(isset($laatsteOrderStatus))
           {
             $this->pdf->ln();
             $this->xlsData[] = array();
           }

           $this->pdf->Row(array('','',"Order: ".$__ORDERvar['orderStatus'][$orderData['order']['orderStatus']]));
           $this->xlsData[] = array('','',"Order: ".$__ORDERvar['orderStatus'][$orderData['order']['orderStatus']]);
         }

         foreach($this->orders[$orderId]['orderregels'] as $orderregelId=>$orderregel)
         {
           $aantal = $this->formatAantal($orderregel["aantal"],0,true);
           if($orderregel["bedrag"] <> 0)
             $aantal = "€ " . $this->formatAantal($orderregel["bedrag"],0,true);
           if($orderData['order']['koersLimiet']<>0)
             $limietKoers=$orderData['order']['koersLimiet'];
           else
             $limietKoers='';

           $row=array();
           $row['transactieType']=$__ORDERvar["transactieType"][$orderData['order']['transactieType']];
           $row['transactieSoort']=$orderData['order']['transactieSoort'];//$__ORDERvar["transactieSoort"][];

           if($row['transactieType']=='Limiet')
             $row['transactieType'].=": ".$this->formatAantal($row['koersLimiet'],2);

           $this->pdf->Row(array($orderregel['positie'], $__appvar['bedrijf'] . $orderId, $orderData['order']['fondsOmschrijving'], $orderData['order']['ISINCode'],
                               $this->orders[$orderId]['fonds']['Valuta'], $aantal, $row['transactieSoort'], $row['transactieType'],$limietKoers,
                               date('d-m-Y',db2jul($orderData['order']['tijdsLimiet'])) ));
           $this->xlsData[] = array($orderregel['positie'], $__appvar['bedrijf'] . $orderId, $orderData['order']['fondsOmschrijving'], $orderData['order']['ISINCode'],
               $this->orders[$orderId]['fonds']['Valuta'], ($orderregel["bedrag"]<>0?$orderregel["bedrag"]:$orderregel["aantal"]), $row['transactieSoort'], $row['transactieType'],$limietKoers,$orderData['order']['tijdsLimiet']);

         }
         $laatsteOrderStatus=$orderData['order']['orderStatus'];
       }

      if($this->beheerderRec['OrderLoggingOpNota']==1)
      {
        if($this->pdf->getY()<140)
          $this->pdf->SetY(140);
        else
          $this->pdf->ln(4);

        $this->pdf->MultiCell(200,4,$renteOpmerking);

        if($orderRec['memo'] <> '')
          $this->pdf->MultiCell(200,4,$orderRec['memo']);

        $this->pdf->Row(array(''));
        $this->pdf->setwidths(array(60,120,50));
        $this->pdf->setaligns(array("L","L","L","L"));

        $orderLogs = new orderLogs();
        $this->pdf->SetFont('arial','',8);
        foreach ($this->orders as $orderId=>$orderData)
        {
          $logData = $orderLogs->getForOrder($orderId);
          $logData=array_reverse($logData);
          foreach ($logData as $log)
          {
            if($this->orderRecord['orderSoort']=='C')
              $log['message']="(".$orderId.") ".$log['message'];
            $this->pdf->row(array(date('d-m-Y H:i:s', db2jul($log['change_date'])) . $log['timeOffset'] . '/' . $log['add_user'], (($log['fixOrderId'] != 0)?$log['fixOrderId']:"") . ' ' . $log['message']));
            $this->xlsData[] = array(date('d-m-Y H:i:s', db2jul($log['change_date'])) . $log['timeOffset'] . '/' . $log['add_user'], (($log['fixOrderId'] != 0)?$log['fixOrderId']:"") . ' ' . $log['message']);
          }
        }
      }
    }
  }
  
}

if($_GET['orderid'])
{
  $orderPrint=new orderPrint($_GET['orderid']);
  $orderPrint->createPrint();
  if($_GET['uitvoer']=='pdf') 
    $orderPrint->pdfUitvoer();
  if($_GET['uitvoer']=='xls')   
    $orderPrint->xlsUitvoer();
  //listarray($orderPrint);
}



?>