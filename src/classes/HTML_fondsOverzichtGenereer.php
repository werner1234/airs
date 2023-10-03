<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/11/03 13:06:20 $
 		File Versie					: $Revision: 1.7 $

 		$Log: HTML_fondsOverzichtGenereer.php,v $
 		Revision 1.7  2017/11/03 13:06:20  cvs
 		fondseenheid gebruiken voor actuele waarde
 		
 		Revision 1.6  2017/01/16 15:47:42  cvs
 		call 5583
 		
 		Revision 1.5  2017/01/11 14:25:16  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/01/10 09:09:28  cvs
 		call 4830 eerste commit
 		
 		Revision 1.3  2017/01/10 09:01:28  cvs
 		call 4830 eerste commit
 		
 		Revision 1.2  2016/12/22 09:39:52  rm
 		Html rapportage
 		
 		Revision 1.1  2016/12/07 08:47:29  cvs
 		call 5469
 		

 		
*/




class HTML_fondsOverzichtGenereer
{
  var $selectData;
  var $orderby;
  var $user;
  var $dbWaarden;
  var $statics;

  function HTML_fondsOverzichtGenereer($selectData)
  {
    // init stuff
    global $USR;
    //$this->initModule();
    $this->user = $USR;
    $this->selectData = $selectData;
    $this->orderby = " Clienten.Client ";
  }

