<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
 		File Versie					: $Revision: 1.10 $

 		$Log: RapportRISK_L53.php,v $
 		Revision 1.10  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/03/29 15:57:04  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/06/19 15:22:08  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/06/20 14:07:32  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/06/18 15:48:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/06/04 16:13:28  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/31 13:51:07  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/05 15:52:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/30 16:03:17  rvv
 		*** empty log message ***
 		
 		
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");


//ini_set('max_execution_time',60);
class RapportRISK_L53
{
	function RapportRISK_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Risico Portefeuille.";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
    
       $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
	  $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}





	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars['RISKPaginas']=$this->pdf->page;



		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$tmp = $DB->LookupRecord();
    $memo=$tmp['memo'];
		$kleuren = unserialize($tmp['grafiek_kleur']);
    $this->ratingKleuren=array('AAA'=>array(255,204,0),'AA'=>array(102,102,102),'A'=>array(204,204,204),'BBB'=>array(255,255,102),'Non Inv. Grade'=>array(0,0,0),'Geen rating'=>array(255,255,255));
 
		foreach ($kleuren['Rating'] as $rating=>$waarde)
		  $this->ratingKleuren[$rating]=array($waarde['R']['value'],$waarde['G']['value'],$waarde['B']['value']);
		foreach ($kleuren['OIB'] as $cat=>$waarde)
		  $this->categorieKleuren[$cat]=array($waarde['R']['value'],$waarde['G']['value'],$waarde['B']['value']);


    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(10);

    
    $y=$this->pdf->getY();


  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$this->cashfow->genereerTransacties();
		$regels = $this->cashfow->genereerRows();
		$huidigeJaar=date("Y",$this->pdf->rapport_datum);
		foreach ($this->cashfow->regelsRaw as $regel)
		{

		  if($regel[2]=='lossing')
		  {
		    $jaar=substr($regel['0'],6,4);
		   // echo "$jaar > ".($huidigeJaar+15)."<br>\n";
		    if($jaar > ($huidigeJaar+15))
		      $jaar='Overig';

		    $cashflowJaar[$jaar] +=$regel[3];
		    $cashflowTotaal +=$regel[3];
		  }
		}





$this->toonZorgplicht(60+22);

$this->toonVerdeling(170,60);

$this->toonDuration(170,130);

$this->toonRating(20,130);


	//	$this->pdf->setXY(150,110);
  //  $this->VBarDiagram(150,60,$barData);



	}


	function toonVerdeling($x,$y)
	{
	  global $__appvar;
    $DB = new DB();
		$query = "SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as waardeEur ,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.HoofdcategorieOmschrijving
FROM
TijdelijkeRapportage
WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ) AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."

