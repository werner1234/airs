<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/05/02 16:13:47 $
 		File Versie					: $Revision: 1.27 $

 		$Log: RapportPERF_L26.php,v $
 		Revision 1.27  2018/05/02 16:13:47  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2015/06/17 15:52:40  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2012/06/30 14:42:50  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.22  2011/07/17 14:52:22  rvv
 		*** empty log message ***

 		Revision 1.21  2011/07/13 13:03:18  rvv
 		*** empty log message ***

 		Revision 1.20  2011/05/05 09:45:31  rvv
 		*** empty log message ***

 		Revision 1.19  2011/04/19 11:16:29  rvv
 		*** empty log message ***

 		Revision 1.18  2011/04/16 11:48:06  rvv
 		*** empty log message ***

 		Revision 1.17  2011/04/15 11:38:01  cvs
 		*** empty log message ***

 		Revision 1.16  2011/04/09 14:30:46  rvv
 		*** empty log message ***

 		Revision 1.15  2011/02/24 08:53:12  rvv
 		afronding

 		Revision 1.14  2011/02/06 14:36:59  rvv
 		*** empty log message ***

 		Revision 1.13  2011/01/08 14:27:56  rvv
 		*** empty log message ***

 		Revision 1.12  2010/12/12 15:35:54  rvv
 		*** empty log message ***

 		Revision 1.11  2010/11/27 16:16:50  rvv
 		*** empty log message ***

 		Revision 1.10  2010/11/20 17:02:14  rvv
 		*** empty log message ***

 		Revision 1.9  2010/09/22 13:57:50  rvv
 		*** empty log message ***

 		Revision 1.8  2010/09/18 15:37:41  rvv
 		*** empty log message ***

 		Revision 1.7  2010/09/15 16:29:09  rvv
 		*** empty log message ***

 		Revision 1.6  2010/09/11 15:17:37  rvv
 		*** empty log message ***

 		Revision 1.5  2010/07/28 17:18:22  rvv
 		*** empty log message ***

 		Revision 1.4  2010/07/24 12:02:53  rvv
 		*** empty log message ***

 		Revision 1.3  2010/07/21 17:36:35  rvv
 		*** empty log message ***

 		Revision 1.2  2010/07/18 17:04:44  rvv
 		*** empty log message ***

 		Revision 1.1  2010/07/14 17:33:49  rvv
 		*** empty log message ***


*/

class RapportPERF_L26
{

	function RapportPERF_L26($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->rapport_jaar=date('Y',$this->pdf->rapport_datum);
		$this->pdf->rapport_titel = "Totaal overzicht van ".date("d-m-Y",$this->pdf->rapport_datumvanaf)." tot en met ".date("d-m-Y",$this->pdf->rapport_datum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->checks=array();

		if($this->pdf->HENIndex)
	  	$this->grafiek=false;
    else
		  $this->grafiek=true;
		$this->pdf->excelData 	= array();
	}


	function formatGetal($waarde, $dec,$procent=false,$toonNull=false)
	{
	  if($waarde==0 && $toonNull==false)
	    return;
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
		  $data.="%";
		return $data;
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder,
		                 Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client
		                 FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$this->tmpData2=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],$this->portefeuille);
		$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		$this->pdf->SetLineWidth(0.1);
    $nietToeTeRekenenKosten=0;


