<?php

include_once("rapportRekenClass.php");

class FondslijstKlein
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function FondslijstKlein( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "fondslijst";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Client ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function getFondsKoers($fonds, $datum)
  {
    $db = new DB();
    $query = "SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers = $db->lookupRecord();
    
    return $koers['Koers'];
  }
  function getValutaKoers($valuta, $datum)
  {
    $db = new DB();
    $query = "SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers = $db->lookupRecord();
    
    return $koers['Koers'];
  }



function writeRapport()
	{
		global $__appvar;

		if($this->selectData['datumTm'])
		{
		$einddatum = jul2sql($this->selectData['datumTm']);
		$jaar = date("Y",$this->selectData['datumTm']);
		}
		else
		{
		$einddatum = date("Y-m-d");
		$this->selectData['datumTm']=$einddatum;
		$jaar = date("Y");
		}

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";
  
		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			  $this->progressbar->hide();
			exit;
		}
    
    
    $query = "SELECT
              Rekeningen.Portefeuille,
              Portefeuilles.Vermogensbeheerder,
              Vermogensbeheerders.geenStandaardSector,
              Portefeuilles.Client,
              Fondsen.fonds,
              Fondsen.ISINcode,
              Fondsen.Valuta,
              Fondsen.Fondseenheid,
              Fondsen.Omschrijving AS FondsOmschrijving,
              IF(Fondsen.optieCode <> '',Fondsen.optieCode,Fondsen.FondsImportCode ) AS FondsImportCode,
              Fondsen.BBLandcode,
              Fondsen.Beurs,
              SUM(Rekeningmutaties.Aantal) AS totaalAantal
	            FROM Rekeningmutaties
	            JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
	            JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	            JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
	            JOIN Fondsen ON Fondsen.Fonds = Rekeningmutaties.Fonds
              WHERE
	            Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	            YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	            Rekeningmutaties.Verwerkt = '1' AND
	            Rekeningmutaties.Boekdatum <= '$einddatum' $extraquery
		          GROUP BY Rekeningen.Portefeuille,Rekeningmutaties.Fonds
	            HAVING round(totaalAantal,6) <> 0
	            ORDER BY Rekeningen.Portefeuille,Rekeningmutaties.Fonds ; ";
    
    
    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$n=0;
		$this->pdf->excelData[] = array('Account Number','ISIN','Currency','Amount','Rate','Value');
		while($data = $DB->nextRecord())
		{
      $koppelingdata=getFondsKoppelingen($data['Vermogensbeheerder'],$einddatum,$data['fonds'],$data['geenStandaardSector']);
      $data['Beleggingscategorie']=$koppelingdata['beleggingscategorie'];
      
       $fondsKoers=$this->getFondsKoers($data['fonds'],$einddatum);
       $valutaKoers=$this->getValutaKoers($data['Valuta'],$einddatum);
       $waarde=$data['totaalAantal']*$fondsKoers*$data['Fondseenheid'];
       
		  
			 $this->pdf->excelData[] = array(
         $data['Portefeuille'],
         $data['ISINcode'],
         $data['Valuta'],
         round($data['totaalAantal'],6),
         $fondsKoers,
         round($waarde*$valutaKoers,2)
         );
		}
      
      if(1)//isset($this->selectData['fondslijst_Liq']) && $this->selectData['fondslijst_Liq'] == 1)
      {
        $query = "SELECT Rekeningen.Portefeuille,Portefeuilles.Client, Rekeningen.Valuta as rekeningValuta, Rekeningen.Tenaamstelling  as Tenaamstelling , Rekeningen.Beleggingscategorie,
      Rekeningen.Depotbank as Depotbank,  round(SUM(Rekeningmutaties.Bedrag),2) as totaal, Rekeningen.Rekening, ValutaPerRegio.Regio
    FROM
      Rekeningmutaties
      JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      LEFT JOIN ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
    WHERE
      Rekeningen.Memoriaal = 0 AND
      Rekeningmutaties.boekdatum >= '$jaar-01-01' AND
      Rekeningmutaties.boekdatum <=  '$einddatum' $extraquery
    GROUP BY
      Rekeningen.Portefeuille,
      Rekeningmutaties.Rekening
    HAVING totaal <> 0
    ORDER BY
      Rekeningen.Portefeuille";
        $DB->SQL($query);
        $DB->Query();
        while ($rekening = $DB->nextRecord())
        {
          $valutaKoers = $this->getValutaKoers($rekening['rekeningValuta'], $einddatum);
          $row = array(
            $rekening['Portefeuille'],
            $rekening['Rekening'],
            $rekening['rekeningValuta'],
            round($rekening['totaal'], 2),
            $valutaKoers,
            round($rekening['totaal'] * $valutaKoers, 2)
            );
          
           $this->pdf->excelData[] = $row;
        }
      }

	 		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$exceldata = generateCSV($this->pdf->excelData);
			fwrite($fp,$exceldata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
	}
}

if($debug==1)
{
  $lijst=new FondslijstKlein();
}
?>