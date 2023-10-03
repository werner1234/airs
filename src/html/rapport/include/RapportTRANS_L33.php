<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/07 15:30:49 $
File Versie					: $Revision: 1.18 $

$Log: RapportTRANS_L33.php,v $
Revision 1.18  2019/08/07 15:30:49  rvv
*** empty log message ***

Revision 1.17  2018/01/10 16:26:03  rvv
*** empty log message ***

Revision 1.16  2018/01/06 18:10:41  rvv
*** empty log message ***

Revision 1.15  2015/08/30 11:44:35  rvv
*** empty log message ***

Revision 1.14  2014/04/09 16:17:47  rvv
*** empty log message ***

Revision 1.13  2013/10/16 15:35:04  rvv
*** empty log message ***

Revision 1.12  2013/10/12 15:54:06  rvv
*** empty log message ***

Revision 1.11  2013/05/04 15:59:49  rvv
*** empty log message ***

Revision 1.10  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.9  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.8  2012/02/26 15:17:43  rvv
*** empty log message ***

Revision 1.7  2011/06/29 16:54:20  rvv
*** empty log message ***

Revision 1.6  2011/04/09 14:35:27  rvv
*** empty log message ***

Revision 1.5  2011/04/03 08:35:46  rvv
*** empty log message ***

Revision 1.4  2011/03/18 15:02:38  rvv
*** empty log message ***

Revision 1.3  2011/03/17 15:28:24  rvv
*** empty log message ***

Revision 1.2  2011/03/13 18:36:37  rvv
*** empty log message ***

Revision 1.1  2011/02/13 17:50:29  rvv
*** empty log message ***

Revision 1.1  2011/02/06 14:36:59  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/rapportATTberekening.php");

class RapportTRANS_L33
{
	function RapportTRANS_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Transactieoverzicht";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
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
  
 function gemiddeldeTransactieValutaKoers($fonds)
  {
    $valutaKoers=$this->pdf->ValutaKoersBegin;
    if($fonds=='')
      return $this->pdf->ValutaKoersBegin;
      
     $query="SELECT Boekdatum,Debet,Credit,Bedrag,Omschrijving ,((Credit*Valutakoers)-(Debet*Valutakoers)) as BedragEur,Transactietype
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype NOT IN('V','L','A/S','V/S')";
     $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $totaalEur=0;
    $waardeRapportageKoers=0;
    while($data = $DB->nextRecord())
    { 
      if($data['Transactietype']=='B')
      {
        $tmp=fondsWaardeOpdatum($this->portefeuille,$fonds,$data['Boekdatum'],'EUR');
   			$bedrag = ($tmp['fondsEenheid'] * $tmp['totaalAantal']) * $tmp['beginwaardeLopendeJaar'] *  $tmp['beginwaardeValutaLopendeJaar'];
      }
      else
        $bedrag=abs($data['BedragEur']);
        
      $valutaKoers=getValutaKoers($this->pdf->rapportageValuta,$data['Boekdatum']);
      if($valutaKoers=='')
        $valutaKoers=$this->pdf->ValutaKoersBegin;
      //$waardeRapportageKoers+=($bedrag*$valutaKoers);
      $waardeRapportageKoers+=($bedrag/$valutaKoers);
      
      //echo "$fonds $bedrag*$valutaKoers=".($bedrag*$valutaKoers)."<br>\n";
      $totaalEur+=$bedrag;  
    }
    //$gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur;
    //echo "$fonds $gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur; <br>\n";
    $gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers;
   // echo "$fonds $gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers; <br>\n";

    if($gemiddeldeValutakoers <> 0)
      return $gemiddeldeValutakoers;
    else    
      return $valutaKoers;
  }
  
	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
		$this->pdf->AddPage();
		$this->pdf->templateVars['TRANSPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);

			  $transactietypenÓmschrijving= array('A'=>'Aankooptransacties',
	                                      'A/O'=>'Aankoop / openen',
	                                      'A/S'=>'Aankoop / sluiten',
	                                      'D'=>'Deponering',
	                                      'L'=>'Lichting',
	                                      'V'=>'Verkooptransacties',
	                                      'V/O'=>'Verkoop / openen',
	                                      'V/S'=>'Verkoop / sluiten',);



		$query="SELECT Rekeningmutaties.id,
Rekeningmutaties.Grootboekrekening,
Fondsen.Omschrijving,
Fondsen.Fondseenheid,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Transactietype,
Rekeningmutaties.Valuta,
Rekeningmutaties.Afschriftnummer,
Rekeningmutaties.Omschrijving AS rekeningOmschrijving,
Rekeningmutaties.Aantal AS Aantal,
Rekeningmutaties.Fonds,
Rekeningmutaties.Fondskoers,
Rekeningmutaties.Debet AS Debet,
Rekeningmutaties.Credit AS Credit,
Rekeningmutaties.Valutakoers,
1 $koersQuery AS Rapportagekoers
FROM Rekeningmutaties
JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE Rekeningen.Portefeuille =  '".$this->portefeuille."'
AND  Rekeningmutaties.Verwerkt = '1'
AND   Rekeningmutaties.Transactietype NOT IN ('B')
AND (Grootboekrekeningen.FondsAanVerkoop = '1' OR Rekeningmutaties.Grootboekrekening IN('KOBU','KOST','VALK','TOB'))
AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'
AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
ORDER BY Rekeningmutaties.Transactietype,Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();
		$kostenArray =array();

		while($mutaties = $DB->nextRecord())
		{
		  if($mutaties['Grootboekrekening']=='KOST' || $mutaties['Grootboekrekening']=='KOBU' || $mutaties['Grootboekrekening']== 'VALK' || $mutaties['Grootboekrekening']== 'TOB')
		  {
		    $kostenArray[$mutaties['Boekdatum']][$mutaties['Fonds']] += ($mutaties['Debet'] * $mutaties['Valutakoers']);
		  }
		  else
		  {
		    $mutaties['kosten']=$kostenArray[$mutaties['Boekdatum']][$mutaties['Fonds']];
		  	$buffer[] = $mutaties;
		  }
		}


		foreach ($buffer as $mutaties)
		{
			//if($mutaties[Transactietype] != "A/S")
			$mutaties['absAantal'] =  abs($mutaties[Aantal]);

			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;


			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],'EUR','',$mutaties['id']);
				$historischekostprijs = -1 * $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
			  $beginditjaar         = -1 * $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        $resultaat = ($mutaties['Credit']-$mutaties['Debet']) * $mutaties['Valutakoers'] - $beginditjaar;
        
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar          = -1 * $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $mutaties['Fondseenheid'];
          $resultaat = ($mutaties['Credit']-$mutaties['Debet']) - $beginditjaar;
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {

		      $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,'',$mutaties['id']);
		      $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		      //$beginditjaar         = $beginditjaar         / $historie['historischeRapportageValutakoers'];
          $beginditjaar = $beginditjaar/$this->gemiddeldeTransactieValutaKoers($mutaties['Fonds']);

