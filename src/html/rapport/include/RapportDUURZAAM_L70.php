<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/12 15:23:21 $
File Versie					: $Revision: 1.1 $

$Log: RapportDUURZAAM_L70.php,v $
Revision 1.1  2019/06/12 15:23:21  rvv
*** empty log message ***

Revision 1.3  2019/06/09 14:52:19  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***

Revision 1.3  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.2  2013/10/16 15:35:04  rvv
*** empty log message ***

Revision 1.1  2013/08/18 12:24:51  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportDUURZAAM_L70
{
	function RapportDUURZAAM_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
  
		$this->pdf->rapport_type = "DUURZAAM";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_jaar  =date('Y',$this->pdf->rapport_datumvanaf);
		$this->pdf->excelData 	= array();


	//	$this->pdf->rapport_titel = "Performance overzicht";
    $this->pdf->rapport_titel = "Allocaties inclusief uitsplitsing";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
	}

	function addDoorkijk()
  {
    global $__appvar;
    include_once($__appvar["basedir"]."/html/rapport/include/RapportDOORKIJK_L70.php");
    $this->doorkijk=new RapportDOORKIJK_L70($this->pdf, $this->portefeuille, 	$this->rapportageDatumVanaf , $this->rapportageDatum);
    
    $db=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde
                  ";
  
    $db->SQL($query);
    $db->Query();
    $this->doorkijk->kleuren=array();
    while($data = $db->nextRecord())
    {
      $this->doorkijk->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }
  
  
    $this->doorkijk->pdf->AddPage();
    $this->doorkijk->vulPagina();
    
  }


	function writeRapport()
	{
    $this->addDoorkijk();
	}
}
