<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/04 16:00:11 $
 		File Versie					: $Revision: 1.20 $

 		$Log: Fondslijst.php,v $
 		Revision 1.20  2020/07/04 16:00:11  rvv
 		*** empty log message ***
 		


*/
include_once("rapportRekenClass.php");

class Fondslijst
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Fondslijst( $selectData )
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

		$this->pdf->tmdatum = $this->selectData[datumTm];
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

		if($this->selectData['VermogensbeheerderVan']=='WWO' && $this->selectData['VermogensbeheerderTm']=='WWO')
		{

		 $rapportageDatum['a']=jul2sql($this->selectData[datumVan]);
		 $rapportageDatum['b']=jul2sql($this->selectData[datumTm]);

	   $rapporStartMaand = date("m",$this->selectData[datumVan]);
	   $rapportStartDag = date("d",$this->selectData[datumVan]);
	   if($rapporStartMaand == 1 && $rapportStartDag == 1)
	    $startjaar = 1;
	   else
	    $startjaar = 0;

	   $rapporEindMaand = date("m",$this->selectData[datumTm]);
	   $rapportEindDag = date("d",$this->selectData[datumTm]);
	   if($rapporEindMaand == 1 && $rapportEindDag == 1)
	    $startjaar2 = 1;
	   else
	    $startjaar2 = 0;

	   $DB = new DB();
	 	 $DB2 = new DB();

		  $query = "SELECT
      CategorienPerHoofdcategorie.beleggingscategorie,CategorienPerHoofdcategorie.Hoofdcategorie
      FROM CategorienPerHoofdcategorie
      LEFT JOIN  Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
      WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = 'WWO'
      ORDER BY Beleggingscategorien.Afdrukvolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($categorien = $DB->NextRecord())
		  {
       $beleggingscategorien[$categorien['beleggingscategorie']] =$categorien['Hoofdcategorie'];
       $categorieKoppeling[$categorien['Hoofdcategorie']][]=$categorien['beleggingscategorie'];
  	  }

  	  foreach ($beleggingscategorien as $beleggingscategorie=>$hoofdcategorie)
  	  {
  	    if(in_array($beleggingscategorie,$beleggingscategorien))
  	    {
  	      $hoofdcategoriePerCategorie[$beleggingscategorie]=$hoofdcategorie;
  	      $supercategorien[$hoofdcategorie][]=$beleggingscategorie;
  	    }
  	  }


  	 $query = "SELECT Portefeuilles.Client,
                      Portefeuilles.Portefeuille,
                      CRM_naw.naam,
                      CRM_naw.naam1,
                      CRM_naw.adres,
                      CRM_naw.pc,
                      CRM_naw.plaats,
                      CRM_naw.land
               FROM
                  Portefeuilles
                  LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
                  WHERE 1 $extraquery";

		 $DB->SQL($query);
		 $DB->Query();

		 $records = $DB->records();
  	 if($this->progressbar)
		 {
			 $this->progressbar->moveStep(0);
			 $pro_step = 0;
			 $pro_multiplier = 100 / $records;
		 }

		 $this->pdf->excelData[] =array('Client','RekNr','Naam1','Naam2','Adres','PC','Woonplaats','Land','Fondscode','Fondsnaam','Categorie','Vermogen '.jul2form($this->selectData['datumVan']),'Vermogen '.jul2form($this->selectData['datumTm']));

		 while($data = $DB->nextRecord())
		 {
		   $waarden=array();
		   $waardenBegin=berekenPortefeuilleWaarde($data['Portefeuille'], $rapportageDatum['a'],$startjaar,$pdata['RapportageValuta'],$rapportageDatum['a']);

		   foreach ($waardenBegin as $regel)
		   {
		     if($regel['fonds'] <> '')
		     {
		       $waarden[$regel['fonds']]['beginPortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
		       $waarden[$regel['fonds']]['fondsOmschrijving']=$regel['fondsOmschrijving'];
		       if($regel['type'] == 'fondsen')
		       {
		         $waarden[$regel['fonds']]['beleggingscategorie']=$regel['beleggingscategorie'];
		       }
		     }
		     elseif($regel['type']=='rekening')
		     {
		       $waarden[$regel['rekening']]['beginPortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
		       $waarden[$regel['rekening']]['fondsOmschrijving']=$regel['rekening'];
		     }
		   }

		   $waardenEind=berekenPortefeuilleWaarde($data['Portefeuille'], $rapportageDatum['b'],$startjaar2,$pdata['RapportageValuta'],$rapportageDatum['a']);

		   foreach ($waardenEind as $regel)
		   {
		     if($regel['fonds'] <> '')
		     {
		       $waarden[$regel['fonds']]['actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
		       $waarden[$regel['fonds']]['fondsOmschrijving']=$regel['fondsOmschrijving'];
		       if($regel['type'] == 'fondsen')
		       {
		         $waarden[$regel['fonds']]['beleggingscategorie']=$regel['beleggingscategorie'];
		       }
		     }
		     elseif($regel['type']=='rekening')
		     {
		       $waarden[$regel['rekening']]['actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
		       $waarden[$regel['rekening']]['fondsOmschrijving']=$regel['rekening'];
		     }
		   }
		   $waardes=array();
		   foreach ($waarden as $fonds=>$values)
		   {
		       $waardes[$fonds]['start']+=$values['beginPortefeuilleWaardeEuro'];
		       $waardes[$fonds]['eind']+=$values['actuelePortefeuilleWaardeEuro'];

		         $waardes[$fonds]['omschrijving']=$values['fondsOmschrijving'];
		         $waardes[$fonds]['beleggingscategorie']=$values['beleggingscategorie'];


		       $query="SELECT FondsImportCode FROM Fondsen WHERE Fonds = '".$fonds."' ";
		       $DB2->SQL($query);
		       $FondsImportCode=$DB2->lookupRecord();
		       $waardes[$fonds]['FondsImportCode']=$FondsImportCode['FondsImportCode'];
		   }

		   foreach ($waardes as $fonds=>$waarde)
		   {
			   $this->pdf->excelData[] =array($data['Client'],$data['Portefeuille'],
	       $data['naam'],$data['naam1'],$data['adres'],$data['pc'],$data['plaats'],$data['land'],
		     $waarde['FondsImportCode'],$waarde['omschrijving'],$hoofdcategoriePerCategorie[$beleggingscategorien[$waarde['beleggingscategorie']]],$waarde['start'],$waarde['eind']);
		   }

		   if($this->progressbar)
			 {
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			 }
		 }
		}
		else
		{
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
		$this->pdf->excelData[] = array('Portefeuille','Client','Fonds','FondsOmschrijving','FondsImportCode','ISINcode','Valuta','BBLandcode','Beurs','totaalAantal','Fondskoers','Waarde','Valutakoers','WaardeEUR','RenteEUR');
		while($data = $DB->nextRecord())
		{
       $koppelingdata=getFondsKoppelingen($data['Vermogensbeheerder'],$einddatum,$data['fonds'],$data['geenStandaardSector']);
       $data['Beleggingscategorie']=$koppelingdata['beleggingscategorie'];

       $rente=renteOverPeriode($data,$einddatum,(substr($einddatum,5,5)=='01-01'?true:false));
       $fondsKoers=$this->getFondsKoers($data['fonds'],$einddatum);
       $valutaKoers=$this->getValutaKoers($data['Valuta'],$einddatum);
       $waarde=$data['totaalAantal']*$fondsKoers*$data['Fondseenheid'];
       
		   if(strstr($data['Beleggingscategorie'],"OBL") != '')
		     $data['BBLandcode'] = '';

			 $this->pdf->excelData[] = array(
										$data['Portefeuille'],
				            $data['Client'],
				            $data['fonds'],
										$data['FondsOmschrijving'],
										$data['FondsImportCode'],
				 $data['ISINcode'],
				 $data['Valuta'],
										$data['BBLandcode'],
				 $data['Beurs'],
										$data['totaalAantal'],
         $fondsKoers,
         round($waarde,2),
         $valutaKoers,
         round($waarde*$valutaKoers,2),
         round($rente*$valutaKoers,2)
         
         );
		}
      
      if(isset($this->selectData['fondslijst_Liq']) && $this->selectData['fondslijst_Liq'] == 1)
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
            $rekening['Client'],
            $rekening['Rekening'],
            $rekening['Tenaamstelling'],
            '',
            '',
            $rekening['rekeningValuta'],
            '',//$rekening['Beleggingscategorie'],
            '',//$rekening['Beleggingscategorie'],
            '',//$rekening['Regio'],
            '',
            round($rekening['totaal'], 2),
            $valutaKoers,
            round($rekening['totaal'] * $valutaKoers, 2),
            '');
          
           $this->pdf->excelData[] = $row;
        }
      }

/*
		$query = "
	  SELECT
      Rekeningen.Portefeuille ,
      Rekeningen.Valuta as rekeningValuta,
      round(SUM(Rekeningmutaties.Bedrag),2) as totaal
    FROM
      Rekeningmutaties, Rekeningen, Portefeuilles $extraTable
    WHERE
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND
      Rekeningen.Memoriaal = 0 AND
      Rekeningen.Deposito = 1 AND
      Rekeningmutaties.boekdatum >= '$jaar-01-01' AND
      Rekeningmutaties.boekdatum <=  '$einddatum'  $extraquery
    GROUP BY
      Rekeningmutaties.Rekening
    HAVING
      totaal <> 0
    ORDER BY
      Rekeningen.Portefeuille";

	  $db = new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
		  $this->pdf->excelData[] = array($data['Portefeuille'],$data['rekeningValuta'],'deposito','',$data['totaal']);
		}
*/



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
			echo vt("Fout:")." ".vt("kan niet schrijven naar")." ".$filename;
		}
	}
}
