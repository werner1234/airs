<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.2 $

 		$Log: RendementDetails.php,v $
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2015/01/31 20:00:33  rvv
 		*** empty log message ***
 		
 
*/

include_once("rapportRekenClass.php");
include_once("indexBerekening.php");

class RendementDetails
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RendementDetails( $selectData )
	{
		$this->pdf = new PDFRapport('L','mm');
	  $this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->orderby  = " Portefeuilles.ClientVermogensbeheerder ";

		$this->pdf->excelData = array();
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{

		$einddatum = jul2sql($this->selectData['datumTm']);
		$jaar = date("Y",$this->datumTm);

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();


		if($records <= 0)
		{
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
    $this->pdf->excelData[] = array('Portefeuille','Periode begin',"Periode eind","Rendement","Beginwaarde","Eindwaarde","Stortingen","Onttrekkingen","Resultaat periode");

    $index=new indexHerberekening();
    
    

		foreach($portefeuilles as $pdata)
		{

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			$portefeuille = $pdata['Portefeuille'];
      
      
      if(0==1)
      {
        include_once("rapport/include/ATTberekening_L35.php");
        $att=new ATTberekening_L35($this);
        $tmp=$att->bereken(date("Y-m-d",$this->selectData['datumVan']),date("Y-m-d",$this->selectData['datumTm']),'EUR','hoofdcategorie',$this->selectData['periode']);
        $waarden=array();
        foreach($tmp['totaal']['perfWaarden'] as $index=>$indexWaarden)
          $waarden[]=array('periodeForm'=>substr($indexWaarden['periode'],0,10)." - ".substr($indexWaarden['periode'],11,10),
          'performance'=>$indexWaarden['procent'],
          'waardeBegin'=>$indexWaarden['beginwaarde'],
          'waardeHuidige'=>$indexWaarden['eindwaarde'],
          'stortingen'=>$indexWaarden['stort'],
          'onttrekkingen'=>$indexWaarden['onttrekking'],
          'resultaatVerslagperiode'=>$indexWaarden['resultaat']);
        listarray($waarden);
      }
      else
      { 
       $waarden=$index->getWaarden(date("Y-m-d",$this->selectData['datumVan']),date("Y-m-d",$this->selectData['datumTm']),$portefeuille,'',$this->selectData['periode']);
		  }
      foreach($waarden as $i=>$indexWaarden)
		  	$this->pdf->excelData[] = array(
									 $pdata['Portefeuille'],
									 substr($indexWaarden['periodeForm'],0,10), 
                   substr($indexWaarden['periodeForm'],13,10),
                   $indexWaarden['performance'],
                   $indexWaarden['waardeBegin'],
                   $indexWaarden['waardeHuidige'],
                   $indexWaarden['stortingen'],
                   $indexWaarden['onttrekkingen'],
                   $indexWaarden['resultaatVerslagperiode']
									 );
                   
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}

}
?>