          $resultaat = ($mutaties['Credit']-$mutaties['Debet']) * $mutaties['Valutakoers']/getValutaKoers($this->pdf->rapportageValuta,$mutaties['Boekdatum']) - $beginditjaar;

    
   if($mutaties['Fonds']=='Tullow Oil')
   {
    //echo $mutaties['Boekdatum']." $beginditjaar <br>\n";
    
   // echo ($beginditjaar/$this->gemiddeldeTransactieValutaKoers($mutaties['Fonds']) )."=".$beginditjaar/$this->gemiddeldeTransactieValutaKoers($mutaties['Fonds']);
//    listarray($historie);
   }
   
//$data['historischeWaardeRapVal'] = $historischekostprijs / $this->gemiddeldeTransactieKoersInValuta($mutaties,$mutaties['Boekdatum']);
//$data['verkoopWaardeRapVal']=($mutaties['Credit']-$mutaties['Debet']) * $mutaties['Valutakoers'] / getValutaKoers($this->pdf->rapportageValuta,$mutaties['Boekdatum']);
//$resultaat = $data['verkoopWaardeRapVal'] - $data['historischeWaardeRapVal'];//$beginditjaar;

        }


				

          
			
			}
			else
			{
				$resultaat = "";
			}

      if($mutaties['Transactietype'] <> $lastTransactietype )
      {

         if(isset($lastTransactietype))
         {
           	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           	$this->pdf->CellBorders = array('','','','','','','','','T','T','T');
           	$this->pdf->row(array('','','','','','','','',$this->formatGetal($totalen[$lastTransactietype]['bedragEur'],2),$this->formatGetal($totalen[$lastTransactietype]['resultaat'],2),$this->formatGetal($totalen[$lastTransactietype]['kosten'],2)));

		        unset($this->pdf->CellBorders);
         }
         $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
         $this->pdf->MultiCell(280,4,vertaalTekst($transactietypenÓmschrijving[$mutaties['Transactietype']],$this->pdf->rapport_taal) , 0, "L");
         $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }


    if ($this->pdf->rapportageValuta != "EUR" )
    {
      if($mutaties['Valuta']==$this->pdf->rapportageValuta)
        $koers=$mutaties['Valutakoers'];
      else
	      $koers = getValutaKoers($this->pdf->rapportageValuta,$mutaties['Boekdatum']);
      
    }
	  else
	    $koers = 1;

      $bedragEur=abs($mutaties['Credit']-$mutaties['Debet']) * $mutaties['Valutakoers'] /$koers;
      
    //  if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] != $this->pdf->rapportageValuta)
    //    $resultaat=$resultaat/$koers;
        
      $mutaties['kosten']=$mutaties['kosten']/$koers;
			$this->pdf->row(array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
			        rclip($mutaties['Omschrijving'],35),
            				$mutaties['Valuta'],
				            $this->formatGetal($mutaties['Aantal'],0),
                    $this->formatGetal($mutaties['Fondskoers'],2),
				            $this->formatGetal($mutaties['Valutakoers'],4),
                    $this->formatGetal(abs($mutaties['Credit']-$mutaties['Debet']),2),
                    '',
										$this->formatGetal($bedragEur,2),
										$this->formatGetal($resultaat,2),
										$this->formatGetal($mutaties['kosten'],2)
										));
      $totalen[$mutaties['Transactietype']]['bedragEur']+=$bedragEur;
      $totalen[$mutaties['Transactietype']]['resultaat']+=$resultaat;
      $totalen[$mutaties['Transactietype']]['kosten']+=$mutaties['kosten'];

			$lastTransactietype=$mutaties['Transactietype'];

			}
			if(isset($lastTransactietype))
      {
        	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        	$this->pdf->CellBorders = array('','','','','','','','','T','T','T');
         	$this->pdf->row(array('','','','','','','','',$this->formatGetal($totalen[$lastTransactietype]['bedragEur'],2),$this->formatGetal($totalen[$lastTransactietype]['resultaat'],2),$this->formatGetal($totalen[$lastTransactietype]['kosten'],2)));
	      	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      unset($this->pdf->CellBorders);
      }
      $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
	}
}
?>