  function genereer()
  {
    global $__appvar;
    $this->statics = array();
    include_once("../classes/portefeuilleSelectieClass.php");
    include_once("../html/rapport/rapportRekenClass.php");
    $einddatum = jul2db($this->selectData['datumTm']);
    $this->statics["einddatum"] = $einddatum;
    //debug($this->selectData, "in Genereer $einddatum");
    // selecteer koers van fonds op datum uit fonds tabel.
    $query = "
      SELECT 
        Valutakoersen.Koers 
      FROM 
        Valutakoersen, Fondsen 
      WHERE 
        Fondsen.Fonds  			 = '".$this->selectData['fonds']."' AND 
        Valutakoersen.Valuta = Fondsen.Valuta AND 
        Valutakoersen.Datum <= '".$einddatum."' 
      ORDER BY 
        Valutakoersen.Datum DESC ";
    $db = new DB();

    $kdata 	= $db->lookupRecordByQuery($query);

    $valutakoers = $kdata["Koers"];
    $this->statics["valutakoers"] = $valutakoers;

    // selecteer koers van fonds op datum uit fonds tabel.
    $query = "
      SELECT 
        Fondskoersen.Koers , 
        Fondsen.Fondseenheid, 
        Fondsen.Omschrijving, 
        Fondsen.ISINCode,
        Fondsen.Valuta 
      FROM 
        Fondskoersen, Fondsen 
      WHERE 
        Fondskoersen.Fonds = Fondsen.Fonds AND 
        Fondskoersen.Fonds = '".$this->selectData['fonds']."' AND 
        Fondskoersen.Datum <='".$einddatum."' 
      ORDER BY 
        Fondskoersen.Datum DESC";

    $fdata 	= $db->lookupRecordByQuery($query);

    $this->statics["fonds"] = $fdata;


    $jaar = date("Y",$this->selectData['datumTm']);
  
    $selectie = new portefeuilleSelectie($this->selectData);
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
    $extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";

    //$extraquery=" AND ( Portefeuilles.Vermogensbeheerder >= '".$this->selectData["VermogensbeheerderVan"]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData["VermogensbeheerderTm"]."' ) ";


    // selecteer alleen portefeuilles waar het fonds voorkomt!
    $query = "
    SELECT 
      Portefeuilles.ClientVermogensbeheerder, 
      Portefeuilles.Vermogensbeheerder, 
      Portefeuilles.Portefeuille, 
      Portefeuilles.Depotbank, 
      Portefeuilles.Accountmanager, 
      Clienten.Client, 
      Clienten.Naam, 
      Clienten.Naam1,
      Portefeuilles.Risicoklasse,
      Portefeuilles.SoortOvereenkomst,
			CRM_naw.profielOverigeBeperkingen 
    FROM 
      (Rekeningmutaties, Rekeningen, Portefeuilles, Clienten)
		LEFT JOIN CRM_naw ON 
		  Portefeuilles.Portefeuille=CRM_naw.portefeuille  
    WHERE  
      Portefeuilles.Client = Clienten.Client AND
      Rekeningmutaties.Fonds = '".$this->selectData['fonds']."' AND 
      Rekeningmutaties.Grootboekrekening = 'FONDS' AND 
      Rekeningmutaties.Rekening = Rekeningen.Rekening AND  
      Rekeningen.Portefeuille = Portefeuilles.Portefeuille  ".$extraquery." AND  
      YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND 
      Rekeningmutaties.Verwerkt = '1' AND 
      Rekeningmutaties.Boekdatum <= '".$einddatum."' AND 
      Rekeningmutaties.Fonds IS NOT NULL 
    GROUP BY 
      Portefeuilles.Portefeuille 
    ORDER BY 
      ".$this->orderby;
//debug($query);
    $db->executeQuery($query);
    $statics["query"] = $query;
    $records = $db->records();

    // Maak header voor CSV bestand

    $lineNr = 1;
    while($portefeuille = $db->NextRecord())
    {
//      debug($portefeuille);
      $crmNaam=getCrmNaam($portefeuille['Portefeuille']);
      if($crmNaam)
      {
        $portefeuille['Naam'] = $crmNaam['naam'];
        $portefeuille['Naam1'] = $crmNaam['naam1'];
      }

      $db2 = new DB();
      $portefeuilleData = berekenPortefeuilleWaardeQuick($portefeuille['Portefeuille'], $einddatum);
      vulTijdelijkeTabel($portefeuilleData,$portefeuille['Portefeuille'],$einddatum);

      // selecteer fondswaarde portefeuille
      $query = "
        SELECT 
          totaalAantal, 
          actuelePortefeuilleWaardeEuro  
        FROM 
          TijdelijkeRapportage 
        WHERE 
          type = 'fondsen' AND 
          Fonds = '".$this->selectData['fonds']."' AND 
          rapportageDatum ='".$einddatum."' AND 
          portefeuille = '".$portefeuille['Portefeuille']."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];

      $fdata = $db2->lookupRecordByQuery($query);
      $fondsWaarde = $fdata['actuelePortefeuilleWaardeEuro'];
      $fondsAantal = $fdata['totaalAantal'];

      $query = "
        SELECT 
          SUM(Rekeningmutaties.Aantal) as optieAantal
	 			FROM 
	 			  (Rekeningmutaties, Rekeningen, Portefeuilles)
				JOIN Fondsen on 
				  Fondsen.Fonds =  Rekeningmutaties.Fonds
				WHERE
	 				Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  
	 				Portefeuilles.Portefeuille = '".$portefeuille['Portefeuille']."' AND
	 				YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				Rekeningmutaties.Verwerkt = '1' AND
	 				Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				Fondsen.OptieBovenliggendFonds =  '".$this->selectData['fonds']."' AND
	 				Rekeningmutaties.Grootboekrekening = 'FONDS' ";

      $optieAantal = $db2->lookupRecordByQuery($query);

      if( $fondsAantal <> 0  OR
          ($optieAantal['optieAantal'] <> 0 AND $this->selectData['optiesWeergeven'] == 1))
      {
        $query = "
          SELECT 
            SUM(actuelePortefeuilleWaardeEuro) AS totaal 
          FROM 
            TijdelijkeRapportage 
          WHERE 
            rapportageDatum ='".$einddatum."' AND 
            portefeuille = '".$portefeuille['Portefeuille']."' "
            .$__appvar['TijdelijkeRapportageMaakUniek'];

        $tdata = $db2->lookupRecordByQuery($query);
        $totaalWaarde = $tdata['totaal'];

        // selecteer fondswaarde portefeuille
        $query = "
          SELECT 
            SUM(actuelePortefeuilleWaardeEuro) AS totaal 
          FROM 
            TijdelijkeRapportage 
          WHERE 
            type = 'rekening' AND 
            rapportageDatum ='".$einddatum."' AND 
            portefeuille = '".$portefeuille["Portefeuille"]."' "
            .$__appvar['TijdelijkeRapportageMaakUniek'];

        $liqWaarde = $db2->lookupRecordByQuery($query);
        $liqWaarde = $liqWaarde['totaal'];

        // selecteer belegingscategorie
        $query  = " 
          SELECT 
            BeleggingscategoriePerFonds.Beleggingscategorie 
          FROM 
            BeleggingscategoriePerFonds, Portefeuilles 
          WHERE 
            Portefeuilles.Portefeuille = '".$portefeuille['Portefeuille']."' AND 
            Portefeuilles.Vermogensbeheerder =  BeleggingscategoriePerFonds.Vermogensbeheerder AND 
            BeleggingscategoriePerFonds.Fonds = '".$this->selectData['fonds']."' ";

        $cdata = $db2->lookupRecordByQuery($query);
        $categorie = $cdata['Beleggingscategorie'];

        // selecteer totaal in categorie portefeuille
        $query = "
          SELECT 
            SUM(actuelePortefeuilleWaardeEuro) AS totaalWaarde 
          FROM 
            TijdelijkeRapportage 
          WHERE 
            type = 'fondsen' AND 
            beleggingscategorie = '".$categorie."' AND 
            rapportageDatum ='".$einddatum."' AND 
            portefeuille = '".$portefeuille['Portefeuille']."' "
            .$__appvar['TijdelijkeRapportageMaakUniek'];

        $cdata = $db2->lookupRecordByQuery($query);
        $categorieWaarde = $cdata["totaalWaarde"];


//        switch($this->selectData['berekeningswijze'])
//        {
//          case "Totaal vermogen" :
//            $totaalRekenwaarde = $totaalWaarde;
//            break;
//          case "Totaal belegd vermogen" :
//            $totaalRekenwaarde = $totaalWaarde - $liqWaarde;
//            break;
//          case "Belegd vermogen per beleggingscategorie" :
//            $totaalRekenwaarde = $categorieWaarde;
//            break;
//        }
//
//        $percentage = $fondsWaarde / ($totaalRekenwaarde / 100);

        $aandeelop = array();
        $aandeelop['totaalvermogen'] =  $fondsWaarde / ($totaalWaarde/100);
        $aandeelop['vermogenbelegd'] =  $fondsWaarde / (($totaalWaarde - $liqWaarde)/100);
        $aandeelop['beleggingscat'] =  $fondsWaarde / (($categorieWaarde)/100);

        // bereken historische waarde
        $hist = fondsWaardeOpdatum($portefeuille['Portefeuille'], $this->selectData['fonds'], $einddatum);
        //debug($hist);
        $koersPerAandeel = $hist["actueleFonds"] * $hist["fondsEenheid"];

        $this->dbWaarden[]=array(
          'Rapport'                       => 'Fondsen',
          'Portefeuille'                  => $portefeuille['Portefeuille'],
          'Vermogensbeheerder'            => $portefeuille['Vermogensbeheerder'],
          'Client'                        => $portefeuille['Client'],
          'Naam'                          => $portefeuille['Naam'],
          'Naam1'                         => $portefeuille['Naam1'],
          'Fonds'                         => $this->selectData['fonds'],
          'Kostprijs'                     => round($hist['historischeWaarde'],2),
          'AandeelTotaalvermogen'         => round($aandeelop['totaalvermogen'],2),
          'AandeelBeleggingscategorie'    => round($aandeelop['beleggingscat'],2),
          'AandeelTotaalBelegdvermogen'   => round($aandeelop['vermogenbelegd'],2),
          'AantalInPortefeuille'          => round($fondsAantal,6),
          'accountmanager'                => $portefeuille['Accountmanager'],
          'depotbank'                     => $portefeuille['Depotbank'],
          'risicoklasse'                  => $portefeuille['Risicoklasse'],
          'soortOvereenkomst'             => $portefeuille['SoortOvereenkomst'],
          'actueleWaarde'                 => $koersPerAandeel * round($fondsAantal,6)
        );

        if($this->selectData['optiesWeergeven'] == 1)
        {//$portefeuille[Client]

          $query = 	"
            SELECT
	 				    Rekeningmutaties.Fonds,
	 				    Fondsen.OptieBovenliggendFonds,
	 				    Fondsen.Valuta
	 				  FROM 
	 				    (Rekeningmutaties, Rekeningen, Portefeuilles)
					  JOIN Fondsen on 
					    Fondsen.Fonds =  Rekeningmutaties.Fonds
					  WHERE
	 				    Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	 				    Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Portefeuilles.Portefeuille = '".$portefeuille['Portefeuille']."' AND
	 				    YEAR(Rekeningmutaties.Boekdatum) = '$jaar' AND
	 				    Rekeningmutaties.Verwerkt = '1' AND
	 				    Rekeningmutaties.Boekdatum <= '$einddatum' AND
	 				    Fondsen.OptieBovenliggendFonds =  '".$this->selectData['fonds']."' AND
	 				    Rekeningmutaties.Grootboekrekening = 'FONDS'
	 				  GROUP BY 
	 				    Rekeningmutaties.Fonds ";

          $db2->executeQuery($query);
          $records = $db2->records();

          $opties = array();
          while($optie = $db2->NextRecord())
          {
            $optieData = optieAantalOpdatum($portefeuille['Portefeuille'], $optie['Fonds'], $einddatum);
            $hist = fondsWaardeOpdatum($portefeuille['Portefeuille'], $optie['Fonds'], $einddatum);
            $koersPerAandeel = $hist["actueleFonds"] * $hist["fondsEenheid"];
            $optieWaarde=($optieData['totaalAantal'] * $optieData['fondsEenheid'] * $hist['actueleFonds']);
            $percentage = $optieWaarde / ($totaalRekenwaarde/100);
            if(round($optieData['totaalAantal'],0) <> 0)
            {

              $aandeelop = array();
              $aandeelop['totaalvermogen'] =  $optieWaarde / ($totaalWaarde/100);
              $aandeelop['vermogenbelegd'] =  $optieWaarde / (($totaalWaarde - $liqWaarde)/100);
              $aandeelop['beleggingscat'] =  $optieWaarde / (($categorieWaarde)/100);

              $this->dbWaarden[]=array(
                'Rapport'                       => 'Fondsen',
                'Portefeuille'                  => $portefeuille['Portefeuille'],
                'Vermogensbeheerder'            => $portefeuille['Vermogensbeheerder'],
                'Client'                        => $portefeuille['Client'],
                'Naam'                          => $portefeuille['Naam'],
                'Naam1'                         => $portefeuille['Naam1'],
                'Fonds'                         => $optie['Fonds'],
                'Kostprijs'                     => round($hist['historischeWaarde'],2),
                'AandeelTotaalvermogen'         => round($aandeelop['totaalvermogen'],2),
                'AandeelBeleggingscategorie'    => round($aandeelop['beleggingscat'],2),
                'AandeelTotaalBelegdvermogen'   => round($aandeelop['vermogenbelegd'],2),
                'AantalInPortefeuille'          => round($optieData['totaalAantal'],6),
                'accountmanager'                => $portefeuille['Accountmanager'],
                'depotbank'                     => $portefeuille['Depotbank'],
                'risicoklasse'                  => $portefeuille['Risicoklasse'],
                'soortOvereenkomst'             => $portefeuille['SoortOvereenkomst'],
                'actueleWaarde'                 => $koersPerAandeel * round($fondsAantal,6),
              );
            }
          }
        }
        $lineNr ++;
        $totaalAantal += $fondsAantal;
      }
      // verwijder Data van tijdelijke tabel
      verwijderTijdelijkeTabel($portefeuille['Portefeuille'],$einddatum);
    }
    return $this->dbWaarden;
  }



}

?>