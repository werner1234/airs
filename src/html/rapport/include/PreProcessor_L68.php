<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/25 17:11:27 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PreProcessor_L68.php,v $
 		Revision 1.4  2018/08/25 17:11:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/07/05 16:06:40  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/03/18 20:30:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/02/19 10:59:55  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/10/04 11:52:21  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/06/13 13:16:01  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/11/07 17:07:29  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/11/03 18:14:13  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/09/09 17:35:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/09/01 14:27:48  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/07/29 10:24:33  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/07/22 12:56:44  rvv
 		*** empty log message ***


*/

class PreProcessor_L68
{


  function PreProcessor_L68($portefeuille, $datum = '', $pdf)
  {
    global $__appvar;
    $this->portefeuille = $portefeuille;
    $this->rapporten = array();
    $this->portefeuilles = array();
    $this->nodigVoor = array('ATT', 'VHO', 'PERFG','RISK','PERF','PERFD','HUIS');
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