		$DB=new DB();
		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingssectorPerFonds.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
			$DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
		    $perRegio[$data['Hoofdcategorie']][$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Hoofdcategorie']][$data['Regio']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
		    $alleData['fondsen'][]=$data['Fonds'];

		  }
		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Inner Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Hoofdcategorie']][$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Hoofdcategorie']][$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Hoofdcategorie']][$data['Regio']][$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }
    $this->totalen['gemiddeldeWaarde']=0;
    $perfTotaal=$this->fondsPerformance($alleData,true);
    $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
    foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
		    $perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($hoofdcategorieData);




    foreach ($perHoofdcategorie as $hoofdcategorie=>$hoofdcategorieData)
    {
      $data=$hoofdcategorieData['perf'];

    if($data['bijdrage'] < 0)
      $this->pdf->CellFontColor = array('','','','','','','','',$this->pdf->rapport_font_rood,'');
    else
      $this->pdf->CellFontColor = array('','','','','','','','',$this->pdf->rapport_font_groen,'');

      $this->pdf->row(array(substr($perHoofdcategorie[$hoofdcategorie]['omschrijving'],0,25),
												$this->formatGetal($data['beginwaarde'],0,false,true),
												$this->formatGetal($data['eindwaarde'],0,false,true),
												$this->formatGetal($data['stortEnOnttrekking'],0,false,true),
										//		$this->formatGetal($data['onttrekking'],0,false,true),
												$this->formatGetal($data['gemWaarde'],0,false,true),
												$this->formatGetal($data['resultaat'],0,false,true),
												$this->formatGetal($data['procent'],2,true),
                        $this->formatGetal($data['weging']*100,2,true),
                        $this->formatGetal($data['bijdrage']*100,2,true)));
      $totaalSom['beginwaarde'] += $data['beginwaarde'];
      $totaalSom['eindwaarde'] += $data['eindwaarde'];
      $totaalSom['storting'] += $data['storting'];
      $totaalSom['onttrekking'] += $data['onttrekking'];
      $totaalSom['stortEnOnttrekking'] += $data['stortEnOnttrekking'];
      $totaalSom['gerealiseerd'] += $data['gerealiseerd'];
      $totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
      $totaalSom['kosten'] += $data['kosten'];
      $totaalSom['resultaat'] += $data['resultaat'];
      $totaalSom['gemWaarde'] += $data['gemWaarde'];
      $totaalSom['weging'] += $data['weging'];
      $totaalSom['bijdrage'] += $data['bijdrage'];
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $perfTotaal=$totaalSom;
    $this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T');
    if($perfTotaal['bijdrage'] < 0)
      $this->pdf->CellFontColor = array('','','','','','','','',$this->pdf->rapport_font_rood,'');
    else
      $this->pdf->CellFontColor = array('','','','','','','','',$this->pdf->rapport_font_groen,'');
    $this->pdf->row(array(substr('Totaal',0,12),
												$this->formatGetal($perfTotaal['beginwaarde'],0,false,true),
												$this->formatGetal($perfTotaal['eindwaarde'],0,false,true),
												$this->formatGetal($perfTotaal['stortEnOnttrekking'],0,false,true),
											//	$this->formatGetal($perfTotaal['onttrekking'],0,false,true),
											  $this->formatGetal($perfTotaal['gemWaarde'],0,false,true),
											  $this->formatGetal($perfTotaal['resultaat'],0,false,true),
											  '',
                        $this->formatGetal($perfTotaal['weging']*100,2,true),
                        $this->formatGetal($perfTotaal['bijdrage']*100,2,true)));






 unset($this->pdf->CellFontColor);
    $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers)) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Fonds,Rekeningmutaties.Grootboekrekening";
