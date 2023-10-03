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

class PreProcessor_L12
{
   function PreProcessor_L12($portefeuille, $datum = '', $pdf=array())
   {
      global $__appvar;
      $this->portefeuille = $portefeuille;
      $this->db = new DB();
      $this->categorieData = array();
      $this->pdf = $pdf;
      $dagen = array();
      if ($datum <> '')
      {
        $dagen[] = $datum;
      }
      else
      {
        $query = "SELECT rapportageDatum FROM TijdelijkeRapportage WHERE portefeuille = '$portefeuille' " . $__appvar['TijdelijkeRapportageMaakUniek'] . " GROUP BY rapportageDatum";
        $this->db->SQL($query);
        $this->db->Query();
    
        while ($data = $this->db->nextRecord())
        {
          $dagen[] = $data['rapportageDatum'];
        }
      }
  
      foreach ($dagen as $dag)
      {
    
        $this->bepaalBewaarder($dag);
        $this->liquiditeitenSector($dag);
      }
  }
  
  function liquiditeitenSector($dag)
  {
    global $__appvar;
    $query="UPDATE TijdelijkeRapportage SET beleggingssector='Spaarrekeningen',beleggingssectorOmschrijving='Spaarrekeningen' WHERE Beleggingscategorie<>'Liquiditeiten' AND type='rekening' AND rapportageDatum='$dag' AND portefeuille = '".$this->portefeuille."'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $this->db->SQL($query);
    $this->db->Query();
  }
  
    function bepaalBewaarder($dag)
    {
      global $__appvar;
      $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $query="SELECT
	Rekeningmutaties.Fonds,
  Portefeuilles.Depotbank,
  Portefeuilles.Vermogensbeheerder,
  round(sum(Rekeningmutaties.aantal),3) as aantal,
  IF(Rekeningmutaties.Bewaarder <> '',	Rekeningmutaties.Bewaarder,IF (Rekeningen.Depotbank <> '',	Rekeningen.Depotbank,	Portefeuilles.Depotbank)) AS BewaarderSort
  FROM Rekeningmutaties
  JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
  JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	WHERE
	Rekeningen.Portefeuille = '".$this->portefeuille."' AND
	YEAR(Rekeningmutaties.Boekdatum) = '".substr($dag,0,4)."' AND
	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum <= '$dag' AND
  Rekeningmutaties.GrootboekRekening = 'FONDS'
	GROUP BY Rekeningmutaties.Fonds , BewaarderSort
  HAVING aantal <> 0
	ORDER BY Rekeningmutaties.Fonds,aantal desc";
      $this->db->SQL($query);
      $this->db->Query();
      $fondsen=array();
      while($data=$this->db->nextRecord())
        $fondsen[]=$data;

      $lastFonds='';
      foreach($fondsen as $fonds)
      {
        if($fonds['Fonds']<>$lastFonds)
        {
          $queries[] = "UPDATE TijdelijkeRapportage SET Bewaarder='".$fonds['BewaarderSort']."' WHERE Fonds='" . mysql_real_escape_string($fonds['Fonds']) . "' AND $vasteWhere";
        }
        $lastFonds=$fonds['Fonds'];
      }

      foreach($queries as $query)
      {
        $this->db->SQL($query);
        $this->db->Query();
      }
      unset($queries);
    }
}

?>