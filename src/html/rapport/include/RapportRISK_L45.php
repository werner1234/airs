<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
 		File Versie					: $Revision: 1.9 $

 		$Log: RapportRISK_L45.php,v $
 		Revision 1.9  2018/08/18 12:40:15  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.8  2016/12/07 16:31:40  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/12/30 19:01:23  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/12/02 16:16:29  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/11/01 17:52:49  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/11/01 17:32:17  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/11/01 17:25:34  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/10/28 16:42:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/09/10 16:15:55  rvv
 		*** empty log message ***
 		
 
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L22.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

//ini_set('max_execution_time',60);
class RapportRISK_L45
{
	function RapportRISK_L45($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_RISK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_RISK_titel;
		else
			$this->pdf->rapport_titel = "Rendement & Risicokenmerken";

		$this->pdf->rapport_titel2='';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";

		$this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec,$nb=false)
	{
	  if($nb==true && is_Null($waarde))
      return 'N/B';
    
    
		return number_format($waarde,$dec,",",".");
	}
  


	function writeRapport()
	{
		global $__appvar;

		$this->pdf->addPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->headerY=30;


    
    //$this->printSTDDEV();
    $this->printAFM();
    $this->printVAR();
    //$this->toonRating(120,120);
    
    $this->pdf->SetFillColor(255);
    $this->pdf->SetDrawColor(0);
    $this->pdf->SetTextColor(0);
      
	}
 
  
  function standard_deviation($aValues)
  {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= count($aValues)-1;
    return (float) sqrt($fVariance);
  }
  
  function printSTDDEV()
  {

  
    
    $this->pdf->SetY($this->headerY);
		$this->pdf->SetWidths(array(150,75,21,21));
  	$this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge+150,$this->pdf->getY(),120,(2*4));
    $this->pdf->Row(array('','Standaarddeviatie',date('d-m-Y',$this->pdf->rapport_datumvanaf),date('d-m-Y',$this->pdf->rapport_datum)));
  	$this->pdf->row(array('','portefeuille',$this->formatGetal($stddev['totaal'][$this->rapportageDatumVanaf],1),
     $this->formatGetal($stddev['totaal'][$this->rapportageDatum],1)));
   
    $this->pdf->Rect($this->pdf->marge+150,$this->pdf->getY(),120,((count($stddev))*4));