//echo $query;exit;

 		$DB->SQL($query);
		$DB->Query();
		$omschrijvingen=array('DIV'=>'Uitkeringen dividend en coupon','DIVBE'=>'Belasting dividend en coupon','RENTE'=>'Rente');
    $grootboekOpbrengst=array();
    $grootboekWaarden=array();
		while($data = $DB->NextRecord())
		{
		  if(!isset($omschrijvingen[$data['Grootboekrekening']]))
		    $omschrijvingen[$data['Grootboekrekening']]=$data['Omschrijving'];
		  $grootboekWaarden[$data['Grootboekrekening']]+=$data['waarde'];
		  if($data['Kosten']==1)
		  {
		    $grootboekKosten[$data['Grootboekrekening']]+=$data['waarde'];
		    if($data['Fonds']=='')
        {
        //  listarray($data);
          $nietToeTeRekenenKosten += $data['waarde'];
        }
		  }
		  if($data['Opbrengst']==1)
		  {
		    if($data['Grootboekrekening']=='RENME')
		    {
		      $waardeRenme+=$data['waarde'];
		    }
		    else
		    {
		      if($data['Grootboekrekening']=='RENOB')
		        $data['Grootboekrekening']="DIV";
		      $grootboekOpbrengst[$data['Grootboekrekening']]+=$data['waarde'];
		    }
		  }
		}



    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->rect($this->pdf->getX(),$this->pdf->getY(),270,8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($this->pdf->widths[0],$this->pdf->rowHeight,'Niet toe te rekenen kosten', 0,0, "L");
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','','',$this->formatGetal((($perfTotaal['onttrekking']+$perfTotaal['storting'])-$perfTotaal['stortEnOnttrekking']),0,false,true),'',$this->formatGetal($nietToeTeRekenenKosten,0,false,true)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($this->pdf->widths[0],$this->pdf->rowHeight,'Netto Resultaat / rendement', 0,0, "L");
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $nettoResultaat=$perfTotaal['resultaat']+$nietToeTeRekenenKosten;
    $nettoRendement=$nettoResultaat/$perfTotaal['gemWaarde']*100;
//echo $this->rapportageDatumVanaf." -> ".$this->rapportageDatum." $nettoRendement = $nettoResultaat/".$perfTotaal['gemWaarde']."*100;";exit;
    $this->pdf->row(array('',$this->formatGetal($perfTotaal['beginwaarde'],0,false,true),$this->formatGetal($perfTotaal['eindwaarde'],0,false,true),$this->formatGetal(($perfTotaal['storting']+$perfTotaal['onttrekking']),0,false,true),$this->formatGetal($perfTotaal['gemWaarde'],0,false,true),$this->formatGetal($nettoResultaat,0),$this->formatGetal($nettoRendement,2,true)));

    	 $this->tmpData1=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,
	 round($perHoofdcategorie['G-RISD']['perf']['beginwaarde'],0),
	 round($perHoofdcategorie['G-RISD']['perf']['eindwaarde'],0),
	 round($perHoofdcategorie['G-RISD']['perf']['stortEnOnttrekking'],0),
	 round($perHoofdcategorie['G-RISD']['perf']['gemWaarde'],0),
	 round($perHoofdcategorie['G-RISD']['perf']['resultaat'],0),
	 round($perHoofdcategorie['G-RISD']['perf']['procent'],2),
	 round($perHoofdcategorie['G-RISD']['perf']['weging']*100,2),
	 round($perHoofdcategorie['G-RISD']['perf']['bijdrage']*100,2),
	 round($perHoofdcategorie['G-RISM']['perf']['beginwaarde'],0),
	 round($perHoofdcategorie['G-RISM']['perf']['eindwaarde'],0),
	 round($perHoofdcategorie['G-RISM']['perf']['stortEnOnttrekking'],0),
	 round($perHoofdcategorie['G-RISM']['perf']['gemWaarde'],0),
	 round($perHoofdcategorie['G-RISM']['perf']['resultaat'],0),
	 round($perHoofdcategorie['G-RISM']['perf']['procent'],2),
	 round($perHoofdcategorie['G-RISM']['perf']['weging']*100,2),
	 round($perHoofdcategorie['G-RISM']['perf']['bijdrage']*100,2),
	 round($perHoofdcategorie['G-LIQ']['perf']['beginwaarde'],0),
	 round($perHoofdcategorie['G-LIQ']['perf']['eindwaarde'],0),
	 round($perHoofdcategorie['G-LIQ']['perf']['stortEnOnttrekking'],0),
	 round($perHoofdcategorie['G-LIQ']['perf']['gemWaarde'],0),
	 round($perHoofdcategorie['G-LIQ']['perf']['resultaat'],0),
	 round($perHoofdcategorie['G-LIQ']['perf']['procent'],2),
	 round($perHoofdcategorie['G-LIQ']['perf']['weging']*100,2),
	 round($perHoofdcategorie['G-LIQ']['perf']['bijdrage']*100,2),
	 round($perfTotaal['beginwaarde'],0),
	 round($perfTotaal['eindwaarde'],0),
	 round($perfTotaal['stortEnOnttrekking'],0),
	 round($perfTotaal['gemWaarde'],0),
	 round($perfTotaal['resultaat'],0),
	 '',//round($perfTotaal['weging']*100,2)
	 round($perfTotaal['bijdrage']*100,2),
	 round($nietToeTeRekenenKosten,0),
	 round($nettoResultaat,0),
	 round($nettoRendement,2)
	 );

    $this->pdf->ln(8);
    $dataWidth=array(40+25,25);
 	 	$this->pdf->SetWidths($dataWidth);
 	 	$this->pdf->CellBorders = array('U','U');
 	 	$indexY=$this->pdf->getY();
 	 	$this->pdf->row(array('Uitsplitsing Ongerealiseerd - Gerealiseerd',''));
 	 	$this->pdf->CellBorders = array();
 	 	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
