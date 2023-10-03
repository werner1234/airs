<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.8 $

$Log: RapportPERF_L72.php,v $
Revision 1.8  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.7  2017/10/26 07:54:10  rvv
*** empty log message ***

Revision 1.6  2017/10/25 16:00:03  rvv
*** empty log message ***

Revision 1.5  2017/06/10 18:08:40  rvv
*** empty log message ***

Revision 1.4  2017/06/07 16:28:08  rvv
*** empty log message ***

Revision 1.3  2017/05/31 16:09:43  rvv
*** empty log message ***

Revision 1.27  2017/03/25 16:25:17  rvv
*** empty log message ***

Revision 1.25  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.24  2017/02/06 07:28:21  rvv
*** empty log message ***

Revision 1.23  2017/02/04 19:11:39  rvv
*** empty log message ***

Revision 1.22  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.21  2015/07/05 07:44:49  rvv
*** empty log message ***

Revision 1.20  2015/06/20 15:34:20  rvv
*** empty log message ***

Revision 1.19  2015/04/15 18:23:22  rvv
*** empty log message ***

Revision 1.18  2014/11/30 13:19:33  rvv
*** empty log message ***

Revision 1.17  2014/11/23 14:13:22  rvv
*** empty log message ***

Revision 1.16  2014/11/12 16:50:48  rvv
*** empty log message ***

Revision 1.15  2014/05/14 15:28:41  rvv
*** empty log message ***

Revision 1.14  2013/05/29 15:49:59  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L72.php");

