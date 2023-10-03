<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/01 09:53:45 $
 		File Versie					: $Revision: 1.3 $

 		$Log: PreProcessor_L80.php,v $
 		Revision 1.3  2020/03/01 09:53:45  rvv
 		*** empty log message ***
 		



*/

class PreProcessor_L80
{


  function PreProcessor_L80($portefeuille, $datum = '', $pdf)
  {
    global $__appvar;
    $this->portefeuille = $portefeuille;
    $this->rapporten = array();
    $this->portefeuilles = array();
    $this->nodigVoor = array('PERFD');
    $berekenen = false;
    $dagen = array();
    if (is_object($pdf))
    {
      $this->pdf = &$pdf;
      $this->rapporten = $this->pdf->rapport_typen;
      $this->portefeuilles = $this->pdf->portefeuilles;
    }
    else
    {
      include_once($__appvar["basedir"]."/html/rapport/PDFRapport.php");
      $this->pdf = new PDFRapport('L', 'mm');
    }

    $this->db = new DB();
    $query="CREATE TEMPORARY TABLE
IF NOT EXISTS `tempVerdeling` (
	`id` INT (11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
	`hoofdPortefeuille` VARCHAR (24) NOT NULL,
  `portefeuille` VARCHAR (24) NOT NULL,
	`waarde` double NOT NULL,
  `aandeel` double NOT NULL,
	PRIMARY KEY (`id`),
  KEY `datum` (`datum`),
	KEY `portefeuille` (`portefeuille`(8)),
  KEY `hoofdPortefeuille` (`hoofdPortefeuille`(8))
)";
    $this->db->SQL($query);
    $this->db->Query();

    $berekenenVanaf = $this->pdf->rapport_datumvanaf;
    $berekenenTot = $this->pdf->rapport_datum;
    foreach ($this->nodigVoor as $rapport)
    {
      if (in_array($rapport, $this->rapporten))
      {
        $berekenen = true;
        if ($rapport == 'ATT')
        {
          $vanafJul = $this->pdf->rapport_datumvanaf;
        }
        else
        {
          $vanafJul = db2jul($this->pdf->PortefeuilleStartdatum);
        }
        $berekenenVanaf = min($vanafJul, $berekenenVanaf);
      }
    }
    $this->waarden = array();

    if ($berekenen == true)
    {
      $index = new indexHerberekening();
      if ($datum)
      {
        $dagen[$datum] = $datum;
      }
      else
      {
        $maanden = $index->getMaanden($berekenenVanaf, $berekenenTot);
        foreach ($maanden as $waarden)
        {
          $dagen[$waarden['start']] = $waarden['start'];
          $dagen[$waarden['stop']] = $waarden['stop'];
        }
      }

      foreach ($this->portefeuilles as $cPortefeuille)
      {
        foreach ($dagen as $dag)
        {
          if (substr($dag, 5, 5) == '01-01')
          {
            $startJaar = true;
          }
          else
          {
            $startJaar = false;
          }

          $query="SELECT
HistorischePortefeuilleIndex.PortefeuilleWaarde
FROM HistorischePortefeuilleIndex WHERE HistorischePortefeuilleIndex.Portefeuille='$cPortefeuille' AND HistorischePortefeuilleIndex.Datum='$dag' AND HistorischePortefeuilleIndex.periode='m'";
          $this->db->SQL($query);
          $this->db->Query();
          $HistorischePortefeuilleIndex=$this->db->nextRecord();
          if($HistorischePortefeuilleIndex['PortefeuilleWaarde'])
          {
            $totaleWaarde = $HistorischePortefeuilleIndex['PortefeuilleWaarde'];
          }
          else
          {
            $fondswaarden = berekenPortefeuilleWaarde($cPortefeuille, $dag, $startJaar, 'EUR', $dag);
            $totaleWaarde = 0;
            foreach ($fondswaarden as $fondsData)
            {
              $totaleWaarde += $fondsData['actuelePortefeuilleWaardeEuro'];
            }
          }
          $this->waarden[$dag]['consolidatie'][$cPortefeuille]['totaleWaarde'] = $totaleWaarde;
          $this->waarden[$dag]['portefeuille']['totaleWaarde'] += $totaleWaarde;
        }
      }

      foreach($this->waarden as $dag=>$hoofdverdeling)
      {
        foreach($hoofdverdeling['consolidatie'] as $cPortefeuille=>$waarden)
        {
          $aandeel=$waarden['totaleWaarde']/$hoofdverdeling['portefeuille']['totaleWaarde'];
          $query="INSERT INTO tempVerdeling SET datum='$dag',hoofdPortefeuille='".$this->portefeuille."',portefeuille='".$cPortefeuille."',waarde='".$waarden['totaleWaarde']."',aandeel='$aandeel'";
          $this->db->SQL($query);
          $this->db->Query();
        }
      }
/*
      $this->db->SQL("SELECT * FROM tempVerdeling");
      $this->db->Query();
      while($data=$this->db->nextRecord())
        listarray($data);

      listarray($this->waarden);
      exit;
*/

    }


  }
}

?>