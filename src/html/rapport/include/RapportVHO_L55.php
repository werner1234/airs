<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.9 $

$Log: RapportVHO_L55.php,v $
Revision 1.9  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.8  2018/02/24 18:33:46  rvv
*** empty log message ***

Revision 1.7  2018/02/18 14:58:36  rvv
*** empty log message ***

Revision 1.6  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.5  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.4  2014/10/11 16:24:58  rvv
*** empty log message ***

Revision 1.3  2014/09/13 14:38:35  rvv
*** empty log message ***

Revision 1.2  2014/08/30 16:31:49  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportVHO_L55
{
	function RapportVHO_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		if($this->pdf->rapport_VHO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VHO_titel;
		else
			$this->pdf->rapport_titel = "Kostprijsoverzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->hseTotalen=array();
   // 	$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
	//	  $this->cashfow->genereerTransacties();
	//	  $this->cashfow->genereerRows();
      $this->db = new DB();
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
      return '';
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
    if($waarde==0)
      return '';
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
    if($waarde==0)
      return '';
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

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{
		//$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}

		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst')
		{
	    $y = $this->pdf->getY();
      $this->pdf->setY($y);
  	  $this->pdf->Cell($writerow,4,$data, 0,0, "L");
      //	$this->pdf->ln();
  	}
		else
		{
		  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{ 
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//	$this->pdf->ln();
		}
    
   // if($this->pdf->GetY()>45)
  //    $this->pdf->ln(-4);
  //  else
  //  {
      $this->pdf->ln(0);
      
    //echo $this->pdf->GetY();  
    //listarray($data);
  //  }
		//$this->pdf->setY($y);
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
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND ".
//     " Rekeningmutaties.Boekdatum >= '".  $this->rapportageDatumVanaf."' AND ".
			" Rekeningmutaties.Boekdatum <= '".  $this->rapportageDatum."' AND
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

	function printTotaal($title, $data ,$gtotaal=false )
	{
		$hoogte = 20;

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->ln();

		if($gtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";
      
      $data['procentResultaat']=($data['resultaatFonds']+$data['resultaatValuta'])/$data['historischeWaardeTotaalValuta']*100;

			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      if($gtotaal == true)
		  	$this->printCol(0,$title,"tekst");
      if($data['historischeWaardeTotaalValuta'] <>0)
				$this->printCol(5,$this->formatGetal($data['historischeWaardeTotaalValuta'],$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalactueel
			if($data['actuelePortefeuilleWaardeEuro'] <>0)
				$this->printCol(9,$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalactueel
			if($data['resultaatFonds'] <>0)
				$this->printCol(11,$this->formatGetal($data['resultaatFonds'],$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalpercentage
			if($data['resultaatValuta'] <>0)
				$this->printCol(12,$this->formatGetal($data['resultaatValuta'],$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalpercentage
		if($data['totaalDividend'] <>0)
			$this->printCol(13,$this->formatGetal($data['totaalDividend'],$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalpercentage


			if($data['procentResultaat'] <>0)
				$this->printCol(14,$this->formatGetal($data['procentResultaat'],1),$grandtotaal);//$totaalpercentage




		$this->pdf->ln();
    $this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		return $totaalB;
	}

	function printKop($title, $type="default")
	{

		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bu';
        $this->pdf->SetTextColor(133,140,140);
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize+2;
				$fonttype = 'bi';
        $this->pdf->SetTextColor(140,178,209);
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
  /*
    $this->pdf->SetTextColor($this->categorieKleuren[$this->categoriePerOmgeschijving[$title]]['R']['value'],
                             $this->categorieKleuren[$this->categoriePerOmgeschijving[$title]]['G']['value'],
                             $this->categorieKleuren[$this->categoriePerOmgeschijving[$title]]['B']['value']);
  */         
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);

	}

	function writeRapport($noPdf=false)
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    
    	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];

    
    $q="SELECT beleggingscategorie,omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		while($cat=$DB->nextRecord())
      $this->categoriePerOmgeschijving[$cat['omschrijving']]=$cat['beleggingscategorie'];
      
 
    if($noPdf==false)
    {
    	$fondsresultwidth = 5;
	    $omschrijvingExtra = 9;
		  //$this->pdf->widthA = array(65,35,25,15,25,25,0,0,20,25,20,20,0,0);
		  //$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
      
		$this->pdf->widthB = array(1,56,18,15,22,22,2,15,22,22,2,20,20,20,25);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->widthA = array(60   ,18,15,22,22,2,15,25,22,2,20,20,20,25);
		$this->pdf->alignA = array('L',    'R','R','R','R','R','R','R','R','R','R','R','R','R');
      
      $this->pdf->SetFillColor(230,230,230);

      $this->pdf->AddPage();
      $this->pdf->templateVars['VHOPaginas']=$this->pdf->page;
    }
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];
    
    $query = "SELECT TijdelijkeRapportage.hoofdcategorie,
    TijdelijkeRapportage.hoofdcategorieOmschrijving,
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage
    WHERE 
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'  "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY  TijdelijkeRapportage.hoofdcategorie ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc";
		$DB->SQL($query);
		$DB->Query();
    while($data=$DB->nextRecord())
    {
      $hoofdcategorienTotaal[$data['hoofdcategorie']]=$data['actuelePortefeuilleWaardeEuro'];
    } 
    
		$actueleWaardePortefeuille = 0;

			$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.RegioOmschrijving,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.beginwaardeValutaLopendeJaar,

TijdelijkeRapportage.historischeWaarde, 
TijdelijkeRapportage.historischeValutakoers, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, 

Fondsen.rating as fondsRating,
Fondsen.OptieBovenliggendFonds,
if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.fondsOmschrijving ,optie.Omschrijving) as onderliggendFonds, 
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.type,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				   TijdelijkeRapportage.portefeuille

FROM ".
			" TijdelijkeRapportage 
Left Join Fondsen ON TijdelijkeRapportage.Fonds = Fondsen.Fonds 
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds   
      ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND 
      TijdelijkeRapportage.type <> 'rente'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.fondsOmschrijving asc, 
      TijdelijkeRapportage.type asc";
      
   //   echo $query;exit;
  //    echo $this->pdf->rapportageValuta;exit;

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

    $fondsRegels=array();
    $categorieAantallen=array();
		while($fonds = $DB->NextRecord())
		{
		  $categorieAantallen[$fonds['Omschrijving']]+=1;
		  $fondsRegels[]=$fonds;
    }
    unset($lastCategorie);

    
    $categorieRegel=0;
    $n=0;
    foreach($fondsRegels as $fonds)
    { 
      
      $q="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='".$fonds['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"  
			.$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB2->SQL($q);
      $rente=$DB2->lookupRecord();
      $fonds['rente']=$rente['actuelePortefeuilleWaardeEuro'];
      
            $q="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='".$fonds['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatumVanaf."'"  
			.$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB2->SQL($q);
      $rente=$DB2->lookupRecord();
      $fonds['renteBegin']=$rente['actuelePortefeuilleWaardeEuro'];
  
      if($fonds['hoofdcategorie'] <> 'EFI_AAND')
        $fonds['RegioOmschrijving']='';

		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';
		  if($fonds['beleggingssectorOmschrijving']=='')
		    $fonds['beleggingssectorOmschrijving']='Geen sector';

      if($fonds['beleggingscategorie'] <> 'AAND')
        $fonds['beleggingssectorOmschrijving']='';
        
 
      $ytm='';        
			// print categorie headers
			//$this->pdf->SetWidths($this->pdf->widthA);
			//$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			/*
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{

        $procentResultaat=$totaalBijdrage/$totaalpercentage*100;
        $n=0;
        $actueleWaardePortefeuille += $this->printTotaal('subtotaal '.$lastCategorie, $subtotaal,false);//$procentResultaat
         
        $subtotaal=array();

        $this->hseTotalen[$lastHCategorie][$lastCategorie]=array('waardeEUR'=>$totaalactueel,'aandeel'=>$totaalactueel/$totaalWaarde);

        $categorieRegel=0;
			}
      $categorieRegel++;
*/
			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{// echo $this->pdf->GetY()." ".$fonds['hoofdcategorieOmschrijving']."<br>\n";
			//	listarray($subtotaal);
				$actueleWaardePortefeuille += $this->printTotaal('subtotaal '.$lastCategorie, $subtotaal,false);//$procentResultaat

				$subtotaal=array();
			  	$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}
/*
			if($lastCategorie <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> '')
			{
					$this->printKop(vertaalTekst($fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}
*/
   
    if($categorieAantallen[$fonds['Omschrijving']] > 0 && $categorieAantallen[$fonds['Omschrijving']]-$categorieRegel<1)
    {
//      echo $fonds['Omschrijving']." check h ".$this->pdf->GetY()." <br>\n" ;
      if($this->pdf->GetY() > 185)
         $this->pdf->AddPage();
      // listarray($categorieAantallen);
    }

			$dividend=$this->getDividend($fonds['fonds']);


			$fondsResultaat = ($fonds['actuelePortefeuilleWaardeInValuta'] - $fonds['historischeWaardeTotaal']) * $fonds['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $fonds['historischeWaardeTotaal']) * 100;

				if($fonds['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeTotaalValuta'] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				//$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeTotaalValuta']) / ($fonds['historischeWaardeTotaalValuta'] /100));
			  $procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeTotaalValuta']  + $dividend['corrected']) / ($fonds['historischeWaardeTotaalValuta'] /100));

			$gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($fonds['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);
        $bijdrage=$procentResultaat*$percentageVanTotaal/100;
          
        if($fonds['type']=='rekening')
        {
          $resultaat=0;
          $fondsResultaat=0;
          $fondsResultaatprocent=0;
          $valutaResultaat=0;
          $procentResultaat=0;
          $procentResultaattxt='';
          $fonds['totaalAantal']=0;
          $fonds['actueleFonds']=0;
          $fonds['historischeWaarde']=0;
          $fondsResultaattxt='';
          $valutaResultaattxt='';
        }

			if($dividend['totaal'] <> 0)
				$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
			else
				$dividendtxt='';


				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $n=fillLine($this->pdf,$n,array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1));
				$this->pdf->row(array('',
													$fonds['fondsOmschrijving'],
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$this->formatGetal($fonds['historischeWaarde'],2),
													$this->formatGetal($fonds['historischeWaardeTotaal'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds['historischeWaardeTotaalValuta'],$this->pdf->rapport_VOLK_decimaal),
													'',
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),

													'',
													$fondsResultaattxt,
													$valutaResultaattxt,
													$dividendtxt,
													$procentResultaattxt,
													'',
													''));
                          
                  
        if($fonds['rente'])
        {         
          $percentageVanTotaalRente = ($fonds['rente']) / ($totaalWaarde/100);
          $percentageVanHcatRente   = ($fonds['rente']) / ($hoofdcategorienTotaal[$fonds['hoofdcategorie']]/100);
     	    $percentageVanTotaalRentetxt = $this->formatGetal($percentageVanTotaalRente,$this->pdf->rapport_VOLK_decimaal_proc)." %";
          $percentageVanHcatRentetxt   = $this->formatGetal($percentageVanHcatRente,$this->pdf->rapport_VOLK_decimaal_proc)." %";
			
        	$subtotaal['percentageVanTotaal'] +=$percentageVanTotaalRente;
          $subtotaal['percentageVanHcat'] +=$percentageVanHcatRente;


          $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaalRente;
          $hcatTotaal['percentageVanHcat'] +=$percentageVanHcatRente;
        }                      



				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];
        $subtotaal['actuelePortefeuilleWaardeEuro']+= $fonds['actuelePortefeuilleWaardeEuro']+$fonds['rente'];
        $subtotaal['historischeWaardeTotaalValuta']+= $fonds['historischeWaardeTotaalValuta'];
			  $subtotaal['totaalDividend'] += $dividend['totaal'];
			  $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
        $subtotaal['resultaatFonds']+= $fondsResultaat;// + $subtotaal['totaalDividendCorrected'];
        $subtotaal['resultaatValuta']+= $valutaResultaat;
  			$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        //$subtotaal['percentageVanHcat'] +=$percentageVanHcat;
				$subtotaal['totaalResultaat'] +=$resultaat;
				$subtotaal['totaalBijdrage'] += $bijdrage;
        $subtotaal['rente'] += $fonds['rente'];
        $subtotaal['renteBegin'] += $fonds['renteBegin'];
        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
       // $hcatTotaal['percentageVanHcat'] +=$percentageVanHcat;
				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];
        $hcatTotaal['totaalbegin'] += $fonds['beginPortefeuilleWaardeEuro'];
        
        $gTotaal['actuelePortefeuilleWaardeEuro']+= $fonds['actuelePortefeuilleWaardeEuro']+$fonds['rente'];
        //$gTotaal['historischeWaardeTotaalValuta']+= $fonds['historischeWaardeTotaalValuta'];
        //$gTotaal['resultaatFonds']+= $fondsResultaat;
        //$gTotaal['resultaatValuta']+= $valutaResultaat;

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];


			$totaaldividend        += $subtotaal['totaalDividend'];
			$totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];
      
$ongerealiseerdResultaat += $subtotaal['totaalResultaat'] ;
$inProcent += $subtotaal['totaalBijdrage'] ;   


		}
    unset($this->pdf->fillCell);
    
//    listarray($totaalBijdrage);


      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
$n=0;
		  $actueleWaardePortefeuille += $this->printTotaal('subtotaal'.$lastCategorie, $subtotaal,false);
		
    $this->hseTotalen[$lastHCategorie][$lastCategorie]=array('waardeEUR'=>$totaalactueel,'aandeel'=>$totaalactueel/$totaalWaarde);
    
    $aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100;
    //echo "$aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100; <br>\n ".($actueleWaardePortefeuille-$totaalWaarde);exit;

    $this->printTotaal(vertaalTekst("Totale waarde portefeuille",$this->pdf->rapport_taal), $gTotaal,true);
    $this->pdf->Ln();
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
    $this->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    $this->pdf->SetFillColor(0);
     unset($this->pdf->fillCell);
   
	}
  
   function printValutaoverzicht($portefeuille, $rapportageDatum,$omkeren=false)
  {
 		global $__appvar;
		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ".
		" TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
		  $this->pdf->ln();
		  $this->pdf->ln();
			$t=0;
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$t] = $valuta;
				$t++;
			}

      $regels = ceil((count($valutas)));
			if(count($valutas) > 4)
			{
				$regels = ceil((count($valutas) / 2));
			}
  		$hoogte = ($regels * 4) + 4;
	  	if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$kop = "Gehanteerde koersen";



			$this->pdf->SetTextColor($this->pdf->rapport_kop4_fontcolor[r],$this->pdf->rapport_kop4_fontcolor[g],$this->pdf->rapport_kop4_fontcolor[b]);
			$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
			$this->pdf->Cell($this->pdf->widthB[1],4, vertaalTekst($kop,$this->pdf->rapport_taal), 0,1, "L");

			$plusmarge = 0;

			$y = $this->pdf->getY();
			$start = false;
			//while ($valuta = $DB->NextRecord())
			for($a=0; $a < count($valutas); $a++)
			{
				if($this->pdf->rapport_valutaoverzicht_rev)
				{
					if($valutas[$a]['actueleValuta'] <> 0 )
					$valutas[$a]['actueleValuta'] = 1 / $valutas[$a]['actueleValuta'];
				}

				if(count($valutas) > 4)
				{
					if($a >= $regels && $start == false)
					{
						$y2 = $this->pdf->getY();
						$this->pdf->setY($y);
						$plusmarge = 60;
						$start = true;
					}
				}

				$this->pdf->SetX($this->pdf->marge+$plusmarge);
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->Cell(35,4, vertaalTekst($valutas[$a]['ValutaOmschrijving'],$this->pdf->rapport_taal), 0,0, "L");
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


				if($this->pdf->ValutaKoersEind > 0)
				  $valutas[$a]['actueleValuta'] = $valutas[$a]['actueleValuta'] / $this->pdf->ValutaKoersEind ; 

        if($omkeren==true)
          $this->pdf->Cell(20,4, $this->pdf->formatGetal(1/$valutas[$a]['actueleValuta'],4), 0,1, "R");
        else
			  	$this->pdf->Cell(20,4, $this->pdf->formatGetal($valutas[$a]['actueleValuta'],4), 0,1, "R");

			}

			if($start == true)
				$this->pdf->setY($y2);
		}

  }
}
?>