class RapportPERF_L72
{
	function RapportPERF_L72($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_jaar  =date('Y',$this->pdf->rapport_datumvanaf);
		$this->pdf->excelData 	= array();

		//	$this->pdf->rapport_titel = "Performance overzicht";
		$this->pdf->rapport_titel = vertaalTekst("Performance overzicht",$this->pdf->rapport_taal);//." ".date("d-m-Y",$this->pdf->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("d-m-Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec,$procent=false,$nulTonen=false)
	{
		if($waarde==0 && $nulTonen==false)
			return;
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
			$data.="%";
		return $data;
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
		if($waarde==0)
			return;
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



	function printSubTotaal($lastCategorieOmschrijving,$allData,$style='')
	{
		if($lastCategorieOmschrijving != 'Totaal')
		{
			$prefix='Subtotaal';
			$this->pdf->CellBorders = array('','','','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS');
		}
		else
		{
			$prefix='';
			$this->pdf->CellBorders = array('','','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
		}

		$this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

		$this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
		$this->pdf->setX($this->pdf->marge);

		$data=$allData['perf'];


		if($data['bijdrage'] < 0)
			$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
		else
			$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

		$this->pdf->row(array(substr(vertaalTekst($categorieData['omschrijving'],$this->pdf->rapport_taal),0,25),
											substr($categorieData['fondsOmschrijving'][$id],0,25),
											$categorieData['fondsValuta'][$id],
											$this->formatGetal($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal,false,true),
											$this->formatGetal($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal,false,true),
											$this->formatGetal($data['stort'],0,false,true),
											$this->formatGetal($data['resultaat'],0,false,true),
											$this->formatGetal($data['gemWaarde'],0,false,true),
											$this->formatGetal($data['procent'],2,false,true),//$data['resultaat']/$data['gemWaarde']*100,3,false,true),
											$this->formatGetal($data['weging'],2,true,true),
											$this->formatGetal($data['bijdrage'],2,true,true)));

		$this->pdf->CellBorders = array();
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
			$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
		$this->pdf->Cell(40,4,vertaalTekst($title,$this->pdf->rapport_taal),0,1,'L');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}


	function writeRapport()
	{
		global $__appvar,$USR;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();
		$this->pdf->templateVars['HSEPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		$this->pdf->SetLineWidth(0.1);

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde['begin'] = $totaalWaarde['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$totaalWaarde['eind'] = $totaal['totaal'];

		$query = "SELECT ".
			"SUM(((TO_DAYS('".$this->rapportageDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$this->rapportageDatum."') - TO_DAYS('".$this->rapportageDatumVanaf."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
			"FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();
		$gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];

		$this->totalen['begin']=$totaalWaarde['begin'];
		$this->totalen['eind']=$totaalWaarde['eind'];
		$this->totalen['gemiddeldeWaarde']=$gemiddelde;



		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingssectorPerFonds.Regio,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
			$perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
			$perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
			$perRegio[$data['Hoofdcategorie']]['omschrijving']=$data['regioOmschrijving']; //$data['Regio']
			$perRegio[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];  //$data['Regio']
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];//[$data['Regio']]
			$alleData['fondsen'][]=$data['Fonds'];
		}


		$query="SELECT SUM(Rekeningmutaties.bedrag) as saldo,
Rekeningmutaties.rekening,
Rekeningen.Inactief,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
rekeningBank.Omschrijving as depotbankOmschrijving
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Inner Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND Rekeningen.Memoriaal=0 AND  
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Beleggingscategorien.Afdrukvolgorde,depotbankOmschrijving,
Rekeningen.Valuta,Rekeningen.Rekening";

		$DB->SQL($query);//Rekeningen.Inactief=0 AND
		$DB->Query();
		while($data = $DB->NextRecord())
		{
			//if(round($data['saldo'],2) <> 0.00 || $data['Inactief']==0)
			//{
				$perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving'] = $data['hoofdCategorieOmschrijving'];
				$perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][] = $data['rekening'];
				$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving'] = $data['categorieOmschrijving'];
				$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['rekeningen'][] = $data['rekening'];
				$alleData['rekeningen'][] = $data['rekening'];
			//}
		}

		$this->totalen['gemiddeldeWaarde']=0;
		
		$att=new ATTberekening_L72($this);
//		$att=new ATTberekening_L72($this);


		$alleData['categorie']='totaal';
		$perfTotaal=$att->fondsPerformanceOld($alleData,$this->rapportageDatumVanaf,$this->rapportageDatum,true,true,$this->pdf->rapportageValuta);

		//listarray($perfTotaal);exit;

	//	$perfTotaal=$this->fondsPerformance($alleData,true);

	//	listarray($perfTotaal);

		$this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];



		foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
		{
			//$perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($hoofdcategorieData);
			$perHoofdcategorie[$hoofdCategorie]['perf'] = $att->fondsPerformanceOld($hoofdcategorieData,$this->rapportageDatumVanaf,$this->rapportageDatum,true,true,$this->pdf->rapportageValuta);
		}
		/*
       foreach ($perRegio as $hoofdCategorie=>$regioData)
         foreach ($regioData as $regio=>$regioWaarden)
           $perRegio[$hoofdCategorie][$regio]['perf'] = $this->fondsPerformance($regioWaarden);
    */

		foreach ($perCategorie as $hoofdCategorie=>$regioData)
			foreach ($regioData as $categorie=>$categorieData)
			{
				//$perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->fondsPerformance($categorieData); //[$regio]
				$perCategorie[$hoofdCategorie][$categorie]['perf'] = $att->fondsPerformanceOld($categorieData,$this->rapportageDatumVanaf,$this->rapportageDatum,true,true,$this->pdf->rapportageValuta);
			}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		foreach ($perHoofdcategorie as $hoofdcategorie=>$hoofdcategorieData)
		{
			$data=$hoofdcategorieData['perf'];
			if($data['bijdrage'] < 0)
				$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
			else
				$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

			$totaalSom['beginwaarde'] += $data['beginwaarde'];
			$totaalSom['eindwaarde'] += $data['eindwaarde'];
			$totaalSom['stort'] += $data['stort'];
			$totaalSom['gerealiseerd'] += $data['gerealiseerd'];
			$totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
			$totaalSom['kosten'] += $data['kosten'];
			$totaalSom['resultaat'] += $data['resultaat'];
			$totaalSom['gemWaarde'] += $data['gemWaarde'];
			$totaalSom['weging'] += $data['weging'];
			$totaalSom['bijdrage'] += $data['bijdrage'];
		}


		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


	//	$perfTotaal=$totaalSom;


		$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T','T','T','T');

		//if($perfTotaal['bijdrage'] < 0)
		//   $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
		// else
		//   $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

		if($this->pdf->debug == true)
			listarray($perRegio);

		unset($this->pdf->CellBorders);

		foreach ($perCategorie as $hoofdcategorie=>$categorieData)
		{

			$this->printKop($perHoofdcategorie[$hoofdcategorie]['omschrijving'],'BI',true);
			//foreach ($regioData as $regio=>$categorieData)
			//{

			if($lastHoofdcategorie!=$hoofdcategorie)
				$extraRegel=false;
			else
				$extraRegel=true;

			foreach ($categorieData as $categorie=>$fondsData)
			{

				if($categorie!=$lastCategorie)
					$this->printKop( $perCategorie[$hoofdcategorie][$categorie]['omschrijving'],'');
				$lastCategorie=$categorie;
				$widthsBackup=$this->pdf->widths;
				$newIndex=0;
				$newWidths=array();

				foreach ($this->pdf->widths as $index=>$waarde)
				{
					if($index < 2)
						$newIndex+=$waarde;
					else
						$newIndex=$waarde;
					if($index == 0)
						$newWidths[]=0;
					else
						$newWidths[]=$newIndex;
				}

				$this->pdf->widths=$newWidths;

				foreach ($fondsData['fondsen'] as $id=>$fonds)
				{
					$tmp=array();
					$tmp['fondsen']=array($fonds);
				//	$data=$this->fondsPerformance($tmp);
					$data = $att->fondsPerformanceOld($tmp,$this->rapportageDatumVanaf,$this->rapportageDatum,true,true,$this->pdf->rapportageValuta,$fonds);
		//			listarray($data);
//echo $this->pdf->getY(). " ".$fondsData['fondsOmschrijving'][$id]."<br>\n";
					if($this->pdf->getY() > 185)
						$this->pdf->AddPage();
					$this->pdf->widths=$newWidths;

					//  echo $this->pdf->getX()." ".substr($fondsData['fondsOmschrijving'][$id],0,30)." <br>\n";

					if(round($data['beginwaarde'],2) <> 0 || round($data['eindwaarde'],2) <> 0 || round($data['stort'],2) <> 0 || round($data['resultaat'],2) <> 0 || round($data['gemWaarde'],2) <> 0 )
					{

			//			listarray($data);
						$this->pdf->row(array('','    '.substr($fondsData['fondsOmschrijving'][$id],0,50),
															$fondsData['fondsValuta'][$id],
															$this->formatGetal($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
															$this->formatGetal($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
															$this->formatGetal($data['stort'],0),
															$this->formatGetal($data['resultaat'],0),
															$this->formatGetal($data['gemWaarde'],0),
															$this->formatGetal($data['procent'],2),//$data['resultaat']/$data['gemWaarde']*100,2),
															$this->formatGetal($data['weging'],2,true),
															$this->formatGetal($data['bijdrage'],2,true)));

						$this->pdf->excelData[]=array($fondsData['fondsOmschrijving'][$id],
							$fondsData['fondsValuta'][$id],
							round($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
							round($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
							round($data['stort'],0),
							round($data['resultaat'],0),
							round($data['gemWaarde'],0),
							round($data['procent'],2),//$data['resultaat']/$data['gemWaarde']*100,2),
							round($data['weging']*100,2),
							round($data['bijdrage']*100,2));
					}

				}
				$this->pdf->widths=$widthsBackup;

				$rekeningData=array();
				$totaalRekeningen=0;
				foreach ($fondsData['rekeningen'] as $id=>$rekening)
				{
					$tmp=array();
					$tmp['rekeningen']=array($rekening);
					//$data=$this->fondsPerformance($tmp);
					$data = $att->fondsPerformanceOld($tmp,$this->rapportageDatumVanaf,$this->rapportageDatum,true,true,$this->pdf->rapportageValuta);
					$rekeningData[$id]=array('perf'=>$data,'rekening'=>$rekening);
					$rekeningWaarde[$id]=$data['eindwaarde'];
					$totaalRekeningen+=$data['eindwaarde'];
				}
				//arsort($rekeningWaarde);


				$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
				$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
				$DB = new DB();
				$DB->SQL($q);
				$DB->Query();
				$kleuren = $DB->LookupRecord();
				$allekleuren = unserialize($kleuren['grafiek_kleur']);
				$OIVKleur = $allekleuren['OIV'];

				//foreach ($fondsData['rekeningen'] as $id=>$rekening)
				$aantalRegels=0;
				foreach ($rekeningWaarde as $id=>$waarde)
				{
					$fullRekeningData=$rekeningData[$id];
					$rekening=$fullRekeningData['rekening'];
					$data=$fullRekeningData['perf'];

					$query="SELECT
Rekeningen.Rekening,Rekeningen.Valuta,
if(Rekeningen.Depotbank <> '',rekeningBank.Omschrijving, Depotbanken.Omschrijving) as Omschrijving
FROM
Rekeningen
Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
Inner Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
WHERE Rekeningen.Rekening='$rekening' AND Portefeuilles.Portefeuille <> 'C_$USR'";
					$DB->SQL($query);
					$depot=$DB->lookupRecord();

					//$tmp=array();
					//$tmp['rekeningen']=array($rekening);
					//$data=$this->fondsPerformance($tmp);
					if($data['bijdrage'] < 0)
						$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
					else
						$this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

					if($_POST['anoniem'] !=1)
						$rekening=$depot['Omschrijving'].' '.substr($fondsData['rekeningen'][$id],0,strlen($fondsData['rekeningen'][$id])-3);
					else
						$rekening="Effectenrekening";


/*

					$valutaVerdeling[$depot['Valuta']]+=$data['eindwaarde']/$totaalRekeningen*100;
					$valutaDepotVerdeling[$depot['Valuta']]['waarde']+=$data['eindwaarde'];
					$valutaDepotVerdeling[$depot['Valuta']]['percentage']+=$data['eindwaarde']/$totaalRekeningen*100;
					$valutaDepotVerdeling[$depot['Valuta']]['depotbanken'][$rekening]['waarde']=$data['eindwaarde'];
					$valutaDepotVerdeling[$depot['Valuta']]['depotbanken'][$rekening]['percentage']=$data['eindwaarde']/$totaalRekeningen*100;
*/
					$aantalRegels++;

					if($this->pdf->getY() > 185)
						$this->pdf->AddPage();

					$this->pdf->row(array('','    '.$rekening,
														$depot['Valuta'],
														$this->formatGetal($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($data['stort'],0),
														$this->formatGetal($data['resultaat'],0),
														$this->formatGetal($data['gemWaarde'],0),
														$this->formatGetal($data['procent'],2),//$data['resultaat']/$data['gemWaarde']*100,2),
														$this->formatGetal($data['weging'],2,true),
														$this->formatGetal($data['bijdrage'],2,true)));

					$this->pdf->excelData[]=array($rekening,
						$depot['Valuta'],
						round($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
						round($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
						round($data['stort'],0),
						round($data['resultaat'],0),
						round($data['gemWaarde'],0),
						round($data['procent'],2),//$data['resultaat']/$data['gemWaarde']*100,2),
						round($data['weging']*100,2),
						round($data['bijdrage']*100,2));
				}
				if($this->pdf->getY() > 185)
				{
					$this->pdf->AddPage();
					$this->pdf->Ln();
				}

				$this->pdf->widths=$widthsBackup;
				$this->printSubTotaal($perCategorie[$hoofdcategorie][$categorie]['omschrijving'],$perCategorie[$hoofdcategorie][$categorie]);
			}
//listarray($perHoofdcategorie[$hoofdcategorie]);
			// $this->printSubTotaal($perRegio[$hoofdcategorie][$regio]['omschrijving'],$perRegio[$hoofdcategorie][$regio]);
			//}
			//  echo $this->pdf->getY()." ".$perHoofdcategorie[$hoofdcategorie]['omschrijving']." ".$perHoofdcategorie[$hoofdcategorie]."<br>\n";ob_flush();
			if($this->pdf->getY() > 185)
			{
				$this->pdf->AddPage();
				$this->pdf->Ln();
			}
			$this->printSubTotaal($perHoofdcategorie[$hoofdcategorie]['omschrijving'],$perHoofdcategorie[$hoofdcategorie],'BI');
			$lastHoofdcategorie=$hoofdcategorie;
		}

		if($this->pdf->getY() > 185)
		{
			$this->pdf->AddPage();
			$this->pdf->Ln();
		}
		$this->printSubTotaal('Totaal',array('perf'=>$perfTotaal),'BI');


		$benodigdeHoogte=($aantalRegels+count($valutaVerdeling))*4;
		$y = $this->pdf->getY()+10;
		if($y > 140 || ($y+$benodigdeHoogte > 180))
		{
			$this->pdf->addPage();
			$y=$this->pdf->getY()+10;
		}


//$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);



		//$this->pdf->SetXY(210, $this->pdf->headerStart);
/*
		$grafiekKleur=array();
		foreach ($valutaVerdeling as $valuta=>$percentage)
		{
			$grafiekKleur[]=array($OIVKleur[$valuta]['R']['value'],$OIVKleur[$valuta]['G']['value'],$OIVKleur[$valuta]['B']['value']);
			$valutaDepotVerdeling[$valuta]['kleur']=array($OIVKleur[$valuta]['R']['value'],$OIVKleur[$valuta]['G']['value'],$OIVKleur[$valuta]['B']['value']);
		}
		//listarray($OIVKleur);


		// listarray($valutaVerdeling);
		// listarray($valutaDepotVerdeling);

		$startX=140;
		$width=40;
		$height=40;
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
		$this->pdf->setXY($startX+5,$y-3);
		$this->pdf->Cell(130,4,vertaalTekst('Verdeling liquiditeiten over valuta\'s', $this->pdf->rapport_taal),0,0,"C");
		$this->pdf->setXY($startX,$y);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->setX($startX);
		$this->PieChart($width, $height, $valutaVerdeling, '%l (%p)', $grafiekKleur,$valutaDepotVerdeling);
		$hoogte = ($this->pdf->getY() - $y) + 8;
		$this->pdf->setY($y);
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$this->pdf->setX($startX);

*/

		unset($this->pdf->CellFontColor);

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
			$actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
	}


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
	{
		// loopje over Grootboekrekeningen Opbrengsten = 1
		if(is_array($fonds))
			$fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
		elseif($fonds!='')
			$fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
		else
			$fondsenWhere='';

		if ($this->pdf->rapportageValuta <> 'EUR')
		{
			$koersQuery =	", (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";
			$startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
			$eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
		}
		else
		{
			$koersQuery = ", 1 as Rapportagekoers";
			$startValutaKoers= 1;
			$eindValutaKoers= 1;
		}

		$query = "SELECT Fondsen.Omschrijving, ".
			"Fondsen.Fondseenheid, ".
			"Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
			"Rekeningmutaties.Fondskoers, ".
			"Rekeningmutaties.Debet as Debet, ".
			"Rekeningmutaties.Credit as Credit, ".
			"Rekeningmutaties.Valutakoers $koersQuery ".
			"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND $fondsenWhere AND ".
			"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
			"Rekeningmutaties.Transactietype <> 'B' AND ".
			"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
			"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
			"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
			"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);

//echo $query;exit;


		$DB->Query();

		// haal koersresultaat op om % te berekenen


		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

		foreach ($buffer as $mutaties)
		{
			$mutaties[Aantal] = abs($mutaties[Aantal]);
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
			//$mutaties['Rapportagekoers']=1;

			switch($mutaties[Transactietype])
			{
				case "A" :
					// Aankoop
					$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
					$t_aankoop_koers					= $mutaties[Fondskoers];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if($t_aankoop_waarde > 0)
						$aankoop_koers 					= $t_aankoop_koers;
					if($t_aankoop_waardeinValuta > 0)
						$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
					if($t_aankoop_koers > 0)
						$aankoop_waarde 				= $t_aankoop_waarde;
					break;
				case "A/O" :
					// Aankoop / openen
					$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
					$t_aankoop_koers					= $mutaties[Fondskoers];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if($t_aankoop_waarde > 0)
						$aankoop_koers 					= $t_aankoop_koers;
					if($t_aankoop_waardeinValuta > 0)
						$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
					if($t_aankoop_koers > 0)
						$aankoop_waarde 				= $t_aankoop_waarde;
					break;
				case "A/S" :
					// Aankoop / sluiten
					$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
					$t_aankoop_koers					= $mutaties[Fondskoers];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if($t_aankoop_waarde > 0)
						$aankoop_koers 					= $t_aankoop_koers;
					if($t_aankoop_waardeinValuta > 0)
						$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
					if($t_aankoop_koers > 0)
						$aankoop_waarde 				= $t_aankoop_waarde;

					break;
				case "B" :
					// Beginstorting
					break;
				case "D" :
				case "S" :
					// Deponering
					$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
					$t_aankoop_koers					= $mutaties[Fondskoers];

					$totaal_aankoop_waarde += $t_aankoop_waarde;

					if($t_aankoop_waarde > 0)
						$aankoop_koers 					= $t_aankoop_koers;
					if($t_aankoop_waardeinValuta > 0)
						$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
					if($t_aankoop_waarde > 0)
						$aankoop_waarde 				= $t_aankoop_waarde;
					break;
				case "L" :
					// Lichting
					$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
					$t_verkoop_koers					= $mutaties[Fondskoers];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if($t_verkoop_koers > 0)
						$verkoop_koers 					= $t_verkoop_koers;
					if($t_verkoop_waardeinValuta > 0)
						$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
					if($t_verkoop_waarde > 0)
						$verkoop_waarde 				= $t_verkoop_waarde;
					break;
				case "V" :
					// Verkopen
					$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
					$t_verkoop_koers					= $mutaties[Fondskoers];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if($t_verkoop_koers > 0)
						$verkoop_koers 					= $t_verkoop_koers;
					if($t_verkoop_waardeinValuta > 0)
						$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
					if($t_verkoop_waarde > 0)
						$verkoop_waarde 				= $t_verkoop_waarde;
					break;
				case "V/O" :
					// Verkopen / openen
					$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
					$t_verkoop_koers					= $mutaties[Fondskoers];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if($t_verkoop_koers > 0)
						$verkoop_koers 					= $t_verkoop_koers;
					if($t_verkoop_waardeinValuta > 0)
						$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
					if($t_verkoop_waarde > 0)
						$verkoop_waarde 				= $t_verkoop_waarde;
					break;
				case "V/S" :
					// Verkopen / sluiten
					$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
					$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
					$t_verkoop_koers					= $mutaties[Fondskoers];

					$totaal_verkoop_waarde += $t_verkoop_waarde;

					if($t_verkoop_koers > 0)
						$verkoop_koers 					= $t_verkoop_koers;
					if($t_verkoop_waardeinValuta > 0)
						$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
					if($t_verkoop_waarde > 0)
						$verkoop_waarde 				= $t_verkoop_waarde;
					break;
				default :
					$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
				$mutaties['Transactietype'] == "V" ||
				$mutaties['Transactietype'] == "V/S" ||
				$mutaties['Transactietype'] == "A/S")
			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta,$rapportageDatumVanaf);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
				{
					$historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
					$beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
				}
				elseif ($this->pdf->rapportageValuta != 'EUR')
				{
					$historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
					$beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
				}

				if($historie[voorgaandejarenActief] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}
				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;
				$totaal_resultaat_waarde += $resultaatlopende;
			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
			}

			//	listarray($mutaties);
			$data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
			$data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
			if($mutaties['Credit'])
				$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
			else
				$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
			$data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
			$data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
			$data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
			$data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
			$data['totalen']['gerealiseerdResultaat']+=$result_lopendejaar;//($result_voorgaandejaren+$result_lopendejaar);
			$data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];


		}
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
		$db= new DB();
		$query = "
	  SELECT
  SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	FROM
	Rekeningmutaties ,  Rekeningen

	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";

		$db->SQL($query);
		$db->Query();
		$data = $db->nextRecord();
		return $data['totaal'];
	}



	function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
	{
		$DB=new DB();
		$query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
		$DB->SQL($query); //echo "$fonds $query  <br>\n";
		$DB->Query();
		$totaalWaarde = $DB->NextRecord();

		return $totaalWaarde['totaalWaarde'];
	}


	function fondsPerformance($fondsData,$totaal=false)
	{

		$datumBegin=$this->rapportageDatumVanaf;

		$weegDatum=$datumBegin;
		$datumEind=$this->rapportageDatum;

		global $__appvar;
		$DB=new DB();
		$totaalPerf = 100;

		if(!$fondsData['fondsen'])
			$fondsData['fondsen']=array('geen');
		if(!$fondsData['rekeningen'])
			$fondsData['rekeningen']=array('geen');

		if ($this->pdf->rapportageValuta <> 'EUR')
		{
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
			$startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
			$eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
		}
		else
		{
			$koersQuery = "";
			$startValutaKoers= 1;
			$eindValutaKoers= 1;
		}




		$tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
		$rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
		$tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
		$rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";

		$query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
               SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as liqWaarde,
               SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$start = $DB->NextRecord();
		$beginwaarde = $start['actuelePortefeuilleWaardeEuro'];

		$query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2/$eindValutaKoers  as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
		$DB->SQL($query);
		$DB->Query();
		$eind = $DB->NextRecord();
		$ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
		$eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];
		// listarray($fondsData);


		/*
          if($beginwaarde == 0)
          {
            $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 0 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
                      LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE ($rekeningFondsenWhere OR $rekeningRekeningenWhere ) AND
                      Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
            $DB->SQL($query);
            $DB->Query();
            $start = $DB->NextRecord();
            if($start['Boekdatum'] != '')
              $weegDatum = $start['Boekdatum'];

          }

           if($eindwaarde == 0)
           {
             $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 0 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
                      LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE ($rekeningFondsenWhere OR  $rekeningRekeningenWhere ) AND
                      Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
            $DB->SQL($query);
            $DB->Query();
            $eind = $DB->NextRecord();
            if($eind['Boekdatum'] != '')
              $datumEind = $eind['Boekdatum'];
           }
        */

		$queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))$koersQuery)*-1 AS gewogen, ".
			"SUM(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQuery) AS totaal,
	              SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery)  AS storting,
	              SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1) $koersQuery) AS onttrekking ".
			"FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".//(Grootboekrekeningen.Opbrengst=0 AND Grootboekrekeningen.Kosten =0)
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
		$DB->SQL($queryAttributieStortingenOntrekkingenRekening);
		$DB->Query();
		$AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

		$queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery) AS totaal,
	              SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery)  AS opbrengstTotaal,
	              SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
		$DB->SQL($queryRekeningDirecteKostenOpbrengsten);
		$DB->Query();
		$RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

		$queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM((if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
       SUM((if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0))) as opbrengstTotaal ,
       SUM((if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0))) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
		$DB->SQL($queryFondsDirecteKostenOpbrengsten);
		$DB->Query();
		$FondsDirecteKostenOpbrengsten = $DB->NextRecord();


		$queryAttributieStortingenOntrekkingen = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
			"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
			"FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			" $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
		$DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
		$DB->Query();
		$AttributieStortingenOntrekkingen = $DB->NextRecord();
		//listarray($AttributieStortingenOntrekkingen);


		$AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

		$query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Kruispost=1 OR   Rekeningmutaties.Fonds <> ''  )";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->nextRecord();

		$AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
		$AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
		$AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];


		$queryKostenOpbrengsten = "SELECT
          SUM((if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
          SUM((if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
		$DB->SQL($queryKostenOpbrengsten);
		$DB->Query();
		$nietToegerekendeKosten = $DB->NextRecord();
		$AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
		//echo $rekeningRekeningenWhere; listarray($nietToegerekendeKosten);
		//listarray($AttributieStortingenOntrekkingen);

		$gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
		$performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde) * 100;


		$mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
//listarray($mutatieData);
		if($totaal==true)
		{
			$this->totalen['gemiddeldeWaarde']=$gemiddelde;
		}

		$weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'];
		$resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
		$bijdrage=$resultaat/$gemiddelde*$weging;

		//  echo " $rekeningRekeningenWhere ".round($performance,2)." <br>\n";
		/*
          if($fondsData['fondsen'][0]=='3,5% NL 10-20')
          {
            echo "$queryAttributieStortingenOntrekkingen <br>\n";
            listarray($fondsData['fondsen']);
     echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."; <br>\n";
          }

         if($fondsData['rekeningen'][0]=='233512EUR')
         {

           listarray($fondsData['rekeningen']);
           echo "$queryAttributieStortingenOntrekkingenRekening <br>\n";

            echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."; <br>\n";
         }
    */
		return array(
			'beginwaarde'=>$beginwaarde,
			'eindwaarde'=>$eindwaarde,
			'procent'=>$performance,
			'stort'=>$AttributieStortingenOntrekkingen['totaal'],
			'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
			'storting'=>$AttributieStortingenOntrekkingen['storting'],
			'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
			'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
			'resultaat'=>$resultaat,
			'gemWaarde'=>$gemiddelde,
			'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
			'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
			'weging'=>$weging,
			'bijdrage'=>$bijdrage);
	}


	function PieChart($w, $h, $data, $format, $colors=null,$depotVerdeling)
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

		$x1 = $XPage + $radius*2 + 25 ;
		$x2 = $x1 + $hLegend + $margin;
		$y1 = $YDiag - ($radius) + $margin+5;

		$this->pdf->SetXY($this->pdf->GetX(),$y1-5);


		//for($i=0; $i<$this->pdf->NbVal; $i++)
		foreach ($depotVerdeling as $valuta=>$waarden)
		{

			$this->pdf->SetFillColor($waarden['kleur'][0],$waarden['kleur'][1],$waarden['kleur'][2]);
			$this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
			$this->pdf->SetXY($x2,$y1);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
			$this->pdf->Cell(0,$hLegend,$valuta." ".$this->formatGetal($waarden['waarde'],0)." (".$this->formatGetal($waarden['percentage'],1)."%)");
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$y1+=$hLegend+2;

			foreach ($waarden['depotbanken'] as $rekening=>$rekeningWaarden)
			{
				$this->pdf->SetXY($x2+2,$y1);
				$this->pdf->Cell(0,$hLegend,$rekening." (".$this->formatGetal($rekeningWaarden['percentage'],1,false,true)."%)");
				$y1+=$hLegend+2;
			}

			//$this->pdf->SetXY($x2-30,$y1);
			/*
      $this->pdf->SetX($x2-20);
      if($hcat[$i] <> $lastHcat)
      {
        if(isset($lastHcat))
        {
          $extraY=8;
          //$y1+=3;
        }

        $this->pdf->SetXY($this->pdf->GetX(),$this->pdf->GetY()+$extraY);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->Cell(0,$hLegend,$hcat[$i]);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      }
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1+$extraY);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + 2;
      $lastHcat=$hcat[$i];
      */
		}
		$this->pdf->SetFillColor(0,0,0);

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
}