//


    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro-beginPortefeuilleWaardeEuro) AS resultaat,
               SUM(actuelePortefeuilleWaardeInValuta*actueleValuta - beginPortefeuilleWaardeInValuta*actueleValuta) AS koersResultaat ".
						 " FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdeResultaat = $totaal['resultaat'];
		$ongerealiseerdeFondsResultaat = $totaal['koersResultaat'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro-beginPortefeuilleWaardeEuro) AS resultaat,
		           SUM(actuelePortefeuilleWaardeInValuta*actueleValuta - beginPortefeuilleWaardeInValuta*actueleValuta) AS koersResultaat ".
						 " FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

        		// ophalen van rente totaal A en rentetotaal B
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();
		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $this->pdf->ValutaKoersEind;

    $ongerealiseerdeResultaat=$ongerealiseerdeResultaat-$totaalWaardeVanaf['resultaat']+$waardeRenme+$opgelopenRente;
    $ongerealiseerdeFondsResultaat = $ongerealiseerdeFondsResultaat-$totaalWaardeVanaf['koersResultaat']+$waardeRenme+$opgelopenRente;
    $this->pdf->row(array('Koersresultaat positie',$this->formatGetal($ongerealiseerdeFondsResultaat,0)));
    $this->pdf->row(array('Valutaresultaat positie',$this->formatGetal($ongerealiseerdeResultaat-$ongerealiseerdeFondsResultaat,0)));

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','T');
    $this->pdf->row(array('Totaal Ongerealiseerd',$this->formatGetal($ongerealiseerdeResultaat,0)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','');
    $this->pdf->ln();


//




 	 	$gerealiseerdKoersresultaat=gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,'EUR',true,'Totaal',true);
 	 	//listarray($gerealiseerdKoersresultaat);
    $this->pdf->CellBorders = array();
    $this->pdf->row(array('Koersresultaat verkopen',$this->formatGetal($gerealiseerdKoersresultaat['fonds'],0)));
    $this->pdf->row(array('Valutaresultaat verkopen',$this->formatGetal($gerealiseerdKoersresultaat['valuta'],0)));
    array_push($this->tmpData2,round($gerealiseerdKoersresultaat['fonds'],0));//KoersresultaatVerkopen
    array_push($this->tmpData2,round($gerealiseerdKoersresultaat['valuta'],0));//ValutaresultaatVerkopen

    $totaalGerealiseerd=$gerealiseerdKoersresultaat['fonds']+$gerealiseerdKoersresultaat['valuta'];
    foreach ($grootboekOpbrengst as $grootboek=>$waarde)
    {
      $this->pdf->row(array($omschrijvingen[$grootboek],$this->formatGetal($waarde,0)));
      $totaalGerealiseerd+=$waarde;
    }
    array_push($this->tmpData2,round($grootboekOpbrengst['DIV'],0));//Uitkeringen
    array_push($this->tmpData2,round($grootboekOpbrengst['DIVBE'],0));//Belastingen
    array_push($this->tmpData2,round($grootboekOpbrengst['RENTE'],0));//Rente

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','T');
    $this->pdf->row(array('Totaal Gerealiseerd resultaat',$this->formatGetal($totaalGerealiseerd,0)));
    array_push($this->tmpData2,round($totaalGerealiseerd,0));//TotaalGerealiseerd
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','');
    $this->pdf->ln();

    array_push($this->tmpData2,round($ongerealiseerdeFondsResultaat,0));//KoersresultaatPositie
    array_push($this->tmpData2,round($ongerealiseerdeResultaat-$ongerealiseerdeFondsResultaat,0));//ValutaresultaatPositie
    array_push($this->tmpData2,round($ongerealiseerdeResultaat,0));//TotaalGerealiseerd

    foreach ($grootboekKosten as $grootboek=>$waarde)
      $totaalKosten+=$waarde;

    $valutaresultaatLiquiditeiten=$nettoResultaat-$totaalGerealiseerd-$ongerealiseerdeResultaat-$totaalKosten;
    $this->pdf->row(array('Valutaresultaat Liquiditeiten',$this->formatGetal($valutaresultaatLiquiditeiten,0)));
    array_push($this->tmpData2,round($valutaresultaatLiquiditeiten,0));//Valutaresultaat
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','T');
    $this->pdf->row(array('Totaal Overige',$this->formatGetal($valutaresultaatLiquiditeiten,0)));
    array_push($this->tmpData2,round($valutaresultaatLiquiditeiten,0));//TotaalOverig
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','');
    $this->pdf->ln();
    foreach ($grootboekKosten as $grootboek=>$waarde)
      $this->pdf->row(array($omschrijvingen[$grootboek],$this->formatGetal($waarde,0)));

    if(round($perHoofdcategorie['G-LIQ']['perf']['resultaat'],0)==round($grootboekOpbrengst['RENTE']+$valutaresultaatLiquiditeiten,0))
      $this->checks['valutaResultaat']=array(true,round($perHoofdcategorie['G-LIQ']['perf']['resultaat'],0)."==".round($grootboekOpbrengst['RENTE']+$valutaresultaatLiquiditeiten,0)."");
    else
      $this->checks['valutaResultaat']=array(false,round($perHoofdcategorie['G-LIQ']['perf']['resultaat'],0)."!=".round($grootboekOpbrengst['RENTE']+$valutaresultaatLiquiditeiten,0)."");

//listarray($this->checks);
  //  listarray($grootboekKosten);
    array_push($this->tmpData2,round($grootboekKosten['KOST'],0));//TransactieKosten
    array_push($this->tmpData2,round($grootboekKosten['BEH'],0));//Beheervergoeding
    array_push($this->tmpData2,round($grootboekKosten['BEW'],0));//Bewaarloon
    array_push($this->tmpData2,0);//Performancefee (Welk veld?)
    array_push($this->tmpData2,round($grootboekKosten['Bankkosten'],0));//Bankkosten
    array_push($this->tmpData2,0);//RestitutieRetrocessie (Welk veld?)
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','T');
    $this->pdf->row(array('Totaal Kosten',$this->formatGetal($totaalKosten,0)));
    $this->pdf->ln();
    $this->pdf->row(array('Totaal Resultaat in euro',$this->formatGetal($nettoResultaat,0)));
    array_push($this->tmpData2,round($totaalKosten,0));//TotaalKosten
    array_push($this->tmpData2,round($nettoResultaat,0));//TotaalResultaatInEuro



//Benchmark

$weging['Zeer offensief']=array(90,10,0,0);
$weging['Offensief']=array(63,7,21,9);
$weging['Neutraal']=array(45,5,35,15);
$weging['Defensief']=array(27,3,49,21);

    $this->pdf->setY($indexY +73);
 	 	$this->pdf->SetWidths(array(120,120));
 	 	$this->pdf->CellBorders = array('','U');
 	 	$this->pdf->SetAligns(array('L','L','L','R','R','R','R','R','R','R'));
 	 	$this->pdf->row(array('','Rendement Samengestelde Benchmark: '.$this->pdf->portefeuilledata['Risicoklasse']));
 	 	$this->pdf->CellBorders = array();
    $this->pdf->SetWidths(array(120,30,40,20,20));
    $this->pdf->row(array('','','','Weging','Rendement'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $query="SELECT
    Indices.Vermogensbeheerder,
    Indices.Beursindex,
    BeleggingscategoriePerFonds.Beleggingscategorie,
    CategorienPerHoofdcategorie.Hoofdcategorie,
    Beleggingscategorien.Omschrijving as categorieOmschrijving,
    Fondsen.Omschrijving as fondsOmschrijving,
    ((SELECT  Koers FROM Fondskoersen WHERE Datum <= '".$this->rapportageDatum."' AND  Fonds=Indices.Beursindex  ORDER BY Datum desc limit 1) /(SELECT  Koers FROM Fondskoersen WHERE Datum <= '".$this->rapportageDatumVanaf."' AND  Fonds=Indices.Beursindex  ORDER BY Datum desc limit 1))*100-100  as rendement
    FROM
    Indices
    Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
    Left Join BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    Left Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    Left Join Beleggingscategorien ON Beleggingscategorien.Beleggingscategorie=CategorienPerHoofdcategorie.Hoofdcategorie
    WHERE
    Indices.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Indices.Afdrukvolgorde";
    $DB->SQL($query);
		$DB->Query();
		$n=0;
		while ($index = $DB->nextRecord())
		{
		  if($weging[$this->pdf->portefeuilledata['Risicoklasse']][$n] > 0)
		  {
      if($index['categorieOmschrijving'] != $lastCat)
        $categorie=$index['categorieOmschrijving'];
      else
        $categorie='';
      $this->pdf->row(array('',$categorie,$index['fondsOmschrijving'],$this->formatGetal($weging[$this->pdf->portefeuilledata['Risicoklasse']][$n],0,true),$this->formatGetal($index['rendement'],2,true)));
      $lastCat=$index['categorieOmschrijving'];
		  }
      $n++;
		}
    $query="SELECT ((SELECT  Koers FROM Fondskoersen WHERE Datum <= '".$this->rapportageDatum."' AND  Fonds='".$this->pdf->portefeuilledata['SpecifiekeIndex']."'  ORDER BY Datum desc limit 1) /
    (SELECT  Koers FROM Fondskoersen WHERE Datum <= '".$this->rapportageDatumVanaf."' AND  Fonds='".$this->pdf->portefeuilledata['SpecifiekeIndex']."' ORDER BY Datum desc limit 1))*100-100  as rendement
    , Fondsen.Omschrijving, Fondsen.Fonds FROM Fondsen WHERE Fonds='".$this->pdf->portefeuilledata['SpecifiekeIndex']."' ";
    $DB->SQL($query);
		$DB->Query();
		$index = $DB->nextRecord();
		$indexNaam[$index['Fonds']]=$index['Omschrijving'];
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Totaal '.$this->pdf->portefeuilledata['Risicoklasse'],$index['Omschrijving'],'',$this->formatGetal($index['rendement'],2,true)));
		array_push($this->tmpData1,round($index['rendement'],2));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

    $portStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
    $rapJul=$this->pdf->rapport_datum;
    $dagen=($rapJul-$portStartJul)/86400;
    $step=round($dagen/30);
    $steps=$dagen/$step;
    //echo "$dagen  $step $steps";exit;


    ######

 //   $maanden=$this->getMaanden($this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum);
if($this->grafiek==true)
{

  $index = new indexHerberekening();
  if($this->grafiekHistorie)
  {
    $startJaar=substr($this->pdf->PortefeuilleStartdatum,0,4);
    $eindJaar=substr($this->rapportageDatum,0,4);
    for($jaar=$startJaar;$jaar <=$eindJaar; $jaar++)
    {
      	if($jaar==$startJaar && $jaar==$eindJaar)
      	  $indexWaarden[$jaar] = $index->getWaarden($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'dagYTD');
      	else
      	{
      	  if ($jaar==$startJaar)
      	    $startDatum=$this->pdf->PortefeuilleStartdatum;
      	  else
      	    $startDatum=($jaar-1)."-12-31";

       	  if ($jaar==$eindJaar)
      	    $eindDatum=$this->rapportageDatum;
      	  else
      	    $eindDatum="$jaar-12-31";
      	  $indexWaarden[$jaar] = $index->getWaarden($startDatum,$eindDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'dagYTD');
      	}
    }
  }
  else
    $indexWaarden[$this->rapport_jaar] = $index->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum,$this->portefeuille,$this->pdf->portefeuilledata['SpecifiekeIndex'],'dagYTD');

  $indexFondsen[] = $this->pdf->portefeuilledata['SpecifiekeIndex'];
  $query = "SELECT BeleggingscategoriePerFonds.grafiekKleur, BeleggingscategoriePerFonds.Fonds
          FROM  BeleggingscategoriePerFonds
          WHERE BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategoriePerFonds.Fonds IN('".implode("','",$indexFondsen)."') ";
  $DB->SQL($query);
  $DB->Query();
  while ($data = $DB->nextRecord())
  {
    if($data['grafiekKleur'] !='')
	    $indexKleuren[$data['Fonds']] = unserialize($data['grafiekKleur']);
  }
$aantalWaarden = count($indexWaarden);

foreach ($indexWaarden as $jaarTal=>$indexData)
{
foreach ($indexData as $id=>$waarden)
{
  $start = jul2sql(form2jul(substr($waarden['periodeForm'],0,10)));
  $eind = jul2sql(form2jul(substr($waarden['periodeForm'],13)));
  foreach ($indexFondsen as $fonds)
  {
    $koersFonds=$fonds;
    /*
    if($fonds == $this->pdf->portefeuilledata['SpecifiekeIndex'])
    {
//    	  $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='".$this->portefeuille."' AND tot > '$eind' ORDER BY tot desc limit 1";
	      $DB->SQL($query);
        $oldIndex=$DB->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
          $koersFonds=$oldIndex['specifiekeIndex'];

    }
    */

 	  $q0 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$eind."' AND Fonds = '$koersFonds'  ORDER BY Datum DESC LIMIT 1" ;
 	  $q1 = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$start."' AND Fonds = '$koersFonds'  ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($q0);
	  $DB->Query();
	  $koersEind = $DB->LookupRecord();
	  $DB->SQL($q1);
	  $DB->Query();
	  $koersStart = $DB->LookupRecord();
	  $perf = $koersEind['Koers'] /$koersStart['Koers']  ;
	  if($perf==0)
      $perf =1;
    $indexWaarden[$jaarTal][$id]['fondsPerf'][$fonds] = $perf  ;
	  $indexWaarden[$jaarTal][$id]['fondsIndex'][$fonds] = $indexWaarden[$jaarTal][$id]['fondsPerf'][$fonds];

    $jaar=substr($eind,0,4);
   	if(empty($indexTabel['cumulatief'][$fonds]['jaren']))
   	  $indexTabel['cumulatief'][$fonds]['jaren']=100;

   	if(empty($indexTabel['cumulatief'][$fonds]['cumulatief']))
   	   $indexTabel['cumulatief'][$fonds]['cumulatief']=100;

    $indexTabel['cumulatief'][$fonds]['jaren']      = ($indexTabel['cumulatief'][$fonds]['jaren']*($perf*100))/100;
    $indexTabel['cumulatief'][$fonds]['cumulatief'] = ($indexTabel['cumulatief'][$fonds]['cumulatief']*($perf*100))/100;
    $indexTabel[$jaar][$fonds]['jaar'] = $indexTabel['cumulatief'][$fonds]['jaren'];

    if(substr($eind,5,5) == '12-31' || $aantalWaarden == $id)
    {
      $indexTabel['cumulatief'][$fonds]['jaren'] = 100;
      $indexTabel[$jaar][$fonds]['cumulatief'] = $indexTabel['cumulatief'][$fonds]['cumulatief'];
    }
  }
}
}



$n=0;
$minVal = 99;
$maxVal = 101;
$excelIndex=array();
foreach ($indexWaarden as $jaarTal=>$indexData)
{
foreach ($indexData as $id=>$data)
{
//  listarray($data);
  $grafiekData['portefeuille'][$n]=100 + $data['performance'];//$data['index'];
  $datumArray[$n] = $data['datum'];
  $jaar=substr($data['datum'],0,4);

  if($data['index'] != 0)
  {
    $maxVal=max($maxVal,$data['performance']+100);
    $minVal=min($minVal,$data['performance']+100);
  }

  foreach ($data['fondsIndex'] as $fonds=>$waarde)
  {
    $grafiekData[$fonds][$n]=$waarde *100;
    if($waarde != 0)
    {
      $maxVal=max($maxVal,$waarde *100);
      $minVal=min($minVal,$waarde *100);
    }
  }

  $excelIndex[]=array($this->pdf->portefeuilledata['ClientVermogensbeheerder'],$this->portefeuille,$data['datum'],round($data['index'],2),round($grafiekData[$fonds][$n],2));
  $n++;
}
}


    $YendIndex = $this->pdf->GetY();
    $w=140;
    $h=50;
    $horDiv = 10;

    $this->pdf->setXY(130,$indexY);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 12);
    $this->pdf->Multicell($w,4,"Portefeuille-ontwikkeling",'','C');
    $this->pdf->setXY(130,$indexY+5);

    $legendDatum= $data['Datum'];
    $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 5;
    $maxVal = $maxVal * (1 + ($procentWhiteSpace/100));
    $minVal = $minVal * (1 - ($procentWhiteSpace/100));
    $legendYstep = ($maxVal - $minVal) / $horDiv;

    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);

    $unit = $lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,240));
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
    $n=0;

    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      $this->pdf->Text($XDiag-5, $i, 100-($n*$stapgrootte) ."");
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-5, $i, ($n*$stapgrootte)+100 ."");

      $n++;
      if($n >20)
         break;
    }

    $n=0;
    $laatsteI = count($datumArray)-1;
    $lijnenAantal = count($grafiekData);