GROUP BY TijdelijkeRapportage.Hoofdcategorie
ORDER BY HoofdcategorieVolgorde,HoofdcategorieOmschrijving"; 

		$DB->SQL($query);
		$DB->Query();

		while($categorie = $DB->NextRecord())
	  {
	    if($categorie['HoofdcategorieOmschrijving']=='')
      {
	      $categorie['HoofdcategorieOmschrijving']='Geen hoofdcategorie';
        $categorie['Hoofdcategorie']='Geen hoofdcategorie';
      }
      $categorieOmschrijving[$categorie['Hoofdcategorie']]=$categorie['HoofdcategorieOmschrijving'];
	    $categorieData[$categorie['Hoofdcategorie']]['waarde'] +=$categorie['waardeEur'];
	    $categorieTotaalWaarde +=$categorie['waardeEur'];
	  }
    $categorieGrafiekKleuren=array();
	  foreach ($categorieData as  $categorie=>$waarden)
	  {
	    $categorieData[$categorie]['procent']=$waarden['waarde']/$categorieTotaalWaarde;
	    $this->categorieGrafiek[$categorieOmschrijving[$categorie]]=$categorieData[$categorie]['procent']*100;
      $categorieGrafiekKleuren[]=$this->categorieKleuren[$categorie];
	  }
    $this->categorieData=$categorieData;
    
        
    if(count($this->categorieGrafiek) > 1)
    {
      $this->pdf->setXY($x,$y-10);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->Cell(100,5,"Vermogensverdeling",0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->setXY($x,$y);
	    $this->PieChart(50, 50,$this->categorieGrafiek, '%l (%p)',$categorieGrafiekKleuren);
      //$this->pdf->Circle($x+22,$y+22,3);
    }

	}
  

	function toonRating($x,$y)
	{
	  global $__appvar;
    $DB = new DB();
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
 Left Join Rating ON (Fondsen.rating = Rating.rating )
WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ) AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
AND TijdelijkeRapportage.`type` NOT IN ('rekening','rente') AND CategorienPerHoofdcategorie.Hoofdcategorie='ICP_Obligaties'
GROUP BY Fondsen.rating
ORDER BY  Afdrukvolgorde"; 
		$DB->SQL($query);
		$DB->Query();

    $this->ratingGrafiek=array('AAA'=>0,'AA'=>0,'A'=>0,'BBB'=>0,'Non Inv. Grade'=>0,'Geen rating'=>0);
    
    
		while($rating = $DB->NextRecord())
	  {
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

	    $ratingData[$rating['rating']]['waarde'] +=$rating['waardeEur'];
	    $ratingTotaalWaarde +=$rating['waardeEur'];
	  }
	  foreach ($this->ratingGrafiek as  $rating=>$initWaarde)
	  {
	    $waarden=$ratingData[$rating];
	    $ratingData[$rating]['procent']=$waarden['waarde']/$ratingTotaalWaarde;
	    $this->ratingGrafiek[$rating]=$ratingData[$rating]['procent']*100;
	  }
    $this->ratingData=$ratingData;

    $ratingGrafiekKleuren=array();
    foreach ($this->ratingGrafiek as $rating=>$data)
      $ratingGrafiekKleuren[]=$this->ratingKleuren[$rating];
      
     
    $this->pdf->setXY(20,$y-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(100,5,"Debiteurenrisico Obligaties",0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(20,130);
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
  
  
   function toonZorgplicht($ycenter)
  {
    global $__appvar;
    $DB=new DB();
    $q="SELECT Memo FROM Portefeuilles WHERE Portefeuille = '".$this->portefeuille."'";
    $DB->SQL($q);
    $DB->Query();
    $tmp = $DB->LookupRecord();
    $memo=$tmp['Memo'];
    		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
    if($totaalWaarde['totaal']=='')
      $totaalWaarde['totaal']=0.001;
    
        $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$totaalWaarde['totaal']." as percentage,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.hoofdcategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde";
    $DB->SQL($query); //echo $query;exit;
    $DB->Query();
		while($data= $DB->nextRecord())
		{
		  $categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
      $categorieOmschrijving[$data['Zorgplicht']]=$data['hoofdcategorieOmschrijving'];
		}
    
    $zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;
    $height=count($categorieWaarden)*4+4;
    $this->pdf->setY($ycenter-($height+4)/2);
    
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    //$this->pdf->SetY(50);
    $beginY=$this->pdf->getY();
    $extraX=5;
  
  	$this->pdf->SetWidths(array($extraX,60,20,18,18,18,18));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','beleggingscategorie','portefeuille','minimaal','strategisch','maximaal',"check"));
   	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)

  //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen=''; 
      //if($zpwaarde['categorien'][$cat]['Minimum'])   
        $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
     //   $min='';   
      //if($zpwaarde['categorien'][$cat]['Maximum'])  
        $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
     // else
     //   $max='';  
        $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
        
      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,145+$extraX,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,145+$extraX,$this->pdf->getY(),3.9,3.9);  
        
      
  	  $this->pdf->row(array('',$categorieOmschrijving[$cat],$this->formatGetal($categorieWaarden[$cat],1)."%",$min,$norm,$max));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge+$extraX,$beginY,148,$height);
    $this->pdf->SetWidths(array($extraX,60+20+18+18+18+18));
     $this->pdf->ln(1);
     $this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize-1);
    $this->pdf->row(array('',$memo));
     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  }
  
  function toonDuration($x,$y)
  {
    
    $DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.Hoofdcategorie='ICP_Obligaties' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord(); 
    
 $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
TijdelijkeRapportage.hoofdcategorieOmschrijving AS HcategorieOmschrijving,
TijdelijkeRapportage.historischeWaarde,
TijdelijkeRapportage.historischeValutakoers,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) / ".$this->pdf->ValutaKoersStart." AS beginPortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0))  as beginwaardeLopendeJaar,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, totaalAantal) as totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieVolgorde as Afdrukvolgorde,
TijdelijkeRapportage.type,
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.variabeleCoupon,
Fondsen.Renteperiode,
Fondsen.Rentedatum,
emittentPerFonds.emittent,
TijdelijkeRapportage.fonds,
emittenten.rating as emittentRating,
TijdelijkeRapportage.fondsEenheid
FROM
TijdelijkeRapportage
Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds
Left Join emittentPerFonds ON emittentPerFonds.Fonds = TijdelijkeRapportage.Fonds  AND emittentPerFonds.vermogensbeheerder='$beheerder'
LEFT Join emittenten ON emittentPerFonds.emittent = emittenten.emittent AND emittentPerFonds.vermogensbeheerder = '$beheerder'
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek']." AND TijdelijkeRapportage.Hoofdcategorie='ICP_Obligaties'
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.rekening";

    $this->db=new DB();
    $DB->SQL($query);
		$DB->Query();
    $durationChart=array('0-3'=>0,'3-5'=>0,'5-7'=>0,'7-10'=>0,'>10'=>0,'overig'=>0);

    
    $actueleWaardePortefeuille=0;
		while ($data=$DB->nextRecord())
		{
      $rente=getRenteParameters($data['fonds'], $this->rapportageDatum);
      foreach($rente as $key=>$value)
        $data[$key]=$value;
     $actueleWaardePortefeuille+=$data['actuelePortefeuilleWaardeEuro'];
     
     
     
     if($data['Lossingsdatum'] <> '')
        $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
     else
        $lossingsJul=0;
        

   
       $rentedatumJul = adodb_db2jul($data['Rentedatum']);
       $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));

       $koers=getRentePercentage($data['fonds'],$this->rapportageDatum);

				  $renteDag=0;
			  if($data['variabeleCoupon'] == 1)
			  {
			    $rapportJul=adodb_db2jul($this->rapportageDatum);
			    $renteJul=adodb_db2jul($data['Rentedatum']);
          $renteStap=($data['Renteperiode']/12)*31556925.96;
          $renteDag=$renteJul;
          if($renteStap > 100000)
            while($renteDag<$rapportJul)
            {
              $renteDag+=$renteStap;
            }
			  }

       $ytm=0;
       $duration=0;
       $modifiedDuration=0;
       
       $aandeel=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde['totaal']*100;

        if($lossingsJul > 0)
	      {

	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $data['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$data['fonds']]['lossingskoers'];
	        $year = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$year)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         $duration=$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['fonds']]['ActueelWaarde'];
	         if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
	         else
	           $modifiedDuration=$duration/(1+$ytm/100);
	         

           $totalen['yield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['actueleValuta']*$aandeel;
	         $totalen['ytm']+=$ytm*$aandeel;
	         $totalen['duration']+=$duration*$aandeel;
	         $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen['restLooptijd']+=$restLooptijd*$aandeel;
           
           
           if($duration<3)
             $durationChart['0-3']+=$aandeel;
           elseif($duration<5)
             $durationChart['3-5']+=$aandeel;
           elseif($duration<7)
             $durationChart['5-7']+=$aandeel;
           elseif($duration<10)
             $durationChart['7-10']+=$aandeel;
           else
             $durationChart['>10']+=$aandeel;
             
 
	      }
        else
        {
          $durationChart['overig']+=$aandeel;
        }
    }
    
    $durationChartKleuren=array();
    $kleuren=array('0-3'=>array(255,204,0),'3-5'=>array(102,102,102),'5-7'=>array(204,204,204),'7-10'=>array(255,255,102),'>10'=>array(0,0,0),'overig'=>array(255,255,255));
    //listarray($kleuren);
    //listarray($durationChart);     
    foreach($durationChart as $key=>$value)
    {
      $durationChartKleuren[]=$kleuren[$key];
    } 
	  // listarray($durationChart);
    $this->pdf->setXY($x,$y-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(100,5,"Duration obligaties",0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetXY($x,$y); //
    $this->PieChart(50, 50,$durationChart, '%l (%p)',$durationChartKleuren);
    
  }

}
?>