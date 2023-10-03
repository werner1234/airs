<?php

include_once("rapportRekenClass.php");

class Vermogensverloop
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Vermogensverloop (  $selectData )
	{
    global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "vermogensverloop";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

		if($this->selectData['manExtraVelden'] == 1)
		  $this->extraVeldenTonen=true;
		else
		  $this->extraVeldenTonen=false;

	//	listarray($this->selectData);exit;
		if($this->selectData['verloopGroupBy'] <> '')
		  $this->orderby=" Portefeuilles.".$this->selectData['verloopGroupBy'];
		else
			$this->orderby= " Clienten.Client ";
/*
		if($this->selectData['orderbyVermogensbeheerder'] == 1)
		{
			$this->orderby  = " Portefeuilles.Vermogensbeheerder ";
			if($this->selectData['orderbyAccountmanager'] == 1)
				$this->orderby  .= " , Portefeuilles.Accountmanager ";
		}
		else if($this->selectData['orderbyAccountmanager'] == 1)
				$this->orderby  = " Portefeuilles.Accountmanager ";
		else
		{
			$this->orderby  = " Clienten.Client ";
		}
*/
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
  	global $__appvar;
		$this->pdf->__appvar = $this->__appvar;
    $this->pdf->AddPage();
    if($this->selectData['verloopDetails']==2)
    {
      $this->pdf->SetWidths(array(33, 33, 30, 33, 30, 30, 30, 20, 30));
      $this->pdf->SetAligns(array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R'));
      $header = array("Vermogensbeheerder", "Accountmanager", "Depotbank", "SoortOvereenkomst", "Risicoprofiel", "InternDepot", "Portefeuille", "Clientid", "Eindvermogen");
      $db=new DB();
      $headerXls=$header;
    }
    else
    {
      $this->pdf->SetWidths(array(30, 50, 30, 30, 30, 30, 25, 20, 30));
      $this->pdf->SetAligns(array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R'));
      $header = array("Client", "Naam", "Portefeuille", "Accountmanager", "Beginvermogen", "Stortingen", "Onttrekkingen", "Resultaat", "Eindvermogen");
      if($this->selectData['verloopDetails']==0)
        $headerXls=array("Categorie","Beginvermogen","Stortingen","Onttrekkingen","Resultaat","Eindvermogen");
      else
        $headerXls=array("Client","Naam","Portefeuille","Accountmanager","Startdatum",'Einddatum','Einddatum portefeuille',
          'Risicoprofiel',"Depotbank","Risicoprofiel","SoortOvereenkomst",
          "Beginvermogen","Stortingen","Onttrekkingen","Resultaat","Eindvermogen");
    }

    $this->pdf->Row($header);
  	$this->pdf->excelData[] = $headerXls;
 		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(), $this->pdf->marge + 280,$this->pdf->GetY());
		$this->pdf->SetFont("Times","",10);
		$tel = 0;
    
    if($this->selectData['verloopDetails']==2)
      $metEinddatum=false;
    else
      $metEinddatum=true;

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby,true,$metEinddatum);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie(false);

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

		$rapportageDatum['a'] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);
		// vul eerst de tijdelijketabel
		$optelVelden=array('begin','storting','onttrekking','resultaat','eind');
		$subTotalen=array();
		$totalen=array();


		foreach($portefeuilles as $pdata)
		{
			$categorie=$pdata[$this->selectData['verloopGroupBy']];
			if($categorie=='')
				$categorie='Geen categorie';

			if(isset($lastCategorie) && $categorie<>$lastCategorie)
			{
        //logScherm("categorie  ($categorie)<>($lastCategorie) ");
				if($this->selectData['verloopGroupBy'] <> '')
					$this->addSubtotaal($subTotalen, $lastCategorie);
				$subTotalen=array();
			}
			$lastCategorie=$categorie;

		  $portefeuille=$pdata['Portefeuille'];
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." berekenen.");
			}

			$startdatum = $rapportageDatum['a'];
      $einddatum  = $rapportageDatum['b'];
      
      if(db2jul($pdata['Einddatum'])< db2jul($startdatum))
        continue;
      
      if(db2jul($pdata['Einddatum'])< db2jul($einddatum)) // && db2jul($pdata['Einddatum']) > db2jul($startdatum)
        $einddatum=substr($pdata['Einddatum'],0,10);
      
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;


      $waarden=array();
      
      $fondswaarden['b'] = berekenPortefeuilleWaarde($portefeuille,  $einddatum);
      foreach($fondswaarden['b'] as $regel)
        $waarden['eind']+=$regel['actuelePortefeuilleWaardeEuro'];

      if($this->selectData['verloopDetails']<>2)
      {
        $fondswaarden['a'] = berekenPortefeuilleWaarde($portefeuille, $startdatum, $startjaar);
        foreach($fondswaarden['a'] as $regel)
          $waarden['begin']+=$regel['actuelePortefeuilleWaardeEuro'];
        
        $waarden['storting']=getStortingen($portefeuille,$startdatum,$einddatum);
        $waarden['onttrekking']=getOnttrekkingen($portefeuille,$startdatum,$einddatum);
  
        if(round($waarden['begin'],4) <> 0.00 || round($waarden['eind'],4) <> 0.00 || round($waarden['storting'],4) <> 0.00 || round($waarden['onttrekking'],4) <> 0.00)
        {
    
          if(db2jul($pdata['Einddatum'])<= db2jul($einddatum))
          {
            if($waarden['eind'] <> 0)
            {
              $waarden['onttrekking']+=$waarden['eind'];
              $waarden['eind']=0;
              $pdata['naam']=$pdata['naam']."*";
            }
            //logscherm($waarden['eind'].'-'.$waarden['begin'].'+'.$waarden['onttrekking'].'-'.$waarden['storting']);
          }
          $waarden['resultaat']=$waarden['eind']-$waarden['begin']+$waarden['onttrekking']-$waarden['storting'];
    
          foreach($waarden as $key=>$value)
            $totalen[$key]+=$value;
    
          foreach($optelVelden as $veld)
            $subTotalen[$veld]+=$waarden[$veld];
    
          //logScherm($pdata['Portefeuille']." ".$pdata['Accountmanager']." ".$subTotalen['begin']);
    
          if($this->selectData['verloopDetails']==1)
          {
            $this->pdf->Row(array($pdata['Client'], $pdata['naam'], $pdata['Portefeuille'], $pdata['Accountmanager'],
                              $this->formatGetal($waarden['begin'], 0),
                              $this->formatGetal($waarden['storting'], 0),
                              $this->formatGetal($waarden['onttrekking'], 0),
                              $this->formatGetal($waarden['resultaat'], 0),
                              $this->formatGetal($waarden['eind'], 0)));
            $this->pdf->excelData[] = array($pdata['Client'], $pdata['naam'], $pdata['Portefeuille'], $pdata['Accountmanager'],
              adodb_date("d-m-Y", adodb_db2jul($pdata['Startdatum'])),
              adodb_date("d-m-Y", adodb_db2jul($einddatum)),
              adodb_date("d-m-Y", adodb_db2jul($pdata['Einddatum'])),
              $pdata['Risicoklasse'],
              $pdata['Depotbank'],
              $pdata['Risicoklasse'],
              $pdata['SoortOvereenkomst'],
              round($waarden['begin'], 2),
              round($waarden['storting'], 2),
              round($waarden['onttrekking'], 2),
              round($waarden['resultaat'], 2),
              round($waarden['eind'], 2));
          }
        }
      }
      else
      {
         $query = "SELECT id as Clientid FROM Clienten WHERE Client='" . mysql_real_escape_string($pdata['Client']) . "'";
         $db->SQL($query);
         $tmp = $db->lookupRecord();
         $this->pdf->Row(array($pdata['Vermogensbeheerder'], $pdata['Accountmanager'], $pdata['Depotbank'], $pdata['SoortOvereenkomst'], $pdata['Risicoklasse'], $pdata['InternDepot'], $pdata['Portefeuille'], $tmp['Clientid'], $this->formatGetal($waarden['eind'], 0)));
         $this->pdf->excelData[] = array($pdata['Vermogensbeheerder'], $pdata['Accountmanager'], $pdata['Depotbank'], $pdata['SoortOvereenkomst'], $pdata['Risicoklasse'], $pdata['InternDepot'], $pdata['Portefeuille'], $tmp['Clientid'], round($waarden['eind'], 2));
      }


      
    
	 }
    
    if($this->selectData['verloopDetails']<>2)
    {
      if ($this->selectData['verloopGroupBy'] <> '')
      {
        $this->addSubtotaal($subTotalen, $categorie);
      }
  
      $this->pdf->ln();
      $this->pdf->SetFont("Times", "B", 10);
      $this->pdf->Row(array('Totaal', '', '', '',
                        $this->formatGetal($totalen['begin'], 0),
                        $this->formatGetal($totalen['storting'], 0),
                        $this->formatGetal($totalen['onttrekking'], 0),
                        $this->formatGetal($totalen['resultaat'], 0),
                        $this->formatGetal($totalen['eind'], 0)));
    }

		$this->pdf->SetFont("Times","",10);
		if($this->progressbar)
			$this->progressbar->hide();
	}

	function addSubtotaal($subData,$categorie)
	{
		$this->pdf->SetFont("Times","B",10);
		$this->pdf->Row(array('Totaal',$categorie,'','',
											$this->formatGetal($subData['begin'],0),
											$this->formatGetal($subData['storting'],0),
											$this->formatGetal($subData['onttrekking'],0),
											$this->formatGetal($subData['resultaat'],0),
											$this->formatGetal($subData['eind'],0)));

		if($this->selectData['verloopDetails']==0)
			$this->pdf->excelData[] = array('Totaal '.$categorie,
				round($subData['begin'],2),
				round($subData['storting'],2),
				round($subData['onttrekking'],2),
				round($subData['resultaat'],2),
				round($subData['eind'],2));

		$this->pdf->SetFont("Times","",10);
		if($this->selectData['verloopDetails']==1)
		  $this->pdf->Ln();
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