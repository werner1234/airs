<?php

include_once("rapportRekenClass.php");

class FondsenlijstDoorkijk
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function FondsenlijstDoorkijk( $selectData )
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
    
    if($this->selectData['datumTm'])
    {
      $this->einddatum = jul2sql($this->selectData['datumTm']);
      $this->jaar = date("Y",$this->selectData['datumTm']);
    }
    else
    {
      $this->einddatum = date("Y-m-d");
      $this->selectData['datumTm']= $this->einddatum ;
      $this->jaar = date("Y");
    }
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
  
  function bepaalMsWegingPerFonds($fonds,$doorkijkSoort,$waarde)
  {
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->einddatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);
  
    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
  
    $wegingDoorkijkCategorie=array();
    if($db->records()>0)
    {
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['msCategorie']]['weging'] = round($row['weging'],4);
        $wegingDoorkijkCategorie[$row['msCategorie']]['waarde'] = $waarde * $row['weging'] / 100;
      }
    }
    else
    {
      $wegingDoorkijkCategorie['Geen MS']['weging'] = 100;
      $wegingDoorkijkCategorie['Geen MS']['waarde'] = $waarde;
    }
    return $wegingDoorkijkCategorie;
  }
  
  function bepaalWegingPerFonds($fonds,$doorkijkSoort,$airsCategorie,$waarde)
  {
    
    
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->einddatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);
    
    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie=array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];
      if($this->debug)
      {
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['weging'] = $row['weging'];
        $this->debugData['MSfondsWeging'][$doorkijkSoort][$fonds][$row['msCategorie']]['waarde'] = $waarde*$row['weging']/100;
      }
    }

    $wegingDoorkijkCategorie=array();
    $airsKoppelingen=array('REGION_ZOTHERND','ZSECTOR_OTHERND');
    if(count($wegingPerMsCategorie)>0)
    {
      foreach($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";
          
          if($db->records()>0)
          {
            
            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$categorie] / 100;
              
              if ($this->debug)
              {
                
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$categorie];
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde * $wegingPerMsCategorie[$categorie] / 100;
              }
            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }
      
      $msCategorienWhere=" bronKoppeling IN ('".implode("','",array_keys($wegingPerMsCategorie))."')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE $msCategorienWhere AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='MS' AND vermogensbeheerder='". $this->Vermogensbeheerder."'";
      $db->executeQuery($query);
      
      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
        }
      }
    }
    else
    {
      if(in_array($doorkijkSoort,array('Beleggingscategorien','Beleggingssectoren','Regios','Rating','Coupon','Looptijd')))
      {
        $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='" . mysql_real_escape_string($doorkijkSoort) . "' AND systeem='AIRS' AND vermogensbeheerder='" . $this->Vermogensbeheerder . "'";
        $db->executeQuery($query);
        if($db->records())
        {
          while ($row = $db->nextRecord())
          {
            $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
            $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
            
            if ($this->debug)
            {
              $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
              $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
            }
          }
        }
        else
        {
          $wegingDoorkijkCategorie[$airsCategorie]['weging'] = 100;
          $wegingDoorkijkCategorie[$airsCategorie]['waarde'] = $waarde;
          
          if($this->debug)
          {
            $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['weging'] = 100;
            $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['waarde'] = $waarde;
          }
        }
      }
      else
      {
        $wegingDoorkijkCategorie[$airsCategorie]['weging'] = 100;
        $wegingDoorkijkCategorie[$airsCategorie]['waarde'] = $waarde;
        
        if($this->debug)
        {
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['weging'] = 100;
          $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$airsCategorie]['waarde'] = $waarde;
        }
        
      }
    }
    
    
    return $wegingDoorkijkCategorie;
  }
  
  function getBuckets()
  {
  
    if(isset($this->buckets[$this->Vermogensbeheerder]))
      return $this->buckets[$this->Vermogensbeheerder];
    
    $db=new DB();
    $query="SELECT
doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,
doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie,
doorkijk_categoriePerVermogensbeheerder.min,
doorkijk_categoriePerVermogensbeheerder.max
FROM
doorkijk_categoriePerVermogensbeheerder
WHERE doorkijk_categoriePerVermogensbeheerder.Vermogensbeheerder='".$this->Vermogensbeheerder."'
ORDER BY doorkijk_categoriePerVermogensbeheerder.doorkijkCategoriesoort,doorkijk_categoriePerVermogensbeheerder.afdrukVolgorde,doorkijk_categoriePerVermogensbeheerder.min,doorkijk_categoriePerVermogensbeheerder.doorkijkCategorie";
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();

    while($row = $db->nextRecord())
    {
      $this->buckets[$this->Vermogensbeheerder][$row['doorkijkCategoriesoort']][$row['doorkijkCategorie']]=$row;
    }
    return $this->buckets[$this->Vermogensbeheerder];
}


