<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/17 11:20:00 $
File Versie					: $Revision: 1.5 $

$Log: RapportVHO_L73.php,v $
Revision 1.5  2019/04/17 11:20:00  rvv
*** empty log message ***

Revision 1.4  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.3  2018/08/04 11:54:53  rvv
*** empty log message ***

Revision 1.2  2018/04/08 07:29:47  rvv
*** empty log message ***

Revision 1.1  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.10  2017/11/04 17:40:21  rvv
*** empty log message ***

Revision 1.9  2017/10/25 15:59:05  rvv
*** empty log message ***

Revision 1.8  2017/10/08 08:15:06  rvv
*** empty log message ***

Revision 1.7  2017/10/07 17:36:35  rvv
*** empty log message ***

Revision 1.6  2017/10/07 16:54:34  rvv
*** empty log message ***

Revision 1.5  2017/09/30 16:31:15  rvv
*** empty log message ***

Revision 1.4  2017/08/26 17:37:43  rvv
*** empty log message ***

Revision 1.3  2017/07/01 17:03:24  rvv
*** empty log message ***

Revision 1.2  2017/06/21 16:10:57  rvv
*** empty log message ***

Revision 1.1  2017/06/10 18:09:58  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L73
{
	function RapportVHO_L73($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "Portefeuille overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
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
	  if ($VierDecimalenZonderNullen)
	  {
      $newDec=4;
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
       if($newDec>2)
         $newDec=2;
    
       return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}


	function printKop($title, $type="")
	{
		if(trim($title)=='' || trim($title)=='subleeg')
			return;

		$this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsizeSmall);
//		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
	//	$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsizeSmall);
	}

	function printSub($categorie,$data,$style='')
	{
		if($categorie=='subleeg')
			return '';

		$this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsizeSmall);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(90,4, 'Totaal '.$categorie);
		$this->pdf->SetX($this->pdf->marge);

		$resultaat=$data['fondsResultaat'] + $data['valutaResultaat'] + $data['dividend'];
		$resultaatProcent=($data['fondsResultaat'] + $data['valutaResultaat'] + $data['dividendCorrected'])/$data['historischeWaardeTotaal']*100;
		if($resultaat>0 && $resultaatProcent<0)
			$resultaatProcent=$resultaatProcent*-1;

		$resultaatProcentTxt=$this->formatGetal($resultaatProcent,2);
		if($categorie=='')
		{
			$resultaatProcentTxt='';
		}

		if(round($data['rente'])<>0)
      $renteTxt=$this->formatGetal($data['rente'],0);
		else
			$renteTxt='';
		
		if($categorie=='Liquiditeiten')
      $this->pdf->row(array('','','','','','',
                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                        '','','','','','','',
                        $this->formatGetal($data['weging'],1)
                      ));
		else
		$this->pdf->row(array('','','','','','',
											$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
											$renteTxt,
											$this->formatGetal($data['historischeWaardeTotaal'],0),
											$this->formatGetal($data['fondsResultaat'],0),
											$this->formatGetal($data['valutaResultaat'],0),
											$this->formatGetal($data['dividend'],0),
											$this->formatGetal($resultaat,0),
											$resultaatProcentTxt,
											$this->formatGetal($data['weging'],1)
										));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsizeSmall);
		$this->pdf->ln();
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
     Rekeningmutaties.Boekdatum <= '".  $this->rapportageDatum."' AND
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

		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen , Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM (Portefeuilles, Clienten)  Join Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$this->portefeuilledata['VerouderdeKoersDagen'];


		$this->pdf->widthB = array(14,54,12,15,19,19,19,16,19,18,15,18,15,15,12);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');


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
		$totaalWaarde = $totaalWaarde['totaal'];
		
		$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
		TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    if($DB->QRecords($query) >9)
		{
			$query="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
	    TijdelijkeRapportage.beleggingscategorie='AAND' AND	TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
			if($DB->QRecords($query) >15)
			{
				$subverdeling = "if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regio,
				if(TijdelijkeRapportage.beleggingscategorie='AAND',TijdelijkeRapportage.beleggingssector,''))";
				$subverdelingOmschrijving = "if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regioOmschrijving,
				if(TijdelijkeRapportage.beleggingscategorie='AAND',TijdelijkeRapportage.beleggingssectorOmschrijving,''))";
				$subverdelingVolgorde = "if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regioVolgorde,
				if(TijdelijkeRapportage.beleggingscategorie='AAND',TijdelijkeRapportage.beleggingssectorVolgorde,''))";
			}
			else
			{
				$subverdeling = "if(TijdelijkeRapportage.beleggingscategorie='AAND','',
				if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regio,'')) ";
				$subverdelingOmschrijving = "if(TijdelijkeRapportage.beleggingscategorie='AAND','',
				if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regioOmschrijving,'')) ";
				$subverdelingVolgorde = "if(TijdelijkeRapportage.beleggingscategorie='AAND','',
				if(TijdelijkeRapportage.beleggingscategorie='BEL-Aand',TijdelijkeRapportage.regioVolgorde,'')) ";

			}
		}
		else
		{
      $subverdeling="''";
      $subverdelingOmschrijving="''";
      $subverdelingVolgorde="''";
		}
 
		$query = "SELECT TijdelijkeRapportage.type,
		  TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.Valuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, 
			
			
			(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaalValuta,
			(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaal, 
			
			
			TijdelijkeRapportage.historischeWaarde,".
			"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.hoofdcategorie,
				  TijdelijkeRapportage.hoofdcategorieVolgorde,
				  Rekeningen.IBANnr,
				  Rekeningen.valuta as rekeningValuta,
				  Rekeningen.tenaamstelling,
				  TijdelijkeRapportage.beleggingscategorieOmschrijving,
				  round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
           $subverdeling as subverdeling,
           $subverdelingOmschrijving as subverdelingOmschrijving,
           $subverdelingVolgorde as subverdelingVolgorde ".
			" FROM TijdelijkeRapportage 
			LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening=Rekeningen.Rekening AND Rekeningen.consolidatie=0 
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde,
subverdelingVolgorde,TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		$DB->SQL($query);
		$DB->Query();
		$regels=array();
		$rentePerFonds=array();
		$renteRegel=array();
		while($data = $DB->nextRecord())
		{
			if($data['type']=='rente')
			{
				$rentePerFonds[$data['fonds']]=$data['actuelePortefeuilleWaardeEuro'];
			}
		  $regels[] = $data;

		}

		$totaalLagen=array('sub','cat','all');
		$somWaarden=array('actuelePortefeuilleWaardeEuro','historischeWaardeTotaal','fondsResultaat','valutaResultaat','weging','dividend','dividendCorrected','rente');
		$totalen=array('all'=>array());

		$n=0;
    
    $hoofdcategorien=array();
    foreach($regels as $data)
    {
      $hoofdcategorien[$data['hoofdcategorie']]=$data['hoofdcategorie'];
		}
		
		if(!in_array('ZAK',$hoofdcategorien))
		{
      $this->pdf->lastHoofdcategorie='VAR';
		}
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsizeSmall);
    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
    unset($this->pdf->lastHoofdcategorie);

    foreach($regels as $data)
		{

			//if($data['beleggingscategorieOmschrijving'] =='')
		//		$data['beleggingscategorieOmschrijving'] = 'leeg';
			if($data['subverdelingOmschrijving'] =='')
				$data['subverdelingOmschrijving']='subleeg';

			if($data['type']=='rekening')
			{
				//if($data['beleggingscategorieOmschrijving'] =='')
				//  $data['beleggingscategorieOmschrijving']='Liquiditeiten';
				//if($data['subverdelingOmschrijving'] =='')
			  //	$data['subverdelingOmschrijving']='Liquiditeiten';
			}

			if(!isset($lastSubCategorie) || $data['subverdelingOmschrijving'] <> $lastSubCategorie)
			{
				unset($this->pdf->fillCell);
				if(isset($lastSubCategorie))
					$this->printSub($lastSubCategorie,$totalen['sub'][$lastSubCategorie],'i');
				$totalen['sub'][$data['subverdelingOmschrijving']]=array();
			}

			if(!isset($lastCategorie) || $data['beleggingscategorieOmschrijving'] <> $lastCategorie)
			{
				unset($this->pdf->fillCell);
				if(isset($lastCategorie))
			  	$this->printSub($lastCategorie,$totalen['cat'][$lastCategorie],'b');
				$totalen['cat'][$data['beleggingscategorieOmschrijving']]=array();
			}

			if(isset($this->pdf->lastHoofdcategorie) && $data['hoofdcategorie']<>$this->pdf->lastHoofdcategorie)
			{
				$this->pdf->lastHoofdcategorie=$data['hoofdcategorie'];
				$this->pdf->addPage();
			}
			$this->pdf->lastHoofdcategorie=$data['hoofdcategorie'];



			if(!isset($lastCategorie) || $data['beleggingscategorieOmschrijving'] <> $lastCategorie)
			{
				if($this->pdf->getY() > 179)
					$this->pdf->addPage();
				$this->printKop($data['beleggingscategorieOmschrijving'],'b');
			}

			if(!isset($lastSubCategorie) || $data['subverdelingOmschrijving'] <> $lastSubCategorie)
			{
				if($this->pdf->getY() > 179)
					$this->pdf->addPage();
				$this->printKop('  '.$data['subverdelingOmschrijving'],'i');
			}

			$dividend=$this->getDividend($data['fonds']);
			$data['fondsResultaat'] = ($data['actuelePortefeuilleWaardeInValuta'] - $data['historischeWaardeTotaalValuta']) * $data['actueleValuta'] / $this->pdf->ValutaKoersEind;
			$data['valutaResultaat'] = $data['actuelePortefeuilleWaardeEuro'] - $data['historischeWaardeTotaal'] - $data['fondsResultaat'] ;
			$data['weging'] = ($data['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;
			$data['dividend']=	$dividend['totaal'];
			$data['dividendCorrected']=$dividend['corrected'];

			$data['rente']=$rentePerFonds[$data['fonds']];

			if($data['type']=='rente')
			{
        $renteRegel['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
				$data=array('actuelePortefeuilleWaardeEuro'=>0,'weging'=>$data['weging'],'beleggingscategorieOmschrijving'=>$data['beleggingscategorieOmschrijving'],'subverdelingOmschrijving'=>$data['subverdelingOmschrijving']);
    
				$renteRegel['weging'] +=$data['weging'];
			}
			elseif($data['type']=='rekening')
			{

				if($n%2 == 0)
					$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				else
					unset($this->pdf->fillCell);

				//if($data['tenaamstelling'] <> '')
				//	$data['fondsOmschrijving']=$data['tenaamstelling'].' '.$data['IBANnr'];
				//else
					$data['fondsOmschrijving'].=' '.$data['IBANnr'];

				$this->pdf->row(array('', $data['fondsOmschrijving'], $data['rekeningValuta'], '', '', '',	$this->formatGetal($data['actuelePortefeuilleWaardeEuro'], 0),'','','','','','','',$this->formatGetal($data['weging'], 1)));
				$data=array('actuelePortefeuilleWaardeEuro'=>$data['actuelePortefeuilleWaardeEuro'],'weging'=>$data['weging'],'subverdelingOmschrijving'=>$data['subverdelingOmschrijving'],'beleggingscategorieOmschrijving'=>$data['beleggingscategorieOmschrijving']);
		    $n++;
			}
			else
			{
				if($data['koersLeeftijd'] > $maxDagenOud && $data['actueleFonds'] <> 0)
					$markering="*";
				else
					$markering="";


				$omschrijvingWidth = $this->pdf->GetStringWidth($data['fondsOmschrijving']);
				$cellWidth = $this->pdf->widths[1] - 2;
				$omschrijving='';
				if ($omschrijvingWidth > $cellWidth)
				{
					$dotWidth = $this->pdf->GetStringWidth('...');
					$chars = strlen($data['fondsOmschrijving']);
					$newOmschrijving = $data['fondsOmschrijving'];
					for ($i = 3; $i < $chars; $i++)
					{
						$omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
						if ($cellWidth > ($omschrijvingWidth + $dotWidth))
						{
							$omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
							break;
						}
					}
				}
				else
				{
					$omschrijving = $data['fondsOmschrijving'];
				}

				if($n%2 == 0)
			  	$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				else
					unset($this->pdf->fillCell);

				$resultaat=$data['fondsResultaat'] + $data['valutaResultaat'] + $data['dividend'];
				$resultaatProcent=($data['fondsResultaat'] + $data['valutaResultaat'] + $data['dividendCorrected'])/abs($data['beginPortefeuilleWaardeEuro'])*100;


				if($data['hoofdcategorie']=='ZAK')
					$renteTxt='';
				else
					$renteTxt=$this->formatGetal($data['rente'], 0);

				$this->pdf->row(array($this->formatAantal($data['totaalAantal'], 0, true),
													$omschrijving,
													$data['Valuta'],
													$this->formatGetal($data['actueleFonds'], 2).$markering,
													$this->formatGetal($data['beginwaardeLopendeJaar'], 2),
													$this->formatGetal($data['historischeWaarde'], 2),
													$this->formatGetal($data['actuelePortefeuilleWaardeEuro'], 0),
													$renteTxt,
													$this->formatGetal($data['historischeWaardeTotaal'], 0),
													$this->formatGetal($data['fondsResultaat'], 0),
													$this->formatGetal($data['valutaResultaat'], 0),
													$this->formatGetal($data['dividend'], 0),
													$this->formatGetal($resultaat, 0),
													$this->formatGetal($resultaatProcent, 1),
													$this->formatGetal($data['weging'], 1)
												));
				$n++;
			}

			foreach($totaalLagen as $laag)
			{
				foreach ($somWaarden as $veld)
				{
			//		echo "$laag | $veld | $omschrijving | ".$data[$veld]."<br>\n";
					$totalen[$laag][$data['beleggingscategorieOmschrijving']][$veld]+=$data[$veld];
					$totalen[$laag][$data['subverdelingOmschrijving']][$veld]+=$data[$veld];
					$totalen[$laag]['totaal'][$veld]+=$data[$veld];
				}
			}


			$lastCategorie=$data['beleggingscategorieOmschrijving'];
			$lastSubCategorie=$data['subverdelingOmschrijving'];
		
		}
		//listarray($totalen['cat']);exit;
		if($n%2 == 0)
			$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		else
			unset($this->pdf->fillCell);
    
    $totalen['cat'][$lastCategorie]['actuelePortefeuilleWaardeEuro']+=$renteRegel['actuelePortefeuilleWaardeEuro'];
    $totalen['all']['totaal']['actuelePortefeuilleWaardeEuro']+=$renteRegel['actuelePortefeuilleWaardeEuro'];
		$this->pdf->row(array('',"Opgelopen rente", '', '', '', '',	$this->formatGetal($renteRegel['actuelePortefeuilleWaardeEuro'], 0),'','','','','','','',$this->formatGetal($renteRegel['weging'], 1)));
		unset($this->pdf->fillCell);
		if(isset($lastCategorie))
			$this->printSub($lastCategorie,$totalen['cat'][$lastCategorie],'b');
		$totalen['cat'][$data['beleggingscategorieOmschrijving']]=array();



		$this->printSub('',$totalen['all']['totaal'],'b');

		$this->pdf->ln();
		$this->pdf->MultiCell(297-$this->pdf->marge*2,5,vertaalTekst("Koersen met een * zijn ouder dan $maxDagenOud dagen",$this->pdf->rapport_taal),0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
