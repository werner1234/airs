<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2010/11/27 16:16:18 $
File Versie					: $Revision: 1.3 $

$Log: ClientAnalyse.php,v $
Revision 1.3  2010/11/27 16:16:18  rvv
*** empty log message ***

Revision 1.2  2010/11/17 17:16:33  rvv
*** empty log message ***

Revision 1.1  2010/11/14 10:46:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once("indexBerekening.php");

class ClientAnalyse
{
	var $selectData;

	function ClientAnalyse( $selectData )
	{

	  $this->selectData = $selectData;
		$this->pdf->excelData = array();
		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "ClientAnalyse";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->excelData = array();

		//$this->accountmanagerVeld="Accountmanager";
		$this->accountmanagerVeld="tweedeAanspreekpunt";
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	  $einddatum = jul2sql($this->selectData['datumTm']);
		$jaar = date("Y",$this->selectData['datumTm']);
    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby,true);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie(false);

		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if(is_object($this->progressbar))
			{
		 	  $this->progressbar->hide();
			  exit;
			}
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

    $vanaf=date("Y-m-d",$this->selectData['datumVan']);
    $tot=date("Y-m-d",$this->selectData['datumTm']);
		$portefeuilleSelectie=array();
		foreach($portefeuilles as $pdata)
		{
		  if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

  	  if($pdata['Portefeuille'] <> '')
  	  {
		    $index=new indexHerberekening();
        $indexData = $index->getWaarden($vanaf,$tot,$pdata['Portefeuille'],'','maanden');

        foreach ($indexData as $data)
        {
          if($pdata['SoortOvereenkomst'] == '')
            $pdata['SoortOvereenkomst']='Geen overeenkomst';

          $vermogensontwikkeling[$pdata['SoortOvereenkomst']][$data['datum']]['waarde']+=$data['waardeHuidige'];
          $vermogensontwikkeling[$pdata['SoortOvereenkomst']][$data['datum']]['stortingen']+=$data['stortingen'];
          $vermogensontwikkeling[$pdata['SoortOvereenkomst']][$data['datum']]['onttrekkingen']+=$data['onttrekkingen'];
          $vermogensontwikkeling[$pdata['SoortOvereenkomst']][$data['datum']]['perfWaarde']+=$data['performance']*$data['waardeHuidige'];
          if($data['datum'] == $tot)
            $portefeuilleWaarde[$pdata['Portefeuille']]=$data['waardeHuidige'];
        }
  	  }
  	  else
  	    $indexData=array();

		  $startJulian=db2jul($pdata['Startdatum']);
	    if ($startJulian >= $this->selectData['datumVan'] && $startJulian < $this->selectData['datumTm'])
		    $type='nieuw';
      else
		    $type='bestaande';

	    if($pdata[$this->accountmanagerVeld] == '')
		    $pdata[$this->accountmanagerVeld]='Geen Accountmanager';
		  $clienten[$type][$pdata[$this->accountmanagerVeld]]['vermogen']+=$portefeuilleWaarde[$pdata['Portefeuille']];
		  $clienten[$type][$pdata[$this->accountmanagerVeld]]['aantal']+=1;
		  $totalen[$type]['vermogen']+=$portefeuilleWaarde[$pdata['Portefeuille']];
		  $totalen[$type]['aantal']+=1;

		  $portefeuilleSelectie[]=$pdata['Portefeuille'];
		}

		$db=new DB();
		$query="SELECT prospectEigenaar,huidigesamenstellingTotaal FROM CRM_naw WHERE portefeuille NOT IN('".implode("','",$portefeuilleSelectie)."') AND Prospect=1";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if($data['prospectEigenaar'] == '')
        $data['prospectEigenaar']='Geen Prospect Eigenaar';
      $clienten['prospects'][$data['prospectEigenaar']]['vermogen']+=$data['huidigesamenstellingTotaal'];  //  vermogenTotaalBelegbaar -> huidigesamenstellingTotaal
      $clienten['prospects'][$data['prospectEigenaar']]['aantal']+=1;
		  $totalen['prospects']['vermogen']+=$data['huidigesamenstellingTotaal'];
		  $totalen['prospects']['aantal']+=1;

    }





		$vertaling=array('prospects'=>'Prospects','nieuw'=>'Geopende rekeningen','bestaande'=>'Cliënten');
		foreach ($clienten as $type=>$accountmanagerData)
		{
		  $getoond=array();
		  $this->pdf->excelData[] = array($vertaling[$type]);
		  $this->pdf->excelData[] = array("Accountmanager","Aantal","%","Vermogen","%");
		  foreach ($accountmanagerData as $accountmanager=>$waardes)
		  {
		    $this->pdf->excelData[] = array($accountmanager,$waardes['aantal'],$waardes['aantal']/$totalen[$type]['aantal'],$waardes['vermogen'],$waardes['vermogen']/$totalen[$type]['vermogen']);
		    $getoond['aantal']+=$waardes['aantal'];
		    $getoond['vermogen']+=$waardes['vermogen'];
		  }
		  $this->pdf->excelData[] = array('Totalen',$waardes['aantal'],$getoond['aantal']/$totalen[$type]['aantal'],$waardes['vermogen'],$getoond['vermogen']/$totalen[$type]['vermogen']);
		  $this->pdf->excelData[] = array("");
		}

		 $this->pdf->excelData[] = array("Vermogensverdeling per diensverlening");
		foreach ($vermogensontwikkeling as $type=>$datumData)
		{
		  $this->pdf->excelData[] = array($type);
		  $this->pdf->excelData[] = array("Datum","Waarde","Onttrekkingen","Stortingen","Performance");
		  foreach ($datumData as $datum=>$waardes)
		  {
		    $this->pdf->excelData[] = array($datum,$waardes['waarde'],$waardes['onttrekkingen'],$waardes['stortingen'],$waardes['perfWaarde']/$waardes['waarde']);
		  }
		  $this->pdf->excelData[] = array("");
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