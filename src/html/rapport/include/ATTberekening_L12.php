<?php
include_once ('rapportATTberekening_L12.php');



class ATTberekening_L12 extends rapportATTberekening_L12
{
  function ATTberekening_L12($rapportData)
  {
    $this->rapport=&$rapportData;
    $this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
    $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
    $this->rapport_jaar=date('Y',$this->rapport_datumvanaf);
    $this->indexPerformance=false;
  
  }


}
?>