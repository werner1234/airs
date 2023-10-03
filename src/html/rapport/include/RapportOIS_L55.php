<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/11/05 06:48:26 $
File Versie					: $Revision: 1.14 $

$Log: RapportOIS_L55.php,v $
Revision 1.14  2018/11/05 06:48:26  rvv
*** empty log message ***

Revision 1.13  2018/11/03 18:45:31  rvv
*** empty log message ***

Revision 1.12  2018/10/08 06:43:54  rvv
*** empty log message ***

Revision 1.11  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.10  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.9  2018/04/21 17:55:51  rvv
*** empty log message ***

Revision 1.8  2018/04/11 09:14:19  rvv
*** empty log message ***

Revision 1.7  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.6  2018/03/21 17:04:24  rvv
*** empty log message ***

Revision 1.5  2018/03/19 07:19:46  rvv
*** empty log message ***

Revision 1.4  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.3  2018/03/03 17:13:43  rvv
*** empty log message ***

Revision 1.2  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.1  2018/02/18 14:58:36  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportCASH.php");

class RapportOIS_L55
{
	function RapportOIS_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille per ".date('d-m-Y',$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->hseTotalen=array();
		$this->hseCategorieReverseLookup=array();
   // 	$this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
	//	  $this->cashfow->genereerTransacties();
	//	  $this->cashfow->genereerRows();
      $this->db = new DB();
    
    $this->pdf->excelData[]=array("Rapportage startdatum","Rapportage einddatum","Portfeuille","Hoofdcategorie","Sector","Fondsomschrijving","ISIN-code","Aantal","Valuta","Begin Koers","Beginwaarde in " .$this->pdf->rapportageValuta,"Huidige Koers","Huidige waarde in ".$this->pdf->rapportageValuta,"Weging in portefeuille","Totaalrendement in %",'Resultaat');
    
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
    $aantal=array();
    $rente=array();
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
     " Rekeningmutaties.Boekdatum >= '".  $this->rapportageDatumVanaf."' AND ".
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

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{
		$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthA[$tel];
		}

		$writerow = $this->pdf->widthA[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst')
		{
	    $y = $this->pdf->getY();
      $this->pdf->setY($y);
  	  $this->pdf->Cell($writerow,4,$data, 0,0, "L");
  	}
		else
		{
		  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "grandtotaal")
			{
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
		//		$this->pdf->setDash(1,1);
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}


	function headerOIS($soort)
	{
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);

		$this->pdf->fillCell=array();
		for($i=0;$i<count($this->pdf->widthA);$i++)
			$this->pdf->fillCell[] = 1;



		$y = $this->pdf->getY();
		$this->pdf->setY($y);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row(array("\n".vertaalTekst("Fondsomschrijving",$this->pdf->rapport_taal),
											vertaalTekst($soort,$this->pdf->rapport_taal)."\n ",
											vertaalTekst("Aantal",$this->pdf->rapport_taal)."\n ",
											vertaalTekst("Valuta",$this->pdf->rapport_taal)."\n ",
											vertaalTekst("Begin\nKoers",$this->pdf->rapport_taal),
											vertaalTekst("Beginwaarde\nin",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta,
											vertaalTekst("",$this->pdf->rapport_taal)."\n ",
											"\n ",
											vertaalTekst("Huidige\nKoers",$this->pdf->rapport_taal),
											vertaalTekst("Huidige waarde\nin",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta,
											//'',//vertaalTekst("Weging in categorie",$this->pdf->rapport_taal),
											vertaalTekst("Weging in portefeuille",$this->pdf->rapport_taal),
											vertaalTekst("Totaalrende-\nment in %",$this->pdf->rapport_taal),//'',
											"\n ",
											'',''));

		$this->pdf->setY($y);
		$this->pdf->SetFont($this->pdf->rapport_font,"bi",$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row(array(vertaalTekst("Categorie\n",$this->pdf->rapport_taal)));
		//$this->pdf->SetWidths($this->pdf->widthA);
		//	$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
		$this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),297-$this->pdf->marge,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
		unset($this->pdf->fillCell);
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(0,$title,"tekst");
      if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalactueel
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),$grandtotaal);//$totaalbegin
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);//$totaalpercentage
//			if($totaalD <>0)
//				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalResultaat
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalvalutaresultaat

		  $procentResultaat = (($totaalB - $totaalA) / ($totaalA /100));
		  if($procentResultaat <>0)
		  	$this->printCol(11,$this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);//$totaalvalutaresultaat

