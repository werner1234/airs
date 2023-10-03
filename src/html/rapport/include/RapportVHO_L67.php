<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/24 13:02:42 $
File Versie					: $Revision: 1.20 $

$Log: RapportVHO_L67.php,v $
Revision 1.20  2020/06/24 13:02:42  rvv
*** empty log message ***

Revision 1.19  2019/11/06 16:11:20  rvv
*** empty log message ***

Revision 1.18  2018/10/27 16:49:57  rvv
*** empty log message ***

Revision 1.17  2018/09/26 15:53:28  rvv
*** empty log message ***

Revision 1.16  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.15  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.14  2018/02/12 07:32:48  rvv
*** empty log message ***

Revision 1.13  2018/02/10 18:09:12  rvv
*** empty log message ***

Revision 1.12  2017/11/15 17:03:35  rvv
*** empty log message ***

Revision 1.11  2017/08/23 15:23:15  rvv
*** empty log message ***

Revision 1.10  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.9  2017/05/27 09:45:52  rvv
*** empty log message ***

Revision 1.8  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.7  2016/10/26 16:13:40  rvv
*** empty log message ***

Revision 1.6  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.5  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.4  2016/03/14 07:15:53  rvv
*** empty log message ***

Revision 1.3  2016/03/12 17:41:08  rvv
*** empty log message ***

Revision 1.2  2016/03/09 17:24:31  rvv
*** empty log message ***

Revision 1.1  2016/03/06 18:17:00  rvv
*** empty log message ***

Revision 1.8  2014/01/04 17:06:27  rvv
*** empty log message ***

