<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/27 09:45:17 $
File Versie					: $Revision: 1.13 $

$Log: Fondsverloop.php,v $
Revision 1.13  2017/05/27 09:45:17  rvv
*** empty log message ***

Revision 1.12  2016/11/27 11:08:08  rvv
*** empty log message ***

Revision 1.11  2015/12/19 09:11:58  rvv
*** empty log message ***

Revision 1.10  2015/07/29 16:09:07  rvv
*** empty log message ***

Revision 1.9  2015/06/20 15:33:40  rvv
*** empty log message ***

Revision 1.8  2014/08/13 14:53:34  rvv
*** empty log message ***

Revision 1.7  2014/07/23 15:41:55  rvv
*** empty log message ***

Revision 1.6  2014/07/16 16:00:18  rvv
*** empty log message ***

Revision 1.5  2014/07/12 15:29:41  rvv
*** empty log message ***

Revision 1.4  2014/05/21 15:20:33  rvv
*** empty log message ***

Revision 1.3  2014/04/23 16:17:36  rvv
*** empty log message ***

Revision 1.2  2014/04/16 15:50:14  rvv
*** empty log message ***

Revision 1.1  2014/03/16 11:16:56  rvv
*** empty log message ***

Revision 1.26  2013/05/04 15:59:12  rvv
*** empty log message ***

Revision 1.25  2012/11/21 16:27:53  rvv
*** empty log message ***

Revision 1.24  2011/04/30 16:27:12  rvv
*** empty log message ***

Revision 1.23  2010/11/17 17:16:33  rvv
*** empty log message ***

*/

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('max_execution_time',60);

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/config/applicatie_functies.php");