		//	if($totaalF <>0)
		//		$this->printCol(11,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);//$procentHcat
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

		$this->pdf->ln();
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
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    
    $q="SELECT beleggingscategorie,omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		while($cat=$DB->nextRecord())
      $this->categoriePerOmgeschijving[$cat['omschrijving']]=$cat['beleggingscategorie'];


		$lastOmschrijving='';
    if($noPdf==false)
    {
    	$fondsresultwidth = 5;
	    $omschrijvingExtra = 9;
		  $this->pdf->widthA = array(70,45,20,13,20,24,0,0,20,24,20,22,0,0);
		  $this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
      $this->pdf->SetFillColor($this->pdf->regelFillKleur[0],$this->pdf->regelFillKleur[1],$this->pdf->regelFillKleur[2]);
      
      

      $this->pdf->AddPage();
      $this->pdf->templateVars['OISPaginas']=$this->pdf->page;
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
TijdelijkeRapportage.beleggingssectorOmschrijving AS sectorOmschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.beginwaardeValutaLopendeJaar,
Fondsen.rating as fondsRating,
Fondsen.isinCode as isinCode,
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
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds    ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND 
      TijdelijkeRapportage.type <> 'rente'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.fondsOmschrijving asc, 
      TijdelijkeRapportage.type asc";
      
  //    echo $query;exit;
  //    echo $this->pdf->rapportageValuta;exit;

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
    $n=0;
		$regelsPerHoofcategorie=array();
		while($fonds = $DB->NextRecord())
		{
      if ($fonds['type'] == 'rekening')
			{
				$fonds['fondsOmschrijving'] = $fonds['fondsOmschrijving'] . " " . $fonds['valuta'];
			}
			$this->hseCategorieReverseLookup[$fonds['Omschrijving']] = $fonds['beleggingscategorie'];
			$q = "SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='" . $fonds['fonds'] . "' AND TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
      TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "'"
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			$DB2->SQL($q);
			$rente = $DB2->lookupRecord();
			$fonds['rente'] = $rente['actuelePortefeuilleWaardeEuro'];

			$q = "SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='" . $fonds['fonds'] . "' AND TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND
      TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatumVanaf . "'"
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			$DB2->SQL($q);
			$rente = $DB2->lookupRecord();
			$fonds['renteBegin'] = $rente['actuelePortefeuilleWaardeEuro'];

			if ($fonds['hoofdcategorieOmschrijving'] == '')
			{
				$fonds['hoofdcategorieOmschrijving'] = 'Geen hoofdcategorie';
			}
			if ($fonds['Omschrijving'] == '')
			{
				$fonds['Omschrijving'] = 'Geen categorie';
			}
			if ($fonds['beleggingssectorOmschrijving'] == '')
			{
				$fonds['beleggingssectorOmschrijving'] = 'Geen sector';
			}

			if ($fonds['beleggingscategorie'] <> 'AAND')
			{
				$fonds['beleggingssectorOmschrijving'] = '';
			}
			$dbData[]=$fonds;
		 $regelsPerHoofcategorie[$fonds['hoofdcategorieOmschrijving']]++;
		}

		foreach($dbData as $fonds)
		{
      $dividend=$this->getDividend($fonds['fonds']);
      $ytm='';        
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);


			$omschrijving='';
			if($fonds['hoofdcategorie']=='EFI_AAND')
			{
				$soort = 'Sector';
				$omschrijving= $fonds['sectorOmschrijving'];
			}
			elseif($fonds['hoofdcategorie']=='EFI_OBL' || $fonds['hoofdcategorie']=='EFI_OVERIG' || $fonds['hoofdcategorie']=='EFI_KAS')
			{
				$soort = 'Soort';
				$omschrijving=  $fonds['Omschrijving'];
			}

			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{// echo $this->pdf->GetY()." ".$fonds['hoofdcategorieOmschrijving']."<br>\n";
        $regelCounter=0;
				if($noPdf==false)
				{
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalResultaat, $totaalvalutaresultaat, $totaalFondsResultaat);//$procentResultaat
					$n=0;
				}
     //   if($lastOmschrijving<>'')
			//	  $this->hseTotalen[$lastHCategorie][$lastOmschrijving]=array('waardeEUR'=>$totaalactueel,'aandeel'=>$totaalactueel/$totaalWaarde);


				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalactueelRente =0;
				$totaalactueelRenteBegin=0;
				$totaalpercentage = 0;
				$totaalFondsResultaat = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;

				if($noPdf==false)
        {
			    if($this->pdf->GetY() > 156)
            $this->pdf->AddPage();
					$this->headerOIS($soort);
			  	$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
        }
			}