Revision 1.7  2013/06/30 15:07:33  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportCASH.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
class RapportVHO_L67
{
	function RapportVHO_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->totalenCat=array();
		$this->bedragDecimalen=0;
    
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
		$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widths[$tel];
		}

		$writerow = $this->pdf->widths[($tel)];
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
		  $this->pdf->SetX($start);
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
		//	$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
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



	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 10;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		if($this->pdf->rapport_titel=="Liquiditeiten")
		{
			$this->pdf->widths[0]=$this->pdf->widthA[0]-14;
			$this->pdf->widths[9]=$this->pdf->widthA[9]+14;
		}

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";


     $this->pdf->SetFillColor($this->pdf->rapport_totaal_fillcolor[0],$this->pdf->rapport_totaal_fillcolor[1],$this->pdf->rapport_totaal_fillcolor[2]);
     $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY()-1,297-2*$this->pdf->marge,6,'F'); 
     $this->pdf->SetTextColor($this->pdf->rapport_totaal_textcolor[0],$this->pdf->rapport_totaal_textcolor[1],$this->pdf->rapport_totaal_textcolor[2]);

		//$lastCategorie, A $totaalactueelRente, B $totaalactueel, C $totaalpercentage , D $totaalFondsResultaat, E $totaalValutaResultaat, F $totaalResultaat/($totaalactueel-$totaalResultaat)*100);

			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//	$this->printCol(0,$title,"tekst");
      if($this->pdf->rapport_titel=="Vastrentende waarden")
      {
        $this->pdf->SetWidths($this->pdf->widthB);

				$this->printCol(8,$this->formatGetal($this->totalenCat[$title]['restLooptijd'],$this->pdf->rapport_VOLK_decimaal),'sub');
				$this->printCol(9,$this->formatGetal($this->totalenCat[$title]['ytm'],$this->pdf->rapport_VOLK_decimaal),'sub');
				$this->printCol(10,$this->formatGetal($this->totalenCat[$title]['duration'],$this->pdf->rapport_VOLK_decimaal),'sub');

				if($totaalA <>0)
					$this->printCol(12,$this->formatGetal($totaalA,$this->bedragDecimalen,true),$grandtotaal);

        if($totaalB <>0)
			  	$this->printCol(11,$this->formatGetal($totaalB,$this->bedragDecimalen),'sub');
			  if($totaalC <>0)
				  $this->printCol(15,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(13,$this->formatGetal($totaalD,$this->bedragDecimalen),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);

      }
      else
      {
			//	echo $this->pdf->rapport_titel." $title $totaalA <br>\n";ob_flush();
      if($totaalB <>0)
				$this->printCol(7,$this->formatGetal($totaalB,$this->bedragDecimalen),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(9,$this->formatGetal($totaalA,$this->bedragDecimalen,true),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(8,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(10,$this->formatGetal($totaalD,$this->bedragDecimalen),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(11,$this->formatGetal($totaalE,$this->bedragDecimalen),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(12,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
      }  
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
 $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);

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
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize+1;
				$fonttype = 'bi';
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
    
    if($title=='Liquiditeiten' && $type=='bi')
    {
      $this->pdf->rapport_titel = "Liquiditeiten";
			$this->pdf->SetWidths( $this->pdf->widthA);
			$this->pdf->SetAligns( $this->pdf->alignA);

			$this->pdf->widths[0]= $this->pdf->widthA[0]-14;
			$this->pdf->widths[9]= $this->pdf->widthA[9]+14;

			$this->pdf->SetFont($font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array("\n".vertaalTekst("Omschrijving",$this->pdf->rapport_taal),
										vertaalTekst("Aantal",$this->pdf->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$this->pdf->rapport_taal)."\n ",
										vertaalTekst("Kostprijs",$this->pdf->rapport_taal)."\n ",
										vertaalTekst("",$this->pdf->rapport_taal)."\n ",
										"\n ",
										vertaalTekst("Koers",$this->pdf->rapport_taal)."\n ",
										vertaalTekst("Marktwaarde",$this->pdf->rapport_taal)."\n ",
										vertaalTekst("Weging",$this->pdf->rapport_taal)."\n ",
                    vertaalTekst("Opg.\nRente",$this->pdf->rapport_taal),
										vertaalTekst("Fonds\nResultaat",$this->pdf->rapport_taal),
                    vertaalTekst("Valuta\nResultaat",$this->pdf->rapport_taal),
										vertaalTekst("in %",$this->pdf->rapport_taal)."\n "));

    }
		$this->pdf->SetFont($font,$fonttype,$fontsize);
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();

			$fondsresultwidth = 5;
			$omschrijvingExtra = 9;

		// voor kopjes
			$this->pdf->widthA = array(60,25,19,25,0,0,25,25,23,1,23,23,23);
      $this->pdf->widthB = array(70-14,1,18,15,16,16,16,14,13,13,14,23,15,20,15,16);//14
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
      $this->pdf->SetFillColor(230,230,230);
      $fill=true;

		$this->pdf->AddPage();
    $this->pdf->templateVars['VHOPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['VHOPaginas']=$this->pdf->rapport_titel;
    

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
		$totaalWaardePortefeuille = $totaalWaarde['totaal'];
    
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);    

		$actueleWaardePortefeuille = 0;

			$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.koersDatum,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, 
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
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
				   TijdelijkeRapportage.portefeuille,
           (TijdelijkeRapportage.historischeWaarde ) AS historischeKoers,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers) AS historischeWaardeEuro
      FROM ".
			" TijdelijkeRapportage 
Left Join Fondsen ON TijdelijkeRapportage.Fonds = Fondsen.Fonds 
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds   
      ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND 
      TijdelijkeRapportage.type <> 'rente'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc, 
      TijdelijkeRapportage.beleggingscategorieVolgorde asc, onderliggendFonds,
      TijdelijkeRapportage.fondsOmschrijving asc, 
      TijdelijkeRapportage.type asc";

      

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

		while($fonds = $DB->NextRecord())
		{
			$fondsregels[]=$fonds;

			if($fonds['Lossingsdatum'] <> '')
				$lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
			else
				$lossingsJul=0;

			if($lossingsJul > 0)
			{
				$this->totalenCat[$fonds['Omschrijving']]['totaleWaarde'] += $fonds['actuelePortefeuilleWaardeEuro'];
			}
		}

		foreach($fondsregels as $fonds)
		{
			$rente=getRenteParameters($fonds['fonds'], $this->rapportageDatum);
			foreach($rente as $key=>$value)
				$fonds[$key]=$value;

      $q="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='".$fonds['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"  
			.$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB2->SQL($q);
      $rente=$DB2->lookupRecord();
      $fonds['rente']=$rente['actuelePortefeuilleWaardeEuro'];


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
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{
			 
        if($totaalactueelRente <> 0)
        {
          if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
            $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		        $fill=true;
		      }
          $aandeel=$totaalactueelRente/$totaalWaardePortefeuille*100;
	      //  $this->pdf->row(array("Opgelopen rente",'','','','',"",'',$this->formatGetal($totaalactueelRente,$this->pdf->rapport_VOLK_decimaal),
        //  $this->formatGetal($aandeel,1).'%','','','','',''));
        }
        $totaalactueel+=$totaalactueelRente;
        $totaalpercentage+=$aandeel;
        $totaalbegin=$fonds['historischeWaardeEuro'];
       
				$title = '';
        $procentResultaat=$totaalBijdrage/$totaalpercentage*100;

        $actueleWaardePortefeuille += $this->printTotaal($lastCategorie, $totaalactueelRente, $totaalactueel, $totaalpercentage , $totaalFondsResultaat, $totaalValutaResultaat, $totaalResultaat/($totaalactueel-$totaalResultaat)*100);



				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalactueelRente =0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
        $totaalValutaResultaat=0;
        $totaalFondsResultaat=0;
				$totaalBijdrage = 0;
			}

			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{// echo $this->pdf->GetY()." ".$fonds['hoofdcategorieOmschrijving']."<br>\n";
			  if(isset($lastHCategorie))
        {
          if($lastHCategorie=='Zakelijke waarden')
          {
            if($this->pdf->getY()>130)
              $this->pdf->AddPage();
           
              $query="SELECT SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro 
              FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'];
              $DB2->SQL($query);
            	$DB2->Query();
              $tmp=$DB2->lookupRecord();	
              $totaalWaarde=$tmp['WaardeEuro'];
                     
              $query="SELECT TijdelijkeRapportage.beleggingssector,TijdelijkeRapportage.beleggingssectorOmschrijving,
              SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
              FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'].
              " GROUP BY beleggingssector ORDER BY WaardeEuro desc";
              $DB2->SQL($query);
            	$DB2->Query();
          	  while($cat = $DB2->nextRecord())
            	{
								$cat['beleggingssectorOmschrijving']=vertaalTekst($cat['beleggingssectorOmschrijving'],$this->pdf->rapport_taal);
	              $data['sectorVerdeling']['data'][$cat['beleggingssector']]['waardeEur']=$cat['WaardeEuro'];
	              $data['sectorVerdeling']['data'][$cat['beleggingssector']]['Omschrijving']=$cat['beleggingssectorOmschrijving'];
	              $data['sectorVerdeling']['pieData'][$cat['beleggingssectorOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	              $data['sectorVerdeling']['kleurData'][$cat['beleggingssectorOmschrijving']]=$allekleuren['OIS'][$cat['beleggingssector']];
	              $data['sectorVerdeling']['kleurData'][$cat['beleggingssectorOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	            }                          
            	$query="SELECT TijdelijkeRapportage.valuta, TijdelijkeRapportage.ValutaOmschrijving,
              SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
              FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'].
              " GROUP BY Valuta ORDER BY WaardeEuro desc";
            	$DB2->SQL($query);
            	$DB2->Query();
          	  while($cat = $DB2->nextRecord())
            	{
								$cat['ValutaOmschrijving']=vertaalTekst($cat['ValutaOmschrijving'],$this->pdf->rapport_taal);
	              $data['valutaVerdeling']['data'][$cat['valuta']]['waardeEur']=$cat['WaardeEuro'];
	              $data['valutaVerdeling']['data'][$cat['valuta']]['Omschrijving']=$cat['ValutaOmschrijving'];
	              $data['valutaVerdeling']['pieData'][$cat['ValutaOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	              $data['valutaVerdeling']['kleurData'][$cat['ValutaOmschrijving']]=$allekleuren['OIV'][$cat['valuta']];
	              $data['valutaVerdeling']['kleurData'][$cat['ValutaOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	            }
             
              $grafiekY=$this->pdf->getY()+5;
              $this->pdf->setXY(30,$grafiekY);
              $this->printPie($data['sectorVerdeling']['pieData'],$data['sectorVerdeling']['kleurData'],vertaalTekst('Sectorverdeling',$this->pdf->rapport_taal).' '.vertaalTekst($lastHCategorie ,$this->pdf->rapport_taal),60,50);
              $this->pdf->wLegend=0;

              $this->pdf->setXY(160,$grafiekY);
              $this->printPie($data['valutaVerdeling']['pieData'],$data['valutaVerdeling']['kleurData'],vertaalTekst('Valutaverdeling',$this->pdf->rapport_taal).' '.vertaalTekst($lastHCategorie ,$this->pdf->rapport_taal),60,50);
              $this->pdf->wLegend=0;
           
           }
           elseif($lastHCategorie=='Vastrentende waarden')
           {
            if($this->pdf->getY()>130)
              $this->pdf->AddPage();
           
              $query="SELECT SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro 
              FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'];
              $DB2->SQL($query);
            	$DB2->Query();
              $tmp=$DB2->lookupRecord();	
              $totaalWaarde=$tmp['WaardeEuro'];
                     
              $query="SELECT TijdelijkeRapportage.regio,TijdelijkeRapportage.regioOmschrijving,
              SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
              FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'].
              " GROUP BY regio ORDER BY WaardeEuro desc";
              $DB2->SQL($query);
            	$DB2->Query();
          	  while($cat = $DB2->nextRecord())
            	{
								$cat['regioOmschrijving']=vertaalTekst($cat['regioOmschrijving'],$this->pdf->rapport_taal);
	              $data['regioVerdeling']['data'][$cat['regio']]['waardeEur']=$cat['WaardeEuro'];
	              $data['regioVerdeling']['data'][$cat['regio']]['Omschrijving']=$cat['regioOmschrijving'];
	              $data['regioVerdeling']['pieData'][$cat['regioOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	              $data['regioVerdeling']['kleurData'][$cat['regioOmschrijving']]=$allekleuren['OIR'][$cat['regio']];
	              $data['regioVerdeling']['kleurData'][$cat['regioOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	            }                          
            	$query="SELECT Fondsen.rating, Fondsen.rating as ratingOmschrijving,
              SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
              FROM TijdelijkeRapportage JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'	AND TijdelijkeRapportage.hoofdcategorieOmschrijving='$lastHCategorie' AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"	.$__appvar['TijdelijkeRapportageMaakUniek'].
              " GROUP BY rating ORDER BY WaardeEuro desc";
            	$DB2->SQL($query);
            	$DB2->Query();
          	  while($cat = $DB2->nextRecord())
            	{
								$cat['ratingOmschrijving']=vertaalTekst($cat['ratingOmschrijving'],$this->pdf->rapport_taal);
	              $data['ratingVerdeling']['data'][$cat['rating']]['waardeEur']=$cat['WaardeEuro'];
	              $data['ratingVerdeling']['data'][$cat['rating']]['Omschrijving']=$cat['ratingOmschrijving'];
	              $data['ratingVerdeling']['pieData'][$cat['ratingOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	              $data['ratingVerdeling']['kleurData'][$cat['ratingOmschrijving']]=$allekleuren['Rating'][$cat['rating']];
	              $data['ratingVerdeling']['kleurData'][$cat['ratingOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	            }
            
              $grafiekY=$this->pdf->getY()+5;
              $this->pdf->setXY(30,$grafiekY);
              $this->printPie($data['regioVerdeling']['pieData'],$data['regioVerdeling']['kleurData'],vertaalTekst('Regio verdeling',$this->pdf->rapport_taal).' '.vertaalTekst($lastHCategorie ,$this->pdf->rapport_taal),60,50);
              $this->pdf->wLegend=0;

              $this->pdf->setXY(160,$grafiekY);
              $this->printPie($data['ratingVerdeling']['pieData'],$data['ratingVerdeling']['kleurData'],vertaalTekst('Rating verdeling',$this->pdf->rapport_taal).' '.vertaalTekst($lastHCategorie ,$this->pdf->rapport_taal),60,50);
              $this->pdf->wLegend=0;
           
           }
          $this->pdf->rapport_titel =	$fonds['hoofdcategorieOmschrijving'];
          $this->pdf->AddPage();
          if($fonds['hoofdcategorieOmschrijving']=='Liquiditeiten')
          {
            $this->cashflow();
						$this->pdf->SetWidths( $this->pdf->widthA);
						$this->pdf->SetAligns( $this->pdf->alignA);
						$this->pdf->widths[0]= $this->pdf->widthA[0]-14;
						$this->pdf->widths[9]= $this->pdf->widthA[9]+14;
          }
				}
        $this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}

			if($lastCategorie <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> '')
			{
					$this->printKop(vertaalTekst($fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}
      
      
      $fondsResultaat = ($fonds['actuelePortefeuilleWaardeInValuta'] - $fonds['historischeWaardeTotaal']) * $fonds['actueleValuta'] / $this->pdf->ValutaKoersEind;
			$valutaResultaat = $fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeTotaalValuta'] - $fondsResultaat;	
      $resultaat = $fondsResultaat + $valutaResultaat;

      //$resultaat=$fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeEuro'];
 
 
  		$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['historischeWaardeEuro']) / ($fonds['historischeWaardeEuro'] /100));
			if($fonds['historischeWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

			$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaardePortefeuille/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

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
  $fonds['totaalAantal']=0;
  $fonds['actueleFonds']=0;
  $fonds['historischeKoers']=0;
}




				$resultaattxt = "";
        $fondsResultaatTxt='';
        $valutaResultaatTxt='';
        $resultaattxt=$this->formatGetal($resultaat); 
        $fondsResultaatTxt=$this->formatGetal($fondsResultaat); 
        $valutaResultaatTxt=$this->formatGetal($valutaResultaat); 
        
        
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

					 if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
            $this->pdf->SetFillColor(230,230,230);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }




        if($fonds['Lossingsdatum'] <> '')
          $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
        else
          $lossingsJul=0;
        //$rentedatumJul = adodb_db2jul($fonds['Rentedatum']);
        $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

        $koers=getRentePercentage($fonds['fonds'],$this->rapportageDatum);

			  $renteDag=0;
			  if($fonds['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($fonds['Rentedatum']);
          $renteStap=($fonds['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			  }


     $ytm=0;
     $duration=0;
     $modifiedDuration=0;
     if($lossingsJul > 0)
	   {
	  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
	  	  $p = $fonds['actueleFonds'];
        $r = $koers['Rentepercentage']/100;
        $b = $this->cashfow->fondsDataKeyed[$fonds['fonds']]['lossingskoers'];// 100
        $y = $jaar;

        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;
        
        $duration=$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaarde'];

        if($fonds['variabeleCoupon'] == 1 && $renteDag <> 0)
          $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
        else
          $modifiedDuration=$duration/(1+$ytm/100);
             
        $aandeel=$fonds['actuelePortefeuilleWaardeEuro']/$this->totalenCat[$fonds['Omschrijving']]['totaleWaarde'];//$totaalWaardePortefeuille;
			 /*
        $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
        $totalen['ytm']+=$ytm*$aandeel;
        $totalen['duration']+=$duration*$aandeel;
        $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
        $totalen['restLooptijd']+=$restLooptijd*$aandeel;
         */
        $this->totalenCat[$fonds['Omschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
        $this->totalenCat[$fonds['Omschrijving']]['ytm']+=$ytm*$aandeel;
        $this->totalenCat[$fonds['Omschrijving']]['duration']+=$duration*$aandeel;
        $this->totalenCat[$fonds['Omschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
        $this->totalenCat[$fonds['Omschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;
	    }
	    else
	    {
	      $ytm=0;
	      $restLooptijd=0;
	      $duration=0;
	      $modifiedDuration=0;
	    }
      $fonds['fondsOmschrijving']=vertaalTekst($fonds['fondsOmschrijving'],$this->pdf->rapport_taal);
       if($this->pdf->rapport_titel=="Vastrentende waarden")
       {
				 $rentePeriodetxt= "  ".adodb_date('d/m',adodb_db2jul($fonds['Rentedatum']));
				 //$rentePeriodetxt= "  ".date("d-m",db2jul($subdata[rentedatum]));
				 if($fonds['Renteperiode'] <> 12 && $fonds['Renteperiode'] <> 0)
					 $rentePeriodetxt .= " / ".$fonds['Renteperiode'];
				 //date('d/m',$renteJul)
				if($this->pdf->getStringWidth($fonds['fondsOmschrijving'].$rentePeriodetxt) > $this->pdf->widths[0])
				{
					$aantalTekens=strlen($fonds['fondsOmschrijving']);
					for($i=1;$i<$aantalTekens;$i++)
					{
						$omschrijving=substr($fonds['fondsOmschrijving'],0,$aantalTekens-$i)."...".$rentePeriodetxt;
						if($this->pdf->getStringWidth($omschrijving) < 70-15-2)
							break;
					}
				}
				 else
				 {
					 $omschrijving=$fonds['fondsOmschrijving'].$rentePeriodetxt;
				 }

        $this->pdf->SetWidths($this->pdf->widthB);
    		$this->pdf->row(array(
													$omschrijving,
                          '',
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
                          $this->formatGetal($fonds['historischeKoers'],2),
													$this->formatGetal($fonds['actueleFonds'],2),
                          date('d/m',db2jul($fonds['koersDatum'])),
                          $fonds['fondsRating'],
                          $this->formatGetal($restLooptijd,2),
                          $this->formatGetal($ytm,2),
                          $this->formatGetal($duration,2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->bedragDecimalen),
													$this->formatGetal($fonds['rente'],$this->bedragDecimalen),
													$fondsResultaatTxt,
													$procentResultaattxt,
                          $percentageVanTotaaltxt));   
       }
       else
			 {

				 if($this->pdf->rapport_titel=="Liquiditeiten")
					 $rente=$this->formatGetal($fonds['rente']);
				 else
					 $rente='';
				 //  $this->pdf->SetWidths($this->pdf->widthB);

				 if($fonds['hoofdcategorieOmschrijving']=='Liquiditeiten')
				 {
					 $this->pdf->SetWidths( $this->pdf->widthA);
					 $this->pdf->SetAligns( $this->pdf->alignA);
					 $this->pdf->widths[0]= $this->pdf->widthA[0]-14;
					 $this->pdf->widths[9]= $this->pdf->widthA[9]+14;
				 }
				
				 $this->pdf->row(array(
													 $fonds['fondsOmschrijving'],
													 $this->formatAantal($fonds['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
													 $fonds['valuta'],
													 $this->formatGetal($fonds['historischeKoers'], 2),
													 '',
													 "",
													 $this->formatGetal($fonds['actueleFonds'], 2),
													 $this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'], $this->bedragDecimalen),
													 $percentageVanTotaaltxt,
													 $rente,//$fonds['rente']  ,
													 $fondsResultaatTxt,
													 $valutaResultaatTxt,
													 $procentResultaattxt, ''));
			 }


				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['totaalResultaat'] +=$resultaat;
        $subtotaal['totaalFondsResultaat'] +=$fondsResultaat;
        $subtotaal['totaalValutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalBijdrage'] += $bijdrage;
        $subtotaal['rente'] += $fonds['rente'];
        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];

	  	$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];
      $totaalactueelRente+=$fonds['rente'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];

      $totaalFondsResultaat +=	$subtotaal['totaalFondsResultaat'] ;
      $totaalValutaResultaat +=	$subtotaal['totaalValutaResultaat'] ;
			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $bijdrage;

			$grandtotaalFondsResultaat  +=	$subtotaal['totaalFondsResultaat'] ;
      $grandtotaalValutaResultaat  +=	$subtotaal['totaalValutaResultaat'] ;
      $grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;
			$grandtotaalRente+=$fonds['rente'];
			
      
$ongerealiseerdResultaat += $subtotaal['totaalResultaat'] ;
$ongerealiseerdFondsResultaat += $subtotaal['totaalFondsResultaat'] ;
$ongerealiseerdValutaResultaat += $subtotaal['totaalValutaResultaat'] ;
$inProcent += $subtotaal['totaalBijdrage'] ;   

$subtotaal = array();  
		}
    
//    listarray($totaalBijdrage);


      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
		$actueleWaardePortefeuille += $this->printTotaal('', $totaalactueelRente, $totaalactueel,$totaalpercentage,$totaalFondsResultaat,$totaalValutaResultaat,$totaalResultaat/($totaalactueel-$totaalResultaat)*100);
		
    $aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaardePortefeuille*100;
    //echo "$aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaardePortefeuille*100; <br>\n ".($actueleWaardePortefeuille-$totaalWaardePortefeuille);exit;
    $this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), $grandtotaalRente, $actueleWaardePortefeuille,$aandeelOpTotaal,$ongerealiseerdFondsResultaat,$ongerealiseerdValutaResultaat,$ongerealiseerdResultaat/($actueleWaardePortefeuille-$ongerealiseerdResultaat)*100,true);
    $this->pdf->Ln();
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
//	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
//    $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);

$this->pdf->SetFillColor(0);
    $this->pdf->SetTextColor(0);
 unset($this->pdf->fillCell);
	}
  
  
	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
	{

	  $col1=array(255,0,0); // rood
	  $col2=array(0,255,0); // groen
	  $col3=array(255,128,0); // oranje
	  $col4=array(0,0,255); // blauw
	  $col5=array(255,255,0); // geel
	  $col6=array(255,0,255); // paars
	  $col7=array(128,128,128); // grijs
	  $col8=array(128,64,64); // bruin
	  $col9=array(255,255,255); // wit
	  $col0=array(0,0,0); //zwart
	  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();

		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
  			if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
		  	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',10);
			$this->pdf->setXY($startX,$y-4);
			$this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage+$w ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag - ($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + 2;
      }

  }

    function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }
  
  function cashflow()
  {

		global $__appvar;

		$this->pdf->templateVars['CASHYPaginas']=$this->pdf->page;
	  $this->pdf->SetWidths(array(10,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,mktime(0,0,0,1,1,date('Y',$this->pdf->rapport_datumvanaf)),$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
		for($i=1;$i<13;$i++)
		{
		  		  $cashflowHuidigjaar[$maanden[$i]]['lossing'] +=0;
		  $cashflowHuidigjaar[$maanden[$i]]['rente'] +=0;

		}


		$rapJaar=substr($this->rapportageDatum,0,4);
		foreach ($cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+13))
	      $jaar='Overig';
		  $maand=$maanden[intval(substr($regel['0'],3,2))];
		  $cashflowJaar[$jaar]['lossing'] +=0;
		  $cashflowJaar[$jaar]['rente'] +=0;
		  if($jaar==$rapJaar)
		    $cashflowHuidigjaar[$maand][$regel[2]] +=$regel[3];



		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


		 $this->pdf->setY(80);
		 $this->pdf->underlinePercentage=0.8;
    $this->pdf->SetWidths(array(20,15,25,25,25,20,20));

		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',vertaalTekst('maand',$this->pdf->rapport_taal),vertaalTekst('lossing',$this->pdf->rapport_taal),vertaalTekst('coupon',$this->pdf->rapport_taal),vertaalTekst('totaal',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach ($cashflowHuidigjaar as $maand=>$waarden)
		{
       $this->pdf->Row(array('',vertaalTekst($maand,$this->pdf->rapport_taal),$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
		}
		$this->pdf->ln(2);
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

		$this->pdf->setY(80);
		$this->pdf->SetWidths(array(160,15,25,25,25,20,20));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('',vertaalTekst('jaar',$this->pdf->rapport_taal),vertaalTekst('lossing',$this->pdf->rapport_taal),vertaalTekst('coupon',$this->pdf->rapport_taal),vertaalTekst('totaal',$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totalen=array();
		foreach ($cashflowJaar as $jaar=>$waarden)
		{
       $this->pdf->Row(array('',$jaar,$this->formatGetal($waarden['lossing'],0),$this->formatGetal($waarden['rente'],0),$this->formatGetal($waarden['lossing']+$waarden['rente'],0)));
       $totalen['lossing'] +=$waarden['lossing'];
       $totalen['rente'] +=$waarden['rente'];
		}
		$this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
		$this->pdf->ln(2);
		$this->pdf->Row(array('','Totaal',$this->formatGetal($totalen['lossing'],0),$this->formatGetal($totalen['rente'],0),$this->formatGetal($totalen['lossing']+$totalen['rente'],0)));
$this->pdf->CellBorders = array();

    $this->pdf->setXY(20,65);
    $this->VBarDiagram(160,35,$cashflowHuidigjaar,vertaalTekst("Lopend jaar",$this->pdf->rapport_taal));
		$this->pdf->setXY(160,65);
    $this->VBarDiagram(160,35,$cashflowJaar,vertaalTekst("Langere termijn",$this->pdf->rapport_taal));
    $this->pdf->SetY(145);
	}


	function VBarDiagram($w, $h, $data,$titel)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
      $this->pdf->Multicell($w,5,$titel,'','C');
      $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Multicell(20,5,'X 1.000','','L');
      $this->pdf->setXY($xPositie,$yPositie);


      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $datumTotalen[$datum]+=$waarde;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          if($waarde < 0)
          {
            $verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
          }


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }

      $colors=array('rente'=>array(214,213,33),
      'lossing'=>array(129,138,16));

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      if($color == null)
      {
        $color=array(155,155,155);
      }
      $maxVal=max($datumTotalen);
      $minVal = 0;


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

      $unit = $hGrafiek / $maxVal * -1;
      


      $nulYpos =0;

      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      if($absUnit > 0 && $stapgrootte >0)
      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/1000)."",0,0,'L');
        $n++;
      }

    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+5.25,$legenda[$datum],45);

           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }


   $x1=$xPositie+25;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Rente';
   $vertaling['lossing']='Lossingen';

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=40;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
  }   
  
}
?>
