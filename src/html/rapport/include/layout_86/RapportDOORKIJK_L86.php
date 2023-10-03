<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:23:08 $
File Versie					: $Revision: 1.3 $

$Log: RapportDOORKIJK_L51.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("./rapport/RapportDOORKIJK.php");


class RapportDOORKIJK_L86
{
	function RapportDOORKIJK_L86($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->pdf = $pdf;
    $this->portefeuille=$portefeuille;
    $this->rapportageDatum=$rapportageDatum;
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    
    $this->doorkijk=new RapportDOORKIJK($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->pdf->rapport_type = "INDEX";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing";
    $this->doorkijk->componenten=array('alles'=>false,'aandelen'=>true);
	}
 
	function writeRapport()
	{
    $this->doorkijk->writeRapport();
    /*
    if($_POST['extra']=='xls')
    {
      $db=new DB();
      $query = "SELECT KeuzePerVermogensbeheerder.waarde as beleggingscategorie
FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE portefeuille='" . $this->portefeuille . "' AND
KeuzePerVermogensbeheerder.categorieIXP='Aandelen' AND
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' ";
      $db->SQL($query);
      $db->Query();
      $beleggingscategorien = array();
      while ($data = $db->nextRecord())
      {
        $beleggingscategorien[] = $data['beleggingscategorie'];
      }
      
      if (count($beleggingscategorien) > 0)
      {
        $this->kleuren=$this->doorkijk->kleuren;
        $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing zakelijke waarden";
        $this->vulPagina($beleggingscategorien);
      }
      
     // listarray($this->pdf->excelData);
    }
    */
	}
  
  
  
  function vulPagina($belCategorien='')
  {
    $doorkijkCategorieSoorten=array('Beleggingssectoren');//'Beleggingscategorien','Regios',
    $this->pdf->excelData[]=array('Beleggingssectorverdeling aandelen.');
    foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
    {
      $doorKijk=$this->doorkijk->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
      foreach($this->doorkijk->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleurdata) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
      {
        if(isset($doorKijk['details'][$categorie]))
        {
          $this->pdf->excelData[]=array($categorie,round($doorKijk['details'][$categorie]['waardeEUR'],0),round($doorKijk['details'][$categorie]['percentage'],2));
        }
      }
    }
  }
}