<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/24 17:00:00 $
 		File Versie					: $Revision: 1.13 $

 		$Log: ordersV2PDF_L72.php,v $
 		Revision 1.13  2019/08/24 17:00:00  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/04/06 17:08:39  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/12/14 16:41:01  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.9  2018/02/03 18:51:31  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/10/02 06:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/09/27 15:57:59  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/09/23 17:44:09  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/09/20 13:09:27  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/09/11 06:12:50  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/09/10 14:32:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/08/09 16:09:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/08/05 17:24:26  rvv
 		*** empty log message ***
 		
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

class ordersV2PDF_L72
{
  function ordersV2PDF_L72($orderId)
  {
    global $USR;
    $this->db=new DB();




    $query="SELECT id,fonds,beurs,ISINCode,fondsOmschrijving,transactieType,transactieSoort,tijdsLimiet,tijdsSoort,koersLimiet,fondseenheid,fondsValuta,
    orderStatus,memo,depotbank,batchId,orderSoort,giraleOrder,fixOrder,fixVerzenddatum,fixAnnuleerdatum,add_date,add_user,change_date,change_user
    FROM OrdersV2 WHERE id='$orderId'";
    $this->db->SQL($query);
    $this->orderRecord=$this->db->lookupRecord();

    $this->orderRecord=$this->createHeaderValues($this->orderRecord);


    $query = "SELECT OrderLoggingOpNota FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
    $this->db->SQL($query);
    $this->beheerderRec = $this->db->lookupRecord();

    $this->orders=array();
    if($this->orderRecord['orderSoort']=='E' || $this->orderRecord['orderSoort']=='M' || $this->orderRecord['orderSoort']=='N')
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

    $this->pdf=new PDFRapport('P','mm');
    $this->pdf->orderData=$this->orderRecord;

    $this->xls = new AE_xls();


  }
  
  function getRekening($rekening)
  {
    $db=new DB();
    $query="SELECT Rekeningen.Rekening,Rekeningen.Memoriaal ,Vermogensbeheerders.OrderuitvoerBewaarder
FROM Rekeningen
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
WHERE Rekeningen.Rekening='".mysql_real_escape_string($rekening)."' AND Rekeningen.consolidatie=0";
    $db->SQL($query);
    $rekening=$db->lookupRecord();
    if($rekening['OrderuitvoerBewaarder']==1 && $rekening['Memoriaal']==1)
    {
      $rekening=substr($rekening['Rekening'],0,-3);
    }
    else
    {
      $rekening=$rekening['Rekening'];
    }
   
    return $rekening;
  }

  function createHeaderValues($orderRecord)
  {
    $db=new DB();
    $query="SELECT Gebruikers.Gebruiker, Gebruikers.Naam FROM Gebruikers";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $gebruikers[$data['Gebruiker']]=$data['Naam'];
    }

    if(isset($gebruikers[$orderRecord['add_user']]))
      $orderRecord['beheerder1']=$gebruikers[$orderRecord['add_user']];
    else
      $orderRecord['beheerder1']=$orderRecord['add_user'];