class Fondsverloop
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Fondsverloop( $selectData )
	{
		$this->selectData = $selectData;
		$this->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "Fondsverloop";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 280;//190;

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

	function formatGetal($waarde, $dec,$geenNul=false)
	{
	  if ($geenNul && round($waarde,2) == 0)
	    return '';
	  else
		  return number_format($waarde,$dec,",",".");
	}

	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->__appvar = $this->__appvar;
    logScherm("Begin Fondsverloop rapport");

		$einddatum = jul2sql($this->selectData['datumTm']);

    $this->selectData['FondsBeginpositie']=1;
		if ($this->selectData['FondsBeginpositie'])
		  $begindatum = '1990-01-01';
		else
		  $begindatum = jul2sql($this->selectData['datumVan']);

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Valutakoersen.Koers FROM Valutakoersen, Fondsen WHERE ".
						 " Fondsen.Fonds  			= '".$this->selectData['fondsverloopFonds']."' AND ".
						 " Valutakoersen.Valuta = Fondsen.Valuta AND ".
						 " Valutakoersen.Datum <= '".$einddatum."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();
		$kdata 	= $DB2->nextRecord();
		$valutakoers = $kdata['Koers'];
    logScherm("Fondsvalutakoers $valutakoers");

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.Omschrijving FROM Fondskoersen, Fondsen WHERE ".
						 " Fondskoersen.Fonds = Fondsen.Fonds AND ".
						 " Fondskoersen.Fonds = '".$this->selectData['fondsverloopFonds']."' 
               AND  Fondskoersen.Datum < '".$einddatum."' order by datum desc limit 1 ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();

		$fdata 	= $DB2->nextRecord(); 
		$this->pdf->fonds = $fdata['Omschrijving'];
		$fondseenheid = $fdata['Fondseenheid'];
		$koersWaarde 	= $fdata['Koers'];
		$this->pdf->koersWaarde = $fdata['Koers'];
    
    logScherm("fondseenheid $fondseenheid , Koers $koersWaarde");

		$fondsenSelectie = array();
		$fondsenSelectie[] = $this->selectData['fondsverloopFonds'];

		$fondsenSelectie = implode('\',\'',$fondsenSelectie);
	  $fondsenQuery .= " Rekeningmutaties.Fonds IN('$fondsenSelectie')  AND ";

		$jaar = date("Y",$this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";
    logScherm("( $records ) portefeuilles in selectie");
		// selecteer alleen portefeuilles waar het fonds voorkomt!
		$q = "SELECT ".
				" Portefeuilles.ClientVermogensbeheerder, ".
				" Portefeuilles.Portefeuille, ".
				" Portefeuilles.Depotbank, ".
				" Portefeuilles.Accountmanager, ". 
				" Clienten.Client, ".
				" Clienten.Naam, ".
				" Clienten.Naam1, ".
				" Clienten.Adres, ".
				" Clienten.Woonplaats ".
				" FROM (Rekeningmutaties, Rekeningen, Portefeuilles, Clienten)  ".$join.
				" WHERE  ".
				" Portefeuilles.Client = Clienten.Client AND".
				" Rekeningmutaties.Fonds = '".$this->selectData['fondsverloopFonds']."' AND ".
				" Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
				" Rekeningmutaties.Rekening = Rekeningen.Rekening AND  ".
				" Rekeningen.Portefeuille = Portefeuilles.Portefeuille  ".$extraquery." AND  ".
				" Rekeningmutaties.Verwerkt = '1' AND ".
				" Rekeningmutaties.Boekdatum <= '".$einddatum."' AND ".
				" Rekeningmutaties.Grootboekrekening = 'FONDS' $beperktToegankelijk".
				" GROUP BY Portefeuilles.Portefeuille ".
				" ORDER BY ".$this->orderby;

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		$records = $DB->records();

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


		$this->pdf->SetFont("Times","",10);

		// Maak header voor CSV bestand
		$this->pdf->excelData[] = array("Client",
										"Portefeuille",
										"Depotbank",
										"Accountmanager",
                    "Boekdatum",
										"Omschrijving",
                    'Aantal',
                    'Vr.Val. waarde',
                    'Koers',
                    'Kostprijs aankopen',
                    'Opbrengst verkopen',
                    'Kostprijs',
                    'Resultaat',
                    'Saldi aantallen',
                    'Hist. kostprijs',
                    'Kostprijs per stuk',
                    'Koers',
                    'Beurskoers ultimo vorig jaar',
                    'Beurskoers rapportage datum');
                    
 
                          
	//Fonds/actie, datum, aantal,verkr.koers. valuta, verkr.waarde, kosten.

		if ($records < 1)
		{
		  echo 'Geen portefeuilles binnen selectie.';
		  if($this->progressbar)
			  $this->progressbar->hide();
		  exit();
		}

		$portefeuilleLijst=array();
		while($portefeuille = $DB->NextRecord())
		{
		  $portefeuilleLijst[]=$portefeuille;
    }
    
    $DB2 = new DB();
    foreach($portefeuilleLijst as $portefeuille)
    {
		  $crmNaam=getCrmNaam($portefeuille['Portefeuille']);
      if($crmNaam)
      {
        $portefeuille['Naam'] = $crmNaam['naam'];
        $portefeuille['Naam1'] = $crmNaam['naam1'];
      }
		  $this->pdf->rapport_koptext = $portefeuille['Naam'].' '.$portefeuille['Naam1']."\n".$portefeuille['Adres']."\n".$portefeuille['Woonplaats']."\n\nFonds: ".$this->pdf->fonds;
		  $this->pdf->portefeuille = $portefeuille['Portefeuille'];
		  $this->pdf->AddPage();
		  $this->pdf->SetAligns(array("L","L","R","R","R","R","R","R","R","R","R","R","R"));
      
		  $fondsaantalAirs=fondsAantalOpdatum($portefeuille['Portefeuille'],$this->selectData['fondsverloopFonds'],$einddatum);

	    $query = "SELECT
	              Rekeningmutaties.transactietype,
	              Rekeningmutaties.Afschriftnummer,
	              Rekeningmutaties.Boekdatum,
	              Rekeningmutaties.Aantal,
	              Rekeningmutaties.Valuta,
	              Rekeningmutaties.Fondskoers,
	              Rekeningmutaties.Rekening,
	              Rekeningmutaties.Omschrijving,
	              Rekeningmutaties.Bedrag,
                Rekeningmutaties.Valutakoers,
                Fondsen.OptieExpDatum,
                Fondsen.OptieBovenliggendFonds,
	              Fondsen.Fondseenheid,
	              Fondsen.Valuta,
	              Fondsen.Fonds,
	              Fondsen.Omschrijving AS FondsOmschrijving
	              FROM Rekeningmutaties,
	              Rekeningen, Fondsen, Portefeuilles
                WHERE
                Portefeuilles.Portefeuille = '".$portefeuille['Portefeuille']."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Fondsen.Fonds = Rekeningmutaties.Fonds AND
	              $fondsenQuery
	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	              Rekeningmutaties.Boekdatum >='".$begindatum."' AND
	              Rekeningmutaties.Boekdatum <='".$einddatum."'
	              ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Omschrijving, Rekeningmutaties.id ";

		  $DB2->SQL($query);
		  $DB2->Query();
		  $line = 0;
		  $skipJaarafsluiting = false;
		  $verkregenAantal = 0;
		  $verkregenWaarde = 0;
			$verkregenWaardeValuta =0;

		  $eersteFondsBoeking=true;
      $totaal=array();
		  while($mutaties = $DB2->NextRecord())
		  {
        if($mutaties['OptieBovenliggendFonds'] == '' || $mutaties['Fonds'] == $this->selectData['fondsverloopFonds'])
          $fonds=true;
        else
          $fonds=false;

		    if($mutaties['transactietype'] == 'B')
		    {
		     	if($fonds && $eersteFondsBoeking)
		        $skipJaarafsluiting = false;
		      else
		        $skipJaarafsluiting = true;
		    }
		    else
		      $skipJaarafsluiting = false;
	      $eersteFondsBoeking = false;
        
		    if ($skipJaarafsluiting == false)
		    {
          $valutaKoers = $mutaties['Valutakoers'];
		      $fondskoers  = $mutaties['Fondskoers'];

		      if ($mutaties['Fondseenheid'] == '')
		        $mutaties['Fondseenheid'] = 1;

		      $fondsWaardeValuta = $mutaties['Aantal'] * $mutaties['Fondseenheid'] * $mutaties['Fondskoers'];
          $fondsWaarde = $fondsWaardeValuta * $valutaKoers; //in EUR
          if(substr($mutaties['transactietype'],0,1)=='A')
            $kostprijsaankopen=$fondsWaarde;
          else
            $kostprijsaankopen=0;
  
          if(substr($mutaties['transactietype'],0,1)=='V')
          {
            $opbrengstVerkopen=$fondsWaarde*-1;
            $kostprijs=$verkregenKoers*$mutaties['Aantal']*$mutaties['Fondseenheid']*-1;
            $resultaat=$opbrengstVerkopen-$kostprijs;
          }
          else
          {
            $opbrengstVerkopen=0;
            $kostprijs=0;
            $resultaat=0;
          }
          $ultimoKoers=$this->getFondsKoers($mutaties['Fonds'],(substr($mutaties['Boekdatum'],0,4)-1)."-12-31");
          $rapportageDatumKoers=$this->getFondsKoers($mutaties['Fonds'],$einddatum);
					$vorigAantal=$verkregenAantal;
          $verkregenAantal += $mutaties['Aantal'];

					if($__appvar['bedrijf']=='RCN')
					{
						$verkregenWaarde += $fondsWaarde; //in EUR
						$verkregenWaardeValuta += $fondsWaardeValuta; //in EUR
						$verkregenKoers = ($verkregenWaarde / ($verkregenAantal * $mutaties['Fondseenheid'])) / $valutaKoers ; // in valuta
					}
					else
					{
						if ($mutaties['transactietype'] == 'A' || $mutaties['transactietype'] == 'A/O' ||
							  $mutaties['transactietype'] == 'D' || $mutaties['transactietype'] == 'S' || $mutaties['transactietype'] == 'V/O')
						{
							if($vorigAantal==0)
								$verkregenKoers=$mutaties['Fondskoers'];
							else
							  $verkregenKoers = ((($vorigAantal * $verkregenKoers) + ($mutaties['Aantal'] * $mutaties['Fondskoers'])) / ($vorigAantal + $mutaties['Aantal']));
					  }
						elseif ($mutaties['transactietype'] == 'B')
						{
							$verkregenKoers=$mutaties['Fondskoers'];
						}
					}
					$histKostprijs=$verkregenKoers*$verkregenAantal*$mutaties['Fondseenheid'];

          $kostprijsPerStuk=$histKostprijs/$verkregenAantal;
	        $this->pdf->row(array(jul2form(db2jul($mutaties['Boekdatum'])),
                            $mutaties['Omschrijving'],
		                        $this->formatGetal($mutaties['Aantal'],0),
		                        $this->formatGetal($fondskoers,4),
		                        $this->formatGetal($kostprijsaankopen,0),
                            $this->formatGetal($opbrengstVerkopen,0),
		                        $this->formatGetal($kostprijs,0),
                            $this->formatGetal($resultaat,0),
                            $this->formatGetal($verkregenAantal,0),
                            $this->formatGetal($histKostprijs,0),
                            $this->formatGetal($kostprijsPerStuk,2),
                            $this->formatGetal($ultimoKoers,2),
                            $this->formatGetal($rapportageDatumKoers,2)));
                            
         
            $this->pdf->excelData[] = array($portefeuille['Client'],$portefeuille['Portefeuille'],$portefeuille['Depotbank'],
                            $portefeuille['Accountmanager'],
		                        jul2form(db2jul($mutaties['Boekdatum'])),
                            $mutaties['Omschrijving'],
		                        round($mutaties['Aantal'],0),
                            round($verkregenWaardeValuta,2),
		                        round($fondskoers,4),
		                        round($kostprijsaankopen,2),
                            round($opbrengstVerkopen,2),
		                        round($kostprijs,2),
                            round($resultaat,2),
                            round($verkregenAantal,0),
                            round($histKostprijs,2),
                            round($kostprijsPerStuk,2),
                            round($fondskoers,4),
                            round($ultimoKoers,2),
                            round($rapportageDatumKoers,4));
            $totaal['kostprijsaankopen']+=$kostprijsaankopen;
            $totaal['opbrengstVerkopen']+=$opbrengstVerkopen; 
            $totaal['kostprijs']+=$kostprijs; 
            $totaal['resultaat']+=$resultaat;         
		      
		   }
                 
		   if($this->pdf->GetY() > 280)
       {
			   $this->pdf->AddPage();
       }
       
		   $line ++;
		  }
      //listarray($fondsaantalAirs);
      $this->pdf->ln();
      $this->pdf->row(array('','Totaal',
		                        $this->formatGetal($fondsaantalAirs['totaalAantal'],0),
		                        $this->formatGetal($fondsaantalAirs['actueleFonds'],4),
		                        $this->formatGetal($totaal['kostprijsaankopen'],0),
                            $this->formatGetal($totaal['opbrengstVerkopen'],0),
		                        $this->formatGetal($totaal['kostprijs'],0),
                            $this->formatGetal($totaal['resultaat'],0),
                            $this->formatGetal($fondsaantalAirs['totaalAantal'],0),
                            $this->formatGetal($histKostprijs,0)));

       if($fondsaantalAirs['totaalAantal'] <> $verkregenAantal)
		     $this->pdf->row(array('',"Het aantal aandelen kan niet juist berekend worden.(".$fondsaantalAirs['totaalAantal']."<>$verkregenAantal)"));


      // $fondsWaarde=fondsWaardeOpdatum($portefeuille['Portefeuille'],$this->selectData['fondsverloopFonds'],$einddatum,'EUR',$begindatum);
       $this->pdf->Ln();
       //listarray($fondsWaarde);
        $this->pdf->row(array('',"Ongerealiseerd periode resultaat",'','','','','',
        $this->formatGetal(($fondsaantalAirs['totaalAantal']*$rapportageDatumKoers)-($fondsaantalAirs['totaalAantal']*$ultimoKoers) )));//$fondsWaarde['actueleFonds']-$fondsWaarde['beginwaardeLopendeJaar'])*$fondsWaarde['totaalAantal']*$fondsWaarde['fondsEenheid']*$fondsWaarde['actueleValuta']
        $this->pdf->row(array('',"Ongerealiseerd historisch resultaat",'','','','','',
        $this->formatGetal(($fondsaantalAirs['totaalAantal']*$rapportageDatumKoers)-($histKostprijs),0))); //      ($fondsWaarde['actueleFonds']-$fondsWaarde['historischeWaarde'])*$fondsWaarde['totaalAantal']*$fondsWaarde['fondsEenheid']*$fondsWaarde['actueleValuta']


		  if($this->progressbar)
		  {
			 $pro_step += $pro_multiplier;
			 $this->progressbar->moveStep($pro_step);
		  }
      
      logScherm("Portefeuille ".$portefeuille['Portefeuille']." klaar.");//nieuw
		}
    logScherm("Fondsverloop klaar.");
		if($this->progressbar)
			 $this->progressbar->hide();
	}
  
  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
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
