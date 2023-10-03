<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/06/02 07:10:25 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RapportZORG_L65.php,v $
 		Revision 1.2  2016/06/02 07:10:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/06/01 19:48:58  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/25 14:37:00  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/10/09 15:59:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/11/28 17:04:11  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/06/17 13:03:08  rvv
 		*** empty log message ***

 		Revision 1.10  2011/11/05 16:04:41  rvv
 		*** empty log message ***

 		Revision 1.9  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.8  2011/06/18 15:17:55  rvv
 		*** empty log message ***

 		Revision 1.7  2011/06/02 15:04:19  rvv
 		*** empty log message ***

 		Revision 1.6  2011/04/30 16:27:12  rvv
 		*** empty log message ***

 		Revision 1.5  2010/10/06 16:34:31  rvv
 		*** empty log message ***

 		Revision 1.4  2010/08/25 19:02:17  rvv
 		*** empty log message ***

 		Revision 1.3  2010/08/06 16:32:20  rvv
 		*** empty log message ***

 		Revision 1.2  2010/03/24 17:23:03  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/03 09:50:18  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("rapport/Zorgplichtcontrole.php");


class RapportZORG_L65
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportZORG_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{

		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ZORG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_CASH_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_ZORG_titel;
		else
			$this->pdf->rapport_titel = "Zorgplichtcontrole";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData[] = array('Fonds','Aantal','Koers','Waarde EUR','Weging','Zorgplichtcategorie','Min','Norm','Max');
 
  }


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = $this->rapportageDatum;

		$zorgplicht = new Zorgplichtcontrole();

    $this->pdf->setWidths(array(90,30,30,30,30,30,70,30));
		$this->pdf->setAligns(array('L','R','R','R','R','R','L','R'));

		$DB=new DB();
		$query="SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];


		  $pdata=$this->pdf->portefeuilledata;
		  $this->pdf->portefeuille = $pdata['Portefeuille'];
		  $this->pdf->rapport_kop = $pdata['Portefeuille']." - ".$pdata['Client']." - ".$pdata['Naam'];
		  $this->pdf->AddPage();
      $this->pdf->templateVars['ZORGPaginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving['ZORGPaginas']=$this->pdf->rapport_titel;
			$this->zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
			$totalen = array();
			$this->waardeEurTotaalAlles =0;
			$portefeuille = $pdata['Portefeuille'];
		  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$einddatum);

		$zorgDetail=array();
		foreach ($zpwaarde['detail'] as $zorgplicht=>$zorgplichData)
		{
      if($zorgplicht!='Liquiditeiten')
				$zorgDetail[$zorgplicht]=$zorgplichData;
		}
		if(isset($zpwaarde['detail']['Liquiditeiten']))
			$zorgDetail['Liquiditeiten']=$zpwaarde['detail']['Liquiditeiten'];

			foreach ($zorgDetail as $zorgplichData)
			{
			  foreach ($zorgplichData as $zpdata)
			  {
  		  if($zpdata['Zorgplicht'] != $vorigeZpdata['Zorgplicht'])
			  {
			    if($this->waardeEurTotaal <> 0)
			    {
			      $this->pdf->row(array('Totaal', $this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2),$this->formatGetal($this->waardeEurTotaal/$portefeuilleWaarde*100,1)));
			   	  $this->pdf->ln();
					}
  	    	$this->pdf->SetFont("Times","B",10);
  	      $this->pdf->row(array($zpdata['Zorgplicht']));
  	    	$this->pdf->SetFont("Times","",10);
					$this->zorgtotaal=0;
  			  $this->waardeEurTotaal =0;
 			  }
					$percentage=$zpdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
          $this->pdf->row(array($zpdata['fondsOmschrijving'],
                              $this->formatGetal($zpdata['totaalAantal'],0),$this->formatGetal($zpdata['actueleFonds'],2), 
                              $this->formatGetal($zpdata['actuelePortefeuilleWaardeEuro'],2),
                              $this->formatGetal($percentage*100,1)));

					$this->pdf->excelData[] = array($zpdata['fondsOmschrijving'],
						                              round($zpdata['totaalAantal'],6),
					                                round($zpdata['actueleFonds'],4),
						                              round($zpdata['actuelePortefeuilleWaardeEuro'],2),
						                              round($percentage*100,3),
						                             $zpdata['Zorgplicht'],
						                             $zpwaarde['conclusieDetail'][$zpdata['Zorgplicht']]['minimum'],
						                             $zpwaarde['conclusieDetail'][$zpdata['Zorgplicht']]['norm'],
						                             $zpwaarde['conclusieDetail'][$zpdata['Zorgplicht']]['maximum']);
				$this->zorgtotaal += $zpdata['totaal'];
				$this->waardeEurTotaal += $zpdata['actuelePortefeuilleWaardeEuro'];
				$this->waardeEurTotaalAlles  += $zpdata['actuelePortefeuilleWaardeEuro'];
		  	$vorigeZpdata = $zpdata;
			  }
		  }
      if(round($this->waardeEurTotaal,1) <> 0.0)
		  	$this->pdf->row(array('Totaal','','',$this->formatGetal($this->waardeEurTotaal,2)));
	    $this->pdf->ln();
			$this->pdf->row(array('Portefeuillewaarde',$this->formatGetal($zpwaarde['totaalWaarde'],2))); //$this->waardeEurTotaalAlles
	  	$this->pdf->ln();

			$this->pdf->excelData[] = array('');

		 $this->printBenchmarkvergelijking($zpwaarde['conclusie'] );
	}


	function printBenchmarkvergelijking($conclusieData)
	{

		$conclusieTmp=array();
		$conclusie=array();
		foreach($conclusieData as $data)
			$conclusieTmp[$data[0]]=array('waarde'=>$data[3],'conclusie'=>$data[5]);
		foreach ($conclusieTmp as $zorgplicht=>$zorgplichData)
		{
			if($zorgplicht!='Liquiditeiten')
				$conclusie[$zorgplicht]=$zorgplichData;
		}
		if(isset($conclusieTmp['Liquiditeiten']))
			$conclusie['Liquiditeiten']=$conclusieTmp['Liquiditeiten'];

		$DB = new DB();
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

		$zorgplichtcategorien=array();
		$query="SELECT waarde as Zorgplicht FROM KeuzePerVermogensbeheerder WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Zorgplicht' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
		while($data=$DB->nextRecord())
			$zorgplichtcategorien[$data['Zorgplicht']]=$data;

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
              ZorgplichtPerBeleggingscategorie.Zorgplicht,
              beleggingscategorieOmschrijving ".
			"FROM TijdelijkeRapportage
             INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']." 
              GROUP BY Zorgplicht 
              ORDER BY beleggingscategorieVolgorde";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
			$zorgplichtcategorien[$data['Zorgplicht']]=$data;
			$verdeling[$data['Zorgplicht']]['percentage'] = $data['totaal']/$totaalWaarde*100;
		}

		$query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
		$DB->SQL($query);
		$DB->Query();
		$zorgplichtcategorien=array();
		while($zorgplicht = $DB->nextRecord())
		{
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
		}
		$query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."'
 ORDER BY Zorgplicht";
		$DB->SQL($query);
		$DB->Query();
		while($zorgplicht = $DB->nextRecord())
		{
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
		}

		foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
		{
			$query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie 
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Zorgplichtcategorien' AND Categorie='$zorgplicht' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();
			$zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
			$zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
		}

		foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
		{
			$query="SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving 
      FROM benchmarkverdeling 
      JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmark='".$zorgplichtData['fonds']."'";
			$DB->SQL($query);
			$DB->Query();
			while($data = $DB->nextRecord())
				$zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']]=$data;
		}



		foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgplichtData)
		{
			if(!isset($zorgplichtData['fondsSamenselling']))
				$zorgplichtData['fondsSamenselling']=array($zorgplichtData['fonds']=>array('fonds'=>$zorgplichtData['fonds'],
																																									 'percentage'=>100,
																																									 'Omschrijving'=>$zorgplichtData['fondsOmschrijving']));

			$fonds=$zorgplichtData['fonds'];

			$samengesteldeBenchmark[$zorgplichtCategorie]['norm']=$zorgplichtData['norm'];
			$samengesteldeBenchmark[$zorgplichtCategorie]['periode']=$indexData[$fonds]['performance'];
			$samengesteldeBenchmark[$zorgplichtCategorie]['jaar']=$indexData[$fonds]['performanceJaar'];
		}
		$totalen=array();
		foreach($samengesteldeBenchmark as $zorgplichtCategorie=>$data)
		{

			$totalen['norm']+= $data['norm'];
			$totalen['periode']+=$data['norm']*$data['periode']/100;
			$totalen['jaar']+=$data['norm']*$data['jaar']/100;
		}
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->ln(2);
		$this->pdf->Cell(100,4, vertaalTekst("Asset Allocatie per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
		$this->pdf->ln(2);
		$this->pdf->ln();
		$this->pdf->SetTextColor(0,0,0);

		$this->pdf->SetWidths(array(40,20,20,20,20,20,20,20,20,20,20));
		$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('','Waarde','Min',"Norm","Max","Huidig","Verschil","Conclusie"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
		{
			$this->pdf->row(array($zorgplichtCategorie,$conclusie[$zorgplichtCategorie]['waarde'],$this->formatGetal($zorgData['Minimum'],1).'%',
												$this->formatGetal($zorgData['norm'],1).'%',
												$this->formatGetal($zorgData['Maximum'],1).'%',
												$this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
												$this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%',
										  	$conclusie[$zorgplichtCategorie]['conclusie']));
		}


		// listarray($zorgplichtcategorien);


	}
}
?>