function writeRapport()
	{
		global $__appvar;



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
		
		$einddatum=$this->einddatum;
    $jaar=$this->jaar;
	
		$query = "SELECT
              Rekeningen.Portefeuille,
              Portefeuilles.Client,
              Portefeuilles.Vermogensbeheerder,
              Vermogensbeheerders.geenStandaardSector,
              Fondsen.fonds,
              Fondsen.ISINcode,
              Fondsen.Valuta,
              Fondsen.Fondseenheid,
              Fondsen.standaardSector,
              Fondsen.Omschrijving AS FondsOmschrijving,
              IF(Fondsen.optieCode <> '',Fondsen.optieCode,Fondsen.FondsImportCode ) AS FondsImportCode,
              Fondsen.BBLandcode,
              Fondsen.Beurs,
              SUM(Rekeningmutaties.Aantal) AS totaalAantal,
              Fondsen.Rating
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
	            HAVING round(totaalAantal,4) <> 0
	            ORDER BY Rekeningen.Portefeuille,Rekeningmutaties.Fonds";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $DB2 = new DB();
	
    $doorkijkSoorten=array();
		$alleDoorkijkSoorten=array('Beleggingscategorien','Beleggingssectoren','Regios','Rating','Looptijd','Coupon');
		foreach($alleDoorkijkSoorten as $categorie)
    {
      if (isset($this->selectData['fondslijstDK_' . $categorie]) && isset($this->selectData['fondslijstDK_' . $categorie])==1)
      {
        $doorkijkSoorten[]=$categorie;
      }
		}
		$header = array('Portefeuille','Client','Fonds','FondsOmschrijving','FondsImportCode','ISINcode','Valuta','Beleggingscategorie','Beleggingssector','Regio','Rating','Waarde','Valutakoers','WaardeEUR','RenteEUR');
		if(count($doorkijkSoorten)>0)
    {
      $header[]='Doorkijk';
      $header[]='Doorkijkcategorie';
      $header[]='Percentage';
      $header[]='Waarde';
    }
  
    $this->pdf->excelData[] = $header;
    
    $airsCategorieVertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio','Rating'=>'Rating','Looptijd'=>'','Coupon'=>'');
      
 
    
    //'Looptijd'=>'datediff(TijdelijkeRapportage.Lossingsdatum,TijdelijkeRapportage.rapportageDatum)/365.2421','Coupon'=>'TijdelijkeRapportage.rentePercentage','Rating'=>'Fondsen.Rating
		while($data = $DB->nextRecord())
		{
      $koppelingdata=getFondsKoppelingen($data['Vermogensbeheerder'],$einddatum,$data['fonds'],$data['geenStandaardSector']);
      $data['Beleggingscategorie']=$koppelingdata['beleggingscategorie'];
      $data['Beleggingssector']=$koppelingdata['beleggingssector'];
      $data['Regio']=$koppelingdata['Regio'];
		  
		  $this->Vermogensbeheerder=$data['Vermogensbeheerder'];
		  $this->getBuckets();
       $rente=renteOverPeriode($data,$einddatum,(substr($einddatum,5,5)=='01-01'?true:false));
       $renteParameters=getRenteParameters($data['fonds'],$einddatum);
       $fondsKoers=$this->getFondsKoers($data['fonds'],$einddatum);
       $valutaKoers=$this->getValutaKoers($data['Valuta'],$einddatum);
       $waarde=$data['totaalAantal']*$fondsKoers*$data['Fondseenheid'];
      
      if (trim($data['Beleggingssector'])=='')
      {
        $data['Beleggingssector'] = $data['standaardSector'];
      }

      
    //   listarray($renteParameters['Rentepercentage']);
    //   listarray($data);
      if(count($doorkijkSoorten)>0)
      {
        foreach ($doorkijkSoorten as $doorkijkSoort)
        {
         // echo $doorkijkSoort;
          $airsWaarde = $data[$airsCategorieVertaling[$doorkijkSoort]];
          if ($doorkijkSoort == 'Looptijd')
          {
            foreach ($this->buckets[$this->Vermogensbeheerder][$doorkijkSoort] as $bucket => $bucketSettings)
            {
              if (($airsWaarde >= $bucketSettings['min'] && $airsWaarde < $bucketSettings['max']) || ($bucketSettings['min'] == $airsWaarde && $bucketSettings['max'] == $airsWaarde))
              {
                $airsWaarde = $bucket;
                break;
              }
            }
            if ($airsWaarde == '')
            {
              $airsWaarde = 'Overig';
            }
          }
          elseif ($doorkijkSoort == 'Coupon' && $data['fonds'] <> '')
          {
            if (isset($renteParameters['Rentepercentage']))
            {
              $airsWaarde = $renteParameters['Rentepercentage'];
            }
      
            foreach ($this->buckets[$this->Vermogensbeheerder][$doorkijkSoort] as $bucket => $bucketSettings)
            {
              if (($airsWaarde >= $bucketSettings['min'] && $airsWaarde < $bucketSettings['max']) || ($bucketSettings['min'] == $airsWaarde && $bucketSettings['max'] == $airsWaarde))
              {
                $row['airsSoort'] = $bucket;
                break;
              }
            }
            if ($airsWaarde === 0 || $airsWaarde == '')
            {
              $airsWaarde = 'Overig';
            }
      
          }
          elseif ($doorkijkSoort == 'Rating')
          {
            if ($airsWaarde == '')
            {
              $airsWaarde = 'NR';
            }
          }

          if(isset($this->selectData['fondslijstDK_MScat']) && $this->selectData['fondslijstDK_MScat'] == 1)
            $doorkijk = $this->bepaalMsWegingPerFonds($data['fonds'], $doorkijkSoort, $waarde * $valutaKoers);
          else
            $doorkijk = $this->bepaalWegingPerFonds($data['fonds'], $doorkijkSoort, $airsWaarde, $waarde * $valutaKoers);
//          listarray($doorkijk);
    

    
          foreach ($doorkijk as $doorkijkCategorie => $doorkijkDetails)
          {
            $row = array(
              $data['Portefeuille'],
              $data['Client'],
              $data['fonds'],
              $data['FondsOmschrijving'],
              $data['FondsImportCode'],
              $data['ISINcode'],
              $data['Valuta'],
              $data['Beleggingscategorie'],
              $data['Beleggingssector'],
              $data['Regio'],
              $data['Rating'],
              round($waarde, 2),
              $valutaKoers,
              round($waarde * $valutaKoers, 2),
              round($rente * $valutaKoers, 2));
      
            $row[] = $doorkijkSoort;
            $row[] = $doorkijkCategorie;
            $row[] = $doorkijkDetails['weging'];
            $row[] = $doorkijkDetails['waarde'];
      
            $this->pdf->excelData[] = $row;
          }
        }
      }
      else
      {
        $row = array(
          $data['Portefeuille'],
          $data['Client'],
          $data['fonds'],
          $data['FondsOmschrijving'],
          $data['FondsImportCode'],
          $data['ISINcode'],
          $data['Valuta'],
          $data['Beleggingscategorie'],
          $data['Beleggingssector'],
          $data['Regio'],
          $data['Rating'],
          round($waarde, 2),
          $valutaKoers,
          round($waarde * $valutaKoers, 2),
          round($rente * $valutaKoers, 2));
        $this->pdf->excelData[] = $row;
  
      }
		}
    
    if(isset($this->selectData['fondslijstDK_Liq']) && $this->selectData['fondslijstDK_Liq'] == 1)
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
      $DB2->SQL($query);
      $DB2->Query();
      while ($rekening = $DB2->nextRecord())
      {
        $valutaKoers = $this->getValutaKoers($rekening['rekeningValuta'], $einddatum);
        $row = array(
          $rekening['Portefeuille'],
          $rekening['Client'],
          $rekening['Rekening'],
          $rekening['Tenaamstelling'],
          '',
          '',
          $rekening['rekeningValuta'],
          $rekening['Beleggingscategorie'],
          $rekening['Beleggingscategorie'],
          $rekening['Regio'],
          '',
          round($rekening['totaal'], 2),
          $valutaKoers,
          round($rekening['totaal'] * $valutaKoers, 2),
          '');
    
        if (count($doorkijkSoorten) > 0)
        {
          foreach ($doorkijkSoorten as $doorkijkSoort)
          {
            $basis = $row;
            $basis[] = $doorkijkSoort;
            $basis[] = 'Liquiditeiten';
            $basis[] = round(100, 2);
            $basis[] = round($rekening['totaal'], 2);
            $this->pdf->excelData[] = $basis;
          }
        }
        else
        {
          $this->pdf->excelData[] = $row;
        }
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
?>