    $this->pdf->Row(array('','Standaarddeviatie hoofdcategorie',date('d-m-Y',$this->pdf->rapport_datumvanaf),date('d-m-Y',$this->pdf->rapport_datum)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  	foreach ($stddev as $cat=>$stddevData)
    {
      if($cat <> 'totaal')
      {
      if($att->categorien[$cat] <> '')
        $categorieOmschrijving=$att->categorien[$cat];
      else
       $categorieOmschrijving=$cat;
  	  $this->pdf->row(array('','Standaarddeviatie '.$categorieOmschrijving,$this->formatGetal($stddevData[$this->rapportageDatumVanaf],1),
        $this->formatGetal($stddevData[$this->rapportageDatum],1)));
      
      }
      
    }
   // $this->pdf->ln(2);

    
  }


  function printAFM()
  {
    global $__appvar;
    $db=new DB();
    
    $query="SELECT afmCategorien.id,afmCategorien.afmCategorie,afmCategorien.standaarddeviatie,afmCategorien.omschrijving 
    FROM afmCategorien WHERE afmCategorien.standaarddeviatie <> 0 order by omschrijving";
    $db->SQL($query);
    $db->Query();
    $afmCategorienTmp=array('Aandelen'=>array(),'Staatsleningen'=>array(),'Bedrijfsobligaties'=>array(),'Alternatieven'=>array(),'Overige'=>array());
    while($data=$db->nextRecord())
    {
      if(stripos($data['omschrijving'],'Aand')!==false)
        $volgorde='Aandelen';
      elseif(stripos($data['omschrijving'],'Staa')!==false)
        $volgorde='Staatsleningen';
      elseif(stripos($data['omschrijving'],'Bedr')!==false)
        $volgorde='Bedrijfsobligaties'; 
      elseif(stripos($data['omschrijving'],'Hedg')!==false || stripos($data['omschrijving'],'Gron')!==false)
        $volgorde='Alternatieven';     
      else
        $volgorde='Overige';    
      $afmCategorienTmp[$volgorde][]=$data;
      
    }
 /*
    $perioden=array('start'=>$this->rapportageDatumVanaf,'stop'=>$this->rapportageDatum);
    $verdeling=array('afm'=>'afmCategorie');//,'hoofdcat'=>'hoofdCategorie'
    
    //$perioden=array('stop'=>$this->rapportageDatum);
    //$verdeling=array('hoofdcat'=>'hoofdCategorie');//'afm'=>'afmCategorie',
    foreach($perioden as $periode=>$datum)
    {
      foreach($verdeling as $verdelingKort=>$categorie)
      {
        $tmp=new rapportSDberekening($this->portefeuille,$datum);
        $tmp->addReeks($categorie);
        $tmp->berekenWaarden();
       // echo "2 $datum $categorie <br>\n";
//        $stdevWaarden[$periode][$categorie]['stdev']=$tmp->getUitvoer();
//        $stdevWaarden[$periode][$categorie]['catData']=$this->categorien;
        $this->categorien[$periode][$categorie] =$tmp->categorien;
      }
    }

*/

  /*  
    
    
    
    $tmp=new rapportSDberekening($this->portefeuille,$this->rapportageDatumVanaf,'afmCategorie');
    $stdevBegin=$tmp->getUitvoer();
    $tmp=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,'afmCategorie');
    $stdev=$tmp->getUitvoer();
    */
    
    $afmBegin=AFMstd($this->portefeuille,$this->rapportageDatumVanaf,$this->pdf->debug);
    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);
    $sd=new rapportSDberekening($this->portefeuille,$this->rapportageDatum);
    $wensStartDatum=(substr($this->rapportageDatumVanaf,0,4)-3).'-'.substr($this->rapportageDatumVanaf,5,5);
    $wensStartJul=db2jul($wensStartDatum);
    if($wensStartJul > $sd->settings['julStartdatum'])
    {
      $sd->settings['Startdatum'] = $wensStartDatum;
      $sd->settings['julStartdatum'] = $wensStartJul;
    }


    $perfWaarden=$sd->getReeksen();
    $totaalReeks=array();
    foreach($perfWaarden['totaal'] as $datum=>$waarden)
      $totaalReeks[$datum]=$waarden['perf'];
    $perfAantal=count($totaalReeks);



    $rapStartJul=db2jul($this->rapportageDatumVanaf);
    $startCounter=0;
    $eindCounter=0;
    foreach(array_reverse($totaalReeks) as $datum=>$perf)
    {
      $julDatum=db2jul($datum);
      if($julDatum < $rapStartJul)
      {

        if ($startCounter < 12)
        {
          $perfReeksen['start'][12][$datum] = $perf;
        }
        if ($startCounter < 24)
        {
          $perfReeksen['start'][24][$datum] = $perf;
        }
        if ($startCounter < 36)
          $perfReeksen['start'][36][$datum] = $perf;
        $startCounter++;
      }
      if ($eindCounter < 12)
      {
        $perfReeksen['eind'][12][$datum] = $perf;
      }
      if ($eindCounter < 24)
      {
        $perfReeksen['eind'][24][$datum] = $perf;
      }
      if ($eindCounter < 36)
        $perfReeksen['eind'][36][$datum] = $perf;
      $eindCounter++;

    }
    foreach($perfReeksen as $periode=>$maandData)
    {
      foreach($maandData as $maanden=>$reeksData)
      {
        if(count($reeksData) <> $maanden)
          $stdev[$periode][$maanden]='';
        else
          $stdev[$periode][$maanden]=$this->standard_deviation($reeksData)*sqrt(12);
      }
    }



   // $afmHoofcategorieBegin=$tmp->AFMstd($this->portefeuille,$this->rapportageDatumVanaf,'','hoofdcategorie');
   // $afmHoofcategorie=$tmp->AFMstd($this->portefeuille,$this->rapportageDatum,'','hoofdcategorie');


    $this->pdf->SetY($this->headerY);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','C','C','C','C'));
    $this->pdf->SetWidths(array(65,30,30));
    $this->pdf->row(array('',date('d-m-Y',$this->pdf->rapport_datumvanaf),date('d-m-Y',$this->pdf->rapport_datum)));
   // $this->pdf->SetWidths(array(65,21,20,21,20));

    $this->pdf->row(array('Standaarddeviatie AFM*'));//,'AFM methodiek*','AFM methodiek*'
    $this->pdf->SetAligns(array('L','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $n=0;
    foreach($afmCategorienTmp as $categorie=>$categorieRegels)
    {
      if($n<>0)
        $this->pdf->Ln();
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($categorie));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($categorieRegels as $index=>$regel)
      {
        $this->pdf->row(array($regel['omschrijving'],
                              $this->formatGetal($regel['standaarddeviatie'],1,true),
                              $this->formatGetal($regel['standaarddeviatie'],1,true)));
      }
      $n++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Portefeuille',
                              $this->formatGetal($afmBegin['std'],1,true),
                              $this->formatGetal($afm['std'],1,true)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);                          
    $this->pdf->ln();


    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Standaarddeviatie werkelijk **'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('36 maands',
                      $this->formatGetal($stdev['start'][36],1,true),
                      $this->formatGetal($stdev['eind'][36],1,true)));
    $this->pdf->row(array('24 maands',
                      $this->formatGetal($stdev['start'][24],1,true),
                      $this->formatGetal($stdev['eind'][24],1,true)));
    $this->pdf->row(array('12 maands',
                      $this->formatGetal($stdev['start'][12],1,true),
                      $this->formatGetal($stdev['eind'][12],1,true)));



