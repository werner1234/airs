<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/13 15:14:32 $
 		File Versie					: $Revision: 1.3 $

 		$Log: Waardeverloop.php,v $
 		Revision 1.3  2020/06/13 15:14:32  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/06/11 05:31:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/06/10 15:24:35  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2015/01/31 20:00:33  rvv
 		*** empty log message ***
 		
 
*/

include_once("rapportRekenClass.php");
include_once("indexBerekening.php");

class Waardeverloop
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Waardeverloop( $selectData )
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
	
	function getPeriode($julBegin,$julEind,$methode)
  {

    $index=new indexHerberekening();
    $datum=array();
    if($methode=='maanden')
    {
      $datum=$index->getMaanden($julBegin,$julEind);
    }
    elseif($methode=='dagKwartaal')
    {
      $datum=$index->getDagen($julBegin,$julEind);
    }
    elseif($methode=='kwartaal')
    {
      $datum=$index->getKwartalen($julBegin,$julEind);
    }
    elseif($methode=='jaar')
    {
      $datum=$index->getJaren($julBegin,$julEind);
    }
    elseif($methode=='dagYTD')
    {
      $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
    }
    elseif ($methode=='halveMaanden')
    {
      $datum=$index->getHalveMaanden($julBegin,$julEind);
    }
    elseif($methode=='weken')
    {
      $datum=$index->getWeken($julBegin,$julEind);
    }
    elseif($methode=='dagen')
    {
      $datum=$index->getDagen2($julBegin,$julEind);
    }
    $datumNew=array(array('stop'=>date('Y-m-d',$julBegin)));
    foreach($datum as $periode)
      $datumNew[]=$periode;

    return $datumNew;
  }

	function writeRapport()
	{
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
		//
		$perioden=$this->getPeriode($this->selectData['datumVan'],$this->selectData['datumTm'],$this->selectData['WaardeverloopPeriode']);
		
    $this->pdf->excelData[] = array('Portefeuille','Datum',"Categorie","Waarde");


		foreach($portefeuilles as $pdata)
		{
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}
			$portefeuille = $pdata['Portefeuille'];
			$verdeling=$this->selectData['WaardeverloopVerdeling'];
			foreach($perioden as $periode)
      {
        $waardeVerdeling=array();
        $categorieOmschrijvingen=array();
        //echo $periode['stop']." ".(substr($periode['stop'],5,5)=='01-01'?true:false)."<br>\n";
        $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $periode['stop'],(substr($periode['stop'],5,5)=='01-01'?true:false),'EUR',$periode['stop']);
        //listarray($fondswaarden);exit;
        foreach($fondswaarden as $fondsData)
        {
          if($fondsData[$verdeling]=='')
            $fondsData[$verdeling]='geen';
          $waardeVerdeling[$fondsData[$verdeling]]+=$fondsData['actuelePortefeuilleWaardeEuro'];
          $categorieOmschrijvingen[$fondsData[$verdeling]]=$fondsData[$verdeling.'Omschrijving'];
        }

        foreach($waardeVerdeling as $categorie=>$waarde)
        {
          if(isset($categorieOmschrijvingen[$categorie]) && $categorieOmschrijvingen[$categorie] <> '')
            $categorieOmschrijving=$categorieOmschrijvingen[$categorie];
          else
            $categorieOmschrijving=$categorie;
          
          $this->pdf->excelData[] = array(
            $pdata['Portefeuille'],
            $periode['stop'],
            $categorieOmschrijving,
            round($waarde,2));
        }
        

      }
		}

		if($this->progressbar)
			$this->progressbar->hide();
	}

}
?>