    $memoRegels=explode("\n",$orderRecord['memo']);
    $orderRecord['realMemo']='';
    foreach($memoRegels as $regel)
    {
      $regelKeyValue=explode(":",$regel,2);
      $valueRaw=trim($regelKeyValue[1]);
      if(isset($gebruikers[$valueRaw]))
        $value=$gebruikers[$valueRaw];
      else
        $value=$valueRaw;
      $key=strtolower(trim($regelKeyValue[0]));
      if($key=='beheerder 2')
        $orderRecord['beheerder2']=$value;
      elseif($key=='doorgegeven')
        $orderRecord['doorgegeven']=$value;
      elseif($key=='memo')
        $orderRecord['realMemo']=$value;
      else
      {
        if($orderRecord['realMemo']=='')
          $orderRecord['realMemo'] .= $regel;
        else
          $orderRecord['realMemo'] .= "\n".$regel;
      }
    }
    return $orderRecord;
  }


  function getOrderRegels($orderId)
  {
    $query="SELECT OrderRegelsV2.id,OrderRegelsV2.positie,OrderRegelsV2.portefeuille,OrderRegelsV2.client,OrderRegelsV2.rekening,OrderRegelsV2.aantal,
  OrderRegelsV2.bedrag,OrderRegelsV2.orderregelStatus,Rekeningen.depotbank as rekDepot FROM OrderRegelsV2 
  LEFT JOIN Rekeningen on OrderRegelsV2.rekening=Rekeningen.rekening AND Rekeningen.consolidatie=0
  WHERE orderid='$orderId' ORDER BY positie";
    $this->db->SQL($query); 
    $this->db->Query();
    $this->orders[$orderId]['orderregels']=array();
    while($data=$this->db->nextRecord())
    {
      $crmNaam = getCrmNaam($data['portefeuille'],true);
      $data["naam"] = $crmNaam['naam'];
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
    FROM OrdersV2 WHERE batchId='$batchId' AND OrdersV2.orderStatus < 5 ORDER BY orderStatus,id ";
    $this->db->SQL($query);
    $this->db->Query();
    $n=0;
    while($data=$this->db->nextRecord())
    {
      $data=$this->createHeaderValues($data);
      if($n==0)
        $this->orderRecord=$data;
      $n++;
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

    $this->xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $this->xls->excelOpmaak['kopl']=array('setAlign'=>'left','setBgColor'=>'22');
    $this->xls->excelOpmaak['kopr']=array('setAlign'=>'left');

    loadLayoutSettings($this->pdf, $this->orderRecordRegels['portefeuille']);
    $this->pdf->rapport_type='ORDERPC';

    $this->pdf->portefeuilledata['orderAdd_date']=$this->orders[$this->orderRecord['id']]['order']['add_date'];

    $this->pdf->AddPage();
    $this->pdf->SetFont('arial','',8);
    if($this->orderRecord['orderSoort']=='M')
    {
      $this->xls->setColumn[] = array(0,1,10);
      $this->xls->setColumn[] = array(2,2,15);
      $this->xls->setColumn[] = array(3,3,40);
      $this->xls->setColumn[] = array(4,4,20);

      $this->pdf->SetWidths(array(50,100));


      $this->pdf->Row(array('Ordernummer:',$this->orderRecord['id']));
      $this->pdf->Row(array('Depotbank:',$this->orders[$this->orderRecord['id']]['order']['depotbank']));
      $this->pdf->ln(30);
      /*
      $this->pdf->Row(array('Fonds',$this->orderRecord['fondsOmschrijving'].' (Laatste koers '.$this->orders[$this->orderRecord['id']]['fondskoers']['Koers'].')'));
      $this->pdf->Row(array('ISIN',$this->orderRecord['ISINCode']));
      */
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

/*
      if($this->orderRecord['orderSoort']=='N')
        $this->pdf->Row(array('Bedrag',$this->formatGetal($this->orders[$this->orderRecord['id']]['order']['bedrag'],2)));
      else
        $this->pdf->Row(array('Aantal',$this->formatAantal($this->orders[$this->orderRecord['id']]['order']['aantal'],0,true)));
      $this->pdf->Row(array('Fondsvaluta',$this->orders[$this->orderRecord['id']]['fonds']['Valuta'])); 
      $this->pdf->Row(array('TransactieType',$__ORDERvar["transactieType"][$this->orderRecord["transactieType"]]));
      $this->pdf->Row(array('TransactieSoort',$__ORDERvar["transactieSoort"][$this->orderRecord["transactieSoort"]])); 
      $this->pdf->Row(array('Looptijd',$__ORDERvar["tijdsSoort"][$this->orderRecord["tijdsSoort"]].$looptijd));
*/
      $this->pdf->CellBorders=array(array('T','L'),'T','T','T','T','T',array('T','R'));
      $this->pdf->SetWidths(array(30,20,30,23,23,23,40));
      $this->pdf->SetAligns(array('L','L','L','R','R','R'));
      $this->pdf->Row(array('ISIN','Valuta','Fondsnaam','Soort','Type','Limiet','Tijdslimiet'));
      $this->pdf->CellBorders=array(array('U','L','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('T','R','U'));
      $this->pdf->SetFont('arial','B',8);

      if($this->orderRecord["transactieSoort"]=='L')
        $limietKoers=$this->orders[$this->orderRecord['id']]['order']['koersLimiet'];
      else
        $limietKoers='';

      $this->pdf->Row(array($this->orderRecord['ISINCode'],
                            $this->orders[$this->orderRecord['id']]['fonds']['Valuta'],
                            $this->orderRecord['fondsOmschrijving'],
                            $__ORDERvar["transactieSoort"][$this->orderRecord["transactieSoort"]],
                            $__ORDERvar["transactieType"][$this->orderRecord["transactieType"]],
                            $limietKoers,
                            $__ORDERvar["tijdsSoort"][$this->orderRecord["tijdsSoort"]].$looptijd
                            ));

      if($this->orderRecord['realMemo'] <> '')
      {
        $this->pdf->ln();
        $this->pdf->Cell(20, 4, "Extra info:");
        $this->pdf->MultiCell(200, 4, $this->orderRecord['realMemo']);
        $this->pdf->ln();
      }
      else
         $this->pdf->ln(10);
      $this->pdf->SetFont('arial','',8);
      if($this->orderRecord['orderSoort']=='N')
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

      $this->xlsData[] = array();
     // $this->pdf->SetAligns(array('L','L','R','C','R'));
     // $this->pdf->SetWidths(array(10,30,60,30,30+30));

      //$this->pdf->CellBorders=array('','','',array('U','L','T'),array('U','T'));
     // $this->pdf->row(array("", "","", "Uitvoering/Limitering"));
      $this->pdf->SetAligns(array('L','L','L','R','R'));
      $this->pdf->SetWidths(array(10,30,50,30,30,30));

      $this->pdf->CellBorders=array(array('U','T'),array('U','T'),array('U','T'),array('U','T'),array('U','T'));
      //$this->beheerderRec['OrderLoggingOpNota']=1;

      if($this->orderRecord['orderSoort']=='N')
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

       // $this->pdf->row(array("Pos", $aantalTxt, "Portefeuille", "Client", "Rekeningnr", $geschatEur . " eur", $geschat . ' valuta'));
        $this->pdf->row(array("Nr.","Depot","Rekening", "Client",$aantalTxt));
       $this->xlsData[] = array(array('Pos', 'header'), array($aantalTxt, 'header'), array('Portefeuille', 'header'), array('Client', 'header'), array('Rekening', 'header'), array($geschatEur . " eur", 'header'), array($geschat . ' valuta', 'header'));

      }
      else
      {
        $this->pdf->row(array("Nr.","Rekening", "Client",$aantalTxt));
        $this->xlsData[] = array(array('Pos', 'header'), array($aantalTxt, 'header'), array('Portefeuille', 'header'), array('Client', 'header'), array('Rekening', 'header'));
      }
      $this->pdf->CellBorders=array();
      $totaalAantal=0;
      foreach($this->orders[$this->orderRecord['id']]['orderregels'] as $orderregelId=>$orderregel)
      {
       // listarray($this->orderRecord);
       // listarray( $this->orders[$this->orderRecord['id']]);
        if($this->orderRecord['orderSoort']=='N')
        {
          $aantal = $orderregel['bedrag'];
          $totaalAantal+=$aantal;
          $aantalPdf= $this->formatGetal($aantal,2);
        }
        else
        {
          $aantal = $orderregel['aantal'];
          $totaalAantal+=$aantal;
          $aantalPdf=$this->formatAantal($aantal,0,true);
        }

        if($this->orders[$this->orderRecord['id']]['order']['koersLimiet'] <> 0)
          $koers=$this->orders[$this->orderRecord['id']]['order']['koersLimiet'];
        else
          $koers=$this->orders[$this->orderRecord['id']]['fondskoers']['Koers'];

        $datum=$this->orders[$this->orderRecord['id']]['order']['tijdsLimiet'];

        $this->pdf->CellBorders=array();
        if($this->beheerderRec['OrderLoggingOpNota']==1 && $this->orderRecord['orderSoort']!='N')
        {
          if ($this->orders[$this->orderRecord['id']]['fonds']['Fondseenheid'] <> 0)
            $eenheid = $this->orders[$this->orderRecord['id']]['fonds']['Fondseenheid'];
          else
            $eenheid = 1;



          if ($this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsEur'] <> 0)
            $bedrag = $orderregel["aantal"] * $this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsEur'] * $eenheid;
          else
            $bedrag = $orderregel["aantal"] * $eenheid * $koers * $this->orders[$this->orderRecord['id']]['valutakoers']['Koers'];


          if ($this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsValuta'] <> 0)
            $bedragValuta = $orderregel["aantal"] * $this->orders[$this->orderRecord['id']]['uitvoeringen']['gemiddeldePrijsValuta'] * $eenheid;
          else
            $bedragValuta = $orderregel["aantal"] * $eenheid * $koers;

          $this->pdf->Row(array($orderregel['positie'],$orderregel['naam'], $aantalPdf, $orderregel['portefeuille'], $orderregel['client'], $this->getRekening($orderregel['rekening']),number_format($bedrag, 2, ",", "."),number_format($bedragValuta, 2, ",", ".")));
          $this->xlsData[] = array($orderregel['positie'], $aantal, $orderregel['portefeuille'], $orderregel['client'], $this->getRekening($orderregel['rekening']),round($bedrag, 2),round($bedragValuta, 2));
        }
        else
        {
          $this->pdf->Row(array($orderregel['positie'],  $this->getRekening($orderregel['rekening']),$orderregel['naam'].' '. $orderregel['client'],$aantalPdf));
          $this->xlsData[] = array($orderregel['positie'], $aantal, $orderregel['portefeuille'], $orderregel['naam'].' '.$orderregel['client'], $this->getRekening($orderregel['rekening']));

        }
      }
      $this->pdf->CellBorders=array('','','','T');
      $this->pdf->Row(array('', '', 'Totaal',$this->formatGetal($totaalAantal,0)));
      $this->pdf->CellBorders=array();

      $this->xlsData[]=array();
      $this->xlsData[]=array("Printinformatie:",'',$USR,date('d-m-Y'),date("H:i"),$USR);

      if($this->beheerderRec['OrderLoggingOpNota']==1)
      {
        if($this->pdf->getY()<140)
          $this->pdf->SetY(140);
        else
          $this->pdf->ln(4);
        $this->pdf->MultiCell(200,4,$renteOpmerking);

        if($this->orderRecord['realMemo'] <> '')
          $this->pdf->MultiCell(200,4,$this->orderRecord['realMemo']);

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
       $this->pdf->SetWidths(array(40,60));

       $this->pdf->CellBorders=array('','U');
       $this->pdf->Row(array('Cliënt + Code',$this->orderRecordRegels['naam']." ".$this->orderRecordRegels['client']));
      $this->pdf->ln(2);

      $rekening=$this->orderRecordRegels['portefeuille'];
      foreach($this->orders[$this->orderRecord['id']]['orderregels'] as $orderregelId=>$orderregel)
      {
        $rekening=substr($orderregel['rekening'],0,-3);
        $rekening=$rekening." (".$orderregel['rekDepot'].")";
      }

      $this->pdf->Row(array('Depotnummer',$rekening));

      $this->xls->setColumn[] = array(0,1,10);
       $this->xls->setColumn[] = array(2,2,40);
       $this->xls->setColumn[] = array(3,3,15);
       $this->xls->setColumn[] = array(4,5,10);
       $this->xls->setColumn[] = array(6,7,15);



       $this->xlsData[] = array(array('Portefeuille','kopl'),'',array($rekening,'kopr'));//
       $this->xlsData[] = array(array('Client','kopl'),'',array($this->orderRecordRegels['naam'].' '.$this->orderRecordRegels['client'],'kopr'));

       for ($x=0;$x < count($this->xlsData);$x++)
       {
        $this->xls->mergeCells[] = array($x,0,$x,1);
        $this->xls->mergeCells[] = array($x,2,$x,3);
       }

      $this->pdf->ln(10);
      if($this->orderRecord['orderSoort']!='C')
      {
        $this->pdf->ln(10);
        $this->pdf->SetWidths(array(35, 155));
        $this->pdf->CellBorders = array(array('T', 'U', 'L', 'R'), array('T', 'L', 'U', 'R'));
        $this->pdf->SetAligns(array("C", "L"));
        $this->pdf->SetFont('arial', 'B', 8);

        $this->pdf->Row(array("Bijzonderheden m.b.t.\nafrekening / order:", $this->orders[$this->orderRecord['id']]['order']['realMemo']));
        $this->pdf->SetFont('arial', '', 8);
      }
      $this->pdf->line($this->pdf->marge, $this->pdf->getY() + 5, 193 + $this->pdf->marge, $this->pdf->getY() + 5);
      $this->pdf->ln(10);
      if($this->orderRecord['orderSoort']=='N')
        $aantalTxt='Bedrag';
      else
        $aantalTxt="Aantal/Nom.";

       $this->pdf->SetWidths(array(10,45,20,25,18,15,25,35));
       $this->pdf->SetAligns(array("R","L","R","L","R","R","L","L","L"));
       $this->pdf->CellBorders=array(array('T','U','L','R'),array('T','L','U'),array('T','L','U'),array('T','L','U'),array('T','L','U'),array('T','L','U'),array('T','L','U'),array('T','L','U','R'));
       $this->xlsData[]=array();
       $this->pdf->Row(array("Nr:","Fonds",$aantalTxt,"ISIN","Soort","Limiet","geldig t/m",'Bijz.'));
       $this->xlsData[]=array(array('Pos','header'),array('Kenmerk','header'),array('Fonds omschrijving','header'),array('ISIN-code','header'),
                              array('Valuta','header'),array('Aantal','header'),array('Transactiesoort','header'),array('Transactietype','header'),
                              array('Limietkoers','header'),array('tijdsLimiet','header'));

       $this->pdf->CellBorders=array();

      $this->pdf->CellBorders=array(array('T','U','L','R'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U','R'));
       foreach ($this->orders as $orderId=>$orderData)
       {
         /*
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
*/
   //     listarray($this->orders[$orderId]['order']);
//listarray($__ORDERvar['transactieSoort']);
         foreach($this->orders[$orderId]['orderregels'] as $orderregelId=>$orderregel)
         {
           $aantal = $this->formatAantal($orderregel["aantal"],0,true);
           if($orderregel["bedrag"] <> 0)
             $aantal = $this->formatAantal($orderregel["bedrag"],2,true);
           if($orderData['order']['koersLimiet']<>0)
             $limietKoers=$orderData['order']['koersLimiet'];
           else
             $limietKoers='';

           $limiet= $this->orders[$orderId]['order']['transactieType'];


           $row=array();
           $row['transactieType']=$__ORDERvar["transactieType"][$orderData['order']['transactieType']];
           $row['transactieSoort']=$orderData['order']['transactieSoort'];//$__ORDERvar["transactieSoort"][];

           if($row['transactieType']=='Limiet')
           {
             $row['transactieType'] = $limietKoers;
             $datumLimiet=date('d-m-Y',db2jul($orderData['order']['tijdsLimiet']));
           }
           else
           {
             $datumLimiet=$__ORDERvar['tijdsSoort'][$orderData['order']['tijdsSoort']];

           }

           if($this->orders[$orderId]['order']['realMemo']<>'')
             $bijz='*';
           else
             $bijz='';
           $this->pdf->Row(array($orderId, $orderData['order']['fondsOmschrijving'],$aantal,$orderData['order']['ISINCode'],
                             $__ORDERvar['transactieSoort'][$row['transactieSoort']],$row['transactieType'],$datumLimiet,$this->orders[$orderId]['order']['realMemo'] ));
           $this->xlsData[] = array($orderregel['positie'], $__appvar['bedrijf'] . $orderId, $orderData['order']['fondsOmschrijving'], $orderData['order']['ISINCode'],
               $this->orders[$orderId]['fonds']['Valuta'], $orderregel["aantal"], $row['transactieSoort'], $row['transactieType'],$limietKoers,$orderData['order']['tijdsLimiet']);

         }
       //  $laatsteOrderStatus=$orderData['order']['orderStatus'];
       }

      $this->pdf->SetWidths(array(190));
      $this->pdf->SetAligns(array("L"));

      $this->pdf->setY(240);
      $this->pdf->SetFont('arial','',7);
      $this->pdf->CellBorders=array();
      $this->pdf->Row(array('The information contained in this communication is confidential and may be legally privileged. It is intended solely for the use of the individual or entity to whom it is addressed and others authorized to receive it.
If you are not the intended recipient you are hereby notified that any disclosure, copying, distribution or taking any action with respect to the content of this information is strictly prohibited and may be unlawful;
you are kindly requested to inform the sender immediately and destroy the message and its attachments and -if any- delete all copies.
Box Consultants BV is neither liable for the proper and complete transmission of the information contained in this communication nor for any delay in its receipt.
Please note that the confidentially of this communication is not warranted.
The content of this message is not legally binding unless confirmed by letter of email, signed by one or two authorized representatives of the company'));
/*
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
     */
    }
  }
  
}

if($_GET['orderid'])
{
  $orderPrint=new ordersV2PDF_L72($_GET['orderid']);
  $orderPrint->createPrint();
  if($_GET['uitvoer']=='pdf') 
    $orderPrint->pdfUitvoer();
  if($_GET['uitvoer']=='xls')   
    $orderPrint->xlsUitvoer();
  //listarray($orderPrint);
}



?>