//listarray($grafiekData);
  //  $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
  //  $this->pdf->Rect($this->pdf->marge+120, $YendIndex+6, 40, 6 * $lijnenAantal ,'FD','',array(240,240,240));

    $aantalRecords=count($grafiekData['portefeuille']);
    if($aantalRecords > 10)
      $step=floor(1/(10/$aantalRecords));

    foreach ($grafiekData as $fonds=>$data)
    {
      $kleur = array($indexKleuren[$fonds]['R']['value'],$indexKleuren[$fonds]['G']['value'],$indexKleuren[$fonds]['B']['value']);
      $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $kleur);
      for ($i=0; $i<count($data); $i++)
      {
        if(!isset($datumPrinted[$i]) && ($i%$step==0 ||  $i==$aantalRecords-1) )
        {
          $datumPrinted[$i] = 1;
         // if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
         // {
            $this->pdf->TextWithRotation($XDiag+($i+1)*$unit-6,$YDiag+$hDiag+10,date("d-m-Y",db2jul($datumArray[$i])),45);
            $this->pdf->line($XDiag+($i)*$unit, $YDiag, $XDiag+($i)*$unit, $YDiag+$hDiag,array('width' => 0.1, 'cap' => 'round', 'join' => 'miter', 'dash' =>1, 'color' => array(0,0,0)) );
         // }
        }

        if($data[$i] != 0)
        {
          $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
          $yval = $yval2;
        }

      }
     }

     $n=0;
     foreach ($grafiekData as $fonds=>$data)
     {
       $kleur = array($indexKleuren[$fonds]['R']['value'],$indexKleuren[$fonds]['G']['value'],$indexKleuren[$fonds]['B']['value']);
      $fondsNaam = ($indexNaam[$fonds] <> "")?$indexNaam[$fonds]:$fonds;
      $this->pdf->Text(180+$n , 195-38,$fondsNaam);
      $this->pdf->Rect(175+$n , 195-1-38, 1, 1 ,'F','',$kleur);
      $n+=30;
     }

    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
