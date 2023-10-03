<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/16 17:36:34 $
File Versie					: $Revision: 1.16 $

$Log: CashLijst.php,v $
Revision 1.16  2019/11/16 17:36:34  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");

class CashLijst
{
	/*
		XLS en CSV
	*/
	var $selectData;
//	var $excelData;

	function CashLijst( $selectData )
	{

	  $this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "cash";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		$this->pdf->excelData = array();
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	  if(!$this->selectData['datumTm'])
	  {
	    $this->selectData['datumTm'] = time();
	  }
	  $einddatum = jul2sql($this->selectData['datumTm']);
		$jaar = date("Y",$this->selectData['datumTm']);

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		if($records <= 0)
		{
			echo "<b>".vt("Fout:")." ".vt("geen portefeuilles binnen selectie!")."</b>";
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

		$n=0;
		foreach($portefeuilles as $pdata)
		{
		  if($n==0)
		    $portefeuilleSelectie .= '\''.$pdata['Portefeuille'].'\'' ;
		  else
		    $portefeuilleSelectie .= ',\''.$pdata['Portefeuille'].'\'' ;
		  $n++;
		}

		$extraquery .= " AND Rekeningen.Portefeuille IN($portefeuilleSelectie)  ";

		if($this->selectData['bedrijf'])
		{
		  $extraquery .= " AND Portefeuilles.Vermogensbeheerder =  VermogensbeheerdersPerBedrijf.Vermogensbeheerder
		                   AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
		                   AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille
                       AND VermogensbeheerdersPerBedrijf.Bedrijf = '".$this->selectData['bedrijf']."'";
		  $extraTable .= ', VermogensbeheerdersPerBedrijf ';
		}
    
    $xlsHeader=array('Client','Portefeuille','Accountmanager','Rekening','IBAN','tenaamstelling','depotbank','Valuta','Saldo');
    
    if($_POST['nulTonen'] ==1)
      $nulFilter='';
    else
      $nulFilter="HAVING totaal <> 0";

    if($_POST['inactiefTonen'] ==1)
    {
      $inactiefFilter='';
      $xlsHeader[]='Inactief';
    }
    else
    {
      $inactiefFilter="Rekeningen.Inactief = 0 AND";
    }     
    
    $db = new DB();
    $crmVelden='';
    $extraJoin='';
    $extraVelden=array('PeriodiekeOnttrekking','PeriodiekeOnttrekkingTekst');
    foreach($extraVelden as $veld)
    {
      $query="SHOW fields FROM CRM_naw like '$veld'";
      if($db->QRecords($query) > 0)
      {
        $crmVelden.=",CRM_naw.$veld ";
        $xlsHeader[]=$veld;
        
        if($extraJoin=='')
          $extraJoin.=" LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille ";
      }
    }

		if($_POST['depositoTonen']==1)
			$depositoFilter='';
		else
			$depositoFilter='Rekeningen.Deposito = 0 AND';
        
	  $query = "
	  SELECT
      Rekeningen.Portefeuille ,
      Rekeningen.Valuta as rekeningValuta,
      Rekeningen.Tenaamstelling  as Tenaamstelling ,
      Rekeningen.IBANnr  as IBANnr ,
      Rekeningen.Depotbank as Depotbank,
      round(SUM(Rekeningmutaties.Bedrag),2) as totaal,
      Portefeuilles.Client,
      Portefeuilles.Accountmanager,
      Rekeningen.Rekening,
      Rekeningen.Inactief
      $crmVelden
    FROM
      (Rekeningmutaties, Rekeningen $extraTable )
	    Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      $extraJoin
    WHERE
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
      Rekeningen.Memoriaal = 0 AND
      $depositoFilter
      $inactiefFilter
      Rekeningmutaties.boekdatum >= '$jaar-01-01' AND
      Rekeningmutaties.boekdatum <=  '$einddatum'  $extraquery
    GROUP BY
      Portefeuilles.Portefeuille,
      Rekeningmutaties.Rekening
    $nulFilter
    ORDER BY
      Rekeningen.Portefeuille";
    
		$db->SQL($query);
		$db->Query();
		$this->pdf->excelData[] = $xlsHeader;
    $velden=array('Client','Portefeuille','Accountmanager','Rekening','IBANnr','Tenaamstelling','Depotbank','rekeningValuta','totaal');
    if($_POST['inactiefTonen'] ==1)
      $velden[]='Inactief';
		while($data=$db->nextRecord())
		{
		  $tmp=array();
      foreach($velden as $veld)
        $tmp[]=$data[$veld];
      foreach($extraVelden as $veld)
        $tmp[]=$data[$veld]; 
		  $this->pdf->excelData[] = $tmp;//array($data['Client'],$data['Portefeuille'],$data['Rekening'],$data['rekeningValuta'],$data['totaal'],$data['PeriodiekeOnttrekking']);
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
			echo vt("Fout:")." ".vt("kan niet schrijven naar")." ".$filename;
		}

	}
}
?>