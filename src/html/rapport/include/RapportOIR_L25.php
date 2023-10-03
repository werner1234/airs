<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/19 15:02:02 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIR_L25.php,v $
Revision 1.3  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.2  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.1  2018/02/11 13:27:19  rvv
*** empty log message ***

Revision 1.4  2016/10/26 16:13:40  rvv
*** empty log message ***

Revision 1.3  2016/10/24 06:35:13  rvv
*** empty log message ***

Revision 1.2  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.1  2016/10/16 15:11:30  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L25
{
	function RapportOIR_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
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



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
	{
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->SetTextColor($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
          if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
          {
            $this->pdf->CellBorders = array('','','','','SUB','SUB');
            $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc)));
          }
          else
          {
          $this->pdf->CellBorders = array('','','','','SUB','SUB','','','SUB','SUB','SUB','','','SUB');
          if($resultaat)
          {
            $resultaatProcent=$this->formatGetal((($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
            $resultaatWaarde=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal);
            $resultaatDividend=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);

						$historischeMutatie=($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['historischeWaardeTotaal']+$categorieTotaal[$lastCategorieOmschrijving]['totaalDividendCorrected']);
						$rendementTotaal=$historischeMutatie/$categorieTotaal[$lastCategorieOmschrijving]['historischeWaardeTotaal']*100;
						$rendementTotaalTxt=$this->formatGetal($rendementTotaal,1);
					}
          else
          {
            $this->pdf->CellBorders = array('','','','','SUB','SUB','','','','','','');
            $resultaatProcent='';
            $resultaatWaarde='';
            $resultaatDividend='';
          }
          

          $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc),'','',
          $resultaatWaarde,
          $resultaatDividend,
          $resultaatProcent,'','',$rendementTotaalTxt));
          }
          $this->pdf->CellBorders = array();
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		return $totaalB;
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    
    if($type=='BI')
      $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
    else
      $this->pdf->SetTextColor($this->pdf->rapport_kop2_fontcolor['r'],$this->pdf->rapport_kop2_fontcolor['g'],$this->pdf->rapport_kop2_fontcolor['b']);
    
    $this->pdf->row(array($title));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0);
  
	}
  
  function getDividend($fonds,$vanafBegin=false)
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

		if($vanafBegin==false)
			$startDatumFilter="Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND ";
		else
			$startDatumFilter='';

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND $startDatumFilter     
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
  //  listarray($aantal);
//    if($fonds=='Delta Lloyd VAR 12-42')
//    {
//     echo $fonds." ".$rente[$this->rapportageDatum].' - '.$rente[$this->rapportageDatumVanaf]."<br>\n"; 
//      echo "$query <br>\n";// exit;
//    }  
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

	function writeRapport()
	{
		global $__appvar;
		$aandelenCategorie='IndAand';
   // $this->pdf->rapport_fontsize-=1;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
//hoofdscategorieVolgorde.Afdrukvolgorde as hoofdcategorieAfdrukVolgorde,
		$query = "SELECT
TijdelijkeRapportage.type,TijdelijkeRapportage.rekening,
if(TijdelijkeRapportage.type='rente', ifnull(300,TijdelijkeRapportage.hoofdcategorieVolgorde+100),
if(TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten',400,
TijdelijkeRapportage.hoofdcategorieVolgorde))  as hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * 
TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers)
 AS historischeWaardeTotaal,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
						" TijdelijkeRapportage.rentedatum, ".
		  " TijdelijkeRapportage.renteperiode, ".
				" TijdelijkeRapportage.Valuta, 
        TijdelijkeRapportage.fondsEenheid, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
				   Valutas.Valutateken ,
           TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
           if(TijdelijkeRapportage.beleggingscategorie='".$aandelenCategorie."',TijdelijkeRapportage.beleggingssectorVolgorde,200) as sectorVolgorde".
				" FROM TijdelijkeRapportage
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY hoofdcategorieAfdrukVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie ,sectorVolgorde,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
//" TijdelijkeRapportage.type =  'fondsen' AND ".
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$aandelenOnschrijving='Aandelen';

		while($data = $DB->NextRecord())
		{
			if($data['beleggingscategorie']==$aandelenCategorie)
				$aandelenOnschrijving=$data['categorieOmschrijving'];
		  //categorietotalen
		  if($data['type']=='fondsen' && $data['beleggingscategorie'] <> 'Liquiditeiten')
		    $fondsData[]=$data;
		  elseif($data['type']=='rente')
		    $renteData[]=$data;
		  else
		    $liquiditeitenData[]=$data;
		}
    
		foreach ($fondsData as $data)
		{
		  		  $dividend=$this->getDividend($data['fonds'],false);
			$dividendTotaal=$this->getDividend($data['fonds'],true);
		  $dividend=$this->getDividend($data['fonds']);
      if($lastCategorieOmschrijving==$aandelenOnschrijving && $data['beleggingssectorOmschrijving'] != $lastSectorOmschrijving && $lastSectorOmschrijving !='' && is_array($sectorTotaal[$lastSectorOmschrijving]))
         $this->printSubTotaal($lastSectorOmschrijving,$sectorTotaal,$totaalWaarde);
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
        $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving)
        $this->printKop($data['categorieOmschrijving'],'B',false);

      if($data['beleggingscategorie'] == $aandelenCategorie && $data['beleggingssectorOmschrijving'] <> $lastSectorOmschrijving)
      {
        if($lastSectorOmschrijving=='')
          $this->pdf->Ln();
        $this->printKop($data['beleggingssectorOmschrijving'],'B',false);
      }
     	$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] ;

