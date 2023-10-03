<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.12 $

$Log: CashPositie.php,v $
Revision 1.12  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.11  2018/10/27 16:48:02  rvv
*** empty log message ***

Revision 1.10  2015/06/13 13:14:42  rvv
*** empty log message ***

Revision 1.9  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.8  2008/12/03 09:50:18  rvv
*** empty log message ***

Revision 1.7  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.6  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.5  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.4  2006/11/27 09:27:57  rvv
Nu alle rekeningen

Revision 1.3  2006/08/18 08:25:16  cvs
actuelePortefeuilleWaardeEuro toevoegen in csv

Revision 1.2  2006/07/14 12:46:50  cvs
*** empty log message ***

Revision 1.1  2006/07/13 18:31:24  cvs
*** empty log message ***




*/

include_once("rapportRekenClass.php");

class CashPositie
{
	/*
		PDF en CSV
	*/
	var $selectData;
//	var $excelData;

	function CashPositie( $selectData )
	{

	  $this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "cash";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData[datumTm];

		$this->orderby  = " Portefeuilles.ClientVermogensbeheerder ";

		$this->groupering='Portefeuille';
		//$this->groupering='Rekening';

		$this->pdf->excelData = array();
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$einddatum = jul2sql($this->selectData['datumTm']);

		$this->pdf->__appvar = $this->__appvar;
		// controle op einddatum portefeuille
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

		$this->pdf->AddPage();

		$totalenArray = array();
    $valutaArray = array();
    $typenArray = array();
		$db=new DB();
		foreach($portefeuilles as $pdata)
		{
//		  $myData = berekenRekeningWaarde($portefeuille, $rapportageDatum);


			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			// set portefeuillenr
			// load settings.
			$portefeuille = $pdata['Portefeuille'].'';
			$this->pdf->portefeuille = $portefeuille;

      $portWaardeArray = berekenPortefeuilleWaardeQuick($portefeuille, $einddatum);
      

      
      $portefeuilleWaarde = 0;
      foreach($portWaardeArray as $x=>$regelData)
      {
        $totalenArray[$portefeuille]['Client']=$pdata['Client'];
        $totalenArray[$portefeuille]['Naam']=$pdata['Naam'];
        $totalenArray[$portefeuille]['Portefeuille']=$pdata['Portefeuille'];
        $totalenArray[$portefeuille]['Depotbank']=$pdata['Depotbank'];
        $totalenArray[$portefeuille]['SoortOvereenkomst']=$pdata['SoortOvereenkomst'];
        $totalenArray[$portefeuille]['Risicoklasse']=$pdata['Risicoklasse'];
 
        $totalenArray[$portefeuille]['portefeuilleWaarde']+=$regelData["actuelePortefeuilleWaardeEuro"];


        if($regelData["type"]=='rekening')
        {
          $totalenArray[$portefeuille]['rekeningSaldoEur']+=$regelData['actuelePortefeuilleWaardeEuro'];
          
          $query="SELECT Termijnrekening,Deposito FROM Rekeningen WHERE Rekening='".$regelData["rekening"]."'";
          $db->SQL($query);
          $rekeningType=$db->lookupRecord();
          if($rekeningType['Termijnrekening']==1)
          {
            $rekeningSoort='Termijn';
          }
          elseif($rekeningType['Deposito']==1)
          {
            $rekeningSoort='Deposito';
          }
          else
          {
            $rekeningSoort='Rekening';
          }
          $totalenArray[$portefeuille][$regelData['valuta']][$rekeningSoort]['actuelePortefeuilleWaardeInValuta']+=$regelData['actuelePortefeuilleWaardeInValuta'];
          $totalenArray[$portefeuille][$regelData['valuta']][$rekeningSoort]['actuelePortefeuilleWaardeEuro']+=$regelData['actuelePortefeuilleWaardeEuro'];
  
          $valutaArray[$regelData['valuta']][$rekeningSoort]=$rekeningSoort;
          $typenArray[$rekeningSoort]=$rekeningSoort;
        }
        
      }
    
     
		verwijderTijdelijkeTabel($portefeuille, $this->selectData['datumTm']);

		}
    
    
    
    
    $header=array("Client","Naam","Portefeuille","Depotbank",'SoortOvereenkomst','Risicoklasse',"actuelePortefeuilleWaardeEuro");
    foreach($valutaArray as $valuta=>$aanwezigeTypen)
    {
      foreach($typenArray as $rekeningSoort)
      {
        if(in_array($rekeningSoort,$aanwezigeTypen))
        {
          $header[] = "$valuta $rekeningSoort in EUR";
          $header[] = "$valuta $rekeningSoort in Valuta";
        }
      }
    }
    $header[]='Saldo Eur';
    
    $this->pdf->excelData[]=$header;
    
    foreach($totalenArray as $portefeuille=>$pdata)
    {
      $row = array($pdata['Client'],
        $pdata['Naam'],
        $pdata['Portefeuille'],
        $pdata['Depotbank'],
        $pdata['SoortOvereenkomst'],
        $pdata['Risicoklasse'],
        round($pdata['portefeuilleWaarde'], 2));
  
      foreach($valutaArray as $valuta=>$aanwezigeTypen)
      {
        foreach($typenArray as $rekeningSoort)
        {
          if(in_array($rekeningSoort,$aanwezigeTypen))
          {
            $row[] = round($pdata[$valuta][$rekeningSoort]['actuelePortefeuilleWaardeEuro'], 2);
            $row[] = round($pdata[$valuta][$rekeningSoort]['actuelePortefeuilleWaardeInValuta'], 2);
          }
        }
      }
      $row[] = round($pdata['rekeningSaldoEur'], 2);
      $this->pdf->excelData[] =$row;
    }


    
    
		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$excelData = generateCSV($this->pdf->excelData);
			fwrite($fp,$excelData);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}
}
?>