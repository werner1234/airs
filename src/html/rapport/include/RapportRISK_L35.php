<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/01/21 09:00:44 $
 		File Versie					: $Revision: 1.9 $

 		$Log: RapportRISK_L35.php,v $
 		Revision 1.9  2018/01/21 09:00:44  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/01/13 19:10:29  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/04/14 16:51:17  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/03/25 13:27:46  rvv
 		*** empty log message ***

 		Revision 1.5  2012/03/18 16:08:24  rvv
 		*** empty log message ***

 		Revision 1.4  2012/03/17 11:58:16  rvv
 		*** empty log message ***

 		Revision 1.3  2012/03/14 17:30:11  rvv
 		*** empty log message ***

 		Revision 1.2  2012/03/04 11:39:58  rvv
 		*** empty log message ***

 		Revision 1.1  2012/02/29 16:52:49  rvv
 		*** empty log message ***

 		Revision 1.10  2012/02/26 15:17:43  rvv
 		*** empty log message ***

 		Revision 1.9  2012/01/04 16:28:38  rvv
 		*** empty log message ***

 		Revision 1.8  2011/12/07 19:14:53  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/03 06:42:47  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/15 16:14:39  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/13 14:41:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/29 06:38:42  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");


//ini_set('max_execution_time',60);
class RapportRISK_L35
{
	function RapportRISK_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Verdeling van de vastrentende waarden naar kwaliteit en looptijd";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function getRating()
	{
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
AND TijdelijkeRapportage.`type` NOT IN ('rekening','rente') AND TijdelijkeRapportage.hoofdcategorie='VAR'
GROUP BY Fondsen.rating
ORDER BY Hoofdcategorie, Afdrukvolgorde"; //AND CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISM'

		$DB->SQL($query);
		$DB->Query();

		while($rating = $DB->NextRecord())
	  {
	    if($rating['rating']=='')
	      $rating['rating']='Geen rating';

	    $ratingData[$rating['rating']]['waarde'] +=$rating['waardeEur'];
	    $ratingTotaalWaarde +=$rating['waardeEur'];
	  }
	  foreach ($ratingData as  $rating=>$waarden)
	  {
	    $ratingData[$rating]['procent']=$waarden['waarde']/$ratingTotaalWaarde;
	    $this->ratingGrafiek[vertaalTekst($rating,$this->pdf->rapport_taal)]=$ratingData[$rating]['procent']*100;
	  }
    $this->ratingData=$ratingData;

    return $this->ratingData;
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
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
//echo $q;listarray($kleuren);
		foreach ($kleuren['Rating'] as $rating=>$waarde)
		  $ratingKleuren[$rating]=array($waarde['R']['value'],$waarde['G']['value'],$waarde['B']['value']);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(10);

    $this->getRating();
    $y=$this->pdf->getY();





  	$cashflowJaar=array();
		$cashflowTotaal=0;
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $cashfow->genereerRows();
		$huidigeJaar=date("Y",$this->pdf->rapport_datum);
		foreach ($cashfow->regelsRaw as $regel)
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





    $this->pdf->setY(130);
    $this->pdf->SetWidths(array(20,25,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('', vertaalTekst('kwaliteit', $this->pdf->rapport_taal), vertaalTekst('Absoluut', $this->pdf->rapport_taal), vertaalTekst('in %', $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $ratingGrafiekKleuren=array();
    foreach ($this->ratingData as $rating=>$data)
    {
      $this->pdf->Row(array('', vertaalTekst($rating,$this->pdf->rapport_taal),$this->formatGetal($data['waarde'],0),$this->formatGetal($data['procent']*100,1)."%"));
      $ratingGrafiekKleuren[]=$ratingKleuren[$rating];
    }


    $this->pdf->setY(130);
		$this->pdf->SetWidths(array(160,15,25,25,40,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Row(array('',vertaalTekst('Jaar',$this->pdf->rapport_taal),vertaalTekst('Absoluut',$this->pdf->rapport_taal),vertaalTekst("in %",$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		foreach ($cashflowJaar as $jaar=>$waarde)
    {
      $this->pdf->Row(array('',$jaar,$this->formatGetal($waarde,0),$this->formatGetal($waarde/$cashflowTotaal*100,1)."%"));
      $barData[$jaar]['percentage']=$waarde/$cashflowTotaal*100;
    }

    $this->pdf->setXY(44,42);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(0,5,vertaalTekst("Verdeling naar kwaliteit",$this->pdf->rapport_taal));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(20,55);
	  $this->PieChart(50, 50,$this->ratingGrafiek, '%l (%p)',$ratingGrafiekKleuren);



		$this->pdf->setXY(150,110);
    $this->VBarDiagram(150,60,$barData);



	}


    function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
      $this->pdf->Multicell($w,5,vertaalTekst('Verdeling over jaren',$this->pdf->rapport_taal),'','C');
      $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
    //  $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    //  $this->pdf->Multicell(20,5,'%','','L');
      $this->pdf->setXY($xPositie,$yPositie);


      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $datumTotalen[$datum]+=$waarde;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          if($waarde < 0)
          {
            $verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
          }


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }

      $colors=array('lossing'=>array($this->allekleuren['OIB']['OBL-ST']['R']['value'],$this->allekleuren['OIB']['OBL-ST']['G']['value'],$this->allekleuren['OIB']['OBL-ST']['B']['value']),
      'rente'=>array($this->allekleuren['OIB']['Liquiditeiten']['R']['value'],$this->allekleuren['OIB']['Liquiditeiten']['G']['value'],$this->allekleuren['OIB']['Liquiditeiten']['B']['value']));

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      if($color == null)
      {
        $color=array(20,20,150);
      }
      $maxVal=max($datumTotalen);
      $minVal = 0;


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

      $unit = $hGrafiek / $maxVal * -1;

      $nulYpos =0;

      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."%",0,0,'L');
        $n++;
        if($n>1000)
          break;
      }

    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+5.25,$legenda[$datum],45);

           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

/*
   $x1=$xval-50;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Coupons';
   $vertaling['lossing']='Lossingen';

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=40;
         $i++;

      }
*/
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
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