$this->pdf->SetLineWidth(0.1);

}

$this->pdf->excelData[] = $this->tmpData1;
$this->pdf->excelData[] = $this->tmpData2;
foreach ($excelIndex as $indexData)
  $this->pdf->excelData[] = $indexData;

    ######


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
		"Rekeningmutaties.Valutakoers ".
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
      $mutaties['Rapportagekoers']=1;

			switch($mutaties[Transactietype])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta);

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
				$data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
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
    if(substr($this->pdf->PortefeuilleStartdatum,0,10) == $this->rapportageDatumVanaf)
      $weegDatum=date('Y-m-d',$this->pdf->rapport_datumvanaf+86400);
    elseif(date("d-m",$this->pdf->rapport_datumvanaf)=='01-01')
      $weegDatum=date('Y-m-d',$this->pdf->rapport_datumvanaf-86400);
    else
      $weegDatum=$datumBegin;
    $datumEind=$this->rapportageDatum;

    global $__appvar;
	  $DB=new DB();
    $totaalPerf = 100;

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');


      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";


      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) as liqWaarde,
      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];


       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2 as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     //$ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew'];
	     $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];

	     $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)  AND ". //(Grootboekrekeningen.Opbrengst=0 AND Grootboekrekeningen.Kosten =0)
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS kostenTotaal
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
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
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
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )  AS onttrekking ".
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


	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
	     $DB->SQL($query);//echo "$query <br><br>\n";
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];


      $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
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


      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde) * 100;


      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);

      if($totaal==true)
      {
        $this->totalen['gemiddeldeWaarde']=$gemiddelde;
      }

      $weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;

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

	function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}
}
?>