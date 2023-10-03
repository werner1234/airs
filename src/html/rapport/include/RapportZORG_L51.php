<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/04/18 16:17:01 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportZORG_L51.php,v $
 		Revision 1.5  2018/04/18 16:17:01  rvv
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


class RapportZORG_L51
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportZORG_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();


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
		foreach ($zpwaarde['detail'] as $zorgplichData)
		{
			foreach ($zorgplichData as $zpdata)
			{
				if($zpdata['Zorgplicht'] != $vorigeZpdata['Zorgplicht'])
				{
					if($this->waardeEurTotaal <> 0)
					{
						$this->pdf->row(array('Totaal', $this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2)));
					}
					$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
					$this->pdf->row(array($zpdata['Zorgplicht']));
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

					$this->zorgtotaal=0;
					$this->waardeEurTotaal =0;
				}
				$this->pdf->row(array($zpdata['fondsOmschrijving'],
													$this->formatGetal($zpdata['totaalAantal'],0),$this->formatGetal($zpdata['actueleFonds'],2),
													$this->formatGetal($zpdata['actuelePortefeuilleWaardeEuro'],2),
													$this->formatGetal($zpdata['Percentage'],1),
													$this->formatGetal($zpdata['totaal'],2)));
				$this->zorgtotaal += $zpdata['totaal'];
				$this->waardeEurTotaal += $zpdata['actuelePortefeuilleWaardeEuro'];
				$this->waardeEurTotaalAlles  += $zpdata['actuelePortefeuilleWaardeEuro'];
				$vorigeZpdata = $zpdata;
			}
		}
		if(round($this->waardeEurTotaal,1) <> 0.0)
			$this->pdf->row(array('Totaal','','',$this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2)));
		$this->pdf->ln();
		$this->pdf->row(array('Portefeuillewaarde',$this->formatGetal($zpwaarde['totaalWaarde'],2))); //$this->waardeEurTotaalAlles
		foreach ($zpwaarde['conclusie'] as $line)
		{
			$this->pdf->row($line);
		}
		$this->pdf->excelData[] = array('');


	}
}
?>