			$regelCounter++;
			if($this->pdf->GetY() > 156 &&  $regelCounter>$regelsPerHoofcategorie[$fonds['hoofdcategorieOmschrijving']]-2)
				$this->pdf->AddPage();



		//	$lastOmschrijving=$omschrijving;

			$this->hseTotalen[$fonds['hoofdcategorieOmschrijving']][$omschrijving]['waardeEUR']+=($fonds['actuelePortefeuilleWaardeEuro']+$fonds['rente']) ;


			/*
            if($lastCategorie <> $fonds['Omschrijving'])
            {
              if($noPdf==false)
                $this->printKop(vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
            }
            if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> '')
            {
              if($noPdf==false)
                $this->printKop(vertaalTekst($fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
            }
      */
      $resultaat=$fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'];
  		$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($fonds['beginPortefeuilleWaardeEuro'] /100));
			if($fonds['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

			$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
      $percentageVanHcat   = ($fonds['actuelePortefeuilleWaardeEuro']) / ($hoofdcategorienTotaal[$fonds['hoofdcategorie']]/100);
     	$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
      $percentageVanHcattxt   = $this->formatGetal($percentageVanHcat,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
      {
				$procentResultaattxt = "p.m.";
        $procentResultaat=0;
      }
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);
         
         $bijdrage=$procentResultaat*$percentageVanTotaal/100;
          
if($fonds['type']=='rekening')
{
  $resultaat=0;
  $fondsResultaat=0;
  $fondsResultaatprocent=0;
  $valutaResultaat=0;
  $procentResultaat=0;
  $procentResultaattxt='';
  $fonds['totaalAantal']=round($fonds['actuelePortefeuilleWaardeInValuta'],2);
  $fonds['actueleFonds']=0;
  $fonds['beginwaardeLopendeJaar']=0;
}




				$resultaattxt = "";
        if($noPdf==false)
        {
       // $resultaattxt=$this->formatGetal($resultaat); 
        
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		
          //*$fonds['beginwaardeValutaLopendeJaar']

				if($fonds['type']=='fondsen')
				{
					$koersresultaat = $fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'];
					$koersresultaatTxt=$this->formatGetal($koersresultaat,$this->pdf->rapport_VOLK_decimaal);
				}
				else
				{
					$koersresultaat=0;
					$koersresultaatTxt='';
				}
        $n=fillLine($this->pdf,$n);


					$omschrijvingFonds=$fonds['fondsOmschrijving'];
					$omschrijvingWidth = $this->pdf->GetStringWidth($omschrijvingFonds);
				//	echo $omschrijvingWidth." ".$omschrijvingFonds."<br>\n";
					$cellWidth =  $this->pdf->widths[0] - 2;
					if ($omschrijvingWidth > $cellWidth)
					{
						$dotWidth = $this->pdf->GetStringWidth('...');
						$chars = strlen($omschrijvingFonds);
						$newOmschrijving = $omschrijvingFonds;
						for ($i = 3; $i < $chars; $i++)
						{
							$omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
							if ($cellWidth > ($omschrijvingWidth + $dotWidth))
							{
								$omschrijvingFonds = substr($newOmschrijving, 0, $chars - $i) . '...';
								break;
							}
						}
					}

          $this->pdf->excelData[]=array(date('d-m-Y',$this->pdf->rapport_datumvanaf ),
            date('d-m-Y',$this->pdf->rapport_datum ),
						$this->portefeuille ,$fonds['hoofdcategorieOmschrijving'],$omschrijving,
            $fonds['fondsOmschrijving'],
            $fonds['isinCode'],
            round($fonds['totaalAantal'],4),
            $fonds['valuta'],
            round($fonds['beginwaardeLopendeJaar'],2),
            round($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            round($fonds['actueleFonds'],2),
            round($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            round($percentageVanTotaal,2),
                  round($procentResultaat,2),
                        round($resultaat,2));
					
					$this->pdf->row(array(
													$omschrijvingFonds,
													$omschrijving,
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
													$this->formatGetal($fonds['beginwaardeLopendeJaar'],2),
													$this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													'',
													"",
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
													$procentResultaattxt,
													$resultaattxt,
													'',
													''));
                          
                  
        if($fonds['rente'])
        {
					$omschrijvingFonds="Opgelopen rente ".$fonds['fondsOmschrijving'];
					$omschrijvingWidth = $this->pdf->GetStringWidth($omschrijvingFonds);
					//	echo $omschrijvingWidth." ".$omschrijvingFonds."<br>\n";
					$cellWidth =  $this->pdf->widths[0] - 2;
					if ($omschrijvingWidth > $cellWidth)
					{
						$dotWidth = $this->pdf->GetStringWidth('...');
						$chars = strlen($omschrijvingFonds);
						$newOmschrijving = $omschrijvingFonds;
						for ($i = 3; $i < $chars; $i++)
						{
							$omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
							if ($cellWidth > ($omschrijvingWidth + $dotWidth))
							{
								$omschrijvingFonds = substr($newOmschrijving, 0, $chars - $i) . '...';
								break;
							}
						}
					}
          $percentageVanTotaalRente = ($fonds['rente']) / ($totaalWaarde/100);
          $percentageVanHcatRente   = ($fonds['rente']) / ($hoofdcategorienTotaal[$fonds['hoofdcategorie']]/100);
     	    $percentageVanTotaalRentetxt = $this->formatGetal($percentageVanTotaalRente,$this->pdf->rapport_VOLK_decimaal_proc)." %";
          $percentageVanHcatRentetxt   = $this->formatGetal($percentageVanHcatRente,$this->pdf->rapport_VOLK_decimaal_proc)." %";
			
        	$subtotaal['percentageVanTotaal'] +=$percentageVanTotaalRente;
          $subtotaal['percentageVanHcat'] +=$percentageVanHcatRente;
				//	$subtotaal['koersresultaat'] +=$koersresultaat;
          $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaalRente;
          $hcatTotaal['percentageVanHcat'] +=$percentageVanHcatRente;
					//$hcatTotaal['koersresultaat'] +=$koersresultaat;
          $n=fillLine($this->pdf,$n);
 			  	$this->pdf->row(array(
														$omschrijvingFonds,
                          '',
													'',
													'',
													'',
													'',
													'',
													"",
													'',
													$this->formatGetal($fonds['rente'],$this->pdf->rapport_VOLK_decimaal),
														$percentageVanTotaalRentetxt,
                          '',
													'',
													'',
													''));
                          }

        }


				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        $subtotaal['percentageVanHcat'] +=$percentageVanHcat;
			  $subtotaal['koersresultaat'] +=$koersresultaat;
				$subtotaal['totaalResultaat'] +=$resultaat;
				$subtotaal['totaalBijdrage'] += $bijdrage;
        $subtotaal['rente'] += $fonds['rente'];
        $subtotaal['renteBegin'] += $fonds['renteBegin'];
        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        $hcatTotaal['percentageVanHcat'] +=$percentageVanHcat;
			  $hcatTotaal['koersresultaat'] +=$koersresultaat;

				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];
        $hcatTotaal['totaalbegin'] += $fonds['beginPortefeuilleWaardeEuro'];

	  	$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];
      $totaalbegin += $fonds['beginPortefeuilleWaardeEuro'];
      $totaalactueel+=$fonds['rente'];
      //$totaalactueelRenteBegin+=$fonds['renteBegin'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];
      $totaalFondsResultaat += $subtotaal['koersresultaat'];

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];


			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $bijdrage;

			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;
			
      
$ongerealiseerdResultaat += $subtotaal['totaalResultaat'] ;
$inProcent += $subtotaal['totaalBijdrage'] ;   

$subtotaal = array();  
		}
    
//    listarray($totaalBijdrage);


      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
    if($noPdf==false)
    { 
		  $actueleWaardePortefeuille += $this->printTotaal('', $totaalbegin, $totaalactueel,$totaalpercentage,$totaalResultaat,'',$totaalFondsResultaat);
		  $n=0;
    }
   // $this->hseTotalen[$lastHCategorie][$lastCategorie]=array('waardeEUR'=>$totaalactueel,'aandeel'=>$totaalactueel/$totaalWaarde);
		foreach($this->hseTotalen as $hcat=>$catData)
		{
			foreach($catData as $categorie=>$waarden)
			{
				$this->hseTotalen[$hcat][$categorie]['aandeel']=$waarden['waardeEUR']/$totaalWaarde;
			}
		}
    
    $aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100;
    //echo "$aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100; <br>\n ".($actueleWaardePortefeuille-$totaalWaarde);exit;
    if($noPdf==false)
    {
    $this->printTotaal(vertaalTekst("Totale waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,$aandeelOpTotaal,$ongerealiseerdResultaat,'','',true);
    $this->pdf->Ln();
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
    $this->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    $this->pdf->SetFillColor(0);
     unset($this->pdf->fillCell);
    }
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
