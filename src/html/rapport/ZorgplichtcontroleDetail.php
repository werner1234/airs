<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
 		File Versie					: $Revision: 1.15 $

 		$Log: ZorgplichtcontroleDetail.php,v $
 		Revision 1.15  2019/11/16 17:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/08/29 12:20:25  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.12  2013/10/05 15:57:41  rvv
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


class ZorgplichtcontroleDetail
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function ZorgplichtcontroleDetail( $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "zorgplichtcontroleDetail";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->orderby  = " Client ";

		$this->pdf->excelData[]=array('Portefeuille',
      'Categorie',
      'Fonds',
      'Aantal',
      'Koers',
      'Portefeuillewaarde EUR',
      'Percentage',
      'ZorgWaarde',
      'PortefeuilleWaarde totaal',
      'ZP-weging');
		//rvv
		loadLayoutSettings($this->pdf, $this->selectData['portefeuilleVan']); //rvv 29-08-06
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = jul2sql($this->selectData['datumTm']);

		$zorgplicht = new Zorgplichtcontrole();


		$this->pdf->setWidths(array(90,30,30,30,30,30,40));
		$this->pdf->setAligns(array('L','R','R','R','R','R'));

		$this->pdf->__appvar = $this->__appvar;

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->datumTm);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();


		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


		foreach($portefeuilles as $pdata)
		{
		  $this->pdf->portefeuille = $pdata['Portefeuille'];
		  $this->pdf->rapport_kop = $pdata['Portefeuille']." - ".$pdata['Client']." - ".$pdata['Naam'];
		  $this->pdf->AddPage();
			$this->zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
			$totalen = array();
			$this->waardeEurTotaalAlles =0;
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
				$portefeuille = $pdata['Portefeuille'];
			$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,  $einddatum);
			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);
			runPreProcessor($portefeuille);
		  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$einddatum);
      $vorigeZpdata=array();
			foreach ($zpwaarde['detail'] as $zorgplichData)
			{
			  foreach ($zorgplichData as $zpdata)
			  {
  		  if($zpdata['Zorgplicht'] != $vorigeZpdata['Zorgplicht'])
			  {
			    if($this->waardeEurTotaal <> 0)
			    {
			      $this->pdf->row(array('Totaal','','', $this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2)));
			   			    }
  	    	$this->pdf->SetFont("Times","B",10);
  	      $this->pdf->row(array($zpdata['Zorgplicht']));
  	    	$this->pdf->SetFont("Times","",10);
  			  $this->zorgtotaal=0;
  			  $this->waardeEurTotaal =0;
 			  }
        $this->pdf->row(array($zpdata['fondsOmschrijving'],
                         $this->formatGetal($zpdata['totaalAantal'],0),$this->formatGetal($zpdata['actueleFonds'],2), 
                         $this->formatGetal($zpdata['actuelePortefeuilleWaardeEuro'],2),
                         $this->formatGetal($zpdata['Percentage'],1),$this->formatGetal($zpdata['totaal'],2)));
				$this->zorgtotaal += $zpdata['totaal'];
				$this->waardeEurTotaal += $zpdata['actuelePortefeuilleWaardeEuro'];
				$this->waardeEurTotaalAlles  += $zpdata['actuelePortefeuilleWaardeEuro'];
		  	$vorigeZpdata = $zpdata;
        $this->pdf->excelData[]=array($pdata['Portefeuille'],
          $zpdata['Zorgplicht'],
          $zpdata['fondsOmschrijving'],
          round($zpdata['totaalAantal'],0),
          round($zpdata['actueleFonds'],2),
          round($zpdata['actuelePortefeuilleWaardeEuro'],2),
          round($zpdata['Percentage'],1),
          round($zpdata['totaal'],2),
          round($zpwaarde['totaalWaarde'],2),
          round($zpdata['totaal']/$zpwaarde['totaalWaarde']*100,1),);
			  }
		  }
      if(round($this->waardeEurTotaal,1) <> 0.0)
		  	$this->pdf->row(array('Totaal','','', $this->formatGetal($this->waardeEurTotaal,2),'',$this->formatGetal($this->zorgtotaal,2)));
	    $this->pdf->ln();
			$this->pdf->row(array('Portefeuillewaarde','','',$this->formatGetal($zpwaarde['totaalWaarde'],2))); //$this->waardeEurTotaalAlles
			foreach ($zpwaarde['conclusie'] as $line)
			{
			     $this->pdf->row($line);
			     
			}
      $conclusies[$pdata['Portefeuille']]=$zpwaarde['conclusie'];
			
			verwijderTijdelijkeTabel($portefeuille, $einddatum);
		}
    $this->pdf->excelData[] = array('');
		foreach($conclusies as $portefeuille=>$conlusieRegels)
    {
      foreach ($conlusieRegels as $regel)
      {
        $regel[2]=str_replace(array('.',','),array('','.'),$regel[2]);
        $regel[3]=str_replace(array('.',','),array('','.'),$regel[3]);
        $regel[4]=str_replace(array("\n"),array(" "),$regel[4]);
  
        $regel=array_reverse($regel);
        $regel[]=$portefeuille;
        $regel=array_reverse($regel);
        
        $this->pdf->excelData[] = $regel;
      }
    }
		if($this->progressbar)
			$this->progressbar->hide();
	}
}
?>