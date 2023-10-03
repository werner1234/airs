<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
File Versie					: $Revision: 1.3 $

$Log: RapportPERFD_L35.php,v $
Revision 1.3  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.2  2019/10/16 15:23:48  rvv
*** empty log message ***

Revision 1.1  2019/10/05 17:36:53  rvv
*** empty log message ***



*/
include_once("rapport/include/RapportOIB_L35.php");
include_once("rapport/include/RapportPERFG_L35.php");
include_once("rapport/include/ATTberekening_L35.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportPERFD_L35
{
	function RapportPERFD_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf = &$pdf;
	 	$this->oib = new RapportOIB_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->perfg = new RapportPERFG_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Kerngegevens rapportage";
  	$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


  function writeRapport()
	{
		// OIB grafiek rechts boven.
		// Perfg grafiek eerste pagina (totaal)

		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $this->pdf->rapportageValuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

		if ($this->pdf->rapport_layout == 1)
		{
			$kopStyle = "";
		}
		else
		{
			$kopStyle = "u";
		}

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(0, 85, 30, 10, 30, 120);
		$this->pdf->alignA = array('L', 'L', 'R', 'L', 'R');

		// voor kopjes
		$this->pdf->widthB = array(1, 95, 30, 10, 30, 120);
		$this->pdf->alignB = array('L', 'L', 'R', 'L', 'R');

		$this->pdf->AddPage();
		$this->pdf->templateVars['PERFDPaginas'] = $this->pdf->page;


		if (count($this->pdf->portefeuilles) > 0)
		{
//      $this->pdf->templateVars['PERFDPaginas'] = $this->pdf->page;
			$this->writeGeconsolideerd();
		}

	}

	function getFondsKoers($fonds,$datum)
	{
		$db=new DB();
		$query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers=$db->lookupRecord();
		return $koers['Koers'];
	}

	function getValutaKoers($valuta,$datum)
	{
		$db=new DB();
		$query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers=$db->lookupRecord();
		return $koers['Koers'];
	}

	function getPerformance($fonds,$vanaf,$tot,$valuta=false,$indexdata=array())
	{
		$att=new ATTberekening_L35($this);
		$maanden=$att->getMaanden(db2jul($vanaf),db2jul($tot));
		$januari=substr($tot,0,4)."-01-01";

		$totalPerf=0;
		foreach($maanden as $maand)
		{
			if($indexdata['catOmschrijving']=='Benchmark')
			{
				$totaalIndex=$att->indexPerformance('totaal',$maand['start'],$maand['stop']);
				$totalPerf+=($totaalIndex['perf']*100);
			}
			else
			{
				if($valuta==true)
					$indexData=array('fondsKoers_eind'=>$this->getValutaKoers($fonds,$maand['stop']),
													 'fondsKoers_begin'=>$this->getValutaKoers($fonds,$maand['start']),
													 'fondsKoers_jan'=>$this->getValutaKoers($fonds,$januari));
				else
					$indexData=array('fondsKoers_eind'=>$this->getFondsKoers($fonds,$maand['stop']),
													 'fondsKoers_begin'=>$this->getFondsKoers($fonds,$maand['start']),
													 'fondsKoers_jan'=>$this->getFondsKoers($fonds,$januari));

				$jaarPerf=($indexData['fondsKoers_eind'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
				$voorPerf=($indexData['fondsKoers_begin'] - $indexData['fondsKoers_jan']) / ($indexData['fondsKoers_jan']/100 );
				$totalPerf+=($jaarPerf-$voorPerf);
			}
			//echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
		}
		//echo "t $fonds $totalPerf  $vanaf,$tot <br>\n";
		return $totalPerf;
	}





	function writeGeconsolideerd()
	{
		$this->indexberekening=new indexHerberekening();
		$this->perioden=$this->indexberekening->getMaanden(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));


		$realCategorie=array();
		foreach($this->berekening->categorien as $categorie)
		{
			if($this->waarden['lopendeJaar']['eindWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['beginWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['stortingen'][$categorie] <> 0 || $this->waarden['lopendeJaar']['onttrekkingen'][$categorie] <> 0)
			{
				$realCategorie[]=$categorie;
			}
		}

		$tmpCat=array();
		foreach($realCategorie as $categorie)
		{
			if($categorie <> 'Totaal' && $categorie <> 'Liquiditeiten')
				$tmpCat[]=$categorie;
		}

		if(count($realCategorie) > 6)
			$x=185/count($realCategorie)-3;
		else
			$x=23;

		$this->pdf->widthA = array(0,115,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		$this->pdf->widthB = array(0,115,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');



		// if(is_array($this->pdf->__appvar['consolidatie']))
		// {


		$fillPortefeuilles=$this->pdf->portefeuilles;
		$fillPortefeuilles[]=$this->portefeuille;
		foreach($fillPortefeuilles as $portefeuille)
		{
			if(!isset($this->perfWaarden[$portefeuille]))
      {
        $this->perfWaarden[$portefeuille] = $this->getWaarden($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
      }
		}



		$backup=$this->pdf->portefeuilles;
		$aantalPortefeuilles=count($this->pdf->portefeuilles);
		if($aantalPortefeuilles>6)
		{
			$n=1;
			$p=0;
			$verdeling=array();
			$tmp=array();
			foreach($this->pdf->portefeuilles as $index=>$portefeuille)
			{
				//echo "$n $p $aantalPortefeuilles $portefeuille <br>\n";
				$tmp[]=$portefeuille;
				if($n%6==0 || $n == $aantalPortefeuilles)
				{
					$verdeling[$p]=$tmp;
					$tmp=array();
					$p++;
					// $n=0;
				}

				$n++;
			}
			
			foreach($verdeling as $pagina=>$portefeuilles)
			{
			  if($pagina>0)
        {
          $this->pdf->AddPage();
        }
				$this->pdf->portefeuilles=$portefeuilles;
				$this->addconsolidatie();
			}
			$this->pdf->portefeuilles=$backup;
		}
		else
			$this->addconsolidatie();

		// }

		if($this->pdf->debug)
		{
			// listarray($this->berekening->performance);flush();
			// exit;
		}
	}
  
  function getWaarden($portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    
    $backup=$this->portefeuille;
    $backupIndex=$this->pdf->portefeuilledata['SpecifiekeIndex'];
    $this->portefeuille=$portefeuille;
  
    $db = new DB();
    $query = "SELECT SpecifiekeIndex FROM Portefeuilles where Portefeuille='$portefeuille'";
    $db->SQL($query);
    $db->query();
    $port=$db->lookupRecord();
    $this->pdf->portefeuilledata['SpecifiekeIndex']=$port['SpecifiekeIndex'];
    
    $att = new ATTberekening_L35($this);
    $hcatData = $att->bereken( $rapportageDatumVanaf, $rapportageDatum, $this->pdf->rapportageValuta, 'hoofdcategorie');

    foreach ($hcatData as $cat => $waarden)
    {
      unset($hcatData[$cat]['perfWaarden']);
    }
  
    //echo $portefeuille;listarray($hcatData);
  /*
    $stortingen = getStortingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
    $onttrekkingen = getOnttrekkingen($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->rapportageValuta);
    $waardeMutatie=$hcatData['totaal']['eindwaarde']-$hcatData['totaal']['beginwaarde'];
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    $rendementProcent = performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
   // echo $this->pdf->portefeuilledata['PerformanceBerekening'];
   // echo "|$portefeuille | $rendementProcent | $resultaatVerslagperiode<br>\n";
    $hcatData['totaal']['procent']=$rendementProcent;
    $hcatData['totaal']['resultaat']=$resultaatVerslagperiode;
    */
    
    $this->portefeuille=$backup;
    $this->pdf->portefeuilledata['SpecifiekeIndex']=$backupIndex;

    
    
    return $hcatData;
  
  }
	


	function getCRMnaam($portefeuille)
	{
	  global $crm_velden;
		$db = new DB();
  
		if(count($crm_velden)==0)
    {
      $query = "desc CRM_naw";
      $db->SQL($query);
      $db->query();
      while ($data = $db->nextRecord('num'))
      {
        $crm_velden[] = $data[0];
      }
    }
    $extraVeld='';
    if(in_array('PortefeuilleNaam',$crm_velden))
    {
      $extraVeld = ',PortefeuilleNaam';
    }
    if(in_array('PortefeuilleBeheerder',$crm_velden))
    {
      $extraVeld .= ',PortefeuilleBeheerder';
    }
		$query="SELECT naam $extraVeld FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naam=$crmData['PortefeuilleNaam']."\n".$crmData['PortefeuilleBeheerder']."\n$portefeuille";


		return $naam;
	}
	



	function addconsolidatie()
	{
    $dataArray=array();
		if(!isset($this->pdf->__appvar['consolidatie']))
		{
			$this->pdf->__appvar['consolidatie']=1;
			$this->pdf->portefeuilles=array($this->portefeuille);
		}
		//$this->pdf->doubleHeader=true;
	//	$this->pdf->addPage();
		$this->pdf->templateVars['PERFDPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['PERFDPaginas']=$this->pdf->rapport_titel;
		$fillArray=array(0,1);
		$subOnder=array('','');
		$volOnder=array('U','U');
		$subBoven=array('','');
		$header=array("",'');

		$db=new DB();

	//	if(count($this->pdf->portefeuilles)<7)// && count($this->pdf->portefeuilles) > 1)
			$portefeuilles[]=$this->portefeuille;
	//	else
	//		$portefeuilles=array();

		foreach($this->pdf->portefeuilles as $portefeuille)
			$portefeuilles[]=$portefeuille;
		$longName=false;

		$perfWaarden=array();
		foreach($portefeuilles as $portefeuille)
		{
			$kop=$this->getCRMnaam($portefeuille);
			$query="SELECT Depotbanken.omschrijving,Portefeuilles.ClientVermogensbeheerder FROM Depotbanken JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
			$db->SQL($query);
			$depotbank=$db->lookupRecord();
			$volOnder[]='U';
			$volOnder[]='U';
			$subOnder[]='U';
			$subOnder[]='';
			$subBoven[]='T';
			$subBoven[]='';
			$fillArray[]=1;
			$fillArray[]=1;
			if($portefeuille==$this->portefeuille)
				$header[]=vertaalTekst("Totaal",$this->pdf->rapport_taal);
			else
			{
				if($portefeuille<> $kop)
					$header[] = $kop;
				elseif($depotbank['ClientVermogensbeheerder']<>'')
					$header[] =  $depotbank['omschrijving']. "\n" .  $depotbank['ClientVermogensbeheerder'] ;
				else
					$header[] =  $depotbank['omschrijving']. "\n" .$portefeuille;
			}
			$header[]='';

			if(!isset($this->perfWaarden[$portefeuille]))
				$this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);

			$perfWaarden[$portefeuille]=$this->perfWaarden[$portefeuille];
		}
  
		$variabelen=array('beginwaarde'=>'Beginvermogen','stortEnOnttrekking'=>'Mutaties','gerealiseerd'=>'Inkomsten uit beleggingen','eindwaarde'=>'Eindvermogen','resultaatBruto'=>'Rendement','procentBruto'=>'in %','indexPerf'=>'Benchmark');
  
      foreach($perfWaarden[$this->portefeuille]  as $categorie=>$catData)
      {
        foreach($variabelen as $key=>$omschrijving)
        {
          $dataArray[$categorie][$key][]=$omschrijving;
          $dataArray[$categorie][$key][] = '';
        }
      }
    



		foreach($perfWaarden as $portefeuille=>$waarden)
		{
      foreach($waarden as $categorie=>$catData)
      {
        foreach($variabelen as $key=>$omschrijving)
        {
     
          
          if($key=='procentBruto'||$key=='indexPerf')
          {
            $factor=1;
            if($key=='indexPerf')
              $factor=100;
            $dataArray[$categorie][$key][] = $this->formatGetal($catData[$key]*$factor,2);
            $dataArray[$categorie][$key][] = '%';
          }
          else
          {
            $dataArray[$categorie][$key][] = $this->formatGetal($catData[$key],0);
            $dataArray[$categorie][$key][] = '';
          }
          
        }
      }
		}

		// if($longName==true && count($portefeuilles) < 8)
		$cols=7;
		//else
		//  $cols=9;
    $w2=5;
		$w=(297-2*8-40-($w2*$cols))/$cols;
		
		$this->pdf->widthB = array(0,40,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		// $this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,10);//$this->pdf->rapport_kop_fontsize
		//$this->pdf->fillCell=$fillArray;
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($header);



		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$categorieVertaling=array('ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','totaal'=>'Totaal');
		foreach($dataArray as $categorie=>$rows)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($categorieVertaling[$categorie]));
      $this->pdf->line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+40,$this->pdf->getY());
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($rows as $row)
      {
        $this->pdf->row($row);
      }
      $this->pdf->line($this->pdf->marge,$this->pdf->getY(),297-$this->pdf->marge,$this->pdf->getY());
      $this->pdf->ln();
    }
		
		//unset( $this->pdf->CellBorders );
		//$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	

		
	}


}

?>