//			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($data['beginPortefeuilleWaardeEuro'] /100));

			$procentResultaatTotaal = (($data['actuelePortefeuilleWaardeEuro'] - $data['historischeWaardeTotaal'] + $dividendTotaal['corrected']) / ($data['historischeWaardeTotaal'] /100));
    	if($data['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaat = -1 * $procentResultaat;
			$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

			$resultaattxt = "";
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($resultaat,$this->pdf->rapport_VOLK_decimaal);


      if($data['fondsEenheid']=='0.01')
        $percentageTeken=' %';
      else
        $percentageTeken='';
         
			$this->pdf->row(array("  ".$data['fondsOmschrijving'],
				                $this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valutateken'],
												$this->formatGetal($data['actueleFonds'],2).$percentageTeken,
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$this->formatGetal($data['beginwaardeLopendeJaar'],2).$percentageTeken,
												$resultaattxt,
                        $this->formatGetal($dividend['totaal'],0),
												$procentResultaattxt,
												"",
												$this->formatGetal($data['historischeWaarde'],2).$percentageTeken,
											  $this->formatGetal($procentResultaatTotaal,1)));
    unset($this->pdf->fillCell);                    
		$categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
		$categorieTotaal[$data['categorieOmschrijving']]['historischeWaardeTotaal'] +=$data['historischeWaardeTotaal'];
		$categorieTotaal[$data['categorieOmschrijving']]['totaalDividend'] +=$dividendTotaal['totaal'];
		$categorieTotaal[$data['categorieOmschrijving']]['totaalDividendCorrected'] +=$dividendTotaal['corrected'];

		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['historischeWaardeTotaal'] +=$data['historischeWaardeTotaal'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['totaalDividend'] +=$dividendTotaal['totaal'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['totaalDividendCorrected'] +=$dividendTotaal['corrected'];

        $sectorTotaal[$data['beleggingssectorOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$sectorTotaal[$data['beleggingssectorOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $sectorTotaal[$data['beleggingssectorOmschrijving']]['dividend'] +=$dividend['totaal'];
    $sectorTotaal[$data['beleggingssectorOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];




    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
    $lastSectorOmschrijving=$data['beleggingssectorOmschrijving'];
		}
    
		$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);

/*
		// selecteer rente
		$query = "SELECT Valutas.Valutateken,TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fondsOmschrijving, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
		" TijdelijkeRapportage.rentedatum, ".
		" TijdelijkeRapportage.renteperiode, ".
		" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		"  ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
*/

		//if($DB->records() > 0)
		if(count($renteData) > 0)
		{
		  $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
			$totaalRenteInValuta = 0 ;
			//while($data = $DB->NextRecord())
			foreach ($renteData as $data)
			{
			  	$rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
					if($subdata['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
						$rentePeriodetxt .= " / ".$data['renteperiode'];

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->Cell($this->pdf->widths[0],4,"  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt, 0,0, "L");
					$this->pdf->setX($this->pdf->marge);
          
        
					$this->pdf->row(array('','',
					              $data['Valutateken'],'',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','','','','','','',''));
					$categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
		}


/*
		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening as zoekRekening, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);


		$DB1->SQL($query);
		$DB1->Query();
*/
		//if($DB1->records() > 0)
		if(count($liquiditeitenData) > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			/*
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}
			foreach($liqiteitenBuffer as $data)
      */
					$DB1 = new DB();
			foreach($liquiditeitenData as $data)
			{
			  if($data['beginPortefeuilleWaardeEuro'] == 0)
			  {
			    $query="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = '".$data['rekening']."' AND type='rekening' LIMIT 1";
    		  $DB1->SQL($query);
		      $DB1->Query();
		      $begin = $DB1->NextRecord();
		      $data['beginPortefeuilleWaardeEuro']=$begin['actuelePortefeuilleWaardeEuro'];
		    }
            
					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$data['rekening'],'',$data['Valutateken'],'',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','',
												'','','','','',''

												));
					$categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
					$categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
		} // einde liquide

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}

		$this->pdf->CellBorders = array('','','','','SUB','SUB');
		$this->pdf->row(array("Totale actuele waarde portefeuille",'','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();


		if($this->pdf->rapport_VOLK_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;


		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,true);
		}
    
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
				printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
    //$this->pdf->rapport_fontsize+=1;
	}
}
?>