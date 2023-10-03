<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/09 15:11:04 $
 		File Versie					: $Revision: 1.1 $

 		$Log: RapportOIB_L79.php,v $
 		Revision 1.1  2019/10/09 15:11:04  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/03/23 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/01/23 16:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2019/01/20 12:14:00  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/11/04 11:45:31  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/11/04 11:15:32  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/11/01 07:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/31 17:23:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/02/10 18:09:12  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/01/28 11:45:33  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/01/28 09:22:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/01/27 17:31:22  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/12/30 16:38:17  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/11/05 13:37:27  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/11/04 17:40:21  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/10/14 17:27:54  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/10/31 16:59:18  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/05/12 15:11:00  rvv
 		*** empty log message ***
 		
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportOIB_L79
{
	function RapportOIB_L79($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Verdeling bezittingen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

	}

  function formatGetal($waarde, $dec,$procent=false,$toonNul=false)
  {
    if ($waarde===null)
      return '';
    if($waarde==0 && $toonNul==false)
      return '';
    $data=number_format($waarde,$dec,",",".");
    if($procent==true)
      $data.="%";
    return $data;
  }



	function writeRapport()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['OIBPaginas']=$this->pdf->rapport_titel;
    // print categorie headers
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    if(is_array($this->pdf->portefeuilles))
      $this->portefeuilles=$this->pdf->portefeuilles;
    else
      $this->portefeuilles=array($this->portefeuille);

    $db=new DB();
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $db->SQL($q);
    $db->Query();
    $kleuren = $db->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $grafiekData=array();
    
    $query="SELECT rapportageDatum, beleggingscategorie, beleggingscategorieOmschrijving, SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
    WHERE TijdelijkeRapportage.rapportageDatum IN('".$this->rapportageDatumVanaf."','".$this->rapportageDatum."') AND
    TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
    GROUP BY rapportageDatum, beleggingscategorie ORDER BY rapportageDatum,beleggingscategorieVolgorde ";
  
    $db->SQL($query);
    $db->Query();
    $verdelingOpDatum=array();
    $hoofdcategorieOmschrijving=array();
    $totaleWaarde=array();
    while($data=$db->nextRecord())
    {
      $verdelingOpDatum[$data['rapportageDatum']][$data['beleggingscategorie']]+=$data['actuelePortefeuilleWaardeEuro'];
      $hoofdcategorieOmschrijving[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      $totaleWaarde[$data['rapportageDatum']]+=$data['actuelePortefeuilleWaardeEuro'];
    }

    
    if(!isset($allekleuren['OIB']['Geen']))
      $allekleuren['OIB']['Geen']=array('R'=>array('value'=>10),'G'=>array('value'=>10),'B'=>array('value'=>110));
    foreach($verdelingOpDatum as $datum=>$hoofdCategorieData)
    {
      foreach($hoofdCategorieData as $categorie=>$waarde)
      {
        $kleur=$allekleuren['OIB'][$categorie];
        
        $percentage=$waarde/$totaleWaarde[$datum]*100;
        
        $grafiekData[$datum]['Percentage'][$hoofdcategorieOmschrijving[$categorie].' ('.$this->formatGetal($percentage,1).'%)']=$percentage;
        $grafiekData[$datum]['Kleur'][] = array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      }
    }
    
    $headerHeight=30;
    //$lwb=(297/2)-$this->pdf->marge; //133.5
    $vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
    $chartsize=65;
    
    //listarray($grafiekData);
    $i=0;
    $ystart=60;
    foreach($grafiekData as $datum=>$depotbankData)
    {
      if($i>0 && $i%3==0)
      {
        if($this->pdf->getY()>100)
        {
          $this->pdf->addPage();
          $ystart=30;
        }
        else
        {
          $ystart=95;
        }
        $i=0;
      }
      $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
  
  
      $this->pdf->setXY($this->pdf->marge+50+110*$i , $ystart);

      $legendaStart=array($this->pdf->marge+50+110*$i,$ystart+$chartsize+10);
      PieChart_L79($this->pdf, $chartsize, $vwh, $depotbankData['Percentage'], '%l', $depotbankData['Kleur'], vertaalTekst('Verdeling op', $this->pdf->rapport_taal).' '.date('d-m-Y',db2jul($datum)), $legendaStart);
      $i++;
    }

  }


}
?>