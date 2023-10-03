<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/11/19 14:27:19 $
 		File Versie					: $Revision: 1.12 $

 		$Log: Transactieoverzicht.php,v $
 		Revision 1.12  2017/11/19 14:27:19  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/10/26 16:14:22  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/16 15:06:42  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/07/20 16:11:18  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/12/06 18:12:47  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/12/03 17:29:15  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/05/17 16:35:21  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/04/16 15:50:14  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/05/01 15:52:20  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/04/20 16:34:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/03/31 12:34:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/03/27 18:43:46  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/02/10 10:05:24  rvv
 		*** empty log message ***
 		
*/
include_once("rapportRekenClass.php");

class Transactieoverzicht
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Transactieoverzicht( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData[datumTm];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Client ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport($nieuweTransacties='')
	{
		global $__appvar;
    $db=new DB();
    if(!is_array($nieuweTransacties))
    {
  		if($this->selectData['datumTm'])
  		{
  		$einddatum = jul2sql($this->selectData['datumTm']);
  		$jaar = date("Y",$this->selectData['datumTm']);
	  	}
	  	else
	  	{
	  	$einddatum = date("Y-m-d");
	  	$this->selectData['datumTm']=$einddatum;
	  	$jaar = date("Y");
	  	}

		  $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
      $records = $selectie->getRecords();
      $portefeuilles = $selectie->getSelectie();
      $portefeuilleList=array_keys($portefeuilles);
		  $extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";

		  if($records <= 0)		{
			  echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			  if($this->progressbar)
			    $this->progressbar->hide();
			  exit;
		  }
 
    
      foreach($portefeuilles as $portefeuille)
      {
        $vermogensbeheerders[$portefeuille['Vermogensbeheerder']]=$portefeuille['Vermogensbeheerder'];
      }
      
      $nieuweTransacties=array();
      
    
      $query="SELECT max(check_module_ORDERNOTAS) as check_module_ORDERNOTAS FROM Vermogensbeheerders WHERE Vermogensbeheerder IN('".implode("','",$vermogensbeheerders)."')";
      $db->SQL($query);
      $verm=$db->lookupRecord();
      if($verm['check_module_ORDERNOTAS']==1)     
      {
         if(GetModuleAccess("ORDER")==2)
         {
        $query="SELECT
OrdersV2.id as orderid,
OrdersV2.id as Afschriftnummer,
OrdersV2.fonds as Fonds,
OrdersV2.ISINCode,
OrdersV2.transactieSoort as Transactietype,
OrdersV2.OrderStatus as laatsteStatus,
OrdersV2.fondsOmschrijving as Omschrijving,
OrderRegelsV2.portefeuille,
Rekeningen.valuta,
OrderRegelsV2.aantal as Aantal,
OrdersV2.notaValutakoers as Valutakoers,
OrderRegelsV2.opgelopenRente,
OrderRegelsV2.kosten,
OrderRegelsV2.brutoBedrag,
OrderRegelsV2.nettoBedrag,
OrderRegelsV2.brokerkosten,
(SELECT OrderUitvoeringV2.uitvoeringsDatum FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Boekdatum,
(SELECT OrderUitvoeringV2.nettokoers FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as Fondskoers
FROM
OrdersV2
LEFT JOIN Fondsen on OrdersV2.fonds=Fondsen.Fonds
INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
LEFT JOIN Rekeningen on OrderRegelsV2.Rekening=Rekeningen.Rekening
HAVING
Boekdatum > '".date('Y-m-d',$this->selectData['datumVan'])."' AND
Boekdatum <='".date('Y-m-d',$this->selectData['datumTm'])."'";
         }
        else
        {
          $query="SELECT
Orders.orderid,
Orders.orderid as Afschriftnummer,
Orders.fonds as Fonds,
Fondsen.ISINCode,
Orders.transactieSoort as Transactietype,
Orders.laatsteStatus,
Orders.fondsOmschrijving as Omschrijving,
OrderRegels.portefeuille,
OrderRegels.valuta,
OrderRegels.aantal as Aantal,
OrderRegels.valutakoers as Valutakoers,
OrderRegels.fondsKoers as FondskoersBruto,
OrderRegels.opgelopenRente,
OrderRegels.kosten,
OrderRegels.brutoBedrag,
OrderRegels.brutoBedragValuta,
OrderRegels.nettoBedrag,
OrderRegels.brokerkosten,
(SELECT OrderUitvoering.uitvoeringsDatum FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Boekdatum,
(SELECT OrderUitvoering.nettokoers FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid limit 1) as Fondskoers
FROM
Orders
LEFT JOIN Fondsen on Orders.fonds=Fondsen.Fonds
INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid
HAVING
Boekdatum > '".date('Y-m-d',$this->selectData['datumVan'])."' AND
Boekdatum <='".date('Y-m-d',$this->selectData['datumTm'])."'
";
        }


       $db->SQL($query);
       $db->Query();
       while($data=$db->nextRecord())
       {    
         if($data['Boekdatum']=='')
           $data['Boekdatum']='0000-00-00';
         $nieuweTransacties[]=$data;
       }
      
      }
      else
      {
        $query="SELECT Rekeningmutaties.*, Rekeningen.consolidatie
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
WHERE Rekeningmutaties.Boekdatum >'".date('Y-m-d',$this->selectData['datumVan'])."' AND
Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$this->selectData['datumTm'])."' $extraquery";
       $db->SQL($query);
       $db->Query();
       while($data=$db->nextRecord())
       {    
        $nieuweTransacties[]=$data;
       }
     }
   }

    foreach ($nieuweTransacties as $id=>$transactie)
    {
      if($transactie['Fonds'] <> '')
      {
          if(isset($transactie['consolidatie']))
            $consolidatie="AND Rekeningen.Consolidatie='".$transactie['consolidatie']."'";
          else
            $consolidatie='';
          
          if($transactie['Rekening'] <> '')
          {
            $portWhere = "Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille WHERE Rekeningen.Rekening='" . $transactie['Rekening'] . "' $consolidatie";
          }
          else
          {
            $portWhere = "WHERE Portefeuilles.Portefeuille='" . $transactie['portefeuille'] . "'";
          }
          $query="SELECT Portefeuilles.Client, Portefeuilles.Accountmanager,Portefeuilles.Portefeuille,Portefeuilles.clientvermogensbeheerder  
          FROM Portefeuilles $portWhere";
          $db->SQL($query);
          $portefeuille=$db->lookupRecord();
          $transactie['Client']=$portefeuille['Client'];
          $transactie['Accountmanager']=$portefeuille['Accountmanager'];
          $transactie['Portefeuille']=$portefeuille['Portefeuille'];
          $transactie['clientvermogensbeheerder']=$portefeuille['clientvermogensbeheerder'];
        
          $fonds=array();
          if($transactie['Grootboekrekening']=='FONDS')
          {
            $query="SELECT Omschrijving,ISINCode,Valuta,Fonds,Fondseenheid FROM Fondsen WHERE Fonds='".$transactie['Fonds']."'";
            $db->SQL($query);
            $fonds=$db->lookupRecord();
            $fonds['Transactietype']=$transactie['Transactietype'];
            $fonds['Fondskoers']=$transactie['Fondskoers'];
            $fonds['Valutakoers']=$transactie['Valutakoers'];
          }
      
          $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['transacties'] = $transactie;//ABS($transactie['Debet'])+ABS($transactie['Credit']);
          if(isset($fonds['Omschrijving']))
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['fonds']=$fonds;
            

          if(isset($transactie['orderid']))
          {
            $fonds['Transactietype']=$transactie['Transactietype'];
            $fonds['Fondskoers']=$transactie['Fondskoers'];
            $fonds['Valutakoers']=$transactie['Valutakoers'];
            $fonds['Omschrijving']=$transactie['Omschrijving'];
            $fonds['ISINCode']=$transactie['ISINCode'];
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['fonds']=$fonds;
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['KOST']=-1*$transactie['provisie'];
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['KOBU']=-1*$transactie['brokerkosten'];
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['KNBA']=-1*$transactie['kosten'];
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['RENME']=-1*$transactie['opgelopenRente'];
          }
          else                       
            $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']][$transactie['Grootboekrekening']]+=$transactie['Valutakoers']*(abs($transactie['Credit'])-abs($transactie['Debet']));
          $sortBuffer[$transactie['Fonds']][$transactie['Portefeuille']][$transactie['Boekdatum']]['aantal']+=$transactie['Aantal'];
      } 
    }
//listarray($sortBuffer);
      $this->pdf->AddPage('L');
      $this->pdf->SetFont("Times","",7);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetWidths(array(8,18,20,40,14,14,16,16,14,14,14,14,16,11,14,13,13,15));
      $this->pdf->SetAligns(array('L','L','L','L','R','R','R','R','R','R','R','R','R','L','R','R','R','R'));

      $header=array('Type','Portf','Client','ISIN','Naam','Valuta','Aantal','Netto','Bruto in Eur','Bruto','Provisie','Nota kst','Kosten','Rente','Bedrag','ResPortf','Tr Dat','Sett Dat','Nota','W Koers');
      $this->pdf->excelData[]=$header;
      $header=array('Type','Portf','ISIN','Naam','Aantal','Netto','Bruto in Eur','Bruto','Provisie','Nota kst','Kosten','Rente','Bedrag','ResPortf','Tr Dat','Sett Dat','Nota','W Koers');
      $this->pdf->Row($header);
    
      foreach($sortBuffer as $fonds=>$portefeuilleData)
      {
        foreach($portefeuilleData as $portefeuille=>$boekdatumData)
        {
          foreach($boekdatumData as $boekdatum=>$transactieData)
          { 
            if($transactieData['fonds']['Omschrijving'])
            {
              if(isset($lastIsin) && $transactieData['fonds']['ISINCode'] <> $lastIsin)
              {
                $this->subTotaal($totalen);
                $totalen=array();
              }
              if($transactieData['transacties']['brutoBedragValuta'])
                $waardeInValuta=$transactieData['transacties']['brutoBedragValuta'];
              else
                $waardeInValuta=$transactieData['fonds']['Fondskoers']*$transactieData['aantal']*$transactieData['fonds']['Fondseenheid']*-1;
              
              if($transactieData['transacties']['brutoBedrag'])
                $waardeInEur=$transactieData['transacties']['brutoBedrag'];
              else                
                $waardeInEur=$waardeInValuta*$transactieData['fonds']['Valutakoers'];
              $bedrag=$waardeInEur+$transactieData['KOST']+$transactieData['KOBU']+$transactieData['KNBA'];
              //$clientvermogensbeheerder=$transactieData['transacties']['clientvermogensbeheerder'];
              //if($clientvermogensbeheerder=='')
              //  $clientvermogensbeheerder=$portefeuille;

              $this->pdf->excelData[]=array($transactieData['fonds']['Transactietype'],
                $portefeuille,
                       $transactieData['transacties']['Client'],
                       $transactieData['fonds']['ISINCode'],
                       $transactieData['fonds']['Omschrijving'],
                       $transactieData['transacties']['Valuta'],
                       round($transactieData['aantal']),
                       round($transactieData['fonds']['Fondskoers'],2),
                       round($waardeInEur,2), 
                       round($waardeInValuta,2),
                       round($transactieData['KOST'],2),
                       round($transactieData['KOBU'],2),
                       round($transactieData['KNBA'],2),
                       round($transactieData['RENME']+$transactieData['RENOB'],2),
                       round($bedrag,2),
                       '',
                       date('d-m-Y',db2jul($boekdatum)),
                       '',
                       $transactieData['transacties']['Afschriftnummer'],
                       round(1/$transactieData['fonds']['Valutakoers'],4)
                       );            
                
                
              $regel=array($transactieData['fonds']['Transactietype'],
                $portefeuille,
                       $transactieData['fonds']['ISINCode'],
                       $transactieData['fonds']['Omschrijving'],
                       $this->pdf->formatGetal($transactieData['aantal']),
                       $this->pdf->formatGetal($transactieData['fonds']['Fondskoers'],2),
                       $this->pdf->formatGetal($waardeInEur,2), 
                       $this->pdf->formatGetal($waardeInValuta,2),
                       $this->pdf->formatGetal($transactieData['KOST'],2),
                       $this->pdf->formatGetal($transactieData['KOBU'],2),
                       $this->pdf->formatGetal($transactieData['KNBA'],2),
                       $this->pdf->formatGetal($transactieData['RENME']+$transactieData['RENOB'],2),
                       $this->pdf->formatGetal($bedrag,2),
                       '',
                       date('d-m-Y',db2jul($boekdatum)),
                       '',
                       $transactieData['transacties']['Afschriftnummer'],
                       $this->pdf->formatGetal(1/$transactieData['fonds']['Valutakoers'],4)
                       );
                       
               $totalen['aantal']+=$transactieData['aantal'];   
               $totalen['waardeInEur']+=$waardeInEur;
               $totalen['waardeInValuta']+=$waardeInValuta;
               $totalen['KOST']+=$transactieData['KOST'];
               $totalen['KOBU']+=$transactieData['KOBU'];
               $totalen['KNBA']+=$transactieData['KNBA'];
               $totalen['RENME']+=$transactieData['RENME'];
               $totalen['bedrag']+=$bedrag;
               
               $gtotalen['KOST']+=$transactieData['KOST'];
               $gtotalen['KOBU']+=$transactieData['KOBU'];               
               $lastIsin=$transactieData['fonds']['ISINCode'];
               $this->pdf->Row($regel);
             }
          }
        }
      }
      $this->subTotaal($totalen);
      
      $this->subTotaal($gtotalen,true);
          
   
	 		if($this->progressbar)
			$this->progressbar->hide();
	}
  
  function subTotaal($totalen,$kort=false)
  {
    $this->pdf->CellBorders=array('','','','','TS','TS','TS','TS','TS','TS','TS','TS','TS');
    if($kort==true)
    {
      $this->pdf->CellBorders=array('','','','','','','','','TS','TS');
      $this->pdf->Row(array('','','','','','','','',
                  $this->pdf->formatGetal($totalen['KOST'],2),
                  $this->pdf->formatGetal($totalen['KOBU'],2)
                  ));
    }              
    else              
      $this->pdf->Row(array('','','','',
                  $this->pdf->formatGetal($totalen['aantal']),'',
                  $this->pdf->formatGetal($totalen['waardeInEur'],2),
                  $this->pdf->formatGetal($totalen['waardeInValuta'],2),
                  $this->pdf->formatGetal($totalen['KOST'],2),
                  $this->pdf->formatGetal($totalen['KOBU'],2),
                  $this->pdf->formatGetal($totalen['KNBA'],2),
                  $this->pdf->formatGetal($totalen['RENME'],2),
                  $this->pdf->formatGetal($totalen['bedrag'],2)
                  ));
    unset($this->pdf->CellBorders);
    $this->pdf->Ln();
  }


}
?>