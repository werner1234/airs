<?php

/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/26 16:44:29 $
File Versie					: $Revision: 1.30 $

$Log: KostprijsMutatieverloop.php,v $
Revision 1.30  2017/05/26 16:44:29  rvv
*** empty log message ***

Revision 1.29  2016/07/25 05:36:20  rvv
*** empty log message ***

Revision 1.28  2015/12/19 09:11:58  rvv
*** empty log message ***

Revision 1.27  2015/08/30 11:43:30  rvv
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

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/config/applicatie_functies.php");

class KostprijsMutatieverloop
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function KostprijsMutatieverloop( $selectData )
	{
		$this->selectData = $selectData;
		$this->excelData 	= array();

		$this->pdf = new PDFOverzicht('P','mm');
		$this->pdf->rapport_type = "KostprijsMutatieverloop";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 280;//190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData[datumTm];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Client ";
	}

	function formatGetal($waarde, $dec,$geenNul)
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

		$einddatum = jul2sql($this->selectData['datumTm']);

		if ($this->selectData['FondsBeginpositie'])
		  $begindatum = '0000-00-00';
		else
		  $begindatum = jul2sql($this->selectData['datumVan']);

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Valutakoersen.Koers FROM Valutakoersen, Fondsen WHERE ".
						 " Fondsen.Fonds  			= '".$this->selectData['kostprijsFonds']."' AND ".
						 " Valutakoersen.Valuta = Fondsen.Valuta AND ".
						 " Valutakoersen.Datum <= '".$einddatum."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();
		$kdata 	= $DB2->nextRecord();
		$valutakoers = $kdata['Koers'];

		// selecteer koers van fonds op datum uit fonds tabel.
		$query = "SELECT Fondskoersen.Koers , Fondsen.Fondseenheid, Fondsen.Omschrijving FROM Fondskoersen, Fondsen WHERE ".
						 " Fondskoersen.Fonds = Fondsen.Fonds AND ".
						 " Fondskoersen.Fonds = '".$this->selectData['kostprijsFonds']."' AND ".
						 " Fondskoersen.Datum ='".$einddatum."' ";

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();

		$fdata 	= $DB2->nextRecord();
		$this->pdf->fonds = $fdata['Omschrijving'];
		$fondseenheid = $fdata['Fondseenheid'];
		$koersWaarde 	= $fdata['Koers'];
		$this->pdf->koersWaarde = $fdata['Koers'];

		$fondsenSelectie = array();
		$fondsenSelectie[] = $this->selectData['kostprijsFonds'];

    if ($this->selectData['FondsOpties'])
    {
		  $query = "SELECT Fondsen.Fonds FROM Fondsen WHERE OptieBovenliggendFonds = '".$this->selectData['kostprijsFonds']."'";
		  $DB2->SQL($query);
		  $DB2->Query();
	 	  while($optie = $DB2->NextRecord())
		  {
		    $fondsenSelectie[]=$optie['Fonds'];
  		}
    }

		$fondsenSelectie = implode('\',\'',$fondsenSelectie);
	  $fondsenQuery .= " Rekeningmutaties.Fonds IN('$fondsenSelectie')  AND ";

		$jaar = date("Y",$this->selectData['datumTm']);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";

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
				" Rekeningmutaties.Fonds = '".$this->selectData['kostprijsFonds']."' AND ".
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
										"Omschrijving",
										"Boekdatum",
                    'Aantal',
                    'Koers',
                    'Valuta',
                    'Waarde',
                    'Kosten EUR',
                    'Verkregen aantal',
                    'Verkregen koers',
                    'Valuta',
                    'Verkregen waarde EUR');
                    
 
                          
	//Fonds/actie, datum, aantal,verkr.koers. valuta, verkr.waarde, kosten.

		if ($records < 1)
		{
		  echo 'Geen portefeuilles binnen selectie.';
		  if($this->progressbar)
			  $this->progressbar->hide();
		  exit();
		}

		$DB2 = new DB();
		while($portefeuille = $DB->NextRecord())
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
		  $this->pdf->SetAligns(array("L","L","R","R","C","R","R"));

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
	              ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.id";//, Rekeningmutaties.Omschrijving

		  $DB2->SQL($query);
		  $DB2->Query();

		  $fondsaantalAirs=fondsAantalOpdatum($portefeuille['Portefeuille'],$this->selectData['kostprijsFonds'],$einddatum);

		  $line = 0;
		  $skipJaarafsluiting = false;
		  $verkregenAantal = 0;
		  $verkregenWaarde = 0;

		  $eersteFondsBoeking=true;
		  $eersteOptieBoeking=true;

		  $DB3 = new DB();
		  $DB4 = new DB();
		  $fondsKostenTotaal = 0;
		  while($mutaties = $DB2->NextRecord())
		  {
        if($mutaties['OptieBovenliggendFonds'] == '' || $mutaties['Fonds'] == $this->selectData['kostprijsFonds'] )
          $fonds=true;
        else
          $fonds=false;

		    if($mutaties['transactietype'] == 'B')
		    {
		     	if( ($fonds && $eersteFondsBoeking) || ( !$fonds && $eersteOptieBoeking))
		        $skipJaarafsluiting = false;
		      else
		        $skipJaarafsluiting = true;
		    }
		    else
		      $skipJaarafsluiting = false;

		    if($fonds)
	        $eersteFondsBoeking = false;
	      else
          $eersteOptieBoeking = false;


		    if ($skipJaarafsluiting == false)
		    {
		      /*
		      if($mutaties['Valuta'] <> 'EUR')
		      {
		        $query = "SELECT Valutakoersen.Koers FROM Valutakoersen WHERE ".
				  					 " Valutakoersen.Valuta = '".$mutaties['Valuta']."' AND ".
						       " Valutakoersen.Datum <= '".$mutaties['Boekdatum']."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";
				  	$DB3->SQL($query);
		        $valutaKoersData = $DB3->lookupRecord();
		        $valutaKoers = $valutaKoersData['Koers'];
		      }
		      else
          */
            $valutaKoers = $mutaties['Valutakoers'];


		      if ($this->selectData['FondsKosten'])
		      {//boekdatum toevoegen
		  	    $query = "SELECT round(Debet,2) as Debet, Valuta, Valutakoers FROM Rekeningmutaties
		                  WHERE
		                  Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' AND
		                  Rekeningmutaties.Rekening = '".$mutaties['Rekening']."' AND
		                  Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' AND
		                  Rekeningmutaties.Omschrijving = '".$mutaties['Omschrijving']."' AND
		                  Rekeningmutaties.GrootboekRekening IN ('KOST','KOBU') ";
		        $DB3->SQL($query);
		        $DB3->Query();

		        $kostenTransactie=0;
		        $kostenTransactieValuta=0;
		        while($kosten = $DB3->NextRecord())
		        {
		          $kostenValuta = $kosten['Valuta'];
              $valutaKoersKosten = $kosten['Valutakoers'];
              /*
		          if($kostenValuta == "EUR")
		            $valutaKoersKosten = 1;
		          elseif($kostenValuta == $mutaties['Valuta'])
		            $valutaKoersKosten=$valutaKoers;
		          else
		          {
		            $query = "SELECT Valutakoersen.Koers FROM Valutakoersen WHERE ".
				  			    		 " Valutakoersen.Valuta = '".$mutaties['Valuta']."' AND ".
						             " Valutakoersen.Datum <= '".$mutaties['Boekdatum']."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";
				  	    $DB4->SQL($query);
		            $valutaKoersData = $DB4->lookupRecord();
		            $valutaKoersKosten = $valutaKoersData['Koers'];
		          }
              */
		          $kostenTransactie += $kosten['Debet']*$valutaKoersKosten;
		          $fondsKostenTotaal += $kostenTransactie;
		          $kostenTransactieValuta += $kosten['Debet'];
		          $fondsKostenTotaalValuta += $kostenTransactieValuta;
		        }
		      }
		      else
		      {
		        $fondsKostenTotaal = 0;
		        $fondsKostenTotaalValuta =0;
		        $kostenTransactie=0;
		        $kostenTransactieValuta=0;
		      }

		      $fondskoers  = $mutaties['Fondskoers'];

		      if ($mutaties['Fondseenheid'] == '')
		        $mutaties['Fondseenheid'] = 1;

		      $fondsWaardeValuta = $mutaties['Aantal'] * $mutaties['Fondseenheid'] * $mutaties['Fondskoers'];
          $fondsWaarde = $fondsWaardeValuta * $valutaKoers; //in EUR

		      if($mutaties['OptieBovenliggendFonds']=='' || $mutaties['Fonds'] == $this->selectData['kostprijsFonds'] )
		      { //Fonds
		        $verkregenAantal += $mutaties['Aantal'];
		        $verkregenWaarde += $fondsWaarde + $kostenTransactie; //in EUR
		        $verkregenWaardeValuta += $fondsWaardeValuta + $kostenTransactieValuta; //in EUR
		        $verkregenKoers = ($verkregenWaarde / ($verkregenAantal * $mutaties['Fondseenheid'])) / $valutaKoers ; // in valuta

		        $this->pdf->row(array($mutaties['Omschrijving'],
		                        jul2form(db2jul($mutaties['Boekdatum'])),
		                        $this->formatGetal($mutaties['Aantal'],0),
		                        $this->formatGetal($fondskoers,4),
		                        $mutaties['Valuta'],
		                        $this->formatGetal($fondsWaarde,2),
		                        $this->formatGetal($kostenTransactie,2,true)));
            $this->pdf->excelData[] = array($portefeuille['Client'],$portefeuille['Portefeuille'],$portefeuille['Depotbank'],$portefeuille['Accountmanager'],$mutaties['Omschrijving'],
		                        jul2form(db2jul($mutaties['Boekdatum'])),
		                        round($mutaties['Aantal'],0),
		                        round($fondskoers,4),
		                        $mutaties['Valuta'],
		                        round($fondsWaarde,2),
		                        round($kostenTransactie,2),
                            round($verkregenAantal,0),
		                        round($verkregenKoers,4),
		                        $mutaties['Valuta'],
		                        round($verkregenWaarde,2));                
                            
		        $this->pdf->row(array('',
		                        '',
		                        $this->formatGetal($verkregenAantal,0),
		                        $this->formatGetal($verkregenKoers,4),
		                        $mutaties['Valuta'],
		                        $this->formatGetal($verkregenWaarde,2)));
		      }
		      else
		      { //Optie

		        $verkregenWaarde += $fondsWaarde + $kostenTransactie;
		        $verkregenKoers = ($verkregenWaarde / $verkregenAantal ) / $valutaKoers ;
		        $verkregenWaardeValuta += $fondsWaardeValuta + $kostenTransactieValuta; //in EUR

		        $this->pdf->row(array($mutaties['Omschrijving'],
		                        jul2form(db2jul($mutaties['Boekdatum'])),
		                       $this->formatGetal($mutaties['Aantal'],0),
		                       $this->formatGetal($fondskoers,4),
		                       $mutaties['Valuta'],
		                       $this->formatGetal($fondsWaarde,2),
		                       $this->formatGetal($kostenTransactie,2,true)));
            $this->pdf->excelData[] = array($portefeuille['Client'],$portefeuille['Portefeuille'],$portefeuille['Depotbank'],$portefeuille['Accountmanager'],$mutaties['Omschrijving'],
		                        jul2form(db2jul($mutaties['Boekdatum'])),
		                        round($mutaties['Aantal'],0),
		                        round($fondskoers,4),
		                        $mutaties['Valuta'],
		                        round($fondsWaarde,2),
		                        round($kostenTransactie,2),
                            round($verkregenAantal,0),
		                        round($verkregenKoers,4),
		                        $mutaties['Valuta'],
		                        round($verkregenWaarde,2));                         
		        $this->pdf->row(array('',
		                        '',
		                        $this->formatGetal($verkregenAantal,0),
		                        $this->formatGetal($verkregenKoers,4),
		                        $mutaties['Valuta'],
		                        $this->formatGetal($verkregenWaarde,2)));
		     }
		   }

		  if($this->pdf->GetY() > 280)
			  $this->pdf->AddPage();

		  $line ++;
		  }
		  	if($fondsaantalAirs['totaalAantal'] <> $verkregenAantal)
		   {
		     $this->pdf->row(array("Het aantal aandelen kan niet juist berekend worden."));
		   }
		   else
		   {
		     //$this->pdf->row(array("airs:".$fondsaantalAirs['totaalAantal']." mutaties:".$verkregenAantal));
		   }

		  if($this->progressbar)
		  {
			 $pro_step += $pro_multiplier;
			 $this->progressbar->moveStep($pro_step);
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
