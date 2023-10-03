<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L125
{
	function RapportHSE_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->aandeel=1;
	}
  
  function formatGetal($waarde, $dec, $teken='')
  {
    return formatGetal_L125($waarde, $dec, $teken);
  }


	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($this->aandeel <> 1)
	    $waarde=round($waarde,0);
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

  
 function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }

    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

	function writeRapport()
	{
		global $__appvar;

		$DB = new DB();


		$this->pdf->AddPage();
  
		$headerTxt='Waarden per '.date("j",db2jul($this->rapportageDatum))." ".	vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".	date("Y",db2jul($this->rapportageDatum));
   // subHeader_L125($this->pdf,28,array(100-4,280),array('','Waarden per '.date("j",db2jul($this->rapportageDatum))." ".
		//	vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".
		//	date("Y",db2jul($this->rapportageDatum))),$this->pdf->textGrijs);
    
    $widthsh=array(80,30,30,30,30,30);
    $alignsh=array('L','R','R','R','R','R');
    $style=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+2),array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+2));
    subHeader_L125($this->pdf,28,$widthsh,array($headerTxt,'Aantal','Koers','Waarde','Weging','Rendement'),null,$alignsh,$style);
    $widths=array(20-$this->pdf->marge,80,30,30,30,30,30);
    $aligns=array('L','L','R','R','R','R','R');
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;
		
						$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel.") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
        round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.beleggingscategorieOmschrijving,
				  TijdelijkeRapportage.valuta,
				  TijdelijkeRapportage.type,
				  TijdelijkeRapportage.rekening,
				   TijdelijkeRapportage.portefeuille,Rekeningen.IBANnr ".
				" FROM TijdelijkeRapportage LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening=Rekeningen.Rekening AND Rekeningen.Consolidatie=0
           WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

		  $regeldata=array();
    $this->pdf->setWidths($widths);
    $this->pdf->setAligns($aligns);
			while($subdata = $DB2->NextRecord())
      {
 

        $omschrijving=$subdata['fondsOmschrijving'];
        if($subdata['type']=='fondsen')
				{
       
          $dividend=$this->getDividend($subdata['fonds']);
          $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['corrected']+=$dividend['corrected'];
          $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['actueleFonds']+=$subdata['actueleFonds'];
          $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['totaalAantal']+=$subdata['totaalAantal'];
				}
				elseif($subdata['type']=='rente')
				{
          $omschrijving=$subdata['fondsOmschrijving'];
        }
        elseif($subdata['type']=='rekening')
				{
          $omschrijving=substr($subdata['rekening'],0,-3).($subdata['IBANnr']<>''?"\n".$subdata['IBANnr']:'');
				}
  
        $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['type']=$subdata['type'];
        $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['actuelePortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
        $regeldata[$subdata['beleggingscategorieOmschrijving']][$omschrijving]['beginPortefeuilleWaardeEuro']+=$subdata['beginPortefeuilleWaardeEuro'];
      }
    //  listarray($regeldata);
      $this->pdf->ln(10);
      foreach($regeldata as $categorieOmschrijving=>$categorieData)
			{
        $this->pdf->ln(3);
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+1);
        $this->pdf->setTextColor($this->pdf->textGroen[0],$this->pdf->textGroen[1],$this->pdf->textGroen[2]);
        $this->pdf->Row(array('',$categorieOmschrijving));
        $this->pdf->setTextColor(0);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->ln(3);
        $subtotaal=array();
				foreach ($categorieData as $element=>$data)
				{
					if($data['type']!='rekening')
          {
            $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $data['corrected']) / ($data['beginPortefeuilleWaardeEuro'] / 100));
            if ($data['beginPortefeuilleWaardeEuro'] < 0)
            {
              $procentResultaat = -1 * $procentResultaat;
            }
            $rendementTxt = $this->formatGetal($procentResultaat, 1,'%') ;
            $aantalTxt=$this->formatAantal($data['totaalAantal'],0);
            $koersTxt=$this->formatGetal($data['actueleFonds'],0);
          }
          else
          {
            $rendementTxt = '';
            $aantalTxt='';
            $koersTxt='';
          }
          $weging=$data['beginPortefeuilleWaardeEuro']/$totaalWaarde*100;
          $this->pdf->Row(array('',$element,
                            $aantalTxt,
                            $koersTxt,
														$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0,'€'),
														$this->formatGetal($weging,1,'%'),$rendementTxt));
          $subtotaal['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
          $subtotaal['weging']+=$weging;
				}
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+1);
        $this->pdf->ln(3);
        $this->pdf->Row(array('','','','',$this->formatGetal($subtotaal['actuelePortefeuilleWaardeEuro'],0,'€'),$this->formatGetal($subtotaal['weging'],1,'%')));
       
			}
    $this->pdf->ln(3);
    $this->pdf->Line(20,$this->pdf->GetY() ,$this->pdf->w-20,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
    $this->pdf->ln(3);
    $this->pdf->CellFontStyle=array('',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2),'','',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4),array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2));
    $this->pdf->CellFontColor=array('','','','',array('r'=>$this->pdf->textGroen[0],'g'=>$this->pdf->textGroen[1],'b'=>$this->pdf->textGroen[2]),array('r'=>0,'g'=>0,'b'=>0));
    $this->pdf->Row(array('','Totale portefeuillewaarde','','',$this->formatGetal($totaalWaarde,0,'€'),$this->formatGetal(100,0,'%')));
    unset($this->pdf->CellFontStyle);
    unset($this->pdf->CellFontColor);
    
	}
  
 
}
?>