    /*
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('Standaarddeviatie hoofdcategorie'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($this->categorien['stop']['hoofdCategorie'] as $hoofdcategorie=>$categorieData)
    {
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       if($hoofdcategorie <> 'totaal')
       {
         $this->pdf->row(array($categorieData['omschrijving'],
         $this->formatGetal($afmHoofcategorieBegin['std'][$hoofdcategorie],1),
         $this->formatGetal($stdevWaarden['start']['hoofdCategorie']['stdev'][$hoofdcategorie],1),
         $this->formatGetal($afmHoofcategorie['std'][$hoofdcategorie],1),
         $this->formatGetal($stdevWaarden['stop']['hoofdCategorie']['stdev'][$hoofdcategorie],1)));
       }
    }
    */
    $this->pdf->SetWidths(array(20,120));
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->ln();
$this->pdf->Row(array("*","Berekening van de standaarddeviatie met de AFM-methodiek op basis van historische gegevens van de VBA"));
$this->pdf->Row(array("**","Berekening van de werkelijke volatiliteit van de portefeuille op basis van de laatste 36, 24 en 12 maanden voor de aangegeven datum."));

 




    //$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,(2*4));
   // $this->pdf->Row(array('AFM Standaarddeviatie',date('d-m-Y',$this->pdf->rapport_datumvanaf),date('d-m-Y',$this->pdf->rapport_datum)));
  //	$this->pdf->row(array('portefeuille',$this->formatGetal($afmBegin['std'],1),$this->formatGetal($afm['std'],1)));


  }
  
  
  function printVAR()
  {
    global $__appvar;
    $this->pdf->setXY(180,122);
    include_once($__appvar["basedir"]."/html/rapport/include/RapportVAR_L45.php");
    include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L45.php");

    $var=new RapportVAR_L45($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $var->dataOnly=true;
    $var->writeRapport();
    $vardata=$var->varData['totalen']['totaal'];
 		$this->pdf->setXY(180,37);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(180,50,20));
		$this->pdf->SetAligns(array('L','L','R'));
		$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);

   
		$this->pdf->row(array('',vertaalTekst("Karakteristieken risicomijdende portefeuille",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$cashOptions=array(vertaalTekst("Gemiddelde coupon-yield",$this->pdf->rapport_taal)=>$this->formatGetal($vardata['yield']/$vardata['aandeel'],3),
		                   vertaalTekst("Gemiddelde YTM",$this->pdf->rapport_taal)=>$this->formatGetal($vardata['ytm']/$vardata['aandeel'],2),
		                   vertaalTekst("Modified duration",$this->pdf->rapport_taal)=>$this->formatGetal($vardata['modifiedDuration']/$vardata['aandeel'],2),
		                   vertaalTekst('Resterende looptijd',$this->pdf->rapport_taal)=>$this->formatGetal($vardata['restLooptijd']/$vardata['aandeel'],2));
    foreach ($cashOptions as $option=>$waarde)
		  $this->pdf->row(array('',$option,$waarde));
//	  $this->pdf->setXY(160,190);
	//	  $this->VBarDiagram(160,60,$jaarTotalen);


    $ois=new RapportOIS_L45($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $ois->dataOnly=true;
    $ois->writeRapport();
    $oisData=$ois->oisData;
    
    
    $this->pdf->setXY(180,122);
    $ois->BarDiagram(90,70,$oisData['ratingverdeling']['percentage'],'%l (%p)',$oisData['ratingverdeling']['kleurData'],vertaalTekst("Ratingverdeling vastrentende waarden",$this->pdf->rapport_taal));



	}
    

  
  
  
	function toonRating($x,$y)
	{
	  global $__appvar;
    $DB = new DB();
    
    
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
    $this->ratingKleuren=array('AAA'=>array(255,204,0),'AA'=>array(102,102,102),'A'=>array(204,204,204),'BBB'=>array(255,255,102),'Non Inv. Grade'=>array(0,0,0),'Geen rating'=>array(255,255,255));
 
		foreach ($kleuren['Rating'] as $rating=>$waarde)
		  $this->ratingKleuren[$rating]=array($waarde['R']['value'],$waarde['G']['value'],$waarde['B']['value']);


		$query = "SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEur ,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS HoofdcategorieOmschrijving,
Fondsen.rating,
ifnull( Rating.Afdrukvolgorde,100) as Afdrukvolgorde
FROM
TijdelijkeRapportage
Left Join Beleggingscategorien ON (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
Left Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
  Join Rating ON (Fondsen.rating = Rating.rating )
WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ) AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
AND TijdelijkeRapportage.`type` NOT IN ('rekening','rente') AND Fondsen.fondssoort='OBL'
GROUP BY Fondsen.rating
ORDER BY  Afdrukvolgorde"; 
		$DB->SQL($query);
		$DB->Query();
 
		while($rating = $DB->NextRecord())
	  {
	   /*
	   if(substr($rating['rating'],0,3)=='AAA')
       $rating['rating']='AAA';
	   elseif(substr($rating['rating'],0,2)=='AA')
       $rating['rating']='AA';
	   elseif(substr($rating['rating'],0,3)=='BBB')
       $rating['rating']='BBB';     
	   elseif($rating['rating'] <> '')
       $rating['rating']='Non Inv. Grade';
     else           
	     $rating['rating']='Geen rating';
     */
	    $ratingData[$rating['rating']]['waarde'] +=$rating['waardeEur'];
	    $ratingTotaalWaarde +=$rating['waardeEur'];
	  }
	  foreach ($ratingData as  $rating=>$initWaarde)
	  {
	    $waarden=$ratingData[$rating];
	    $ratingData[$rating]['procent']=$waarden['waarde']/$ratingTotaalWaarde;
	    $this->ratingGrafiek[$rating]=$ratingData[$rating]['procent']*100;
	  }
    $this->ratingData=$ratingData;

    $ratingGrafiekKleuren=array();
    foreach ($this->ratingGrafiek as $rating=>$data)
      $ratingGrafiekKleuren[]=$this->ratingKleuren[$rating];
      
     
    $this->pdf->setXY(170,$y-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(100,5,"Rating Obligaties",0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(170,130);
	  $this->PieChart(50, 50,$this->ratingGrafiek, '%l (%p)',$ratingGrafiekKleuren);


    return $this->ratingData;
	}
  
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $w + $radius ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag -($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
      }

  }

}
?>