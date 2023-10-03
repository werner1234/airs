<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/12/06 07:44:05 $
File Versie					: $Revision: 1.1 $

$Log: RapportORDERS.php,v $
Revision 1.1  2015/12/06 07:44:05  rvv
*** empty log message ***

Revision 1.3  2015/12/05 14:37:53  rvv
*** empty log message ***

Revision 1.2  2015/09/26 15:57:19  rvv
*** empty log message ***

Revision 1.1  2015/09/23 16:10:57  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportORDERS
{

	function RapportORDERS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PORTAAL";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
  	$this->pdf->rapport_titel = "";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

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
	   return number_format($waarde,$dec,",",".");
	}
	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$DB = new DB();
		$this->pdf->AddPage('P');
    $this->orderVersie=GetModuleAccess("ORDER");
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
    $this->pdf->rapport_fontsize+=4;
    $this->pdf->rowHeight+=2;
      
      $this->pdf->widthA = array(1,90,35,35,35);
      $this->pdf->alignA = array('L','L','R','R','R','R','R');
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetY(30);
      $this->pdf->Ln();
      $this->pdf->row(array('',vertaalTekst("Fonds",$this->pdf->rapport_taal)."",
                                      vertaalTekst("Aantal",$this->pdf->rapport_taal),
                                      vertaalTekst("Koers",$this->pdf->rapport_taal),
                                      vertaalTekst("Waarde EUR",$this->pdf->rapport_taal)));
                                    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $sumWidth = array_sum($this->pdf->widthA);
      $this->pdf->Line($this->pdf->marge+$this->pdf->widthA[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());


			$query = "SELECT
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.type
FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' And
 TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatum."'  
".$__appvar['TijdelijkeRapportageMaakUniek']."
ORDER BY  TijdelijkeRapportage.type, TijdelijkeRapportage.beleggingscategorieVolgorde, 
TijdelijkeRapportage.beleggingscategorie,  TijdelijkeRapportage.fondsOmschrijving";
    $DB=new DB();
    
    $DB->SQL($query);
    $DB->Query();
    $lastCat='';
    $orderFondsen=array();
    while($data=$DB->nextRecord())
    {
 
      if($lastCat!=$data['beleggingscategorie'])
      {
        if($lastCat<>'')
          $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('',$data['beleggingscategorieOmschrijving']));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      $this->pdf->row(array('',$data['fondsOmschrijving'], 
                               $this->formatAantal($data['totaalAantal'],0,true), 
                               $this->formatGetal($data['actueleFonds'],2),
                               $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2)));
      
      
        $this->zoekOrderRegels($data['fonds']);
        $orderFondsen[]=$data['fonds'];
        $lastCat=$data['beleggingscategorie'];
     }  
      
         
   $this->pdf->ln();
   

   $this->zoekOrderRegels($orderFondsen,true);


	}
  
  function zoekOrderRegels($fonds,$nietAanwezig=false)
  {
    global $__ORDERvar;
    $DB2=new DB();
    
    if($nietAanwezig==true)
      $fondsenWhere='NOT';
    else
      $fondsenWhere='';  
    if(is_array($fonds))
    {

      $fondsenWhere.=" IN('".implode("','",$fonds)."')";
    }  
    else
    {
      $fondsenWhere.=" IN('".$fonds."')";
    }

   
//                (SELECT Valuta FROM Fondsen WHERE Fonds=OrdersV2.fonds ) as FondsValuta,
    if($this->orderVersie==2)
    {  
      $query="SELECT OrdersV2.fonds,OrdersV2.transactieSoort,OrdersV2.fonds,OrderRegelsV2.portefeuille,OrderRegelsV2.aantal,OrdersV2.transactieSoort,OrdersV2.fondsOmschrijving,
                (SELECT max(uitvoeringsDatum) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid = OrdersV2.id) as uitvoeringsDatum,
                (SELECT sum(uitvoeringsAantal) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid = OrdersV2.id) as uitvoeringsAantal,
                (SELECT Fondseenheid FROM Fondsen WHERE Fonds=OrdersV2.fonds ) as Fondseenheid, 
                (SELECT Koers FROM Fondskoersen WHERE Fonds=OrdersV2.fonds ORDER BY Datum desc limit 1) as LaatsteFondsKoers, 
                (SELECT Koers FROM Valutakoersen JOIN Fondsen ON Fondsen.Valuta=Valutakoersen.Valuta WHERE Fondsen.Fonds=OrdersV2.fonds ORDER BY Datum desc limit 1) as LaatsteValutaKoers
                 FROM OrderRegelsV2 INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
              WHERE OrderRegelsV2.portefeuille='".$this->portefeuille."' AND OrderRegelsV2.orderregelStatus < 4 AND OrdersV2.fonds ".$fondsenWhere."";
    }
    else
    {
      $query="SELECT Orders.fonds,Orders.transactieSoort,OrderRegels.portefeuille,OrderRegels.aantal,Orders.transactieSoort,Orders.fondsOmschrijving, 
(SELECT max(uitvoeringsDatum) FROM OrderUitvoering WHERE OrderUitvoering.orderid = Orders.orderid) as uitvoeringsDatum, 
(SELECT sum(uitvoeringsAantal) FROM OrderUitvoering WHERE OrderUitvoering.orderid = Orders.orderid) as uitvoeringsAantal, 
(SELECT Fondseenheid FROM Fondsen WHERE Fonds=Orders.fonds ) as Fondseenheid, 
(SELECT Koers FROM Fondskoersen WHERE Fonds=Orders.fonds ORDER BY Datum desc limit 1) as LaatsteFondsKoers, 
(SELECT Koers FROM Valutakoersen JOIN Fondsen ON Fondsen.Valuta=Valutakoersen.Valuta WHERE Fondsen.Fonds=Orders.fonds ORDER BY Datum desc limit 1) as LaatsteValutaKoers 
FROM OrderRegels 
INNER JOIN Orders ON OrderRegels.orderid = Orders.orderid WHERE OrderRegels.portefeuille='".$this->portefeuille."' AND OrderRegels.status < 4 AND Orders.fonds ".$fondsenWhere."";
    }           
      $DB2->SQL($query);
      $DB2->Query();
      if($DB2->records()>0 && $nietAanwezig==true)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('','Overige orders'));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      
      while($order=$DB2->nextRecord())
      { 
        if($order['uitvoeringsAantal'] <> 0)
        {
          $orderAantal=$order['uitvoeringsAantal'];
          $uitgevoerd='uitgevoerd op '.date('d-m',db2jul($order['uitvoeringsDatum']));
        }
        else
        {
          $orderAantal=$order['aantal'];  
          $uitgevoerd='geschatte waarde';
        }

        $orderWaarde=$orderAantal*$order['Fondseenheid']*$order['LaatsteFondsKoers']*$order['LaatsteValutaKoers'];
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-3);
        $this->pdf->row(array('',$__ORDERvar["transactieSoort"][$order['transactieSoort']].' '.$order['fondsOmschrijving'], 
                               $this->formatAantal($order['aantal'],0,true), 
                               $uitgevoerd,
                               $this->formatGetal($orderWaarde,2)));   
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       }   
  }
  
}
?>